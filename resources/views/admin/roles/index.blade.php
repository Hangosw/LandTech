@extends('layouts.admin')

@section('title', 'Quản lý Phân Quyền — Admin LANDTEK')

@section('content')
<style>
    .prop-page {
        min-height: 100vh;
        background: #f8fafc;
        padding: 2rem 0 3rem;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .prop-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    @media (min-width: 640px)  { .prop-container { padding: 0 1.5rem; } }
    @media (min-width: 1024px) { .prop-container { padding: 0 2rem; } }

    /* Page Header */
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    .page-breadcrumb {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.7rem;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.375rem;
    }
    .page-breadcrumb i { font-size: 0.6rem; }
    .page-title {
        font-size: clamp(1.4rem, 4vw, 1.875rem);
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }
    .page-subtitle {
        font-size: 0.8125rem;
        color: #94a3b8;
        margin-top: 0.25rem;
        font-weight: 500;
    }
    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #0F3460;
        background: rgba(15,52,96,0.08);
        border: 1px solid rgba(15,52,96,0.20);
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
        flex-shrink: 0;
        cursor: pointer;
    }
    .btn-action:hover {
        background: rgba(15,52,96,0.14);
        border-color: rgba(15,52,96,0.35);
    }
    .btn-action.primary {
        background: #0F3460;
        color: #ffffff;
        border-color: #0F3460;
    }
    .btn-action.primary:hover {
        background: #16427b;
    }

    /* Stat Cards */
    .stat-cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.875rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 640px) {
        .stat-cards { grid-template-columns: 1fr; gap: 0.625rem; }
    }
    .stat-card {
        background: white;
        border-radius: 1rem;
        padding: 1rem 1.125rem;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.875rem;
        border-left: 3px solid transparent;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -6px rgba(0,0,0,0.1);
    }
    .stat-card.roles   { border-left-color: #0F3460; }
    .stat-card.perms   { border-left-color: #10b981; }
    .stat-card.users   { border-left-color: #3b82f6; }
    .stat-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.625rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    .stat-icon.navy  { background: rgba(15,52,96,0.10); color: #0F3460; }
    .stat-icon.green { background: #ecfdf5; color: #059669; }
    .stat-icon.blue  { background: #eff6ff; color: #2563eb; }
    .stat-value {
        font-size: 1.375rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }
    .stat-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 0.2rem;
    }

    /* Role Grid Cards */
    .role-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    .role-card {
        background: white;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    .role-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 10px 25px -5px rgba(15,52,96,0.08);
        transform: translateY(-2px);
    }
    .role-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.875rem;
    }
    .role-title-badge {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .role-name {
        font-size: 1.125rem;
        font-weight: 800;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .role-badge {
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    .role-badge.admin { background: #fee2e2; color: #991b1b; }
    .role-badge.agent { background: #fef3c7; color: #92400e; }
    .role-badge.renter { background: #e0e7ff; color: #3730a3; }
    .role-badge.custom { background: #f1f5f9; color: #475569; }

    .role-users-count {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        background: #f8fafc;
        padding: 0.25rem 0.625rem;
        border-radius: 0.5rem;
        border: 1px solid #f1f5f9;
    }
    .permission-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.375rem;
        margin-bottom: 1.25rem;
        max-height: 140px;
        overflow-y: auto;
    }
    .perm-tag {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.625rem;
        border-radius: 0.5rem;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
    }

    /* Section Title */
    .section-title {
        font-size: 1.125rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Permission Matrix Table */
    .matrix-card {
        background: white;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .matrix-table {
        width: 100%;
        border-collapse: collapse;
    }
    .matrix-table th {
        background: #f8fafc;
        padding: 0.875rem 1rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
    }
    .matrix-table td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.8125rem;
        vertical-align: middle;
    }
    .matrix-table tr:hover td {
        background-color: #fafbfc;
    }
    .check-icon {
        color: #10b981;
        font-size: 1rem;
    }
    .cross-icon {
        color: #cbd5e1;
        font-size: 0.875rem;
    }

    /* Modal Overlay & Card */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .modal-card {
        background: white;
        border-radius: 1.25rem;
        max-width: 480px;
        width: 100%;
        padding: 1.5rem;
        box-shadow: 0 20px 40px -15px rgba(0,0,0,0.25);
    }
</style>

<div class="prop-page" x-data="{ openPermModal: false, openRoleModal: false }">
    <div class="prop-container">

        <!-- PAGE HEADER -->
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">
                    <i class="fas fa-home"></i>
                    <span>Admin</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Phân quyền</span>
                </div>
                <h1 class="page-title">Quản lý Phân Quyền</h1>
                <p class="page-subtitle">Xem và phân chia các quyền hạn (Permissions) cho các vai trò (Roles) bằng tiếng Việt.</p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <button type="button" class="btn-action primary" @click="openPermModal = true">
                    <i class="fas fa-plus-circle" style="font-size:0.75rem;"></i>
                    Tạo quyền mới
                </button>
                <button type="button" class="btn-action" @click="openRoleModal = true">
                    <i class="fas fa-user-plus" style="font-size:0.75rem;"></i>
                    Tạo vai trò mới
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn-action">
                    <i class="fas fa-users" style="font-size:0.75rem;"></i>
                    Người dùng
                </a>
            </div>
        </div>

        <!-- STAT CARDS -->
        <div class="stat-cards">
            <div class="stat-card roles">
                <div class="stat-icon navy">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $roles->count() }}</div>
                    <div class="stat-label">Vai trò (Roles)</div>
                </div>
            </div>
            <div class="stat-card perms">
                <div class="stat-icon green">
                    <i class="fas fa-key"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $permissions->count() }}</div>
                    <div class="stat-label">Quyền hạn (Permissions)</div>
                </div>
            </div>
            <div class="stat-card users">
                <div class="stat-icon blue">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div>
                    <div class="stat-value">{{ \App\Models\User::count() }}</div>
                    <div class="stat-label">Tài khoản được phân quyền</div>
                </div>
            </div>
        </div>

        <!-- ROLES LIST CARDS -->
        <div class="section-title">
            <span style="display:flex;align-items:center;gap:0.5rem;">
                <i class="fas fa-user-tag" style="color:#0F3460;"></i>
                Danh sách Vai trò &amp; Quyền hạn
            </span>
        </div>

        <div class="role-grid">
            @foreach($roles as $role)
            <div class="role-card">
                <div>
                    <div class="role-header">
                        <div class="role-title-badge">
                            <span class="role-name">{{ $role->name }}</span>
                            @if($role->name === 'admin')
                                <span class="role-badge admin">ADMIN</span>
                            @elseif($role->name === 'agent')
                                <span class="role-badge agent">MÔI GIỚI</span>
                            @elseif($role->name === 'renter')
                                <span class="role-badge renter">NGƯỜI THUÊ</span>
                            @else
                                <span class="role-badge custom">TÙY CHỈNH</span>
                            @endif
                        </div>
                        <span class="role-users-count" title="Số lượng người dùng gán vai trò này">
                            <i class="fas fa-user-check"></i> {{ $role->users->count() }} người
                        </span>
                    </div>

                    <div style="font-size:0.75rem; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:0.5rem;">
                        Quyền hạn được cấp ({{ $role->permissions->count() }}):
                    </div>

                    <div class="permission-tags">
                        @forelse($role->permissions as $perm)
                            <span class="perm-tag">{{ $perm->name }}</span>
                        @empty
                            <span style="font-size:0.75rem; color:#94a3b8; font-style:italic;">Chưa có quyền hạn nào</span>
                        @endforelse
                    </div>
                </div>

                <div style="border-top:1px solid #f1f5f9; padding-top:0.75rem; display:flex; justify-content:space-between; align-items:center;">
                    @if(!in_array($role->name, ['admin', 'agent', 'renter']))
                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa vai trò này?');" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#dc2626;font-size:0.8125rem;font-weight:600;cursor:pointer;">
                                <i class="fas fa-trash-alt"></i> Xóa
                            </button>
                        </form>
                    @else
                        <span></span>
                    @endif

                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn-action primary">
                        <i class="fas fa-user-edit" style="font-size:0.75rem;"></i>
                        Chỉnh sửa quyền
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- PERMISSION MATRIX TABLE -->
        <div class="section-title">
            <span style="display:flex;align-items:center;gap:0.5rem;">
                <i class="fas fa-th-list" style="color:#0F3460;"></i>
                Ma trận Phân quyền (Permission Matrix)
            </span>
        </div>

        <div class="matrix-card">
            <div style="overflow-x:auto;">
                <table class="matrix-table">
                    <thead>
                        <tr>
                            <th style="width:30%;">Tên Quyền hạn (Permission)</th>
                            <th style="width:20%;">Nhóm tính năng</th>
                            @foreach($roles as $role)
                                <th style="text-align:center; text-transform:uppercase;">{{ $role->name }}</th>
                            @endforeach
                            <th style="text-align:center; width:12%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupedPermissions as $groupName => $groupPerms)
                            @foreach($groupPerms as $perm)
                            @php
                                $isInUse = $perm->users()->exists() || $perm->roles()->whereHas('users')->exists();
                            @endphp
                            <tr>
                                <td>
                                    <strong style="color:#0f172a; font-size:0.875rem;">{{ $perm->name }}</strong>
                                </td>
                                <td>
                                    <span style="display:inline-block; padding:3px 10px; border-radius:6px; background:#f1f5f9; color:#475569; font-weight:700; font-size:0.75rem;">
                                        {{ $groupName }}
                                    </span>
                                </td>
                                @foreach($roles as $role)
                                    <td style="text-align:center;">
                                        @if($role->hasPermissionTo($perm->name))
                                            <i class="fas fa-check-circle check-icon" title="Có quyền"></i>
                                        @else
                                            <i class="fas fa-minus cross-icon" title="Không có quyền"></i>
                                        @endif
                                    </td>
                                @endforeach
                                <td style="text-align:center;">
                                    @if(!$isInUse)
                                        <form action="{{ route('admin.permissions.destroy', $perm->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa quyền [{{ $perm->name }}] không?');" style="margin:0;display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background:none;border:none;color:#dc2626;font-size:0.8125rem;font-weight:600;cursor:pointer;" title="Xóa quyền hạn này">
                                                <i class="fas fa-trash-alt"></i> Xóa
                                            </button>
                                        </form>
                                    @else
                                        <span style="font-size:0.75rem; color:#94a3b8; font-weight:600;" title="Quyền này đang được gán cho người dùng trong hệ thống nên không thể xóa">
                                            <i class="fas fa-lock" style="font-size:0.7rem;"></i> Đang dùng
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- MODAL TẠO QUYỀN MỚI -->
    <div x-show="openPermModal" style="display:none;" class="modal-overlay" @keydown.escape.window="openPermModal = false">
        <div class="modal-card" @click.away="openPermModal = false">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <h3 style="font-size:1.125rem;font-weight:800;color:#0f172a;margin:0;">Tạo Quyền Hạn Mới</h3>
                <button type="button" @click="openPermModal = false" style="background:none;border:none;color:#94a3b8;font-size:1.25rem;cursor:pointer;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf
                <div style="margin-bottom:1.25rem;">
                    <label style="display:block;font-size:0.8125rem;font-weight:700;color:#334155;margin-bottom:0.375rem;">
                        Tên quyền hạn (Tiếng Việt):
                    </label>
                    <input type="text" name="name" required placeholder="Ví dụ: Phê duyệt tin đăng, Xuất báo cáo..."
                           style="width:100%;padding:0.625rem 0.875rem;border:1px solid #cbd5e1;border-radius:0.625rem;font-size:0.875rem;outline:none;box-sizing:border-box;">
                </div>

                <div style="margin-bottom:1.5rem;display:flex;align-items:center;gap:0.5rem;">
                    <input type="checkbox" id="assign_to_admin" name="assign_to_admin" value="1" checked style="width:1rem;height:1rem;accent-color:#0F3460;cursor:pointer;">
                    <label for="assign_to_admin" style="font-size:0.8125rem;font-weight:600;color:#475569;cursor:pointer;">
                        Tự động cấp quyền mới này cho vai trò <strong>Admin</strong>
                    </label>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:0.75rem;">
                    <button type="button" @click="openPermModal = false" class="btn-action">Hủy</button>
                    <button type="submit" class="btn-action primary">Tạo quyền mới</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TẠO VAI TRÒ MỚI -->
    <div x-show="openRoleModal" style="display:none;" class="modal-overlay" @keydown.escape.window="openRoleModal = false">
        <div class="modal-card" @click.away="openRoleModal = false">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <h3 style="font-size:1.125rem;font-weight:800;color:#0f172a;margin:0;">Tạo Vai Trò Mới</h3>
                <button type="button" @click="openRoleModal = false" style="background:none;border:none;color:#94a3b8;font-size:1.25rem;cursor:pointer;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div style="margin-bottom:1.5rem;">
                    <label style="display:block;font-size:0.8125rem;font-weight:700;color:#334155;margin-bottom:0.375rem;">
                        Tên vai trò mới:
                    </label>
                    <input type="text" name="name" required placeholder="Ví dụ: manager, moderator, accountant..."
                           style="width:100%;padding:0.625rem 0.875rem;border:1px solid #cbd5e1;border-radius:0.625rem;font-size:0.875rem;outline:none;box-sizing:border-box;">
                </div>

                <div style="display:flex;justify-content:flex-end;gap:0.75rem;">
                    <button type="button" @click="openRoleModal = false" class="btn-action">Hủy</button>
                    <button type="submit" class="btn-action primary">Tạo vai trò</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
