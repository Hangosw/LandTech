@extends('layouts.admin')

@section('title', 'Sửa Người Dùng — Admin LANDTEK')

@section('content')
<div class="min-h-[100vh] bg-slate-50/50 py-10">
    <div class="container mx-auto px-4 md:px-6 lg:px-8 max-w-7xl">
        
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Sửa Người Dùng</h1>
                <p class="text-sm text-gray-500 mt-1.5 font-medium">Chỉnh sửa thông tin tài khoản: {{ $user->name }}</p>
            </div>
            <div>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-primary hover:opacity-80 transition-opacity bg-primary/10 px-4 py-2 rounded-xl flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-800 rounded-xl shadow-sm text-sm font-semibold">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-8">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Họ và tên</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors" required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Số điện thoại</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Quốc tịch</label>
                        <input type="text" name="nationality" value="{{ old('nationality', $user->nationality) }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Loại tài khoản</label>
                        <select name="user_type" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors cursor-pointer">
                            <option value="seller" {{ old('user_type', $user->user_type) == 'seller' ? 'selected' : '' }}>Seller</option>
                            <option value="buyer" {{ old('user_type', $user->user_type) == 'buyer' ? 'selected' : '' }}>Buyer</option>
                            <option value="owner" {{ old('user_type', $user->user_type) == 'owner' ? 'selected' : '' }}>Owner</option>
                            <option value="renter" {{ old('user_type', $user->user_type) == 'renter' ? 'selected' : '' }}>Renter</option>
                            <option value="agent" {{ old('user_type', $user->user_type) == 'agent' ? 'selected' : '' }}>Agent (Môi giới)</option>
                            <option value="admin" {{ old('user_type', $user->user_type) == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Trạng thái</label>
                        <select name="status" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors cursor-pointer">
                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Kích hoạt (Active)</option>
                            <option value="pending" {{ old('status', $user->status) == 'pending' ? 'selected' : '' }}>Chờ duyệt (Pending)</option>
                            <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Vô hiệu hóa (Inactive)</option>
                            <option value="banned" {{ old('status', $user->status) == 'banned' ? 'selected' : '' }}>Cấm (Banned)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Mật khẩu mới</label>
                        <input type="password" name="password" minlength="6" placeholder="Để trống nếu không đổi"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Xác nhận mật khẩu mới</label>
                        <input type="password" name="password_confirmation" minlength="6" placeholder="Nhập lại mật khẩu mới"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors">
                    </div>
                </div>

                <!-- PERMISSION SECTION -->
                <div id="permission-section" class="mt-8 pt-6 border-t border-gray-200">
                    <div class="mb-4">
                        <h3 class="text-lg font-extrabold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-user-shield text-primary"></i> Phân Quyền &amp; Vai Trò Spatie Cho Tài Khoản Này
                        </h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium">Gán vai trò chính và các quyền trực tiếp riêng cho tài khoản: <strong class="text-gray-800">{{ $user->name }}</strong></p>
                    </div>

                    <!-- 1. Roles Selection -->
                    <div class="mb-6 bg-slate-50 p-4 rounded-xl border border-gray-200">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Vai trò (Roles) áp dụng:</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach($roles as $role)
                                <label class="inline-flex items-center gap-2 bg-white px-3.5 py-2 rounded-lg border border-gray-200 text-sm font-bold text-gray-800 cursor-pointer hover:border-primary transition-colors">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                           {{ in_array($role->name, $userRoles) ? 'checked' : '' }}
                                           class="w-4 h-4 accent-primary rounded cursor-pointer">
                                    <span>{{ strtoupper($role->name) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. Direct Permissions Selection -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-gray-200">
                        <input type="hidden" name="direct_permissions_submitted" value="1">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Quyền hạn trực tiếp (Direct Permissions riêng cho tài khoản):</label>

                        @foreach($groupedPermissions as $groupName => $groupPerms)
                            <div class="mb-4 last:mb-0">
                                <div class="text-xs font-extrabold text-primary uppercase tracking-wide mb-2 flex items-center gap-1.5 border-b border-gray-200 pb-1">
                                    <i class="fas fa-layer-group"></i> {{ $groupName }}
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach($groupPerms as $perm)
                                        @php
                                            $isViaRole = $user->hasPermissionTo($perm->name) && !in_array($perm->name, $userDirectPermissions);
                                        @endphp
                                        <label class="flex items-center gap-2 bg-white p-2.5 rounded-lg border border-gray-200 text-xs font-bold text-gray-700 cursor-pointer hover:border-primary transition-colors">
                                            <input type="checkbox" name="direct_permissions[]" value="{{ $perm->name }}"
                                                   {{ in_array($perm->name, $userDirectPermissions) ? 'checked' : '' }}
                                                   class="w-4 h-4 accent-primary rounded cursor-pointer">
                                            <span class="truncate" title="{{ $perm->name }}">{{ $perm->name }}</span>
                                            @if($isViaRole)
                                                <span class="ml-auto text-[10px] bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded font-extrabold" title="Quyền này được thừa hưởng từ Vai trò">Từ Role</span>
                                            @endif
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-10 pt-6 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="bg-primary hover:opacity-90 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-opacity shadow-sm flex items-center gap-2">
                        <i class="fas fa-save"></i> Lưu Thay Đổi
                    </button>
                </div>
            </form>
        </div>

        @if($user->user_type === 'agent')
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Danh sách bài đăng của môi giới</h2>
                <span class="px-3 py-1 bg-primary/10 text-primary rounded-lg text-sm font-bold">{{ $user->properties->count() }} bài đăng</span>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm py-4 mb-8">
                <div class="overflow-x-auto w-full rounded-t-xl">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead class="bg-primary/10 text-primary">
                            <tr class="text-xs font-bold uppercase tracking-wider">
                                <th class="px-6 py-4 border-b border-primary/20">Mã BĐS</th>
                                <th class="px-6 py-4 border-b border-primary/20">Tiêu đề</th>
                                <th class="px-6 py-4 border-b border-primary/20">Loại / Giao dịch</th>
                                <th class="px-6 py-4 border-b border-primary/20">Giá</th>
                                <th class="px-6 py-4 border-b border-primary/20">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($user->properties as $property)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-500 font-semibold">#{{ $property->id }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900 line-clamp-2" title="{{ $property->title }}">{{ $property->title }}</div>
                                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                            <i class="fas fa-map-marker-alt text-gray-400"></i>
                                            <span class="truncate max-w-[200px]" title="{{ $property->address }}">{{ $property->address }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-gray-700 capitalize">{{ $property->property_type }}</div>
                                        <div class="text-xs text-gray-500 mt-1 capitalize">{{ $property->transaction_type }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-primary">
                                            @if($property->transaction_type == 'rent')
                                                {{ number_format($property->monthly_price) }} đ / tháng
                                            @else
                                                {{ number_format($property->price) }} đ
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                         @if($property->status == 'sansangchothue')
                                             <span class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700">Sẵn sàng cho thuê</span>
                                         @elseif($property->status == 'choduyet')
                                             <span class="px-3 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-700">Chờ duyệt</span>
                                         @elseif($property->status == 'dachothue')
                                             <span class="px-3 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">Đã cho thuê</span>
                                         @elseif($property->status == 'nhap')
                                             <span class="px-3 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">Nháp</span>
                                         @elseif($property->status == 'hethantin')
                                             <span class="px-3 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-700">Hết hạn tin</span>
                                         @elseif($property->status == 'ngungkhaithac')
                                             <span class="px-3 py-1 rounded-lg text-xs font-bold bg-purple-100 text-purple-700">Ngừng khai thác</span>
                                         @elseif($property->status == 'bigovipham')
                                             <span class="px-3 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-700">Bị gỡ (vi phạm)</span>
                                         @else
                                             <span class="px-3 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-600">Tạm ẩn</span>
                                         @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm font-medium">
                                        Môi giới này chưa có bài đăng nào.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        
    </div>
</div>
@endsection
