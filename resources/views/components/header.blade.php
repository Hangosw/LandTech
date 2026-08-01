<header class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-[1180px] mx-auto px-4 md:px-6 lg:px-8 py-3 flex justify-between items-center">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="text-[22px] font-extrabold text-navy tracking-wide shrink-0">LANDTEK</a>

        {{-- Desktop nav --}}
        <nav class="hidden md:flex gap-7 items-center flex-1 justify-center text-[14.5px] font-semibold">
            {{-- Tạm ẩn: Thuê nhà, Môi giới --}}
            <a href="{{ route('owner') }}" class="relative text-navy-soft hover:text-navy">
                Chủ nhà &amp; Quản lý gia sản
                <span class="absolute -top-2.5 -right-7 rounded bg-amber-brand px-1.5 py-px text-[8px] font-extrabold text-white">MỚI</span>
            </a>
            <a href="{{ route('projects') }}" class="text-gray-800 hover:text-navy">Dự án</a>
        </nav>

        {{-- Right side actions --}}
        <div class="flex items-center gap-2">

            {{-- ── Đăng tin: primary CTA ── --}}
            <a href="{{ route('property.post') }}"
               class="hdr-btn-post inline-flex items-center justify-center gap-1.5 font-semibold transition-all duration-200 shrink-0
                      bg-navy text-white hover:bg-navy-mid active:scale-95">
                <i class="fas fa-plus" style="font-size:12px;"></i>
                <span class="hdr-post-label">Đăng tin</span>
            </a>

            @if(session()->has('user'))
                @php
                    $hdrUser = \App\Models\User::find(session('user')->id);
                @endphp
                {{-- ── User Avatar Button (Mở menu cá nhân/admin) ── --}}
                <button onclick="openMobMenu()"
                        class="hdr-avatar-btn flex items-center justify-center overflow-hidden shrink-0 transition-all duration-200 active:scale-95 cursor-pointer"
                        title="{{ session('user')->name }}">
                    @if($hdrUser && $hdrUser->avatar_url)
                        <img src="{{ Str::startsWith($hdrUser->avatar_url, 'http') ? $hdrUser->avatar_url : asset($hdrUser->avatar_url) }}"
                             alt="Avatar" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                    @else
                        <i class="fas fa-user" style="font-size:14px;"></i>
                    @endif
                </button>
            @else
                {{-- ── Đăng nhập: ghost button ── --}}
                <a href="{{ route('login') }}" class="hdr-btn-login inline-flex items-center justify-center gap-1.5 shrink-0 font-semibold transition-all duration-200 active:scale-95">
                    <i class="fas fa-circle-user" style="font-size:15px;"></i>
                    <span class="hdr-login-label">Đăng nhập</span>
                </a>
            @endif

            {{-- ── Hamburger: hiện khi chưa đăng nhập trên mobile ── --}}
            @if(!session()->has('user'))
            <button id="mob-menu-btn"
                    class="hdr-btn-menu md:hidden flex items-center justify-center shrink-0 transition-all duration-200 active:scale-95"
                    onclick="openMobMenu()"
                    aria-label="Mở menu">
                <i class="fas fa-bars" style="font-size:13px;"></i>
            </button>
            @endif
        </div>
    </div>
</header>

{{-- ══════════════════════════════════════════
     MOBILE DRAWER — nằm ngoài header, z cao hơn bottom bar
══════════════════════════════════════════ --}}

{{-- Backdrop: phủ toàn màn hình, z-[998] (cao hơn bottom bar z-[999]? không — để drawer + backdrop z > bottom bar) --}}
<div id="mob-drawer-backdrop"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1001;"
     onclick="closeMobMenu()"></div>

