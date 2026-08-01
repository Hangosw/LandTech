@extends('layouts.admin')

@section('title', 'Chỉnh sửa Quyền hạn Vai trò [' . $role->name . '] — Admin LANDTEK')

@section('content')
<style>
    .prop-page {
        min-height: 100vh;
        background: #f8fafc;
        padding: 2rem 0 3rem;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .prop-container {
        max-width: 960px;
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
    .btn-back {
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
    }
    .btn-back:hover {
        background: rgba(15,52,96,0.14);
        border-color: rgba(15,52,96,0.35);
    }

    /* Edit Form Card */
    .form-card {
        background: white;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    .group-header {
        font-size: 0.875rem;
        font-weight: 800;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .perm-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    .perm-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
    }
    .perm-item:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    .perm-item input[type="checkbox"] {
        width: 1.125rem;
        height: 1.125rem;
        accent-color: #0F3460;
        cursor: pointer;
    }
    .perm-label {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #1e293b;
    }
    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.75rem;
        background: #0F3460;
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
        border-radius: 0.75rem;
        border: none;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-submit:hover {
        background: #16427b;
    }
</style>

<div class="prop-page">
    <div class="prop-container">

        <!-- PAGE HEADER -->
        <div class="page-header">
            <div>
                <div class="page-breadcrumb">
                    <i class="fas fa-home"></i>
                    <span>Admin</span>
                    <i class="fas fa-chevron-right"></i>
                    <a href="{{ route('admin.roles.index') }}" style="color:inherit;text-decoration:none;">Phân quyền</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Chỉnh sửa</span>
                </div>
                <h1 class="page-title">Cập nhật Vai trò: <span style="color:#0F3460;text-transform:uppercase;">{{ $role->name }}</span></h1>
                <p class="page-subtitle">Tích chọn hoặc bỏ chọn các quyền hạn (Permissions) bằng tiếng Việt gán cho vai trò này.</p>
            </div>
            <div>
                <a href="{{ route('admin.roles.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left" style="font-size:0.75rem;"></i>
                    Quay lại
                </a>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="form-card">
            <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')

                @foreach($groupedPermissions as $groupName => $groupPerms)
                <div>
                    <div class="group-header">
                        <span><i class="fas fa-layer-group" style="color:#0F3460;margin-right:0.375rem;"></i> {{ $groupName }}</span>
                        <button type="button" onclick="toggleGroup(this)" style="background:none;border:none;color:#0F3460;font-size:0.75rem;font-weight:700;cursor:pointer;">
                            Chọn tất cả / Bỏ chọn
                        </button>
                    </div>

                    <div class="perm-grid">
                        @foreach($groupPerms as $perm)
                        <label class="perm-item">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                   {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                            <span class="perm-label">{{ $perm->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div style="border-top:1px solid #e2e8f0; padding-top:1.25rem; margin-top:1rem; display:flex; justify-content:flex-end; gap:0.75rem;">
                    <a href="{{ route('admin.roles.index') }}" class="btn-back" style="background:#f1f5f9;color:#475569;border-color:#e2e8f0;">
                        Hủy
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i>
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
function toggleGroup(btn) {
    const groupHeader = btn.closest('.group-header');
    const groupGrid = groupHeader.nextElementSibling;
    const checkboxes = groupGrid.querySelectorAll('input[type="checkbox"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}
</script>
@endsection
