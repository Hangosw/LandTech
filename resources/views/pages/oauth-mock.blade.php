@extends('layouts.app')

@section('title', 'Mô phỏng Đăng nhập ' . ucfirst($provider) . ' — LANDTEK')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-16 bg-slate-50/50">
    <div class="w-full max-w-[460px] mx-4">
        
        <!-- Simulation Card Container -->
        <div class="bg-white p-8 md:p-10 rounded-3xl border border-gray-100 shadow-sm flex flex-col items-center">
            
            <!-- Provider Icon Badge -->
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white shadow-sm mb-4 {{ $provider === 'google' ? 'bg-red-500' : 'bg-blue-500' }}">
                @if($provider === 'google')
                    <i class="fab fa-google text-2xl"></i>
                @else
                    <i class="fas fa-comment text-2xl"></i>
                @endif
            </div>
            
            <!-- Heading & Subtitle -->
            <h2 class="text-xl font-extrabold text-gray-900 tracking-tight text-center">
                Mô phỏng Đăng nhập {{ ucfirst($provider) }}
            </h2>
            <p class="text-xs text-gray-400 mt-2 font-medium text-center max-w-xs leading-relaxed">
                Môi trường mô phỏng đăng nhập bằng tài khoản liên kết Google/Zalo để kiểm thử chức năng Multi-Auth Linking.
            </p>

            <!-- Info Alert -->
            <div class="w-full mt-5 p-3.5 bg-[#edf6f9]/80 border border-teal-100/50 rounded-2xl text-[11px] text-teal-800 leading-relaxed font-medium">
                <i class="fas fa-circle-info mr-1 text-teal-600"></i>
                Nhập một email <strong>đã tồn tại</strong> trong hệ thống để thử nghiệm quy trình <strong>Liên kết tài khoản (Link Account)</strong>, hoặc một email mới để tạo tài khoản mới.
            </div>
            
            <!-- Mock OAuth Form -->
            <form action="{{ route('oauth.callback', $provider) }}" method="POST" class="w-full mt-6">
                @csrf
                
                <!-- Mock Provider ID -->
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5 pl-1">ID Tài khoản {{ ucfirst($provider) }}</label>
                    <input 
                        type="text" 
                        name="provider_id" 
                        value="oauth_id_{{ mt_rand(100000, 999999) }}" 
                        placeholder="Provider ID" 
                        class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200/80 rounded-2xl outline-none text-sm text-gray-700 focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary/10 transition-all font-medium"
                        required
                    >
                </div>

                <!-- Email Input -->
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5 pl-1">Email Mock</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="test_oauth@example.com" 
                        placeholder="Nhập email" 
                        class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200/80 rounded-2xl outline-none text-sm text-gray-700 focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary/10 transition-all font-medium"
                        required
                    >
                </div>
                
                <!-- Name Input -->
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5 pl-1">Tên hiển thị</label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ ucfirst($provider) }} Demo User" 
                        placeholder="Họ và tên" 
                        class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200/80 rounded-2xl outline-none text-sm text-gray-700 focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary/10 transition-all font-medium"
                        required
                    >
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="w-full py-3.5 bg-primary text-white font-bold rounded-2xl shadow-sm hover:opacity-90 active:opacity-95 transition-opacity text-sm cursor-pointer flex items-center justify-center gap-2">
                    Tiếp tục Đăng nhập <i class="fas fa-arrow-right text-xs"></i>
                </button>

                <!-- Cancel Button -->
                <a href="{{ route('login') }}" class="block w-full text-center mt-3 py-3 border border-gray-200 text-gray-500 hover:text-gray-700 font-semibold rounded-2xl text-sm transition-colors cursor-pointer bg-white">
                    Hủy bỏ
                </a>
            </form>
            
        </div>
        
    </div>
</div>
@endsection
