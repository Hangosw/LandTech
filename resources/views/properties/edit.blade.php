@extends('layouts.admin')

@section('title', 'Sửa Bài Đăng BĐS — Admin LANDTEK')

@php
    $existingImages = collect();
    $existingVideos = collect();
    foreach ($property->media as $m) {
        $ext = strtolower(pathinfo($m->file_url ?? '', PATHINFO_EXTENSION));
        $isVideo = ($m->media_type === 'video') || in_array($ext, ['mp4', 'mov', 'avi', 'webm'], true);
        $row = ['id' => $m->id, 'url' => $m->file_url, 'type' => $isVideo ? 'video' : 'image', 'is_existing' => true];
        if ($isVideo) {
            $existingVideos->push($row);
        } else {
            $existingImages->push($row);
        }
    }
@endphp

@section('content')
<div class="min-h-[100vh] bg-slate-50/50 py-10" x-data="adminPropertyEdit()">
    <div class="container mx-auto px-4 md:px-6 lg:px-8 max-w-7xl">

        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Sửa Bài Đăng BĐS</h1>
                <p class="text-sm text-gray-500 mt-1.5 font-medium">Chỉnh sửa thông tin bất động sản: #{{ $property->id }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.properties.index') }}" class="text-sm font-semibold text-primary hover:opacity-80 transition-opacity bg-primary/10 px-4 py-2 rounded-xl flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
                @if(!empty($property->slug))
                    <a href="{{ route('rent.detail', $property->slug) }}" target="_blank" class="text-sm font-semibold text-slate-600 hover:opacity-80 transition-opacity bg-slate-100 px-4 py-2 rounded-xl flex items-center gap-2">
                        <i class="fas fa-external-link-alt"></i> Xem tin
                    </a>
                @endif
            </div>
        </div>

        @php
            $prevProperty = $prevProperty ?? null;
            $nextProperty = $nextProperty ?? null;
        @endphp
        <div class="mb-6">
            <x-property-detail-nav
                variant="admin"
                :prev-url="$prevProperty ? route('admin.properties.edit', $prevProperty->id) : null"
                :next-url="$nextProperty ? route('admin.properties.edit', $nextProperty->id) : null"
                :prev-title="$prevProperty ? ('#'.$prevProperty->id.' · '.$prevProperty->title) : null"
                :next-title="$nextProperty ? ('#'.$nextProperty->id.' · '.$nextProperty->title) : null"
            />
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

        <form action="{{ route('admin.properties.update', $property->id) }}" method="POST" enctype="multipart/form-data" @submit.prevent="submitForm" x-ref="form">
            @csrf
            @method('PUT')

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-8">
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
                            <option value="commercial" {{ old('property_type', $property->property_type) == 'commercial' ? 'selected' : '' }}>Mặt bằng KD</option>
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
                        <label class="block text-sm font-bold text-gray-700 mb-2">Khu vực</label>
                        @include('components.area-select', [
                            'name' => 'district',
                            'required' => true,
                            'selectedValue' => old('district', $property->district),
                            'placeholder' => 'Chọn khu vực...',
                            'class' => 'w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors cursor-pointer',
                        ])
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

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Ảnh bìa dự án</label>
                        <div class="flex items-start gap-6">
                            <template x-if="coverPreview">
                                <img :src="coverPreview" alt="Cover" class="w-40 h-24 object-cover rounded-xl border border-gray-200">
                            </template>
                            <div class="flex-1">
                                <input type="file" x-ref="coverInput" name="cover_image" accept="image/*" @change="onCover($event)" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-gray-50/50 hover:bg-gray-50 transition-colors">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Images --}}
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-8">
                <h2 class="text-lg font-extrabold text-gray-900 mb-1">Hình ảnh căn nhà</h2>
                <p class="text-xs text-gray-500 mb-4">Album ảnh xem tin — tách riêng khỏi video</p>
                <div @click="$refs.imgInput.click()" class="border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 flex flex-col items-center justify-center py-8 mb-4">
                    <i class="fas fa-images text-xl text-gray-500 mb-2"></i>
                    <p class="text-sm text-gray-600">Thêm ảnh (JPG/PNG/WEBP)</p>
                    <input type="file" x-ref="imgInput" multiple accept="image/jpeg,image/png,image/jpg,image/webp" @change="addImages($event)" class="hidden">
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" x-show="images.length">
                    <template x-for="(item, index) in images" :key="'i'+index">
                        <div class="relative aspect-video rounded-xl overflow-hidden border border-gray-200 bg-gray-100">
                            <img :src="item.url" class="w-full h-full object-cover" alt="">
                            <button type="button" @click="images.splice(index,1)" class="absolute top-2 right-2 w-7 h-7 rounded-full bg-white border shadow text-gray-500 hover:text-red-500">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Videos --}}
            <div class="bg-white p-6 rounded-2xl border border-amber-100 shadow-sm mb-8">
                <div class="mb-3 flex items-start gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-brand text-navy">
                        <i class="fas fa-video"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-navy">Video tham quan</h2>
                        <p class="text-xs text-gray-500">Upload riêng khỏi album ảnh — giống form đăng tin</p>
                    </div>
                </div>
                <div @click="$refs.vidInput.click()" class="border-2 border-dashed border-amber-200 rounded-xl cursor-pointer bg-amber-50/40 hover:bg-amber-50 flex flex-col items-center justify-center py-8 mb-4">
                    <i class="fas fa-cloud-arrow-up text-lg text-amber-600 mb-2"></i>
                    <p class="text-sm text-gray-600">Thêm video MP4 / MOV / WEBM (tối đa 50MB)</p>
                    <input type="file" x-ref="vidInput" multiple accept="video/mp4,video/quicktime,video/webm,video/x-msvideo" @change="addVideos($event)" class="hidden">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="videos.length">
                    <template x-for="(item, index) in videos" :key="'v'+index">
                        <div class="relative aspect-video rounded-xl overflow-hidden border border-amber-100 bg-black">
                            <video :src="item.url" class="w-full h-full object-contain" controls muted playsinline></video>
                            <span class="absolute left-2 top-2 rounded bg-amber-brand px-2 py-0.5 text-[10px] font-bold text-navy">VIDEO</span>
                            <button type="button" @click="videos.splice(index,1)" class="absolute top-2 right-2 w-7 h-7 rounded-full bg-white border shadow text-gray-500 hover:text-red-500">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Map --}}
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-8">
                @include('components.map-pin-picker', ['property' => $property])
            </div>

            {{-- Revision History Log --}}
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-8">
                <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-navy flex items-center justify-center font-bold text-sm">
                            <i class="fas fa-history"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-gray-900">Lịch sử chỉnh sửa tin đăng</h2>
                            <p class="text-xs text-gray-500">Nhật ký chi tiết các lần thay đổi thông tin bài đăng #{{ $property->id }}</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full">
                        {{ $property->revisions->count() }} lượt chỉnh sửa
                    </span>
                </div>

                @if($property->revisions->isEmpty())
                    <p class="text-xs text-gray-400 italic text-center py-4">Chưa có nhật ký chỉnh sửa nào được ghi nhận cho bài đăng này.</p>
                @else
                    <div class="space-y-4 max-h-[380px] overflow-y-auto pr-1">
                        @foreach($property->revisions as $rev)
                            @php
                                $actionLabel = match($rev->action) {
                                    'created' => 'Tạo bài đăng',
                                    'status_changed' => 'Đổi trạng thái',
                                    'soft_deleted' => 'Đã gỡ/ẩn bài',
                                    default => 'Cập nhật thông tin',
                                };
                                $actionBadgeClass = match($rev->action) {
                                    'created' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'status_changed' => 'bg-amber-50 text-amber-800 border-amber-200',
                                    'soft_deleted' => 'bg-red-50 text-red-700 border-red-200',
                                    default => 'bg-blue-50 text-blue-700 border-blue-200',
                                };
                            @endphp
                            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100 text-xs">
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md border text-[11px] font-bold {{ $actionBadgeClass }}">
                                            {{ $actionLabel }}
                                        </span>
                                        <span class="font-bold text-slate-800">
                                            <i class="fas fa-user-circle text-slate-400 mr-1"></i>
                                            {{ $rev->user_name }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] font-medium text-slate-400">
                                        {{ $rev->created_at ? $rev->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                                    </span>
                                </div>

                                @if(!empty($rev->changes))
                                    <div class="mt-2.5 pt-2 border-t border-slate-200/60 space-y-1.5 font-mono text-[11.5px]">
                                        @foreach($rev->changes as $field => $diff)
                                            <div class="flex flex-wrap items-baseline gap-1.5 text-slate-700">
                                                <span class="font-sans font-semibold text-slate-900 bg-slate-200/60 px-1.5 py-0.5 rounded text-[11px]">{{ $field }}:</span>
                                                <span class="line-through text-red-500/80">{{ is_array($diff['old'] ?? null) ? json_encode($diff['old']) : ($diff['old'] ?? 'rỗng') }}</span>
                                                <i class="fas fa-arrow-right text-[9px] text-slate-400"></i>
                                                <span class="font-semibold text-emerald-600">{{ is_array($diff['new'] ?? null) ? json_encode($diff['new']) : ($diff['new'] ?? 'rỗng') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($rev->action === 'created' && !empty($rev->new_values))
                                    <p class="text-[11.5px] text-slate-500 mt-1 italic">Tạo mới bài đăng với đầy đủ thông tin ban đầu.</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mb-6">
                <x-property-detail-nav
                    variant="admin"
                    :prev-url="$prevProperty ? route('admin.properties.edit', $prevProperty->id) : null"
                    :next-url="$nextProperty ? route('admin.properties.edit', $nextProperty->id) : null"
                    :prev-title="$prevProperty ? ('#'.$prevProperty->id.' · '.$prevProperty->title) : null"
                    :next-title="$nextProperty ? ('#'.$nextProperty->id.' · '.$nextProperty->title) : null"
                />
            </div>

            <div class="flex justify-end pb-10">
                <button type="submit" :disabled="submitting"
                        class="bg-primary hover:opacity-90 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-opacity shadow-sm flex items-center gap-2 disabled:opacity-60">
                    <i class="fas fa-save"></i>
                    <span x-text="submitting ? 'Đang lưu…' : 'Lưu Thay Đổi'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('adminPropertyEdit', () => ({
        images: {!! $existingImages->values()->toJson() !!},
        videos: {!! $existingVideos->values()->toJson() !!},
        coverPreview: @js($property->cover_image_url),
        coverFile: null,
        submitting: false,
        onCover(e) {
            const f = e.target.files?.[0];
            if (!f || !f.type.match('image.*')) return;
            this.coverFile = f;
            this.coverPreview = URL.createObjectURL(f);
        },
        addImages(e) {
            [...(e.target.files || [])].forEach(f => {
                if (!f.type.match('image.*')) return;
                this.images.push({ url: URL.createObjectURL(f), file: f, is_existing: false });
            });
            e.target.value = '';
        },
        addVideos(e) {
            [...(e.target.files || [])].forEach(f => {
                if (!f.type.match('video.*')) return;
                if (f.size > 50 * 1024 * 1024) {
                    SwalWarning.fire({ title: 'Video quá lớn', text: f.name + ' vượt quá 50MB.' });
                    return;
                }
                this.videos.push({ url: URL.createObjectURL(f), file: f, is_existing: false });
            });
            e.target.value = '';
        },
        submitForm() {
            this.submitting = true;
            const form = this.$refs.form;
            const fd = new FormData(form);
            const latInput = form.querySelector('input[name="lat"]');
            const lngInput = form.querySelector('input[name="lng"]');
            if (latInput && lngInput && latInput.value !== '' && lngInput.value !== '' && latInput.value !== 'null') {
                fd.set('lat', latInput.value);
                fd.set('lng', lngInput.value);
            } else {
                fd.delete('lat');
                fd.delete('lng');
            }
            fd.delete('images[]');
            fd.delete('videos[]');
            fd.delete('cover_image');
            fd.delete('existing_media[]');
            fd.delete('existing_videos[]');
            if (this.coverFile) fd.append('cover_image', this.coverFile);
            this.images.forEach(img => {
                if (img.is_existing) fd.append('existing_media[]', img.id);
                else fd.append('images[]', img.file);
            });
            this.videos.forEach(vid => {
                if (vid.is_existing) fd.append('existing_videos[]', vid.id);
                else fd.append('videos[]', vid.file);
            });
            // Ensure empty arrays are signaled for media sync
            if (!this.images.some(i => i.is_existing)) fd.append('existing_media[]', '');
            if (!this.videos.some(v => v.is_existing)) fd.append('existing_videos[]', '');

            fetch(form.action, {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(async (res) => {
                if (res.redirected) {
                    window.location.href = res.url;
                    return;
                }
                if (res.ok) {
                    const data = await res.json().catch(() => ({}));
                    SwalSuccess.fire({ title: 'Thành công!', text: data.message || 'Đã cập nhật bài đăng.' })
                        .then(() => { window.location.href = @json(route('admin.properties.index')); });
                } else {
                    const data = await res.json().catch(() => ({}));
                    const msg = data.errors ? Object.values(data.errors).flat().join(' | ') : (data.message || 'Vui lòng thử lại.');
                    SwalError.fire({ title: 'Không lưu được', text: msg });
                }
            })
            .catch(() => SwalError.fire({ title: 'Lỗi kết nối', text: 'Vui lòng thử lại.' }))
            .finally(() => { this.submitting = false; });
        }
    }));
});
</script>
@endsection
