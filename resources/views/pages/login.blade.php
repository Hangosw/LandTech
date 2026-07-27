@extends('layouts.app')

@section('title', 'Đăng nhập LANDTEK — Thuê & Cho thuê bất động sản Nha Trang')

@section('content')

<style>
/* ═══════════════════════════════════════════════
   LOGIN PAGE — STANDALONE STYLES (no Tailwind dep)
   ═══════════════════════════════════════════════ */

.login-wrap {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4rem 1rem;
    background-color: #f8fafc;
    box-sizing: border-box;
    width: 100%;
}

.login-card {
    background: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 24px;
    box-shadow: 0 1px 8px 0 rgba(0,0,0,0.06);
    padding: 2.5rem 2rem;
    width: 100%;
    max-width: 440px;
    display: flex;
    flex-direction: column;
    align-items: center;
    box-sizing: border-box;
}

/* Icon Badge */
.login-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: oklch(58% 0.13 218);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.25rem;
    margin-bottom: 1rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.10);
}

/* Heading */
.login-title {
    display: block;
    width: 100%;
    font-size: 1.5rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.02em;
    text-align: center !important;
    margin: 0 0 0.375rem 0;
    line-height: 1.25;
}

.login-subtitle {
    font-size: 0.875rem;
    color: #94a3b8;
    font-weight: 500;
    text-align: center;
    margin: 0 0 1.5rem 0;
    width: 100%;
}

/* Role Selector */
.role-group {
    width: 100%;
    margin-bottom: 1.5rem;
    display: none;
}

.role-group.active {
    display: block;
}

.role-label-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.625rem;
    text-align: left;
}

.role-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    width: 100%;
}

.role-radio {
    display: none;
}

.role-card-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0.75rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.15s ease;
    gap: 0.25rem;
    background: #fff;
    text-align: center;
}

.role-card-label:hover {
    background: #f8fafc;
}

.role-radio:checked + .role-card-label {
    border-color: oklch(58% 0.13 218);
    background: oklch(97% 0.02 218);
    color: oklch(51% 0.12 218);
}

.role-card-label i {
    font-size: 1rem;
    color: #94a3b8;
}

.role-radio:checked + .role-card-label i {
    color: oklch(58% 0.13 218);
}

.role-card-label span {
    font-size: 0.8125rem;
    font-weight: 600;
    color: #374151;
}

.role-radio:checked + .role-card-label span {
    color: oklch(51% 0.12 218);
}

#role-error {
    font-size: 0.75rem;
    color: #ef4444;
    margin-top: 0.5rem;
    display: none;
}

#role-error.show {
    display: block;
}

/* Social buttons */
.social-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0.75rem;
    width: 100%;
    margin-bottom: 0.25rem;
}

.social-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    background: #ffffff;
    color: #374151;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s ease, border-color 0.15s ease;
    font-family: 'Plus Jakarta Sans', sans-serif;
    white-space: nowrap;
    width: 100%;
    box-sizing: border-box;
    height: 44px;
}

.social-btn:hover {
    background: #f8fafc;
}

.social-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Divider */
.divider {
    display: flex;
    align-items: center;
    width: 100%;
    gap: 0.75rem;
    margin: 1.25rem 0;
}

.divider-line {
    flex: 1;
    height: 1px;
    background: #f1f5f9;
}

.divider-text {
    font-size: 0.75rem;
    color: #94a3b8;
    font-weight: 500;
    white-space: nowrap;
}

/* Form */
.login-form {
    width: 100%;
}

.field-wrap {
    position: relative;
    margin-bottom: 0.875rem;
}

.field-wrap.mb-lg {
    margin-bottom: 1.25rem;
}

.field-icon {
    position: absolute;
    top: 50%;
    left: 0.875rem;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.875rem;
    pointer-events: none;
    display: flex;
    align-items: center;
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    background: #f8fafc;
    font-size: 0.875rem;
    color: #374151;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 500;
    outline: none;
    transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
    box-sizing: border-box;
    -webkit-appearance: none;
    appearance: none;
}

.form-input.with-icon {
    padding-left: 2.5rem;
}

.form-input::placeholder {
    color: #94a3b8;
    font-weight: 400;
}

.form-input:focus {
    border-color: oklch(58% 0.13 218);
    background: #ffffff;
    box-shadow: 0 0 0 3px oklch(93% 0.04 218);
}

/* Error box */
.error-box {
    margin-bottom: 1rem;
    padding: 0.75rem 1rem;
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    border-radius: 12px;
    font-size: 0.8125rem;
    font-weight: 600;
}

/* Submit button */
.btn-primary {
    width: 100%;
    padding: 0.875rem 1rem;
    background: oklch(58% 0.13 218);
    color: #ffffff;
    font-size: 0.9375rem;
    font-weight: 700;
    font-family: 'Plus Jakarta Sans', sans-serif;
    border: none;
    border-radius: 14px;
    cursor: pointer;
    transition: opacity 0.15s ease, transform 0.1s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.10);
    box-sizing: border-box;
    letter-spacing: 0.01em;
}

