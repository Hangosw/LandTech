@extends('layouts.app')

@section('title', 'Dự án trọng điểm — LANDTEK Nha Trang')
@section('description', 'Danh mục dự án căn hộ chuẩn hóa tại Nha Trang: Mường Thanh, Vinpearl, Sun Group, Scenia Bay, Gold Coast.')

@section('content')
<div class="bg-white min-h-screen">

    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-navy-deep via-navy to-navy-mid px-4 py-12 md:px-8 md:py-16">
        <div class="pointer-events-none absolute -top-28 -right-24 h-[380px] w-[380px] rounded-full bg-[radial-gradient(circle,rgba(245,158,11,0.16),transparent_70%)]"></div>
        <div class="relative z-10 mx-auto max-w-[1180px]">
            <p class="mb-3 text-[12.5px] font-bold tracking-[0.2em] text-amber-brand">DỰ ÁN TRỌNG ĐIỂM</p>
            <h1 class="mb-3 max-w-2xl text-3xl font-extrabold leading-snug text-white md:text-4xl">
                Căn hộ chuẩn hóa theo từng dự án tại Nha Trang
            </h1>
            <p class="max-w-xl text-[15px] leading-relaxed text-navy-muted">
                LANDTEK tập trung nguồn tin đã xác thực theo dự án — giúp bạn lọc nhanh khu vực, mức giá và loại căn phù hợp.
            </p>
            <div class="mt-6 flex flex-wrap gap-6 text-sm">
                <div>
                    <div class="text-xl font-extrabold text-white">{{ count($projects) }}</div>
                    <div class="text-[11.5px] text-[#8FB1DE]">Dự án đang theo dõi</div>
                </div>
                <div>
                    <div class="text-xl font-extrabold text-white">{{ collect($projects)->sum('listing_count') }}+</div>
                    <div class="text-[11.5px] text-[#8FB1DE]">Tin tham chiếu (tạm)</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Project grid --}}
    <section class="px-4 py-12 md:px-8 md:py-16">
        <div class="mx-auto grid max-w-[1180px] gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($projects as $proj)
                <article class="group flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-navy/20 hover:shadow-lg">
                    <div class="relative aspect-[16/10] overflow-hidden bg-navy-mist">
                        <img src="{{ $proj['image'] }}"
                             alt="{{ $proj['label'] }}"
                             class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                             onerror="this.src='/images/hero-nhatrang.jpg'">
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-navy-deep/80 to-transparent p-4 pt-12">
                            <h2 class="text-lg font-extrabold text-white">{{ $proj['label'] }}</h2>
                            <p class="text-xs text-navy-muted">{{ $proj['district'] ?? '' }}</p>
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col gap-3 p-5">
                        <p class="text-[13px] font-semibold text-navy">{{ $proj['tagline'] ?? '' }}</p>
                        <p class="text-[12.5px] leading-relaxed text-gray-500">{{ $proj['description'] ?? '' }}</p>

                        @if(!empty($proj['highlights']))
                            <ul class="space-y-1.5">
                                @foreach($proj['highlights'] as $h)
                                    <li class="flex items-start gap-2 text-[12px] text-gray-600">
                                        <i class="fas fa-check mt-0.5 text-[10px] text-amber-brand"></i>
                                        <span>{{ $h }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="mt-auto flex items-center justify-between border-t border-gray-100 pt-3 text-[12px]">
                            <div>
                                <span class="font-bold text-navy">{{ $proj['listing_count'] ?? 0 }} tin</span>
                                <span class="text-gray-400"> · từ {{ $proj['price_from'] ?? '—' }}</span>
                            </div>
                            <a href="{{ route('rent.list', ['project' => $proj['slug']]) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-navy px-3 py-2 text-[12px] font-bold text-white transition hover:bg-navy-mid">
                                Xem tin thuê
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="border-t border-gray-100 bg-navy-mist px-4 py-12 md:px-8">
        <div class="mx-auto flex max-w-[1180px] flex-col items-start justify-between gap-6 md:flex-row md:items-center">
            <div>
                <h2 class="text-xl font-extrabold text-navy md:text-2xl">Không tìm thấy dự án bạn cần?</h2>
                <p class="mt-1.5 text-sm text-gray-500">Liên hệ LANDTEK để được tư vấn khu vực và căn phù hợp — dữ liệu xác thực tận nơi.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('rent.list') }}"
                   class="inline-flex items-center gap-2 rounded-lg border-[1.5px] border-navy px-5 py-2.5 text-sm font-bold text-navy transition hover:bg-navy hover:text-white">
                    Xem tất cả tin thuê
                </a>
                <a href="tel:0868979799"
                   class="inline-flex items-center gap-2 rounded-lg bg-amber-brand px-5 py-2.5 text-sm font-bold text-navy transition hover:brightness-110">
                    <i class="fas fa-phone"></i> 086 8979799
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
