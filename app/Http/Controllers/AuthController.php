<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // =========================================================
    // GOOGLE OAUTH (Real Socialite)
    // =========================================================

    /**
     * Redirect to Google OAuth — opens in a popup window.
     * The popup redirects here, Google sends them back to googleCallback().
     */
    public function googleRedirect(Request $request)
    {
        if ($request->has('user_type')) {
            session(['oauth_user_type' => $request->user_type]);
        }
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Handle Google OAuth callback.
     * After authentication, this closes the popup and sends the result back to parent.
     */
    public function googleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $providerData = [
                'id'           => $googleUser->getId(),
                'email'        => $googleUser->getEmail(),
                'name'         => $googleUser->getName(),
                'avatar'       => $googleUser->getAvatar(),
                'access_token' => $googleUser->token,
                'user_type'    => session()->pull('oauth_user_type', null),
            ];

            $result = $this->authService->findOrCreateUserFromOAuth('google', $providerData);

            // If we need to offer linking (email exists but not linked)
            if (is_array($result) && isset($result['action']) && $result['action'] === 'link') {
                // Store link-offer data in session, then close popup to trigger the parent page
                session()->put('oauth_link_offer', [
                    'provider'    => 'google',
                    'email'       => $providerData['email'],
                    'name'        => $providerData['name'],
                    'provider_id' => $providerData['id'],
                ]);
                // Return JS to close popup and tell parent to show link offer
                return $this->popupClose('link_offer');
            }

            if ($result->status === 'pending') {
                return $this->popupClose('error', 'Tài khoản của bạn đang chờ duyệt. Vui lòng quay lại sau.');
            }
            if ($result->status === 'banned') {
                return $this->popupClose('error', 'Tài khoản của bạn đã bị khóa.');
            }
            if ($result->status === 'inactive') {
                return $this->popupClose('error', 'Tài khoản của bạn đã bị vô hiệu hóa.');
            }

            // Log in the user
            session(['user' => $result]);

            // Close popup and redirect parent window to intended page
            return $this->popupClose('success');

        } catch (Exception $e) {
            return $this->popupClose('error', $e->getMessage());
        }
    }

    /**
     * Return an HTML page that closes the popup and communicates result to parent.
     */
    private function popupClose(string $status, string $message = '')
    {
        $statusJson = json_encode(['status' => $status, 'message' => $message]);
        return response()->make(
            "<!DOCTYPE html><html><head><title>Đang xử lý...</title></head><body>
            <script>
                try {
                    if (window.opener && !window.opener.closed) {
                        window.opener.postMessage({$statusJson}, window.location.origin);
                    }
                } catch(e) {}
                window.close();
            </script>
            <p style='font-family:sans-serif;text-align:center;margin-top:60px;color:#555'>
                Đang xử lý, cửa sổ này sẽ tự đóng...
            </p>
            </body></html>"
        );
    }

    // =========================================================
    // OAUTH MOCK (Zalo / Other providers without real OAuth)
    // =========================================================

    /**
     * Redirect to OAuth Mock Login screen.
     */
    public function oauthRedirect(Request $request, string $provider)
    {
        if ($request->has('user_type')) {
            session(['oauth_user_type' => $request->user_type]);
        }
        return redirect()->route('oauth.mock', ['provider' => $provider]);
    }

    /**
     * Show mock login screen for the provider.
     */
    public function oauthMock(string $provider)
    {
        return view('pages.oauth-mock', ['provider' => $provider]);
    }

    /**
     * Callback for Mock OAuth.
     */
    public function oauthCallback(Request $request, string $provider)
    {
        $providerData = [
            'id'           => $request->input('provider_id') ?: 'mock_id_' . mt_rand(100000, 999999),
            'email'        => $request->input('email'),
            'name'         => $request->input('name', 'OAuth User'),
            'access_token' => 'mock_access_token_' . bin2hex(random_bytes(10)),
            'user_type'    => session()->pull('oauth_user_type', null),
        ];

        if (empty($providerData['email'])) {
            return redirect()->route('login')->with('error', 'Cần cung cấp email để thực hiện liên kết.');
        }

        $result = $this->authService->findOrCreateUserFromOAuth($provider, $providerData);

        if (is_array($result) && isset($result['action']) && $result['action'] === 'link') {
            return redirect()->route('auth.link-provider-offer', [
                'provider'    => $provider,
                'email'       => $providerData['email'],
                'name'        => $providerData['name'],
                'provider_id' => $providerData['id'],
            ]);
        }

        if ($result->status === 'pending') {
            return redirect()->route('login')->with('error', 'Tài khoản của bạn đang chờ duyệt. Vui lòng quay lại sau.');
        }
        if ($result->status === 'banned') {
            return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị khóa.');
        }
        if ($result->status === 'inactive') {
            return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị vô hiệu hóa.');
        }

        session(['user' => $result]);
        return redirect()->intended(route('home'));
    }

    // =========================================================
    // ACCOUNT LINKING
    // =========================================================

    /**
     * Show linking offer page (for popup link-offer flow).
     */
    public function linkProviderOffer(Request $request)
    {
        // Support both query params (Zalo mock flow) and session (Google popup flow)
        $data = session()->pull('oauth_link_offer', []);

        return view('pages.link-provider-offer', [
            'provider'    => $request->query('provider', $data['provider'] ?? ''),
            'email'       => $request->query('email', $data['email'] ?? ''),
            'name'        => $request->query('name', $data['name'] ?? ''),
            'provider_id' => $request->query('provider_id', $data['provider_id'] ?? ''),
        ]);
    }

    /**
     * Link provider action.
     */
    public function linkProvider(Request $request)
    {
        $provider = $request->input('provider');
        $providerData = [
            'id'    => $request->input('provider_id'),
            'email' => $request->input('email'),
            'name'  => $request->input('name'),
        ];

        $user = User::where('email', $providerData['email'])->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Không tìm thấy tài khoản để liên kết.');
        }

        try {
            $this->authService->linkOAuthProvider($user, $provider, $providerData);
            session(['user' => $user]);
            return redirect()->route('home')->with('success', 'Liên kết tài khoản ' . ucfirst($provider) . ' thành công!');
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', $e->getMessage());
        }
    }

    /**
     * Unlink provider.
     */
    public function unlinkProvider(string $provider)
    {
        $userSession = session('user');
        if (!$userSession) {
            return redirect()->route('login');
        }

        $user = User::find($userSession->id);
        if ($user) {
            $user->oauthProviders()->where('provider', $provider)->delete();
            session(['user' => $user]);
        }

        return redirect()->back()->with('success', 'Đã gỡ liên kết tài khoản ' . ucfirst($provider));
    }

    // =========================================================
    // STANDARD AUTH (Email / Phone)
    // =========================================================

    /**
     * Show login page.
     */
    public function login()
    {
        if (session()->has('user')) {
            return redirect()->route('home');
        }
        return view('pages.login');
    }

    /**
     * Handle login submission.
     */
    public function handleLogin(Request $request)
    {
        $action   = $request->input('action', 'login');
        $loginId  = $request->input('login_id');
        
        $isEmail  = filter_var($loginId, FILTER_VALIDATE_EMAIL) !== false;
        $email    = $isEmail ? $loginId : null;
        $phone    = !$isEmail ? $loginId : null;

        if ($action === 'register') {
            $name = $request->input('name', 'Khách hàng Demo');
            $userType = $request->input('user_type');
            if (!in_array($userType, ['renter', 'agent'])) {
                $userType = 'renter'; // Default to renter if invalid or not provided (should be provided due to frontend validation)
            }

            $user = User::where(function($query) use ($loginId) {
                $query->where('email', $loginId)->orWhere('phone', $loginId);
            })->first();
            
            if ($user) {
                return redirect()->back()->with('error', 'Email hoặc Số điện thoại này đã tồn tại trong hệ thống.');
            }

            $user = User::create([
                'name'        => $name,
                'email'       => $email,
                'phone'       => $phone ?: '09' . mt_rand(10000000, 99999999),
                'password_hash' => \Illuminate\Support\Facades\Hash::make($request->input('password')),
                'user_type'   => $userType,
                'is_verified' => false,
                'status'      => 'pending',
            ]);

            return redirect()->route('login')->with('register_success', 'Đăng ký thành công, vui lòng đợi kiểm duyệt');
        }

        // Login flow
        $user = User::where(function($query) use ($loginId) {
            $query->where('email', $loginId)->orWhere('phone', $loginId);
        })->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Tài khoản không tồn tại. Vui lòng đăng ký.');
        }

        if (empty($user->password_hash)) {
            return redirect()->back()->with('error', 'Tài khoản này chưa thiết lập mật khẩu. Vui lòng đăng nhập bằng Google/Zalo.');
        }

        if (!\Illuminate\Support\Facades\Hash::check($request->input('password'), $user->password_hash)) {
            return redirect()->back()->with('error', 'Mật khẩu không chính xác.');
        }
        
        if ($user->status === 'pending') {
            return redirect()->back()->with('error', 'Tài khoản của bạn đang chờ duyệt. Vui lòng quay lại sau.');
        }
        if ($user->status === 'banned') {
            return redirect()->back()->with('error', 'Tài khoản của bạn đã bị khóa.');
        }
        if ($user->status === 'inactive') {
            return redirect()->back()->with('error', 'Tài khoản của bạn đã bị vô hiệu hóa.');
        }

        session(['user' => $user]);
        return redirect()->intended(route('home'));
    }

    /**
     * Handle logout.
     */
    public function logout()
    {
        session()->forget('user');
        return redirect()->route('home');
    }
}