{{-- Drawer panel --}}
<div id="mob-drawer"
     style="position:fixed; top:0; left:0; height:100%; width:288px; max-width:85vw;
            background:#fff; z-index:1002; display:flex; flex-direction:column;
            transform:translateX(-100%); transition:transform 0.28s cubic-bezier(.4,0,.2,1);
            box-shadow: 4px 0 24px rgba(0,0,0,0.18);">

    {{-- Drawer header --}}
    <div style="display:flex; align-items:center; justify-content:space-between;
                padding:16px 20px; border-bottom:1px solid #f3f4f6; flex-shrink:0;">
        <span style="font-size:18px; font-weight:800; color:#0F3460;">LANDTEK</span>
        <button onclick="closeMobMenu()"
                style="width:32px; height:32px; border-radius:50%; border:none; background:#f3f4f6;
                       color:#6b7280; font-size:14px; cursor:pointer; display:flex;
                       align-items:center; justify-content:center; transition:background .2s;"
                onmouseover="this.style.background='#e5e7eb'"
                onmouseout="this.style.background='#f3f4f6'">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- User info card (chỉ hiện khi đã đăng nhập) --}}
    @if(session()->has('user'))
        @php
            $drawerUser = \App\Models\User::find(session('user')->id);
            $drawerRole = 'Khách hàng';
            if ($drawerUser) {
                if ($drawerUser->user_type === 'admin') $drawerRole = 'Quản trị viên';
                elseif ($drawerUser->user_type === 'agent') $drawerRole = 'Môi giới';
            }
        @endphp
        <div style="display:flex; align-items:center; gap:12px; padding:14px 20px;
                    background:#EBF3FF; border-bottom:1px solid #e5e7eb; flex-shrink:0;">
            @if($drawerUser && $drawerUser->avatar_url)
                <img src="{{ Str::startsWith($drawerUser->avatar_url, 'http') ? $drawerUser->avatar_url : asset($drawerUser->avatar_url) }}"
                     alt="Avatar"
                     style="width:40px; height:40px; border-radius:50%; object-fit:cover; flex-shrink:0;">
            @else
                <div style="width:40px; height:40px; border-radius:50%; background:#EBF3FF;
                            display:flex; align-items:center; justify-content:center;
                            color:#0F3460; font-size:16px; flex-shrink:0;">
                    <i class="fas fa-user"></i>
                </div>
            @endif
            <div style="overflow:hidden;">
                <p style="margin:0; font-size:14px; font-weight:700; color:#0f172a;
                          white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ session('user')->name }}
                </p>
                <p style="margin:2px 0 0; font-size:12px; color:#0F3460; font-weight:500;">{{ $drawerRole }}</p>
            </div>
        </div>
    @endif

    {{-- Nav links --}}
    <nav style="flex:1; overflow-y:auto; padding:8px 0;">

        {{-- Tạm ẩn: Thuê nhà, Môi giới --}}
        <a href="{{ route('owner') }}" class="mob-drawer-link">
            <i class="fas fa-key" style="color:#0F3460; width:18px;"></i> Chủ nhà &amp; Quản lý gia sản
        </a>
        <a href="{{ route('projects') }}" class="mob-drawer-link">
            <i class="fas fa-building" style="color:#0F3460; width:18px;"></i> Dự án
        </a>
        <a href="{{ route('wishlist') }}" class="mob-drawer-link">
            <i class="fas fa-heart" style="color:#0F3460; width:18px;"></i> Yêu thích
        </a>

        <div style="margin:8px 20px; border-top:1px solid #f3f4f6;"></div>

        <a href="{{ route('property.post') }}" class="mob-drawer-link" style="color:#0F3460; font-weight:700;">
            <i class="fas fa-plus-circle" style="color:#0F3460; width:18px;"></i> Đăng tin
        </a>

        @if(session()->has('user'))
            @php $freshUser2 = \App\Models\User::find(session('user')->id); @endphp
            <div style="margin:8px 20px; border-top:1px solid #f3f4f6;"></div>

            @if(isset($freshUser2) && $freshUser2->user_type === 'agent')
                <a href="{{ route('my-properties') }}" class="mob-drawer-link">
                    <i class="fas fa-list" style="color:#9ca3af; width:18px;"></i> Tin đăng của tôi
                </a>
                <a href="{{ route('my-bookings') }}" class="mob-drawer-link">
                    <i class="fas fa-calendar-check" style="color:#9ca3af; width:18px;"></i> Lịch hẹn của tôi
                </a>
            @endif

            @if(isset($freshUser2))
                @if($freshUser2->user_type === 'admin' || $freshUser2->hasPermissionTo('Quản Lý Người Dùng'))
                    <a href="{{ route('admin.users.index') }}" class="mob-drawer-link">
                        <i class="fas fa-users" style="color:#9ca3af; width:18px;"></i> Quản lý người dùng
                    </a>
                @endif
                @if($freshUser2->user_type === 'admin')
                    <a href="{{ route('admin.roles.index') }}" class="mob-drawer-link">
                        <i class="fas fa-user-shield" style="color:#9ca3af; width:18px;"></i> Phân quyền
                    </a>
                @endif
                @if($freshUser2->user_type === 'admin' || $freshUser2->hasAnyPermission(['Quản Lý Tin Đăng', 'Quản Lý Bất Động Sản']))
                    <a href="{{ route('admin.properties.index') }}" class="mob-drawer-link">
                        <i class="fas fa-building" style="color:#9ca3af; width:18px;"></i> Quản lý tin đăng
                    </a>
                @endif
                @if($freshUser2->user_type === 'admin' || $freshUser2->hasPermissionTo('Quản Lý Dự Án'))
                    <a href="{{ route('admin.projects.index') }}" class="mob-drawer-link">
                        <i class="fas fa-city" style="color:#9ca3af; width:18px;"></i> Quản lý dự án
                    </a>
                @endif
            @endif

            <a href="{{ route('profile.edit') }}" class="mob-drawer-link">
                <i class="fas fa-id-card" style="color:#9ca3af; width:18px;"></i> Thông tin cá nhân
            </a>

            <div style="margin:8px 20px; border-top:1px solid #f3f4f6;"></div>

            <a href="{{ route('logout') }}" class="mob-drawer-link" style="color:#dc2626;">
                <i class="fas fa-sign-out-alt" style="color:#dc2626; width:18px;"></i> Đăng xuất
            </a>
        @else
            <div style="margin:8px 20px; border-top:1px solid #f3f4f6;"></div>
            <a href="{{ route('login') }}" class="mob-drawer-link">
                <i class="fas fa-user" style="color:#9ca3af; width:18px;"></i> Đăng nhập
            </a>
        @endif
    </nav>

    {{-- Drawer footer --}}
    <div style="padding:14px 20px; border-top:1px solid #f3f4f6; background:#f9fafb; flex-shrink:0;">
        <p style="font-size:11px; color:#9ca3af; text-align:center;">© {{ date('Y') }} LANDTEK — Nha Trang</p>
    </div>
