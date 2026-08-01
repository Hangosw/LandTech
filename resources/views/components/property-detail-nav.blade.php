{{-- Listing prev/next: wide bottom highlight zones --}}
@props([
    'prevUrl' => null,
    'nextUrl' => null,
    'prevTitle' => null,
    'nextTitle' => null,
    'variant' => 'overlay', // overlay | admin
])

@if($variant === 'admin')
    <nav {{ $attributes->merge(['class' => 'flex items-stretch gap-4']) }} aria-label="Điều hướng tin">
        @if($prevUrl)
            <a href="{{ $prevUrl }}"
               class="flex flex-1 items-center justify-start rounded-xl px-2 py-3 text-slate-400/70 transition hover:bg-slate-50 hover:text-slate-700"
               title="{{ $prevTitle ?: 'Tin trước' }}"
               aria-label="Tin trước">
                <i class="fas fa-arrow-left text-sm" aria-hidden="true"></i>
            </a>
        @else
            <span class="flex flex-1 items-center justify-start px-2 py-3 text-slate-200" aria-hidden="true">
                <i class="fas fa-arrow-left text-sm"></i>
            </span>
        @endif
        <div class="w-10 shrink-0" aria-hidden="true"></div>
        @if($nextUrl)
            <a href="{{ $nextUrl }}"
               class="flex flex-1 items-center justify-end rounded-xl px-2 py-3 text-slate-400/70 transition hover:bg-slate-50 hover:text-slate-700"
               title="{{ $nextTitle ?: 'Tin sau' }}"
               aria-label="Tin sau">
                <i class="fas fa-arrow-right text-sm" aria-hidden="true"></i>
            </a>
        @else
            <span class="flex flex-1 items-center justify-end px-2 py-3 text-slate-200" aria-hidden="true">
                <i class="fas fa-arrow-right text-sm"></i>
            </span>
        @endif
    </nav>
@else
    {{-- Bottom-left / bottom-right wide white highlight panels --}}
    <div {{ $attributes->merge(['class' => 'absolute inset-x-0 bottom-0 z-20 flex items-end justify-between gap-[16%] px-2 pb-2 pointer-events-none']) }}
         aria-label="Điều hướng tin">
        @if($prevUrl)
            <a href="{{ $prevUrl }}"
               title="{{ $prevTitle ?: 'Tin trước' }}"
               aria-label="Tin trước"
               onclick="event.stopPropagation()"
               class="listing-nav-zone pointer-events-auto flex h-[72px] w-[38%] max-w-[200px] items-center justify-start rounded-xl px-4
                      border border-transparent bg-white/25 text-slate-700/70
                      transition duration-200
                      hover:border-white/80 hover:bg-white/75 hover:text-slate-900 hover:shadow-sm
                      focus:outline-none focus-visible:border-white focus-visible:bg-white/80
                      active:bg-white/85">
                <i class="fas fa-arrow-left text-base" aria-hidden="true"></i>
            </a>
        @else
            <span class="flex h-[72px] w-[38%] max-w-[200px] items-center justify-start rounded-xl px-4 bg-white/10 text-white/35" aria-hidden="true">
                <i class="fas fa-arrow-left text-base"></i>
            </span>
        @endif

        @if($nextUrl)
            <a href="{{ $nextUrl }}"
               title="{{ $nextTitle ?: 'Tin sau' }}"
               aria-label="Tin sau"
               onclick="event.stopPropagation()"
               class="listing-nav-zone pointer-events-auto flex h-[72px] w-[38%] max-w-[200px] items-center justify-end rounded-xl px-4
                      border border-transparent bg-white/25 text-slate-700/70
                      transition duration-200
                      hover:border-white/80 hover:bg-white/75 hover:text-slate-900 hover:shadow-sm
                      focus:outline-none focus-visible:border-white focus-visible:bg-white/80
                      active:bg-white/85">
                <i class="fas fa-arrow-right text-base" aria-hidden="true"></i>
            </a>
        @else
            <span class="flex h-[72px] w-[38%] max-w-[200px] items-center justify-end rounded-xl px-4 bg-white/10 text-white/35" aria-hidden="true">
                <i class="fas fa-arrow-right text-base"></i>
            </span>
        @endif
    </div>
@endif
