@extends('layouts.admin')

@section('title', 'Sửa Bài Đăng BĐS — Admin LANDTEK')

@section('content')
<div class="min-h-[100vh] bg-slate-50/50 py-10">
    <div class="container mx-auto px-4 md:px-6 lg:px-8 max-w-7xl">
        
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Sửa Bài Đăng BĐS</h1>
                <p class="text-sm text-gray-500 mt-1.5 font-medium">Chỉnh sửa thông tin bất động sản: #{{ $property->id }}</p>
            </div>
            <div>
                <a href="{{ route('admin.properties.index') }}" class="text-sm font-semibold text-primary hover:opacity-80 transition-opacity bg-primary/10 px-4 py-2 rounded-xl flex items-center gap-2">
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
            <form action="{{ route('admin.properties.update', $property->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tiêu đề bài đăng</label>
                        <input type="text" name="title" value="{{ old('title', $property->title) }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Mô tả chi tiết</label>
                        <textarea name="description" rows="5" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors">{{ old('description', $property->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Loại Bất động sản</label>
                        <select name="property_type" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors cursor-pointer">
                            <option value="apartment" {{ old('property_type', $property->property_type) == 'apartment' ? 'selected' : '' }}>Căn hộ chung cư</option>
                            <option value="house" {{ old('property_type', $property->property_type) == 'house' ? 'selected' : '' }}>Nhà riêng</option>
                            <option value="villa" {{ old('property_type', $property->property_type) == 'villa' ? 'selected' : '' }}>Biệt thự</option>
                            <option value="office" {{ old('property_type', $property->property_type) == 'office' ? 'selected' : '' }}>Văn phòng</option>
                            <option value="land" {{ old('property_type', $property->property_type) == 'land' ? 'selected' : '' }}>Đất nền</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Loại giao dịch</label>
                        <select name="transaction_type" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors cursor-pointer">
                            <option value="sale" {{ old('transaction_type', $property->transaction_type) == 'sale' ? 'selected' : '' }}>Bán</option>
                            <option value="rent" {{ old('transaction_type', $property->transaction_type) == 'rent' ? 'selected' : '' }}>Cho thuê</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Khu vực (Quận/Huyện)</label>
                        <input type="text" name="district" value="{{ old('district', $property->district) }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors" required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Địa chỉ cụ thể</label>
                        <input type="text" name="address" value="{{ old('address', $property->address) }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors" required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Giá (VND)</label>
                        <input type="number" name="price" value="{{ old('price', $property->price) }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors" required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Diện tích (m2)</label>
                        <input type="number" step="0.1" name="area" value="{{ old('area', $property->area) }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors" required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Trạng thái bài đăng</label>
                        <select name="status" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors cursor-pointer">
                            <option value="nhap" {{ old('status', $property->status) == 'nhap' ? 'selected' : '' }}>1. Nháp</option>
                            <option value="choduyet" {{ old('status', $property->status) == 'choduyet' ? 'selected' : '' }}>2. Chờ duyệt</option>
                            <option value="sansangchothue" {{ old('status', $property->status) == 'sansangchothue' ? 'selected' : '' }}>3. Sẵn sàng cho thuê</option>
                            <option value="dachothue" {{ old('status', $property->status) == 'dachothue' ? 'selected' : '' }}>4. Đã cho thuê</option>
                            <option value="taman" {{ old('status', $property->status) == 'taman' ? 'selected' : '' }}>5. Tạm ẩn</option>
                            <option value="hethantin" {{ old('status', $property->status) == 'hethantin' ? 'selected' : '' }}>6. Hết hạn tin</option>
                            <option value="ngungkhaithac" {{ old('status', $property->status) == 'ngungkhaithac' ? 'selected' : '' }}>7. Ngừng khai thác</option>
                            <option value="bigovipham" {{ old('status', $property->status) == 'bigovipham' ? 'selected' : '' }}>8. Bị gỡ (vi phạm)</option>
                        </select>
                    </div>

                    @if(in_array($property->status, ['nhap', 'choduyet']))
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Thay đổi ảnh bìa dự án</label>
                        <div class="flex items-start gap-6">
                            @if($property->cover_image_url)
                                <img src="{{ $property->cover_image_url }}" alt="Cover" class="w-40 h-24 object-cover rounded-xl border border-gray-200">
                            @endif
                            <div class="flex-1">
                                <input type="file" name="cover_image" accept="image/*" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors">
                                <p class="text-xs text-gray-500 mt-2">Chỉ hỗ trợ đổi ảnh bìa khi bài đăng đang ở trạng thái Nháp hoặc Chờ duyệt.</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Ảnh bìa dự án</label>
                        @if($property->cover_image_url)
                            <img src="{{ $property->cover_image_url }}" alt="Cover" class="w-40 h-24 object-cover rounded-xl border border-gray-200">
                        @else
                            <p class="text-sm text-gray-500 italic">Chưa có ảnh bìa</p>
                        @endif
                    </div>
                    @endif
                </div>

                <div class="mt-10 pt-6 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="bg-primary hover:opacity-90 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-opacity shadow-sm flex items-center gap-2">
                        <i class="fas fa-save"></i> Lưu Thay Đổi
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Hiển thị Media của Bài đăng -->
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Hình ảnh & Video đính kèm</h2>
            <span class="px-3 py-1 bg-primary/10 text-primary rounded-lg text-sm font-bold">{{ $property->media->count() }} tệp tin</span>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8">
            @if($property->media->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($property->media as $media)
                        <div class="relative group aspect-square rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                            @if(in_array($media->media_type, ['image', 'photo', 'picture']))
                                <img src="{{ $media->file_url ?? asset('storage/' . $media->file_path) }}" alt="Property Media" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-video text-3xl mb-2"></i>
                                    <span class="text-xs font-semibold">{{ $media->media_type }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mx-auto mb-3">
                        <i class="fas fa-images text-xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium text-sm">Chưa có hình ảnh nào được tải lên cho bài đăng này.</p>
                </div>
            @endif
        </div>
        
    </div>
</div>
@endsection
