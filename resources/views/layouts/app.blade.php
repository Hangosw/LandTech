<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LANDTEK — Thuê & Cho thuê bất động sản Nha Trang')</title>
    <meta name="description" content="@yield('description', 'Nền tảng thuê nhà chuyên biệt tại Nha Trang')">
    
    {{-- CSS --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
    @include('components.header')
    
    <main class="flex-1">
        @yield('content')
    </main>
    
    @include('components.footer')
    
    {{-- Scripts --}}
    @vite('resources/js/app.js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ===== GLOBAL SWEETALERT2 MIXIN =====
        window.SwalSuccess = Swal.mixin({
            icon: 'success',
            confirmButtonText: 'Đóng',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'swal-btn-primary',
                cancelButton: 'swal-btn-secondary',
            }
        });
        window.SwalError = Swal.mixin({
            icon: 'error',
            confirmButtonText: 'Đóng',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'swal-btn-primary',
            }
        });
        window.SwalWarning = Swal.mixin({
            icon: 'warning',
            confirmButtonText: 'Đóng',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'swal-btn-primary',
                cancelButton: 'swal-btn-secondary',
            }
        });
        window.SwalConfirm = function(message, callback) {
            Swal.fire({
                title: 'Xác nhận',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'swal-btn-danger',
                    cancelButton: 'swal-btn-secondary',
                }
            }).then(function(result) {
                if (result.isConfirmed) callback();
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
            SwalSuccess.fire({ title: 'Thành công!', text: '{{ addslashes(session('success')) }}' });
            @endif
            @if(session('error'))
            SwalError.fire({ title: 'Lỗi!', text: '{{ addslashes(session('error')) }}' });
            @endif
            @if(session('warning'))
            SwalWarning.fire({ title: 'Cảnh báo!', text: '{{ addslashes(session('warning')) }}' });
            @endif
            @if(session('register_success'))
            SwalSuccess.fire({ title: 'Thành công!', text: '{{ addslashes(session('register_success')) }}' });
            @endif
        });
    </script>
    <style>
        .swal-btn-primary { background: oklch(58% 0.13 218) !important; color: white !important; padding: 0.625rem 1.5rem !important; border-radius: 0.75rem !important; font-weight: 700 !important; font-size: 0.875rem !important; border: none !important; cursor: pointer !important; transition: opacity 0.2s !important; }
        .swal-btn-primary:hover { opacity: 0.88 !important; }
        .swal-btn-secondary { background: #f1f5f9 !important; color: #475569 !important; padding: 0.625rem 1.5rem !important; border-radius: 0.75rem !important; font-weight: 700 !important; font-size: 0.875rem !important; border: none !important; cursor: pointer !important; margin-right: 0.5rem !important; transition: background 0.2s !important; }
        .swal-btn-secondary:hover { background: #e2e8f0 !important; }
        .swal-btn-danger { background: #dc2626 !important; color: white !important; padding: 0.625rem 1.5rem !important; border-radius: 0.75rem !important; font-weight: 700 !important; font-size: 0.875rem !important; border: none !important; cursor: pointer !important; transition: opacity 0.2s !important; }
        .swal-btn-danger:hover { opacity: 0.88 !important; }
        .swal2-popup { border-radius: 1.25rem !important; font-family: 'Plus Jakarta Sans', sans-serif !important; }
    </style>
    @stack('scripts')
</body>
</html>
