<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LANDTEK — Đăng tin')</title>
    
    {{-- CSS --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    
    <!-- Alpine.js for interactive wizard UI (if needed in views) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50/50 min-h-screen flex flex-col font-sans">
    
    <!-- Minimal Header (Focus Mode) -->
    <header class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 md:px-6 py-3 lg:py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="group flex items-center gap-2 text-gray-500 hover:text-teal-600 transition-colors font-medium">
                <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-teal-50 transition-colors">
                    <i class="fas fa-arrow-left text-sm"></i>
                </div>
                <span class="hidden sm:inline">Quay lại trang chủ</span>
            </a>
            
            <a href="{{ route('home') }}" class="text-xl sm:text-2xl font-black text-teal-600 tracking-tight">
                LANDTEK<span class="text-teal-400">.</span>
            </a>
            
            <!-- Placeholder to balance flex-between -->
            <div class="w-[120px] hidden sm:block"></div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col">
        @yield('content')
    </main>
    
    {{-- Scripts --}}
    @vite('resources/js/app.js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
