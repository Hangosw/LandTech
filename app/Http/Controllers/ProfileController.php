<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Database\UniqueConstraintViolationException;
use App\Models\User;
use Exception;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit(Request $request)
    {
        $userSession = session('user');
        if (!$userSession) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        // Fetch fresh user data from DB
        $user = User::find($userSession->id);
        if (!$user) {
            return redirect()->route('login');
        }

        return view('pages.profile', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $userSession = session('user');
        if (!$userSession) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $user = User::find($userSession->id);
        if (!$user) {
            return redirect()->route('login');
        }

        $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'email'            => ['nullable', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'address'          => 'nullable|string|max:255',
            'nationality'      => 'nullable|string|max:100',
            'avatar'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'current_password' => 'nullable|string',
            'new_password'     => 'nullable|string|min:6|confirmed',
        ], [
            'phone.unique' => 'Số điện thoại này đã được sử dụng bởi tài khoản khác.',
            'email.unique' => 'Email này đã được sử dụng bởi tài khoản khác.',
            'email.email'  => 'Email không đúng định dạng.',
            'name.required'=> 'Vui lòng nhập họ tên.',
            'phone.required'=> 'Vui lòng nhập số điện thoại.',
        ]);

        try {
            // Handle Password Change
            if ($request->filled('new_password')) {
                if ($user->password_hash) {
                    if (!$request->filled('current_password') || !Hash::check($request->current_password, $user->password_hash)) {
                        return redirect()->back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
                    }
                }
                $user->password_hash = Hash::make($request->new_password);
            }

            // Handle Avatar Upload
            if ($request->hasFile('avatar')) {
                $file     = $request->file('avatar');
                $filename = uniqid('avatar_') . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('avatars'), $filename);
                $user->avatar_url = '/avatars/' . $filename;
            }

            // Update fields
            $user->name        = $request->name;
            $user->phone       = $request->phone;
            $user->email       = $request->email;
            $user->address     = $request->address;
            $user->nationality = $request->nationality;

            $user->save();

            // Sync session
            session(['user' => $user]);

            return redirect()->route('profile.edit')->with('success', 'Cập nhật thông tin thành công!');

        } catch (UniqueConstraintViolationException $e) {
            // Bắt lỗi trùng email / phone từ DB (phòng trường hợp race condition)
            $msg = $e->getMessage();
            if (str_contains($msg, 'email')) {
                return redirect()->back()->withInput()->with('error', 'Email này đã được sử dụng bởi tài khoản khác.');
            }
            if (str_contains($msg, 'phone')) {
                return redirect()->back()->withInput()->with('error', 'Số điện thoại này đã được sử dụng bởi tài khoản khác.');
            }
            return redirect()->back()->withInput()->with('error', 'Thông tin bị trùng với tài khoản khác. Vui lòng kiểm tra lại.');

        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật. Vui lòng thử lại sau.');
        }
    }
}
