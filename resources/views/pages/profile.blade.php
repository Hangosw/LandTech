@extends('layouts.app')

@section('title', 'Thông tin cá nhân — LANDTEK')

@section('content')
<div class="min-h-screen py-12 bg-slate-50/50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Thông tin cá nhân</h1>
            <p class="text-sm text-gray-500 mt-2">Quản lý thông tin hồ sơ và bảo mật tài khoản của bạn</p>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Avatar Column -->
                <div class="md:col-span-1">
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col items-center">
                        <div class="relative group cursor-pointer" onclick="document.getElementById('avatar-input').click()">
                            <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-md bg-gray-100 flex items-center justify-center relative">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover" id="avatar-preview">
                                @else
                                    <span class="text-4xl font-bold text-gray-300" id="avatar-initial">{{ substr($user->name, 0, 1) }}</span>
                                    <img src="" alt="Avatar" class="w-full h-full object-cover hidden" id="avatar-preview">
                                @endif
                                
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <i class="fas fa-camera text-white text-2xl"></i>
                                </div>
                            </div>
                        </div>
                        <input type="file" name="avatar" id="avatar-input" class="hidden" accept="image/jpeg,image/png,image/webp,image/jpg" onchange="previewAvatar(event)">
                        
                        <h3 class="mt-4 font-bold text-gray-900 text-lg">{{ $user->name }}</h3>
                        <p class="text-xs font-medium text-gray-400 capitalize px-3 py-1 bg-gray-100 rounded-full mt-1">{{ $user->user_type }}</p>
                        
                        <p class="text-xs text-gray-400 mt-4 text-center">
                            Định dạng hỗ trợ: JPEG, PNG, WEBP.<br>Tối đa 5MB.
                        </p>
                    </div>
                </div>

                <!-- Info Column -->
                <div class="md:col-span-2 space-y-6">
                    
                    <!-- General Info Card -->
                    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-100 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center">
                            <i class="fas fa-user-circle text-primary mr-2"></i> Thông tin chung
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Họ và tên <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200/80 rounded-2xl outline-none text-sm text-gray-700 focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary/10 transition-all font-medium" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Địa chỉ</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <input type="text" name="address" value="{{ old('address', $user->address) }}" placeholder="Ví dụ: 123 Trần Phú, Nha Trang" class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-200/80 rounded-2xl outline-none text-sm text-gray-700 focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary/10 transition-all font-medium">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact & Account Card -->
                    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-100 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center">
                            <i class="fas fa-id-card text-primary mr-2"></i> Thông tin liên hệ & Tài khoản
                        </h3>
                        <p class="text-xs text-gray-500 mb-5">Số điện thoại hoặc Email được sử dụng để đăng nhập vào hệ thống.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Số điện thoại <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                                        <i class="fas fa-phone-alt"></i>
                                    </span>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-200/80 rounded-2xl outline-none text-sm text-gray-700 focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary/10 transition-all font-medium" required>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Địa chỉ Email</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-200/80 rounded-2xl outline-none text-sm text-gray-700 focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary/10 transition-all font-medium">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Card -->
                    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-100 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center">
                            <i class="fas fa-shield-alt text-primary mr-2"></i> Bảo mật
                        </h3>
                        
                        <div class="space-y-4">
                            @if($user->password_hash)
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mật khẩu hiện tại</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" name="current_password" placeholder="Nhập mật khẩu hiện tại" class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-200/80 rounded-2xl outline-none text-sm text-gray-700 focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary/10 transition-all font-medium">
                                </div>
                            </div>
                            @else
                            <div class="p-4 mb-4 text-sm text-blue-800 rounded-xl bg-blue-50 border border-blue-100 flex items-start">
                                <i class="fas fa-info-circle mt-0.5 mr-2"></i>
                                <span>Tài khoản của bạn hiện chưa có mật khẩu (có thể do đăng nhập bằng mạng xã hội). Hãy tạo mật khẩu mới bên dưới.</span>
                            </div>
                            @endif

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mật khẩu mới</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                                            <i class="fas fa-key"></i>
                                        </span>
                                        <input type="password" name="new_password" placeholder="Tối thiểu 6 ký tự" class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-200/80 rounded-2xl outline-none text-sm text-gray-700 focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary/10 transition-all font-medium">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Xác nhận mật khẩu mới</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                                            <i class="fas fa-check-double"></i>
                                        </span>
                                        <input type="password" name="new_password_confirmation" placeholder="Nhập lại mật khẩu mới" class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-200/80 rounded-2xl outline-none text-sm text-gray-700 focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary/10 transition-all font-medium">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="py-3 px-8 bg-primary text-white font-bold rounded-2xl shadow-sm hover:opacity-90 active:opacity-95 transition-all text-sm flex items-center gap-2 cursor-pointer">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div>

<script>
    function previewAvatar(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const preview = document.getElementById('avatar-preview');
                const initial = document.getElementById('avatar-initial');
                
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                
                if (initial) {
                    initial.classList.add('hidden');
                }
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