.btn-primary:hover {
    opacity: 0.9;
}

.btn-primary:active {
    opacity: 0.95;
    transform: scale(0.99);
}

/* Footer links */
.login-footer {
    margin-top: 1.5rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
    width: 100%;
}

.login-footer p {
    margin: 0;
    font-size: 0.8125rem;
    color: #94a3b8;
    font-weight: 500;
}

.login-footer a {
    color: oklch(58% 0.13 218);
    font-weight: 700;
    text-decoration: none;
    transition: opacity 0.15s ease;
}

.login-footer a:hover {
    opacity: 0.8;
}
</style>

<div class="login-wrap">
    <div style="width:100%; max-width:440px;">

        <!-- Card -->
        <div class="login-card">

            <!-- Icon -->
            <div class="login-icon">
                <i class="fas fa-water"></i>
            </div>

            <!-- Title & Subtitle -->
            <h2 class="login-title" id="auth-title">Đăng nhập LANDTEK</h2>
            <p class="login-subtitle" id="auth-subtitle">Chào mừng bạn quay lại</p>

            <!-- Role Selector (shown on register) -->
            <div id="role-selector-group" class="role-group">
                <p class="role-label-title">Bạn đăng ký với vai trò gì? <span style="color:#ef4444">*</span></p>
                <div class="role-grid">
                    <div>
                        <input type="radio" name="user_type_selection" id="role-renter" value="renter" class="role-radio" onchange="syncRole('renter')">
                        <label for="role-renter" class="role-card-label">
                            <i class="fas fa-user"></i>
                            <span>Người thuê</span>
                        </label>
                    </div>
                    <div>
                        <input type="radio" name="user_type_selection" id="role-agent" value="agent" class="role-radio" onchange="syncRole('agent')">
                        <label for="role-agent" class="role-card-label">
                            <i class="fas fa-briefcase"></i>
                            <span>Môi giới</span>
                        </label>
                    </div>
                </div>
                <p id="role-error">Vui lòng chọn vai trò trước khi tiếp tục</p>
            </div>

            <!-- Social Buttons -->
            <div class="social-grid">
                <button
                    type="button"
                    id="btn-google-login"
                    onclick="openGooglePopup()"
                    class="social-btn"
                >
                    <i class="fab fa-google" style="color:#ea4335; font-size:1rem;"></i>
                    <span id="google-btn-text">Google</span>
                </button>
                <button
                    type="button"
                    onclick="openZaloOAuth()"
                    class="social-btn"
                    style="display: none;"
                >
                    <i class="fas fa-comment" style="color:#0068ff; font-size:1rem;"></i>
                    <span>Zalo</span>
                </button>
            </div>

            <!-- Divider -->
            <div class="divider">
                <div class="divider-line"></div>
                <span class="divider-text">hoặc</span>
                <div class="divider-line"></div>
            </div>

            <!-- Form -->
            <form action="{{ url('/dang-nhap') }}" method="POST" class="login-form" onsubmit="return checkRoleSelection()">
                @csrf
                <input type="hidden" name="action" id="auth-action" value="login">
                <input type="hidden" name="user_type" id="hidden_user_type" value="">

                @if(session('error'))
                    <div class="error-box">{{ session('error') }}</div>
                @endif

                <!-- Name field (register only) -->
                <div id="name-field-group" class="field-wrap" style="display:none;">
                    <input
                        type="text"
                        name="name"
                        id="name-input"
                        placeholder="Họ và tên"
                        class="form-input"
                    >
                </div>

                <!-- Login ID -->
                <div class="field-wrap">
                    <span class="field-icon"><i class="far fa-envelope"></i></span>
                    <input
                        type="text"
                        name="login_id"
                        placeholder="Số điện thoại hoặc email"
                        class="form-input with-icon"
                        required
                    >
                </div>

                <!-- Password -->
                <div class="field-wrap mb-lg">
                    <input
                        type="password"
                        name="password"
                        placeholder="Mật khẩu"
                        class="form-input"
                        required
                    >
                </div>

                <!-- Submit -->
                <button type="submit" id="auth-submit-btn" class="btn-primary">
                    Đăng nhập
                </button>
            </form>

            <!-- Footer Links -->
            <div class="login-footer" id="auth-links-wrapper">
                <p id="toggle-desc">
                    Chưa có tài khoản?
                    <a href="javascript:void(0)" onclick="toggleAuthState('register')">Đăng ký ngay</a>
                </p>
                <p>
                    Là môi giới?
                    <a href="#">Đăng ký Verified Agent</a>
                </p>
            </div>

        </div><!-- /.login-card -->
    </div>
</div>

