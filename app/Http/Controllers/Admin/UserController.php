<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    private function checkPermission()
    {
        $sessionUser = session('user');
        $user = $sessionUser ? User::find($sessionUser->id ?? $sessionUser['id'] ?? null) : auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        if ($user->user_type !== 'admin' && !$user->hasPermissionTo('Quản Lý Người Dùng')) {
            abort(403, 'Tài khoản của bạn không có quyền Quản Lý Người Dùng.');
        }
        return null;
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        if ($check = $this->checkPermission()) return $check;

        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show create user form.
     */
    public function create()
    {
        if ($check = $this->checkPermission()) return $check;

        return view('admin.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        if ($check = $this->checkPermission()) return $check;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'user_type' => 'required|in:seller,buyer,owner,renter,agent,admin',
            'status' => 'required|in:active,inactive,pending,banned',
            'nationality' => 'nullable|string|max:255',
        ], [
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min' => 'Mật khẩu tối thiểu 6 ký tự.',
        ]);

        User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'password_hash' => Hash::make($validated['password']),
            'user_type' => $validated['user_type'],
            'status' => $validated['status'],
            'nationality' => $validated['nationality'] ?? null,
            'is_verified' => true,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Đã tạo người dùng thành công.');
    }

    /**
     * Update the user status (activate / deactivate).
     */
    public function updateStatus(Request $request, int $id)
    {
        if ($check = $this->checkPermission()) return $check;

        $request->validate([
            'status' => 'required|in:active,inactive,pending,banned',
        ]);

        $user = User::findOrFail($id);
        $user->status = $request->status;
        $user->save();

        $label = match ($user->status) {
            'active' => 'đã kích hoạt',
            'inactive' => 'đã vô hiệu hóa',
            'pending' => 'chờ duyệt',
            'banned' => 'đã cấm',
            default => $user->status,
        };

        return redirect()->back()->with('success', 'Tài khoản ' . $user->name . ' ' . $label . '.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        if ($check = $this->checkPermission()) return $check;

        $user = User::with('properties')->findOrFail($id);
        $roles = \Spatie\Permission\Models\Role::all();
        $permissions = \Spatie\Permission\Models\Permission::all();

        // Group permissions logically
        $groupedPermissions = [
            'Quản lý người dùng' => [],
            'Quản lý bất động sản' => [],
            'Quản lý lịch hẹn' => [],
            'Quản lý dự án' => [],
            'Quyền hạn bổ sung' => [],
        ];

        foreach ($permissions as $p) {
            $nameLower = mb_strtolower($p->name);
            if (str_contains($nameLower, 'người dùng') || str_contains($nameLower, 'user')) {
                $groupedPermissions['Quản lý người dùng'][] = $p;
            } elseif (str_contains($nameLower, 'tin') || str_contains($nameLower, 'bất động sản') || str_contains($nameLower, 'property')) {
                $groupedPermissions['Quản lý bất động sản'][] = $p;
            } elseif (str_contains($nameLower, 'lịch') || str_contains($nameLower, 'booking')) {
                $groupedPermissions['Quản lý lịch hẹn'][] = $p;
            } elseif (str_contains($nameLower, 'dự án') || str_contains($nameLower, 'project')) {
                $groupedPermissions['Quản lý dự án'][] = $p;
            } else {
                $groupedPermissions['Quyền hạn bổ sung'][] = $p;
            }
        }

        $groupedPermissions = array_filter($groupedPermissions, fn($items) => !empty($items));

        $userRoles = $user->roles->pluck('name')->toArray();
        $userDirectPermissions = $user->getDirectPermissions()->pluck('name')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'permissions', 'groupedPermissions', 'userRoles', 'userDirectPermissions'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        if ($check = $this->checkPermission()) return $check;

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nationality' => 'nullable|string|max:255',
            'user_type' => 'required|in:seller,buyer,owner,renter,agent,admin',
            'status' => 'required|in:active,inactive,pending,banned',
            'password' => 'nullable|string|min:6|confirmed',
            'roles' => 'nullable|array',
            'direct_permissions' => 'nullable|array',
        ], [
            'phone.unique' => 'Số điện thoại này đã được sử dụng bởi tài khoản khác.',
            'email.unique' => 'Email này đã được sử dụng bởi tài khoản khác.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min' => 'Mật khẩu tối thiểu 6 ký tự.',
        ]);

        try {
            $data = $request->only(['name', 'phone', 'email', 'nationality', 'user_type', 'status']);
            if ($request->filled('password')) {
                $data['password_hash'] = Hash::make($request->input('password'));
            }
            $user->update($data);

            // Sync Spatie Roles if provided
            if ($request->has('roles')) {
                $user->syncRoles($request->input('roles', []));
            }

            // Sync Direct Permissions for this user
            if ($request->has('direct_permissions_submitted')) {
                $user->syncPermissions($request->input('direct_permissions', []));
            }

            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            return redirect()->route('admin.users.edit', $user->id)->with('success', 'Đã cập nhật thông tin và phân quyền người dùng thành công.');
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'email')) {
                return redirect()->back()->withInput()->with('error', 'Email này đã được sử dụng bởi tài khoản khác.');
            }
            if (str_contains($msg, 'phone')) {
                return redirect()->back()->withInput()->with('error', 'Số điện thoại này đã được sử dụng bởi tài khoản khác.');
            }
            return redirect()->back()->withInput()->with('error', 'Thông tin bị trùng lặp. Vui lòng kiểm tra lại.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật. Vui lòng thử lại sau.');
        }
    }

    /**
     * Soft-deactivate the specified user.
     */
    public function destroy($id)
    {
        if ($check = $this->checkPermission()) return $check;

        $user = User::findOrFail($id);
        $user->status = 'inactive';
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Đã vô hiệu hóa người dùng thành công.');
    }
}
