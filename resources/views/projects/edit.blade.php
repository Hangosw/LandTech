@extends('layouts.admin')

@php
    $isNew = !($project->exists ?? false);
@endphp

@section('title', ($isNew ? 'Thêm dự án' : 'Sửa dự án') . ' — Admin LANDTEK')

@section('content')
<div class="min-h-[100vh] bg-slate-50/50 py-10">
    <div class="container mx-auto px-4 md:px-6 lg:px-8 max-w-3xl">
        <div class="mb-8 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $isNew ? 'Thêm dự án' : 'Sửa dự án' }}</h1>
                <p class="text-sm text-slate-500 mt-1.5">
                    @if($isNew)
                        Tạo dự án mới cho trang /du-an
                    @else
                        #{{ $project->id }} · {{ $project->label }}
                    @endif
                </p>
            </div>
            <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary/10 px-4 py-2 text-sm font-semibold text-primary hover:bg-primary/15">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-100 bg-red-50 p-4 text-sm font-semibold text-red-800">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ $isNew ? route('admin.projects.store') : route('admin.projects.update', $project->id) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6"
            x-data="{
                preview: @js(old('image', $project->image)),
                onFile(e) {
                    const f = e.target.files?.[0];
                    if (!f) return;
                    this.preview = URL.createObjectURL(f);
                }
            }"
        >
            @csrf
            @unless($isNew)
                @method('PUT')
            @endunless

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm space-y-5">
                <div>
                    <label class="mb-1.5 block text-xs font-bold text-slate-700">Tên dự án *</label>
                    <input type="text" name="label" value="{{ old('label', $project->label) }}" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-bold text-slate-700">Slug (URL lọc tin)</label>
                    <input type="text" name="slug" value="{{ old('slug', $project->slug) }}"
                           placeholder="vd: muong-thanh"
                           pattern="[a-z0-9\-]*"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    <p class="mt-1 text-[11px] text-slate-400">Để trống sẽ tự tạo từ tên. Dùng làm `?project=` trên trang thuê.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700">Khu vực</label>
                        <input type="text" name="district" value="{{ old('district', $project->district) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700">Giá từ</label>
                        <input type="text" name="price_from" value="{{ old('price_from', $project->price_from) }}"
                               placeholder="vd: 8 triệu/tháng"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700">Số tin hiển thị</label>
                        <input type="number" min="0" name="listing_count" value="{{ old('listing_count', $project->listing_count ?? 0) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        @unless($isNew)
                            <p class="mt-1 text-[11px] text-slate-400">Tin thật trong DB: {{ $project->liveListingCount() }}</p>
                        @endunless
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold text-slate-700">Thứ tự sắp xếp</label>
                        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $project->sort_order ?? 0) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-bold text-slate-700">Tagline</label>
                    <input type="text" name="tagline" value="{{ old('tagline', $project->tagline) }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-bold text-slate-700">Mô tả</label>
                    <textarea name="description" rows="4"
                              class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary">{{ old('description', $project->description) }}</textarea>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-bold text-slate-700">Điểm nổi bật (mỗi dòng một ý)</label>
                    <textarea name="highlights_text" rows="4"
                              class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary">{{ old('highlights_text', collect($project->highlights ?? [])->implode("\n")) }}</textarea>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-bold text-slate-700">Ảnh bìa dự án</label>
                    <div class="overflow-hidden rounded-xl border border-dashed border-slate-200 bg-slate-50">
                        <template x-if="preview">
                            <img :src="preview" alt="" class="h-48 w-full object-cover" @@error="$el.src='/images/hero-nhatrang.jpg'">
                        </template>
                        <div x-show="!preview" class="flex h-48 items-center justify-center text-sm text-slate-400">Chưa có ảnh</div>
                    </div>
                    <input type="file" name="image_file" accept="image/*" @change="onFile($event)"
                           class="mt-3 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-bold file:text-white">
                    <p class="mt-1 text-[11px] text-slate-400">Upload vào /AnhDuAn — không xóa ảnh cũ trên server khi đổi ảnh.</p>
                </div>

                <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-primary focus:ring-primary"
                           @checked(old('is_active', $project->is_active ?? true))>
                    Hiển thị trên trang /du-an
                </label>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.projects.index') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600">Hủy</a>
                <button type="submit" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white hover:opacity-90 shadow-sm">
                    {{ $isNew ? 'Tạo dự án' : 'Lưu thay đổi' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