<script>
    // ── Save intended URL before login ──────────
    (function () {
        const referrer = document.referrer;
        const loginPath = '/dang-nhap';
        if (referrer && !referrer.includes(loginPath) && referrer.startsWith(window.location.origin)) {
            sessionStorage.setItem('landtek_intended_url', referrer);
        }
    })();

    // ── Toggle login / register ──────────
    function toggleAuthState(state) {
        const actionInput  = document.getElementById('auth-action');
        const titleEl      = document.getElementById('auth-title');
        const subtitleEl   = document.getElementById('auth-subtitle');
        const nameField    = document.getElementById('name-field-group');
        const nameInput    = document.getElementById('name-input');
        const submitBtn    = document.getElementById('auth-submit-btn');
        const toggleDesc   = document.getElementById('toggle-desc');
        const roleGroup    = document.getElementById('role-selector-group');

        if (state === 'register') {
            actionInput.value = 'register';
            titleEl.textContent = 'Tạo tài khoản';
            subtitleEl.textContent = 'Tham gia nền tảng thuê nhà Nha Trang';
            nameField.style.display = 'block';
            nameInput.setAttribute('required', 'required');
            roleGroup.style.display = 'block';
            submitBtn.textContent = 'Đăng ký';
            toggleDesc.innerHTML = 'Đã có tài khoản? <a href="javascript:void(0)" onclick="toggleAuthState(\'login\')">Đăng nhập</a>';
        } else {
            actionInput.value = 'login';
            titleEl.textContent = 'Đăng nhập LANDTEK';
            subtitleEl.textContent = 'Chào mừng bạn quay lại';
            nameField.style.display = 'none';
            nameInput.removeAttribute('required');
            roleGroup.style.display = 'none';
            submitBtn.textContent = 'Đăng nhập';
            toggleDesc.innerHTML = 'Chưa có tài khoản? <a href="javascript:void(0)" onclick="toggleAuthState(\'register\')">Đăng ký ngay</a>';
        }
    }

    function syncRole(role) {
        document.getElementById('hidden_user_type').value = role;
        document.getElementById('role-error').style.display = 'none';
    }

    function checkRoleSelection() {
        const actionInput = document.getElementById('auth-action').value;
        const role = document.getElementById('hidden_user_type').value;
        if (actionInput === 'register' && !role) {
            document.getElementById('role-error').style.display = 'block';
            return false;
        }
        return true;
    }

    function openZaloOAuth() {
        const actionInput = document.getElementById('auth-action').value;
        const role = document.getElementById('hidden_user_type').value;
        if (actionInput === 'register' && !role) {
            document.getElementById('role-error').style.display = 'block';
            return;
        }
        let url = '{{ route('oauth.redirect', 'zalo') }}';
        if (actionInput === 'register' && role) url += '?user_type=' + role;
        window.location.href = url;
    }

    // ── Google OAuth Popup ────────────────────────────────────────────────
    let googlePopup = null;

    function openGooglePopup() {
        const actionInput = document.getElementById('auth-action').value;
        const role = document.getElementById('hidden_user_type').value;
        if (actionInput === 'register' && !role) {
            document.getElementById('role-error').style.display = 'block';
            return;
        }

        const btn  = document.getElementById('btn-google-login');
        const text = document.getElementById('google-btn-text');
        let url = '{{ route('auth.google') }}';
        if (actionInput === 'register' && role) url += '?user_type=' + role;

        const w = 500, h = 650;
        const left = Math.round(window.screenX + (window.outerWidth  - w) / 2);
        const top  = Math.round(window.screenY + (window.outerHeight - h) / 2);
        const features = `width=${w},height=${h},left=${left},top=${top},resizable=yes,scrollbars=yes,status=yes`;

        if (googlePopup && !googlePopup.closed) googlePopup.close();
        googlePopup = window.open(url, 'googleOAuthPopup', features);

        btn.disabled = true;
        text.textContent = 'Đang mở...';
        btn.style.opacity = '0.6';

        const pollTimer = setInterval(() => {
            if (googlePopup && googlePopup.closed) {
                clearInterval(pollTimer);
                btn.disabled = false;
                text.textContent = 'Google';
                btn.style.opacity = '1';
            }
        }, 800);
    }

    // ── postMessage from OAuth popup ──────────────────────────────────────
    window.addEventListener('message', function (event) {
        if (event.origin !== window.location.origin) return;

        const btn  = document.getElementById('btn-google-login');
        const text = document.getElementById('google-btn-text');
        btn.disabled = false;
        btn.style.opacity = '1';

        const { status, message } = event.data || {};

        if (status === 'success') {
            text.textContent = '✓ Thành công';
            const intendedUrl = sessionStorage.getItem('landtek_intended_url');
            sessionStorage.removeItem('landtek_intended_url');
            const homeUrl = '{{ route('home') }}';
            if (intendedUrl && intendedUrl !== window.location.href && !intendedUrl.includes('/dang-nhap')) {
                window.location.href = intendedUrl;
            } else {
                window.location.href = homeUrl;
            }
        } else if (status === 'link_offer') {
            text.textContent = 'Cần liên kết...';
            window.location.href = '{{ route('auth.link-provider-offer') }}';
        } else if (status === 'error') {
            text.textContent = 'Google';
            alert('Đăng nhập Google thất bại: ' + (message || 'Vui lòng thử lại.'));
        } else {
            text.textContent = 'Google';
        }
    });
</script>
@endsection