</div>

<style>
/* ── Header action buttons ─────────────────────────────── */

/* Primary CTA: Đăng tin — desktop: pill có label */
.hdr-btn-post {
    height: 38px;
    padding: 0 16px;
    border-radius: 999px;
    box-shadow: 0 2px 10px rgba(13,148,136,0.28);
    font-size: 13.5px;
    text-decoration: none;
}
.hdr-btn-post:hover {
    box-shadow: 0 4px 16px rgba(13,148,136,0.38);
}
.hdr-post-label {
    font-size: 13.5px;
    letter-spacing: 0.01em;
}

/* Ghost button: Đăng nhập — desktop: pill có label */
.hdr-btn-login {
    height: 38px;
    padding: 0 14px;
    border-radius: 999px;
    border: 1.5px solid #e2e8f0;
    background: transparent;
    color: #374151;
    font-size: 13.5px;
    text-decoration: none;
}
.hdr-btn-login:hover {
    border-color: #0F3460;
    background: #EBF3FF;
    color: #0F3460;
}
.hdr-login-label {
    font-size: 13.5px;
}

/* Avatar circle button */
.hdr-avatar-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    color: #374151;
}
.hdr-avatar-btn:hover {
    border-color: #0F3460;
    background: #EBF3FF;
    color: #0F3460;
}

/* Hamburger button */
.hdr-btn-menu {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    color: #374151;
}
.hdr-btn-menu:hover {
    border-color: #0F3460;
    background: #EBF3FF;
    color: #0F3460;
}

/* ── Mobile (≤ 767px): icon-only, giống ảnh tham chiếu ── */
@media (max-width: 767px) {
    /* Đăng tin: teal CIRCLE, chỉ icon +, ẩn label */
    .hdr-btn-post {
        width: 38px;
        height: 38px;
        padding: 0;
        border-radius: 50%;
        box-shadow: 0 2px 10px rgba(13,148,136,0.30);
    }
    .hdr-post-label {
        display: none;
    }
    /* Đăng nhập: icon-only square bo góc, ẩn label */
    .hdr-btn-login {
        width: 38px;
        height: 38px;
        padding: 0;
        border-radius: 11px;
        background: #f8fafc;
    }
    .hdr-login-label {
        display: none;
    }
    /* Avatar và hamburger giữ kích thước nhất quán */
    .hdr-avatar-btn {
        width: 38px;
        height: 38px;
    }
    .hdr-btn-menu {
        width: 38px;
        height: 38px;
    }
}

/* ── Drawer links ─────────────────────────────────────── */
.mob-drawer-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px 20px;
    color: #374151;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: background 0.15s;
}
.mob-drawer-link:hover {
    background: #EBF3FF;
    color: #0F3460;
}
</style>

<script>
function openMobMenu() {
    document.getElementById('mob-drawer').style.transform = 'translateX(0)';
    document.getElementById('mob-drawer-backdrop').style.display = 'block';
    document.body.style.overflow = 'hidden';
    // Ẩn bottom bar nếu có
    const bar = document.getElementById('mobBottomBar');
    if (bar) bar.style.display = 'none';
}
function closeMobMenu() {
    document.getElementById('mob-drawer').style.transform = 'translateX(-100%)';
    document.getElementById('mob-drawer-backdrop').style.display = 'none';
    document.body.style.overflow = '';
    // Hiện lại bottom bar
    const bar = document.getElementById('mobBottomBar');
    if (bar) bar.style.display = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeMobMenu();
});
</script>
