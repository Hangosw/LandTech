@extends('layouts.app')

@section('title', 'Thuê nhà Nha Trang — Căn hộ, nhà phố, văn phòng | LANDTEK')
@section('description', 'Danh sách bất động sản cho thuê tại Nha Trang với bộ lọc thông minh theo giá, khu vực, dự án và tiện ích.')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">
        
        <form id="filter-form" action="{{ route('rent.list') }}" method="GET">
            {{-- Search Bar (Compact) --}}
            <div class="bg-white rounded-2xl shadow-sm p-2 flex flex-col md:flex-row gap-2 border border-gray-100 mb-6">
                <div class="flex-1 flex items-center bg-transparent px-4 py-2">
                    <i class="fas fa-search text-gray-400 mr-3"></i>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Tìm kiếm khu vực, dự án, tiện ích..." 
                           class="w-full bg-transparent outline-none text-gray-700 placeholder-gray-400 text-sm md:text-base font-medium"
                           onblur="this.form.submit()">
                </div>
                <div class="flex gap-2 w-full md:w-auto px-2 pb-2 md:p-0">
                    <button type="button" onclick="toggleMobileFilters()" class="flex-1 lg:hidden bg-gray-100 text-gray-700 px-4 py-3.5 rounded-xl font-bold text-sm transition-colors shadow-sm flex items-center justify-center gap-2">
                        <i class="fas fa-sliders-h"></i> Lọc
                    </button>
                    <button type="submit" class="flex-1 lg:flex-none bg-teal-600 text-white px-8 py-3.5 md:py-3 rounded-xl hover:bg-teal-700 font-bold text-sm md:text-base transition-colors shadow-sm whitespace-nowrap">
                        Tìm kiếm
                    </button>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[280px_1fr]">
                {{-- Mobile overlay backdrop --}}
                <div id="filterBackdrop" class="fixed inset-0 bg-black/50 z-[1000] hidden lg:hidden" onclick="toggleMobileFilters()"></div>

                {{-- Left Sidebar: Filters --}}
                <aside id="filterSidebar" class="fixed inset-y-0 left-0 z-[1001] w-[85%] max-w-sm -translate-x-full overflow-y-auto bg-white p-5 shadow-2xl transition-transform duration-300 ease-in-out lg:static lg:z-auto lg:w-auto lg:max-w-none lg:translate-x-0 lg:overflow-visible lg:rounded-2xl lg:border lg:border-gray-100 lg:shadow-sm lg:sticky lg:top-20 lg:self-start lg:block space-y-6 pb-safe">
                    <div class="flex items-center justify-between">
                        <h2 class="flex items-center gap-2 font-semibold text-gray-900">
                            <i class="fas fa-sliders-h w-4"></i> Bộ lọc
                        </h2>
                        <div class="flex items-center gap-3">
                            @if(request()->except('page'))
                                <a href="{{ route('rent.list') }}" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-red-500 transition-colors">
                                    <i class="fas fa-times w-3"></i> Xóa lọc
                                </a>
                            @endif
                            <button type="button" class="lg:hidden text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full bg-gray-50" onclick="toggleMobileFilters()">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        <p class="text-sm font-medium text-gray-900">Loại BĐS</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($types as $t)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="type[]" value="{{ $t->slug }}" class="peer sr-only" onchange="this.form.submit()" {{ in_array($t->slug, (array)request('type', [])) ? 'checked' : '' }}>
                                    <div class="rounded-full border px-3 py-1.5 text-sm transition peer-checked:border-teal-600 peer-checked:bg-teal-600 peer-checked:text-white border-gray-200 bg-white hover:border-teal-500 text-gray-700">{{ $t->label }}</div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Giá tối đa --}}
                    <div class="space-y-2.5" x-data="{ price: {{ request('priceMax', 30000000) }} }">
                        <p class="text-sm font-medium text-gray-900">
                            Giá tối đa: <span x-text="price < 30000000 ? new Intl.NumberFormat('vi-VN').format(price) + ' đ/th' : 'Không giới hạn'" class="text-teal-600 font-bold"></span>
                        </p>
                        <input type="range" name="priceMax" min="3000000" max="30000000" step="1000000" 
                               x-model="price" 
                               onchange="this.form.submit()"
                               class="w-full accent-teal-600 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                    </div>

                    {{-- Khu vực --}}
                    <div class="space-y-2.5">
                        <p class="text-sm font-medium text-gray-900">Khu vực</p>
                        <select name="area" onchange="this.form.submit()" class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                            <option value="">Tất cả khu vực</option>
                            @foreach($areas as $a)
                                <option value="{{ $a->slug }}" {{ request('area') === $a->slug ? 'selected' : '' }}>{{ $a->label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Dự án --}}
                    <div class="space-y-2.5">
                        <p class="text-sm font-medium text-gray-900">Dự án</p>
                        <select name="project" onchange="this.form.submit()" class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                            <option value="">Tất cả dự án</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->slug }}" {{ request('project') === $p->slug ? 'selected' : '' }}>{{ $p->label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Số phòng ngủ --}}
                    <div class="space-y-2.5">
                        <p class="text-sm font-medium text-gray-900">Số phòng ngủ</p>
                        <div class="grid grid-cols-4 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="beds" value="" class="peer sr-only" onchange="this.form.submit()" {{ !request('beds') ? 'checked' : '' }}>
                                <div class="rounded-xl border py-2 text-center text-sm transition peer-checked:border-teal-600 peer-checked:bg-teal-600 peer-checked:text-white border-gray-200 bg-white hover:border-teal-500 text-gray-700 font-medium whitespace-nowrap">Tất cả</div>
                            </label>
                            @foreach([1, 2, 3] as $b)
                                <label class="cursor-pointer">
                                    <input type="radio" name="beds" value="{{ $b }}" class="peer sr-only" onchange="this.form.submit()" {{ request('beds') == $b ? 'checked' : '' }}>
                                    <div class="rounded-xl border py-2 text-center text-sm transition peer-checked:border-teal-600 peer-checked:bg-teal-600 peer-checked:text-white border-gray-200 bg-white hover:border-teal-500 text-gray-700 font-medium whitespace-nowrap">{{ $b }}+ PN</div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tiện ích --}}
                    <div class="space-y-2.5">
                        <p class="text-sm font-medium text-gray-900">Tiện ích</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($amenitiesList as $a)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="amenities[]" value="{{ $a->id }}" class="peer sr-only" onchange="this.form.submit()" {{ in_array($a->id, (array)request('amenities', [])) ? 'checked' : '' }}>
                                    <div class="rounded-full border px-3 py-1.5 text-sm transition peer-checked:border-teal-600 peer-checked:bg-teal-600 peer-checked:text-white border-gray-200 bg-white hover:border-teal-500 text-gray-700">
                                        {{ $a->name }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </aside>

                {{-- Right Column: Properties --}}
                <div id="results-container" class="transition-opacity duration-300">
                    @include('partials.rent-list-results')
                </div>
            </div>
        </form>
        
    </div>
</div>

{{-- Script for handling favorites, copied from home.blade.php for consistency --}}
<script>
    function toggleFavorite(button, event) {
        event.preventDefault();
        event.stopPropagation();

        const propertyId = button.getAttribute('data-property-id');
        const icon = button.querySelector('i');
        const isFavorited = button.getAttribute('data-favorited') === 'true';

        // Optimistic UI Update
        if (isFavorited) {
            button.setAttribute('data-favorited', 'false');
            button.classList.remove('bg-red-500', 'text-white', 'shadow-md', 'shadow-red-200');
            button.classList.add('bg-white/80', 'backdrop-blur-sm', 'text-gray-400', 'hover:bg-red-50', 'hover:text-red-400', 'shadow-sm');
            icon.classList.remove('fas');
            icon.classList.add('far');
        } else {
            button.setAttribute('data-favorited', 'true');
            button.classList.add('bg-red-500', 'text-white', 'shadow-md', 'shadow-red-200');
            button.classList.remove('bg-white/80', 'backdrop-blur-sm', 'text-gray-400', 'hover:bg-red-50', 'hover:text-red-400', 'shadow-sm');
            icon.classList.remove('far');
            icon.classList.add('fas');
        }

        fetch('{{ route("wishlist.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ property_id: propertyId })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success && data.redirect) {
                window.location.href = data.redirect;
            }
        })
        .catch(error => console.error('Error toggling wishlist:', error));
    }
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filter-form');
    const container = document.getElementById('results-container');
    
    function fetchResults() {
        container.style.opacity = '0.5';
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        const url = form.action + '?' + params.toString();
        
        window.history.pushState({}, '', url);
        
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
        .then(res => res.text())
        .then(html => {
            container.innerHTML = html;
            container.style.opacity = '1';
        })
        .catch(err => {
            console.error('Lỗi khi tải kết quả lọc:', err);
            container.style.opacity = '1';
        });
    }

    // Gỡ bỏ onchange/onblur cứng trong HTML để dùng AJAX
    form.querySelectorAll('input, select').forEach(el => {
        el.onchange = null;
        el.onblur = null;
        el.removeAttribute('onchange');
        el.removeAttribute('onblur');
        
        el.addEventListener('change', fetchResults);
    });
    
    // Ngăn chặn form tự reload khi nhấn Enter
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        fetchResults();
    });
});

function toggleMobileFilters() {
    const sidebar = document.getElementById('filterSidebar');
    const backdrop = document.getElementById('filterBackdrop');
    
    if (sidebar.classList.contains('-translate-x-full')) {
        // Open
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        // Close
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
        document.body.style.overflow = '';
    }
}
</script>
@endsection
