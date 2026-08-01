<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Delete previous non-title-cased permissions if present
        Permission::whereIn('name', [
            'manage users', 'manage properties', 'post properties', 'edit own properties',
            'delete own properties', 'view properties', 'manage bookings', 'create bookings',
            'view own bookings', 'manage projects', 'view projects',
            'Quản lý người dùng', 'Quản lý bất động sản', 'Đăng tin mới', 'Sửa tin đăng của tôi',
            'Xóa tin đăng của tôi', 'Xem tin đăng', 'Quản lý lịch hẹn', 'Đặt lịch xem nhà',
            'Xem lịch hẹn của tôi', 'Quản lý dự án', 'Xem danh sách dự án'
        ])->delete();

        // 1. Create Vietnamese Title Case permissions
        $permissions = [
            // Quản Lý Người Dùng
            'Quản Lý Người Dùng',
            
            // Quản Lý Bất Động Sản / Quản Lý Tin Đăng
            'Quản Lý Bất Động Sản',
            'Quản Lý Tin Đăng',
            'Đăng Tin Mới',
            'Sửa Tin Đăng Của Tôi',
            'Xóa Tin Đăng Của Tôi',
            'Xem Tin Đăng',
            
            // Quản Lý Lịch Hẹn
            'Quản Lý Lịch Hẹn',
            'Đặt Lịch Xem Nhà',
            'Xem Lịch Hẹn Của Tôi',
            
            // Quản Lý Dự Án
            'Quản Lý Dự Án',
            'Xem Danh Sách Dự Án',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create roles and give permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $agentRole = Role::firstOrCreate(['name' => 'agent']);
        $agentRole->syncPermissions([
            'Đăng Tin Mới',
            'Sửa Tin Đăng Của Tôi',
            'Xóa Tin Đăng Của Tôi',
            'Xem Tin Đăng',
            'Xem Lịch Hẹn Của Tôi',
            'Xem Danh Sách Dự Án',
        ]);

        $renterRole = Role::firstOrCreate(['name' => 'renter']);
        $renterRole->syncPermissions([
            'Xem Tin Đăng',
            'Đặt Lịch Xem Nhà',
            'Xem Lịch Hẹn Của Tôi',
            'Xem Danh Sách Dự Án',
        ]);

        // 3. Sync existing users to their corresponding roles
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            if (!empty($user->user_type) && in_array($user->user_type, ['admin', 'agent', 'renter'])) {
                $user->syncRoles([$user->user_type]);
            }
        }
    }
}
