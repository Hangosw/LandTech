@extends('layouts.app')

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
                <input type="text" name="title" required placeholder="VD: Căn hộ 2PN Mường Thanh view biển" value="{{ $property->title ?? '' }}"
                       class="w-full rounded-lg border border-gray-200 bg-gray-50/30 px-4 py-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-colors">
            </div>

            <!-- Hàng 2: Phân loại -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 md:gap-8">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Loại BĐS <span class="text-red-500">*</span></label>
                    <select name="property_type" required class="w-full rounded-lg border border-gray-200 bg-gray-50/30 px-4 py-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-colors appearance-none">
                        <option value="apartment" {{ (isset($property) && $property->property_type == 'apartment') ? 'selected' : '' }}>Căn hộ</option>
                        <option value="house" {{ (isset($property) && $property->property_type == 'house') ? 'selected' : '' }}>Nhà phố</option>
                        <option value="villa" {{ (isset($property) && $property->property_type == 'villa') ? 'selected' : '' }}>Biệt thự</option>
                        <option value="office" {{ (isset($property) && $property->property_type == 'office') ? 'selected' : '' }}>Văn phòng</option>
                        <option value="commercial" {{ (isset($property) && $property->property_type == 'commercial') ? 'selected' : '' }}>Mặt bằng kinh doanh</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Khu vực <span class="text-red-500">*</span></label>
                    <select name="district" required class="w-full rounded-lg border border-gray-200 bg-gray-50/30 px-4 py-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-colors appearance-none">
                        <option value="Lộc Thọ" {{ (isset($property) && $property->district == 'Lộc Thọ') ? 'selected' : '' }}>Lộc Thọ</option>
                        <option value="Phước Hải" {{ (isset($property) && $property->district == 'Phước Hải') ? 'selected' : '' }}>Phước Hải</option>
                        <option value="Vĩnh Hòa" {{ (isset($property) && $property->district == 'Vĩnh Hòa') ? 'selected' : '' }}>Vĩnh Hòa</option>
                        <option value="Vĩnh Nguyên" {{ (isset($property) && $property->district == 'Vĩnh Nguyên') ? 'selected' : '' }}>Vĩnh Nguyên</option>
                        <option value="Tân Lập" {{ (isset($property) && $property->district == 'Tân Lập') ? 'selected' : '' }}>Tân Lập</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Dự án (nếu có)</label>
                    <select name="project" class="w-full rounded-lg border border-gray-200 bg-gray-50/30 px-4 py-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-colors appearance-none">
                        <option value="">— Không thuộc dự án —</option>
                        <option value="muong-thanh" {{ (isset($property) && $property->project == 'muong-thanh') ? 'selected' : '' }}>Mường Thanh</option>
                        <option value="vinpearl" {{ (isset($property) && $property->project == 'vinpearl') ? 'selected' : '' }}>Vinpearl</option>
                        <option value="sun-group" {{ (isset($property) && $property->project == 'sun-group') ? 'selected' : '' }}>Sun Group</option>
                        <option value="gold-coast" {{ (isset($property) && $property->project == 'gold-coast') ? 'selected' : '' }}>Gold Coast</option>
                    </select>
                </div>
            </div>

            <!-- Hàng 3: Thông số chi tiết (4 cột) -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Phòng ngủ <span class="text-red-500">*</span></label>
                    <input type="number" name="bedrooms" value="{{ $property->bedrooms ?? 1 }}" min="0" required
                           class="w-full rounded-lg border border-gray-200 bg-gray-50/30 px-4 py-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-colors text-center">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Phòng tắm <span class="text-red-500">*</span></label>
                    <input type="number" name="bathrooms" value="{{ $property->bathrooms ?? 1 }}" min="0" required
                           class="w-full rounded-lg border border-gray-200 bg-gray-50/30 px-4 py-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-colors text-center">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Diện tích (m²) <span class="text-red-500">*</span></label>
                    <input type="number" name="area" placeholder="60" required value="{{ $property->area ?? '' }}"
                           class="w-full rounded-lg border border-gray-200 bg-gray-50/30 px-4 py-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-colors text-center">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Giá (VNĐ/tháng) <span class="text-red-500">*</span></label>
                    <input type="number" name="monthly_price" placeholder="8000000" required value="{{ $property->monthly_price ?? '' }}"
                           class="w-full rounded-lg border border-gray-200 bg-gray-50/30 px-4 py-3 text-sm text-gray-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition-colors text-center">
                </div>
            </div>

            <!-- Mô tả chi tiết -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Mô tả chi tiết <span class="text-red-500">*</span></label>
                <div class="border border-gray-200 rounded-lg overflow-hidden focus-within:border-teal-500 focus-within:ring-1 focus-within:ring-teal-500 transition-colors bg-white">
                    <textarea name="description" rows="4" placeholder="Mô tả không gian, nội thất, khoảng cách tới biển..." required
                              class="w-full p-4 text-sm text-gray-700 outline-none border-none resize-none">{{ $property->description ?? '' }}</textarea>
                </div>
            </div>

            <!-- Tiện ích -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-3">Tiện ích</label>
                <div class="flex flex-wrap gap-2.5">
                    @if(isset($utilities) && $utilities->count() > 0)
                        @php
                            $propertyUtils = isset($property) ? $property->utilities->pluck('id')->toArray() : [];
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

            <!-- Hình ảnh / Video -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Hình ảnh / Video <span class="text-red-500">*</span></label>
                
                <!-- Dropzone -->
                <div @click="$refs.fileInput.click()" 
                     @dragover.prevent="dragover = true" 
                     @dragleave.prevent="dragover = false" 
                     @drop.prevent="handleDrop($event)"
                     :class="dragover ? 'border-teal-500 bg-teal-50' : 'border-gray-300 bg-gray-50/50 hover:bg-gray-50'"
                     class="border-2 border-dashed rounded-lg cursor-pointer transition-colors flex flex-col items-center justify-center py-8 mb-4 relative">
                    <i class="fas fa-arrow-up-from-bracket text-xl text-gray-600 mb-3" :class="dragover ? 'text-teal-500' : ''"></i>
                    <p class="text-[13px] text-gray-500">Kéo thả ảnh/video vào đây — tự động đóng watermark LANDTEK</p>
                    <p class="text-[11px] text-gray-400 mt-1">Ảnh (JPG, PNG) tối đa 5MB. Video (MP4) tối đa 50MB.</p>
                    <input type="file" x-ref="fileInput" name="images[]" multiple accept="image/*,video/mp4,video/quicktime,video/x-msvideo" @change="handleFiles($event)" class="hidden">
                </div>

                <!-- Preview Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" x-show="previewImages.length > 0">
                    <template x-for="(item, index) in previewImages" :key="index">
                        <div class="relative group aspect-video rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center">
                            <!-- Image Preview -->
                            <template x-if="item.type === 'image'">
                                <img :src="item.url" class="w-full h-full object-cover rounded-lg">
                            </template>
                            
                            <!-- Video Preview -->
                            <template x-if="item.type === 'video'">
                                <video :src="item.url" class="w-full h-full object-cover rounded-lg" controls muted></video>
                            </template>

                            <!-- Xóa ảnh -->
                            <button type="button" @click.prevent="removeImage(index)" 
                                    class="absolute -top-2.5 -right-2.5 w-7 h-7 bg-white text-gray-400 hover:text-red-500 rounded-full flex items-center justify-center shadow-md border border-gray-200 z-10 transition-all hover:scale-110">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

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
            previewImages: {!! isset($property) && $property->media ? json_encode($property->media->map(function($m) { return ['id' => $m->id, 'url' => $m->file_url, 'type' => $m->media_type, 'is_existing' => true]; })) : '[]' !!}, // { url: blobUrl, file: FileObj, is_existing: true/false }
            coverImagePreview: '{!! isset($property) && $property->cover_image_url ? $property->cover_image_url : '' !!}',
            coverImageFile: null,
            removeCoverFlag: false,
            isSubmitting: false,

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

            handleCover(event) {
                if (event.target.files.length > 0) {
                    let file = event.target.files[0];
                    if (file.type.match('image.*')) {
                        if (this.coverImagePreview) {
                            URL.revokeObjectURL(this.coverImagePreview);
                        }
                        this.coverImageFile = file;
                        this.coverImagePreview = URL.createObjectURL(file);
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
            },

            addFiles(files) {
                for (let i = 0; i < files.length; i++) {
                    let file = files[i];
                    let isImage = file.type.match('image.*');
                    let isVideo = file.type.match('video.*');

                    if (isImage || isVideo) {
                        // Create object URL for preview
                        let url = URL.createObjectURL(file);
                        this.previewImages.push({
                            url: url,
                            file: file,
                            type: isVideo ? 'video' : 'image',
                            is_existing: false
                        });
                    }
                }
                // We clear the input so the user can select the same file again if they remove and re-add
                this.$refs.fileInput.value = '';
            },

            removeImage(index) {
                if (!this.previewImages[index].is_existing) {
                    // revoke the object url to free memory
                    URL.revokeObjectURL(this.previewImages[index].url);
                }
                this.previewImages.splice(index, 1);
            },

            submitForm() {
                if (!this.coverImagePreview) {
                    SwalWarning.fire({ title: 'Thiếu ảnh bìa!', text: 'Vui lòng chọn ảnh bìa cho dự án.' });
                    return;
                }
                
                if (this.previewImages.length === 0) {
                    SwalWarning.fire({ title: 'Thiếu hình ảnh!', text: 'Vui lòng chọn ít nhất 1 hình ảnh/video cho bất động sản.' });
                    return;
                }
                
                this.isSubmitting = true;

                // Build FormData manually because standard file input gets cleared/can't be modified programmatically easily
                let form = this.$refs.form;
                let formData = new FormData(form);
                
                // Remove the default empty images[] if any
                formData.delete('images[]');
                
                // Append the files we tracked in Alpine
                formData.delete('cover_image');
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

                // Submit via Fetch
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
                            text: 'Đã gửi bài đăng, vui lòng đợi Admin duyệt.',
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Đồng ý',
                            cancelButtonText: 'Xem danh sách tin đã đăng',
                            customClass: {
                                confirmButton: 'bg-teal-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-teal-700 transition-colors mx-2',
                                cancelButton: 'bg-gray-100 text-gray-700 px-6 py-2.5 rounded-xl font-bold hover:bg-gray-200 transition-colors mx-2'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Quay lại form nhập (reset trang)
                                window.location.reload();
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                // Xem danh sách tin (hiện tại chưa có view nên tạm để #)
                                window.location.href = "/quan-ly-tin-dang"; 
                            }
                        });
                    } else {
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
