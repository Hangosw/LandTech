<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Helper to group permissions logically into Vietnamese categories.
     */
    private function getGroupedPermissions()
    {
        $permissions = Permission::all();

        $groups = [
            'Quản lý người dùng' => [],
            'Quản lý bất động sản' => [],
            'Quản lý lịch hẹn' => [],
            'Quản lý dự án' => [],
            'Quyền hạn bổ sung' => [],
        ];

        foreach ($permissions as $p) {
            $nameLower = mb_strtolower($p->name);
            if (str_contains($nameLower, 'người dùng') || str_contains($nameLower, 'user')) {
                $groups['Quản lý người dùng'][] = $p;
            } elseif (str_contains($nameLower, 'tin') || str_contains($nameLower, 'bất động sản') || str_contains($nameLower, 'property')) {
                $groups['Quản lý bất động sản'][] = $p;
            } elseif (str_contains($nameLower, 'lịch') || str_contains($nameLower, 'booking')) {
                $groups['Quản lý lịch hẹn'][] = $p;
            } elseif (str_contains($nameLower, 'dự án') || str_contains($nameLower, 'project')) {
                $groups['Quản lý dự án'][] = $p;
            } else {
                $groups['Quyền hạn bổ sung'][] = $p;
            }
        }

        // Filter out empty categories
        return array_filter($groups, fn($items) => !empty($items));
    }

    private function checkPermission()
    {
        $sessionUser = session('user');
        $user = $sessionUser ? \App\Models\User::find($sessionUser->id ?? $sessionUser['id'] ?? null) : auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        if ($user->user_type !== 'admin') {
            abort(403, 'Tài khoản của bạn không có quyền truy cập Quản Lý Phân Quyền.');
        }
        return null;
    }

    /**
     * Display a listing of the roles and permissions.
     */
    public function index()
    {
        if ($check = $this->checkPermission()) return $check;

        $roles = Role::with(['permissions', 'users'])->get();
        $permissions = Permission::all();
        $groupedPermissions = $this->getGroupedPermissions();

        return view('admin.roles.index', compact('roles', 'permissions', 'groupedPermissions'));
    }

    /**
     * Show the form for editing permissions of a specific role.
     */
    public function edit(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all();
        $groupedPermissions = $this->getGroupedPermissions();

        return view('admin.roles.edit', compact('role', 'permissions', 'groupedPermissions'));
    }

    /**
     * Update permissions assigned to a role.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->syncPermissions($request->input('permissions', []));

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', "Cập nhật quyền hạn cho vai trò [{$role->name}] thành công!");
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $roleName = strtolower(trim($request->name));
        $role = Role::create(['name' => $roleName]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->input('permissions'));
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', "Đã tạo vai trò mới [{$role->name}] thành công!");
    }

    /**
     * Store a newly created permission.
     */
    public function storePermission(Request $request)
    {
        // Capitalize first letter of each word (Title Case)
        $permName = mb_convert_case(trim($request->name), MB_CASE_TITLE, "UTF-8");

        $request->merge(['name' => $permName]);

        $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name',
            'assign_to_admin' => 'nullable|boolean',
        ]);

        $permission = Permission::create(['name' => $permName]);

        // Automatically assign new permission to Admin role if requested
        if ($request->boolean('assign_to_admin', true)) {
            $adminRole = Role::where('name', 'admin')->first();
            if ($adminRole) {
                $adminRole->givePermissionTo($permission);
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', "Đã tạo quyền mới [{$permission->name}] thành công!");
    }

    /**
     * Remove the specified role.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);

        if (in_array($role->name, ['admin', 'agent', 'renter'])) {
            return redirect()->back()->with('error', "Không thể xóa vai trò hệ thống mặc định [{$role->name}].");
        }

        $role->delete();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', "Đã xóa vai trò [{$role->name}] thành công!");
    }

    /**
     * Remove the specified permission if it is not assigned to any user.
     */
    public function destroyPermission(string $id)
    {
        $permission = Permission::findOrFail($id);

        $hasDirectUsers = $permission->users()->exists();
        $hasRoleUsers = $permission->roles()->whereHas('users')->exists();

        if ($hasDirectUsers || $hasRoleUsers) {
            return redirect()->back()->with('error', "Không thể xóa quyền [{$permission->name}] vì quyền này đang được gán cho người dùng trong hệ thống.");
        }

        $permission->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', "Đã xóa quyền [{$permission->name}] thành công!");
    }
}
