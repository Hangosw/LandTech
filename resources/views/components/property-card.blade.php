@props(['property', 'isFavorited' => false])

@php
    $utilities = $property['utilities'] ?? [];
@endphp

<a href="{{ route('rent.detail', $property['slug']) }}"
   class="group relative rounded-2xl border border-gray-100 bg-white overflow-hidden shadow-sm hover:shadow-lg hover:border-teal-100 transition-all duration-300 flex flex-col hover:-translate-y-0.5">

    <div class="relative w-full aspect-[4/3] overflow-hidden bg-gray-100 shrink-0">
        <img src="{{ $property['images'][0] ?? '/images/hero-nhatrang.jpg' }}"
             alt="{{ $property['title'] }}"
             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">

        <div class="absolute top-2.5 left-2.5 flex gap-1.5 z-10">
            @if($property['verified'] ?? false)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white/90 backdrop-blur-sm text-teal-700 text-[10px] font-bold shadow-sm border border-teal-100">
                    <i class="fas fa-circle-check text-teal-500"></i> Đã xác thực
                </span>
            @endif
            @if($property['is_gold_agent'] ?? false)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-400 text-amber-950 text-[10px] font-bold shadow-sm">
                    <i class="fas fa-crown text-amber-700"></i> Gold
                </span>
            @endif
        </div>

        <button
            type="button"
            data-property-id="{{ $property['id'] }}"
            data-favorited="{{ $isFavorited ? 'true' : 'false' }}"
            onclick="toggleFavorite(this, event)"
            class="favorite-btn absolute top-2.5 right-2.5 z-20 w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200
                   {{ $isFavorited
                        ? 'bg-red-500 text-white shadow-md shadow-red-200'
                        : 'bg-white/80 backdrop-blur-sm text-gray-400 hover:bg-red-50 hover:text-red-400 shadow-sm' }}">
            <i class="{{ $isFavorited ? 'fas' : 'far' }} fa-heart text-[13px]"></i>
        </button>

        @if(!empty($property['distance_sea']))
            <div class="absolute bottom-2.5 left-2.5 z-10">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-black/45 backdrop-blur-md text-white text-[10px] font-medium">
                    <i class="fas fa-water text-cyan-300 text-[9px]"></i> {{ $property['distance_sea'] }}
                </span>
            </div>
        @endif

        <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>
    </div>

    <div class="p-4 flex-1 flex flex-col gap-2">
        <div class="flex items-start justify-between gap-2">
            <span class="text-teal-600 font-extrabold text-base leading-tight">
                {{ $property['price_label'] ?? number_format($property['price']) . ' đ' }}
            </span>
            <span class="shrink-0 text-[10px] font-semibold text-teal-700 bg-teal-50 border border-teal-100 px-2 py-0.5 rounded-md mt-0.5">
                {{ $property['type_label'] ?? $property['type'] }}
            </span>
        </div>

        <h3 class="text-sm font-bold text-gray-900 line-clamp-2 min-h-[2.75rem] group-hover:text-teal-600 transition-colors leading-snug"
            title="{{ $property['title'] }}">
            {{ $property['title'] }}
        </h3>

        <p class="text-[11px] text-gray-500 flex items-center gap-1 truncate -mt-1">
            <i class="fas fa-map-marker-alt text-gray-400 shrink-0 text-[10px]"></i>
            <span class="truncate">{{ $property['location'] }}</span>
        </p>

        <hr class="border-gray-100 my-0.5">

        <div class="flex items-center gap-3 text-[11px] text-gray-600 font-medium">
            <span class="flex items-center gap-1"><i class="fas fa-bed text-gray-400 text-[10px]"></i> {{ $property['bedrooms'] }} PN</span>
            <span class="flex items-center gap-1"><i class="fas fa-bath text-gray-400 text-[10px]"></i> {{ $property['bathrooms'] }} WC</span>
            <span class="flex items-center gap-1"><i class="fas fa-vector-square text-gray-400 text-[10px]"></i> {{ $property['area'] }} m²</span>
        </div>

        @if(!empty($utilities))
            <div class="flex flex-wrap gap-1.5 mt-auto pt-1">
                @foreach(array_slice($utilities, 0, 3) as $util)
                    <span class="inline-flex items-center gap-1 bg-sky-50 text-sky-700 text-[10px] font-medium px-2 py-0.5 rounded-md border border-sky-100">
                        @if(!empty($util['icon'])) <i class="fas fa-{{ $util['icon'] }} text-[9px]"></i> @endif
                        {{ $util['name'] }}
                    </span>
                @endforeach
                @if(count($utilities) > 3)
                    <span class="inline-flex items-center bg-gray-50 text-gray-400 text-[10px] font-medium px-2 py-0.5 rounded-md border border-gray-100">
                        +{{ count($utilities) - 3 }}
                    </span>
                @endif
            </div>
        @else
            <div class="mt-auto pt-1"></div>
        @endif
    </div>
</a>
