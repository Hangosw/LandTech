@extends('layouts.app')

@section('title', 'Yêu cầu liên kết tài khoản — LANDTEK')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-16 bg-slate-50/50">
    <div class="w-full max-w-[480px] mx-4">
        
        <!-- Linking Card Container -->
        <div class="bg-white p-8 md:p-10 rounded-3xl border border-gray-100 shadow-sm flex flex-col items-center">
            
            <!-- Alert Icon Badge -->
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shadow-xs mb-4 border border-amber-100/50">
                <i class="fas fa-link-slash text-2xl"></i>
            </div>
            
            <!-- Heading & Subtitle -->
            <h2 class="text-xl font-extrabold text-gray-900 tracking-tight text-center">
                Liên kết tài khoản
            </h2>
            <p class="text-xs text-gray-400 mt-2 font-medium text-center">
                Phát hiện tài khoản email trùng khớp trong hệ thống
            </p>

            <!-- Warning Information -->
            <div class="w-full mt-6 p-4 bg-slate-50 border border-gray-100 rounded-2xl text-sm text-gray-600 leading-relaxed font-medium">
                Địa chỉ email <strong class="text-gray-900 font-semibold">{{ $email }}</strong> đã được đăng ký trước đây trên hệ thống LandTek.
                <div class="mt-2.5 pt-2.5 border-t border-gray-200/60 text-xs text-gray-500">
                    Bạn có muốn liên kết tài khoản LandTek hiện tại này với tài khoản <strong>{{ ucfirst($provider) }}</strong> vừa chọn không? Sau khi liên kết, bạn có thể đăng nhập bằng cả hai cách.
                </div>
            </div>
            
            <!-- Confirm linking form -->
            <form action="{{ route('auth.link-provider') }}" method="POST" class="w-full mt-6">
                @csrf
                <input type="hidden" name="provider" value="{{ $provider }}">
                <input type="hidden" name="provider_id" value="{{ $provider_id }}">
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="name" value="{{ $name }}">
                
                <!-- Submit Link Button (OKLCH primary color) -->
                <button type="submit" class="w-full py-3.5 bg-primary text-white font-bold rounded-2xl shadow-sm hover:opacity-90 active:opacity-95 transition-opacity text-sm cursor-pointer flex items-center justify-center gap-2">
                    <i class="fas fa-link text-xs"></i> Xác nhận liên kết tài khoản
                </button>

                <!-- Cancel Button -->
                <a href="{{ route('login') }}" class="block w-full text-center mt-3 py-3 border border-gray-200 text-gray-500 hover:text-gray-700 font-semibold rounded-2xl text-sm transition-colors cursor-pointer bg-white">
                    Hủy bỏ & Quay lại
                </a>
            </form>
            
        </div>
        
    </div>
</div>
@endsection
