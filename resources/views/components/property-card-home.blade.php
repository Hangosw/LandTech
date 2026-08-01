@php
    $isFavorited = isset($userWishlists) && in_array($property['id'], $userWishlists);
    $compact = $compact ?? false;
@endphp
<a href="{{ route('rent.detail', $property['slug']) }}"
   class="group relative flex {{ $compact ? 'min-w-[260px] flex-shrink-0' : '' }} flex-col overflow-hidden rounded-xl border border-gray-200 bg-white transition hover:-translate-y-0.5 hover:shadow-lg">
    <div class="relative aspect-[16/10] overflow-hidden bg-gray-100">
        <img src="{{ $property['images'][0] ?? '/images/hero-nhatrang.jpg' }}"
             alt="{{ $property['title'] }}"
             class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        @if($property['verified'] ?? false)
            <span class="absolute left-2.5 top-2.5 inline-flex items-center gap-1 rounded-md bg-white px-2 py-0.5 text-[10px] font-bold text-green-700 shadow-sm">
                <i class="fas fa-circle-check text-green-600"></i> Đã xác thực
            </span>
        @endif
        <button type="button"
                data-property-id="{{ $property['id'] }}"
                data-favorited="{{ $isFavorited ? 'true' : 'false' }}"
                onclick="toggleFavorite(this, event)"
                class="favorite-btn absolute right-2.5 top-2.5 z-20 flex h-8 w-8 items-center justify-center rounded-full transition
                       {{ $isFavorited
                            ? 'bg-red-500 text-white shadow-md shadow-red-200'
                            : 'bg-white/80 text-gray-400 shadow-sm backdrop-blur-sm hover:bg-red-50 hover:text-red-400' }}">
            <i class="{{ $isFavorited ? 'fas' : 'far' }} fa-heart text-[13px]"></i>
        </button>
    </div>
    <div class="flex flex-1 flex-col gap-1.5 p-4">
        <div class="text-base font-extrabold text-navy">
            {{ $property['price_label'] ?? number_format($property['price']) . ' đ' }}
        </div>
        <h3 class="line-clamp-2 min-h-[2.5rem] text-[12.5px] font-semibold leading-snug text-gray-800 group-hover:text-navy">
            {{ $property['title'] }}
        </h3>
        <div class="flex flex-wrap gap-2.5 text-[11.5px] text-gray-500">
            <span>{{ $property['bedrooms'] }} PN</span>
            <span>{{ $property['bathrooms'] }} WC</span>
            <span>{{ $property['area'] }} m²</span>
        </div>
    </div>
</a>
