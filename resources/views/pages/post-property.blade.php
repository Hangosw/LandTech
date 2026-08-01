@extends('layouts.app')

@php
    $targetProp = $property ?? $draftProperty ?? null;
    $isEditMode = isset($property);
    $existingImages = collect();
    $existingVideos = collect();
    if ($targetProp && $targetProp->media) {
        foreach ($targetProp->media as $m) {
            $ext = strtolower(pathinfo($m->file_url ?? '', PATHINFO_EXTENSION));
            $isVideo = ($m->media_type === 'video') || in_array($ext, ['mp4', 'mov', 'avi', 'webm'], true);
            $row = ['id' => $m->id, 'url' => $m->file_url, 'type' => $isVideo ? 'video' : 'image', 'is_existing' => true];
            if ($isVideo) {
                $existingVideos->push($row);
            } else {
                $existingImages->push($row);
            }
        }
    }
@endphp

@section('title', isset($property) ? 'Chỉnh sửa tin đăng — LANDTEK' : 'Đăng tin cho thuê — LANDTEK')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-3xl" x-data="propertyForm()">
    
    <!-- Header Section -->
    <div class="text-center mb-8 flex flex-col items-center">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ isset($property) ? 'Chỉnh sửa tin đăng' : 'Đăng tin cho thuê' }}</h1>
        <p class="text-gray-500 mb-4 text-sm">Điền thông tin chuẩn hóa để tin được duyệt và xác thực nhanh.</p>
        
        <div class="flex items-center justify-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-medium">
                <i class="far fa-check-circle"></i> Verified Property
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-medium">
                <i class="fas fa-wand-magic-sparkles"></i> Tự động watermark & asset marketing
            </span>
        </div>
    </div>

    <!-- Banner khi đang tiếp tục bản nháp -->
    @if(isset($draftProperty) && !isset($property))
    <div id="draft-banner" class="mb-6 bg-teal-50 border border-teal-200 text-teal-900 px-4 py-3 rounded-xl flex items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-sm">
            <i class="fas fa-file-signature text-teal-600 text-base"></i>
            <span><strong>📌 Đang tiếp tục bản nháp:</strong> Hệ thống đã tự động khôi phục thông tin dở dang từ lần nhập trước của bạn.</span>
        </div>
        <button type="button" @click="deleteDraft({{ $draftProperty->id }})" class="text-xs font-semibold text-red-600 hover:text-red-800 underline shrink-0 cursor-pointer">
            <i class="fas fa-trash-alt mr-1"></i> Xóa nháp & làm mới
        </button>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 sm:p-10">
        <!-- Error & Success Messages -->
        @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ isset($property) ? route('property.update', $property->id) : route('property.post.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-8" @submit.prevent="submitForm" x-ref="form">
            @csrf
            
            <!-- Tiêu đề -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Tiêu đề tin đăng <span class="text-red-500">*</span></label>
                <input type="text" name="title" required placeholder="VD: Căn hộ 2PN Mường Thanh view biển" value="{{ $targetProp->title ?? '' }}"
                       class="w-full rounded-lg border border-gray-200 bg-gray-50/30 px-4 py-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-colors">
            </div>

            <!-- Hàng 2: Phân loại -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 md:gap-8">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Loại BĐS <span class="text-red-500">*</span></label>
                    <select name="property_type" required class="w-full rounded-lg border border-gray-200 bg-gray-50/30 px-4 py-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-colors appearance-none">
                        <option value="apartment" {{ (isset($targetProp) && $targetProp->property_type == 'apartment') ? 'selected' : '' }}>Căn hộ</option>
                        <option value="house" {{ (isset($targetProp) && $targetProp->property_type == 'house') ? 'selected' : '' }}>Nhà phố</option>
                        <option value="villa" {{ (isset($targetProp) && $targetProp->property_type == 'villa') ? 'selected' : '' }}>Biệt thự</option>
                        <option value="office" {{ (isset($targetProp) && $targetProp->property_type == 'office') ? 'selected' : '' }}>Văn phòng</option>
                        <option value="commercial" {{ (isset($targetProp) && $targetProp->property_type == 'commercial') ? 'selected' : '' }}>Mặt bằng kinh doanh</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Khu vực <span class="text-red-500">*</span></label>
                    @include('components.area-select', [
                        'name' => 'district',
                        'required' => true,
                        'selectedValue' => $targetProp->district ?? '',
                        'placeholder' => 'Chọn khu vực...',
                        'class' => 'w-full rounded-lg border border-gray-200 bg-gray-50/30 px-4 py-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-colors appearance-none',
                    ])
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Dự án (nếu có)</label>
                    <select name="project" class="w-full rounded-lg border border-gray-200 bg-gray-50/30 px-4 py-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-colors appearance-none">
                        <option value="">— Không thuộc dự án —</option>
                        @php
                            $projectOptions = $projectOptions
                                ?? (\Illuminate\Support\Facades\Schema::hasTable('projects')
                                    ? \App\Models\Project::query()->active()->ordered()->get(['slug', 'label'])
                                    : collect());
                        @endphp
                        @forelse($projectOptions as $opt)
                            <option value="{{ $opt->slug }}" {{ (isset($targetProp) && $targetProp->project == $opt->slug) ? 'selected' : '' }}>
                                {{ $opt->label }}
                            </option>
                        @empty
                            <option value="muong-thanh" {{ (isset($targetProp) && $targetProp->project == 'muong-thanh') ? 'selected' : '' }}>Mường Thanh</option>
                            <option value="vinpearl" {{ (isset($targetProp) && $targetProp->project == 'vinpearl') ? 'selected' : '' }}>Vinpearl</option>
                            <option value="sun-group" {{ (isset($targetProp) && $targetProp->project == 'sun-group') ? 'selected' : '' }}>Sun Group</option>
                            <option value="scenia-bay" {{ (isset($targetProp) && $targetProp->project == 'scenia-bay') ? 'selected' : '' }}>Scenia Bay</option>
                            <option value="gold-coast" {{ (isset($targetProp) && $targetProp->project == 'gold-coast') ? 'selected' : '' }}>Gold Coast</option>
                        @endforelse
                    </select>
                </div>
            </div>

            <!-- Hàng 3: Thông số chi tiết (4 cột) -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Phòng ngủ <span class="text-red-500">*</span></label>
                    <input type="number" name="bedrooms" value="{{ $targetProp->bedrooms ?? 1 }}" min="0" required
                           class="w-full rounded-lg border border-gray-200 bg-gray-50/30 px-4 py-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-colors text-center">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Phòng tắm <span class="text-red-500">*</span></label>
                    <input type="number" name="bathrooms" value="{{ $targetProp->bathrooms ?? 1 }}" min="0" required
                           class="w-full rounded-lg border border-gray-200 bg-gray-50/30 px-4 py-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-colors text-center">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Diện tích (m²) <span class="text-red-500">*</span></label>
                    <input type="number" name="area" placeholder="60" required value="{{ $targetProp->area ?? '' }}"
                           class="w-full rounded-lg border border-gray-200 bg-gray-50/30 px-4 py-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-colors text-center">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Giá (VNĐ/tháng) <span class="text-red-500">*</span></label>
                    <input type="number" name="monthly_price" placeholder="8000000" required value="{{ $targetProp->monthly_price ?? '' }}"
                           class="w-full rounded-lg border border-gray-200 bg-gray-50/30 px-4 py-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-colors text-center">
                </div>
            </div>

            <!-- Mô tả chi tiết -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Mô tả chi tiết <span class="text-red-500">*</span></label>
                <div class="border border-gray-200 rounded-lg overflow-hidden focus-within:border-teal-500 focus-within:ring-1 focus-within:ring-teal-500 transition-colors bg-white">
                    <textarea name="description" rows="4" placeholder="Mô tả không gian, nội thất, khoảng cách tới biển..." required
                              class="w-full p-4 text-sm text-gray-700 outline-none border-none resize-none">{{ $targetProp->description ?? '' }}</textarea>
                </div>
            </div>

            <!-- Tiện ích -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-3">Tiện ích</label>
                <div class="flex flex-wrap gap-2.5">
                    @if(isset($utilities) && $utilities->count() > 0)
                        @php
                            $propertyUtils = isset($targetProp) ? $targetProp->utilities->pluck('id')->toArray() : [];
                        @endphp
                        @foreach($utilities as $utility)
                            <label class="cursor-pointer inline-block">
                                <input type="checkbox" name="utilities[]" value="{{ $utility->id }}" class="hidden peer" {{ in_array($utility->id, $propertyUtils) ? 'checked' : '' }}>
                                <div class="px-3.5 py-1.5 rounded-full border border-gray-200 text-[13px] text-gray-600 bg-white peer-checked:bg-teal-50 peer-checked:text-teal-700 peer-checked:border-teal-300 hover:bg-gray-50 transition-colors">
                                    @if($utility->icon_name)
                                    <i class="{{ $utility->icon_name }} mr-1"></i>
                                    @endif
                                    {{ $utility->name }}
                                </div>
                            </label>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500">Chưa có tiện ích nào được cấu hình.</p>
                    @endif
                </div>
            </div>

            <!-- Ảnh Bìa Dự Án -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Ảnh bìa dự án (Bắt buộc) <span class="text-red-500">*</span></label>
                <input type="hidden" name="remove_cover" :value="removeCoverFlag ? '1' : '0'">
                <div class="border-2 border-dashed border-gray-300 rounded-lg py-6 px-4 bg-gray-50/50 hover:bg-gray-50 flex flex-col items-center justify-center relative transition-colors cursor-pointer" @click="$refs.coverInput.click()">
                    
                    <div x-show="!coverImagePreview" class="text-center">
                        <i class="fas fa-image text-3xl text-gray-400 mb-2"></i>
                        <p class="text-[13px] text-gray-500">Bấm để tải lên ảnh bìa (chỉ 1 ảnh)</p>
                    </div>

                    <div x-show="coverImagePreview" class="relative group rounded-lg overflow-hidden w-full max-w-xs mx-auto aspect-video">
                        <img :src="coverImagePreview" class="w-full h-full object-cover">
                        <button type="button" @click.stop="removeCoverImage()" class="absolute -top-2 -right-2 w-7 h-7 bg-white text-gray-400 hover:text-red-500 rounded-full flex items-center justify-center shadow-md border border-gray-200 z-10 transition-all hover:scale-110">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>

                    <input type="file" x-ref="coverInput" name="cover_image" accept="image/*" @change="handleCover($event)" class="hidden">
                </div>
            </div>

            <!-- Hình ảnh căn nhà (chỉ ảnh) -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Hình ảnh căn nhà <span class="text-red-500">*</span></label>
                <div @click="$refs.fileInput.click()"
                     @dragover.prevent="dragover = true"
                     @dragleave.prevent="dragover = false"
                     @drop.prevent="handleDrop($event)"
                     :class="dragover ? 'border-navy bg-navy/5' : 'border-gray-300 bg-gray-50/50 hover:bg-gray-50'"
                     class="border-2 border-dashed rounded-lg cursor-pointer transition-colors flex flex-col items-center justify-center py-8 mb-4 relative">
                    <i class="fas fa-images text-xl text-gray-600 mb-3"></i>
                    <p class="text-[13px] text-gray-500">Kéo thả hoặc chọn ảnh — album xem tin</p>
                    <p class="text-[11px] text-gray-400 mt-1">JPG, PNG, WEBP · tối đa 10MB/ảnh</p>
                    <input type="file" x-ref="fileInput" name="images[]" multiple accept="image/jpeg,image/png,image/jpg,image/webp" @change="handleFiles($event)" class="hidden">
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" x-show="previewImages.length > 0">
                    <template x-for="(item, index) in previewImages" :key="'img-'+index">
                        <div class="relative group aspect-video rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center">
                            <img :src="item.url" class="w-full h-full object-cover rounded-lg" alt="">
                            <button type="button" @click.prevent="removeImage(index)"
                                    class="absolute -top-2.5 -right-2.5 w-7 h-7 bg-white text-gray-400 hover:text-red-500 rounded-full flex items-center justify-center shadow-md border border-gray-200 z-10 transition-all hover:scale-110">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Video riêng -->
            <div class="rounded-xl border border-amber-100 bg-amber-50/40 p-4 sm:p-5">
                <div class="mb-3 flex items-start gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-brand text-navy">
                        <i class="fas fa-video"></i>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-navy">Video tham quan <span class="font-normal text-gray-400">(tuỳ chọn)</span></label>
                        <p class="mt-0.5 text-[11px] leading-relaxed text-gray-500">
                            Upload riêng khỏi album ảnh — trên trang xem tin, video hiện ở khối riêng để khách không nhầm với carousel hình.
                        </p>
                    </div>
                </div>
                <div @click="$refs.videoInput.click()"
                     class="border-2 border-dashed border-amber-200 rounded-lg cursor-pointer bg-white/80 hover:bg-white flex flex-col items-center justify-center py-6 mb-3 transition-colors">
                    <i class="fas fa-cloud-arrow-up text-lg text-amber-600 mb-2"></i>
                    <p class="text-[13px] text-gray-600">Chọn video MP4 / MOV / WEBM</p>
                    <p class="text-[11px] text-gray-400 mt-1">Tối đa 50MB · có thể thêm nhiều video</p>
                    <input type="file" x-ref="videoInput" name="videos[]" multiple accept="video/mp4,video/quicktime,video/webm,video/x-msvideo" @change="handleVideos($event)" class="hidden">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-show="previewVideos.length > 0">
                    <template x-for="(item, index) in previewVideos" :key="'vid-'+index">
                        <div class="relative rounded-lg border border-amber-100 bg-navy overflow-hidden aspect-video">
                            <video :src="item.url" class="w-full h-full object-contain bg-black" controls muted playsinline></video>
                            <span class="absolute left-2 top-2 rounded bg-amber-brand px-2 py-0.5 text-[10px] font-bold text-navy">VIDEO</span>
                            <button type="button" @click.prevent="removeVideo(index)"
                                    class="absolute top-2 right-2 w-7 h-7 bg-white text-gray-500 hover:text-red-500 rounded-full flex items-center justify-center shadow border border-gray-200">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Ghim bản đồ -->
            @include('components.map-pin-picker')

            <!-- Submit Button -->
            <div class="pt-6">
                <button type="submit" 
                        class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg shadow-teal-600/30 text-base flex items-center justify-center"
                        :disabled="isSubmitting"
                        :class="{'opacity-75 cursor-not-allowed': isSubmitting}">
                    <span x-show="!isSubmitting">{{ isset($property) ? 'Lưu thay đổi' : 'Gửi tin & yêu cầu duyệt' }} <i class="fas fa-paper-plane ml-2"></i></span>
                    <span x-show="isSubmitting"><i class="fas fa-spinner fa-spin mr-2"></i> {{ isset($property) ? 'Đang cập nhật...' : 'Đang tải lên...' }}</span>
                </button>
            </div>
            
        </form>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('propertyForm', () => ({
            dragover: false,
            previewImages: {!! $existingImages->isNotEmpty() ? $existingImages->values()->toJson() : '[]' !!},
            previewVideos: {!! $existingVideos->isNotEmpty() ? $existingVideos->values()->toJson() : '[]' !!},
            coverImagePreview: '{!! isset($targetProp) && $targetProp->cover_image_url ? $targetProp->cover_image_url : '' !!}',
            coverImageFile: null,
            removeCoverFlag: false,
            isSubmitting: false,
            hasSubmitted: false,
            isEditMode: {{ $isEditMode ? 'true' : 'false' }},
            draftId: {{ (!$isEditMode && isset($targetProp) && $targetProp->status == 'nhap') ? $targetProp->id : 'null' }},
            autoSaveTimer: null,

            init() {
                // Không auto-save nháp khi đang sửa tin đã đăng
                if (this.isEditMode) return;

                this.$nextTick(() => {
                    let form = this.$refs.form;
                    if (form) {
                        form.addEventListener('input', () => this.scheduleAutoSave());
                        form.addEventListener('change', () => this.scheduleAutoSave());
                    }
                });

                window.addEventListener('visibilitychange', () => {
                    if (document.visibilityState === 'hidden') {
                        this.autoSaveDraft();
                    }
                });

                window.addEventListener('beforeunload', () => {
                    this.autoSaveDraft();
                });
            },

            scheduleAutoSave() {
                if (this.hasSubmitted) return;
                if (this.autoSaveTimer) clearTimeout(this.autoSaveTimer);
                this.autoSaveTimer = setTimeout(() => {
                    this.autoSaveDraft();
                }, 2000);
            },

            autoSaveDraft() {
                if (this.isEditMode || this.hasSubmitted || !this.$refs.form) return;
                let form = this.$refs.form;
                let formData = new FormData(form);
                if (this.draftId) {
                    formData.append('draft_id', this.draftId);
                }
                formData.delete('images[]');
                formData.delete('videos[]');
                formData.delete('cover_image');

                fetch('{{ route("property.draft.save") }}', {
                    method: 'POST',
                    body: formData,
                    keepalive: true,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data && data.draft_id) {
                        this.draftId = data.draft_id;
                    }
                })
                .catch(err => {});
            },

            deleteDraft(id) {
                Swal.fire({
                    title: 'Xác nhận xóa nháp?',
                    text: 'Mọi thông tin dở dang sẽ bị xóa bỏ.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Xóa nháp',
                    cancelButtonText: 'Hủy',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#9ca3af'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('/dang-tin/draft/' + id, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                });
            },

            handleDrop(event) {
                this.dragover = false;
                if (event.dataTransfer.files.length > 0) {
                    this.addFiles(event.dataTransfer.files);
                }
            },

            handleFiles(event) {
                if (event.target.files.length > 0) {
                    this.addFiles(event.target.files);
                }
            },

            handleVideos(event) {
                if (event.target.files.length > 0) {
                    this.addVideos(event.target.files);
                }
                if (this.$refs.videoInput) this.$refs.videoInput.value = '';
            },

            handleCover(event) {
                if (event.target.files.length > 0) {
                    let file = event.target.files[0];
                    if (file.type.match('image.*')) {
                        if (this.coverImagePreview) {
                            URL.revokeObjectURL(this.coverImagePreview);
                        }
                        this.coverImageFile = file;
                        this.coverImagePreview = URL.createObjectURL(file);
                        this.scheduleAutoSave();
                    }
                }
            },

            removeCoverImage() {
                if (this.coverImagePreview && !this.coverImagePreview.startsWith('/')) {
                    URL.revokeObjectURL(this.coverImagePreview);
                }
                this.coverImageFile = null;
                this.coverImagePreview = null;
                this.removeCoverFlag = true;
                this.$refs.coverInput.value = '';
                this.scheduleAutoSave();
            },

            addFiles(files) {
                for (let i = 0; i < files.length; i++) {
                    let file = files[i];
                    if (!file.type.match('image.*')) continue;
                    this.previewImages.push({
                        url: URL.createObjectURL(file),
                        file: file,
                        type: 'image',
                        is_existing: false
                    });
                }
                this.$refs.fileInput.value = '';
                this.scheduleAutoSave();
            },

            addVideos(files) {
                for (let i = 0; i < files.length; i++) {
                    let file = files[i];
                    if (!file.type.match('video.*')) continue;
                    if (file.size > 50 * 1024 * 1024) {
                        SwalWarning.fire({ title: 'Video quá lớn', text: file.name + ' vượt quá 50MB.' });
                        continue;
                    }
                    this.previewVideos.push({
                        url: URL.createObjectURL(file),
                        file: file,
                        type: 'video',
                        is_existing: false
                    });
                }
                this.scheduleAutoSave();
            },

            removeImage(index) {
                if (!this.previewImages[index].is_existing) {
                    URL.revokeObjectURL(this.previewImages[index].url);
                }
                this.previewImages.splice(index, 1);
                this.scheduleAutoSave();
            },

            removeVideo(index) {
                if (!this.previewVideos[index].is_existing) {
                    URL.revokeObjectURL(this.previewVideos[index].url);
                }
                this.previewVideos.splice(index, 1);
                this.scheduleAutoSave();
            },

            submitForm() {
                if (!this.coverImagePreview) {
                    SwalWarning.fire({ title: 'Thiếu ảnh bìa!', text: 'Vui lòng chọn ảnh bìa cho dự án.' });
                    return;
                }

                if (this.previewImages.length === 0) {
                    SwalWarning.fire({ title: 'Thiếu hình ảnh!', text: 'Vui lòng chọn ít nhất 1 ảnh căn nhà (video upload riêng bên dưới).' });
                    return;
                }

                this.isSubmitting = true;
                this.hasSubmitted = true;

                let form = this.$refs.form;
                let formData = new FormData(form);
                if (this.draftId) {
                    formData.append('draft_id', this.draftId);
                }

                // Nested Alpine map picker: ensure coords survive FormData
                const latInput = form.querySelector('input[name="lat"]');
                const lngInput = form.querySelector('input[name="lng"]');
                if (latInput && lngInput && latInput.value !== '' && lngInput.value !== '' && latInput.value !== 'null') {
                    formData.set('lat', latInput.value);
                    formData.set('lng', lngInput.value);
                } else {
                    formData.delete('lat');
                    formData.delete('lng');
                }

                formData.delete('images[]');
                formData.delete('videos[]');
                formData.delete('cover_image');
                formData.delete('existing_media[]');
                formData.delete('existing_videos[]');
                if (this.coverImageFile) {
                    formData.append('cover_image', this.coverImageFile);
                }

                this.previewImages.forEach((img) => {
                    if (img.is_existing) {
                        formData.append('existing_media[]', img.id);
                    } else {
                        formData.append('images[]', img.file);
                    }
                });

                this.previewVideos.forEach((vid) => {
                    if (vid.is_existing) {
                        formData.append('existing_videos[]', vid.id);
                    } else {
                        formData.append('videos[]', vid.file);
                    }
                });

                // Báo cho backend biết form đã sync media (kể cả khi list rỗng)
                if (this.isEditMode) {
                    formData.append('existing_media[]', '');
                    formData.append('existing_videos[]', '');
                }

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        Swal.fire({
                            title: 'Thành công!',
                            text: @json($isEditMode ? 'Đã cập nhật tin đăng.' : 'Đã gửi bài đăng, vui lòng đợi Admin duyệt.'),
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Đồng ý',
                            cancelButtonText: 'Xem danh sách tin đã đăng',
                            confirmButtonColor: '#0F3460',
                            cancelButtonColor: '#6b7280'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = @json($isEditMode ? route('my-properties') : route('property.post'));
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                window.location.href = "/quan-ly-tin-dang";
                            }
                        });
                    } else {
                        this.hasSubmitted = false;
                        response.json().then(data => {
                            if (data.errors) {
                                SwalError.fire({ title: 'Dữ liệu không hợp lệ!', text: Object.values(data.errors).flat().join(' | ') });
                            } else {
                                SwalError.fire({ title: 'Có lỗi xảy ra!', text: 'Vui lòng thử lại.' });
                            }
                        });
                    }
                })
                .catch(error => {
                    this.hasSubmitted = false;
                    SwalError.fire({ title: 'Lỗi kết nối!', text: 'Vui lòng thử lại sau.' });
                })
                .finally(() => {
                    this.isSubmitting = false;
                });
            }
        }));
    });
</script>
@endsection
