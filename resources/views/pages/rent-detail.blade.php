@extends('layouts.app')

@section('title', $property->title . ' — LANDTEK')
@section('description', Str::limit($property->description ?? '', 160))

@section('content')
@php
    /* ── Collect all images ── */
    $allImages = collect();
    if ($property->cover_image_url) $allImages->push($property->cover_image_url);
    foreach ($property->media->sortBy('display_order') as $m) {
        if ($m->file_url && !$allImages->contains($m->file_url)) $allImages->push($m->file_url);
    }
    if ($allImages->isEmpty()) $allImages->push('/images/hero-nhatrang.jpg');

    /* ── Labels ── */
    $typeLabel   = $property->type_label;
    $locationStr = ($property->district ? $property->district.', ' : '').'Nha Trang';

    /* ── Price display ── */
    if ($property->monthly_price) {
        $priceBig  = number_format($property->monthly_price / 1000000, 0, ',', '.').' triệu';
        $priceSub  = '/ tháng';
        $priceFullLabel = number_format($property->monthly_price / 1000000, 0, ',', '.').' triệu/tháng';
    } else {
        $priceBig  = number_format($property->price).' đ';
        $priceSub  = '';
        $priceFullLabel = number_format($property->price).' đ';
    }

    /* ── Agent info ── */
    $agentName    = $property->user?->name ?? 'Môi giới';
    $agentPhone   = $property->user?->phone ?? '';
    $agentInitial = strtoupper(substr($agentName, 0, 1));
@endphp

{{-- ══════════════════════════════════════════════════
     MOBILE-ONLY STYLES
══════════════════════════════════════════════════ --}}
<style>
/* ── Mobile carousel ── */
.mob-carousel {
    position: relative;
    width: 100%;
    height: 280px;
    overflow: hidden;
}
.mob-carousel-track {
    display: flex;
    height: 100%;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}
.mob-carousel-track::-webkit-scrollbar { display: none; }
.mob-carousel-slide {
    min-width: 100%;
    height: 100%;
    scroll-snap-align: start;
    flex-shrink: 0;
    position: relative;
}
.mob-carousel-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.mob-carousel-dots {
    position: absolute;
    bottom: 44px;
    left: 0; right: 0;
    display: flex;
    justify-content: center;
    gap: 5px;
    pointer-events: none;
    z-index: 10;
}
.mob-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: rgba(255,255,255,.45);
    transition: all .25s;
}
.mob-dot.active {
    background: #fff;
    width: 16px;
    border-radius: 4px;
}
.mob-carousel-count {
    position: absolute;
    bottom: 12px;
    right: 14px;
    background: rgba(0,0,0,.45);
    color: #fff;
    font-size: 11px;
    padding: 3px 9px;
    border-radius: 12px;
    z-index: 10;
}
.mob-carousel-appbar {
    position: absolute;
    top: 0; left: 0; right: 0;
    display: flex;
    justify-content: space-between;
    padding: 10px 12px;
    z-index: 20;
    pointer-events: none;
}
.mob-appbar-btn {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: rgba(15,52,96,.55);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 14px;
    pointer-events: auto;
    cursor: pointer;
    transition: background .2s;
}
.mob-appbar-btn:hover { background: rgba(15,52,96,.8); }
.mob-appbar-right { display: flex; gap: 8px; }

/* ── Mobile price block ── */
@media (max-width: 1023px) {
    .mob-price-block {
        padding: 14px 16px 10px;
        border-bottom: 6px solid #f3f4f6;
    }
    .mob-price-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 5px;
    }
    .mob-price-big {
        font-size: 24px;
        font-weight: 800;
        color: #0F3460;
    }
    .mob-price-badge {
        font-size: 10px;
        font-weight: 700;
        color: #16a34a;
        background: #dcfce7;
        padding: 3px 8px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 3px;
    }
    .mob-title {
        font-size: 15px;
        font-weight: 700;
        color: #1f2937;
        line-height: 1.4;
        margin-bottom: 5px;
    }
    .mob-addr {
        font-size: 12.5px;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .mob-stats-row {
        display: flex;
        gap: 0;
        margin-top: 12px;
        border-top: 1px solid #e5e7eb;
        padding-top: 12px;
    }
    .mob-stat-item {
        flex: 1;
        text-align: center;
    }
    .mob-stat-val {
        font-size: 14px;
        font-weight: 700;
        color: #0F3460;
    }
    .mob-stat-lbl {
        font-size: 10.5px;
        color: #6b7280;
        margin-top: 2px;
    }
    .mob-stat-sep {
        width: 1px;
        background: #e5e7eb;
        margin: 2px 8px;
    }
}

/* ── Mobile section header ── */
@media (max-width: 1023px) {
    .mob-section {
        padding: 16px 16px;
        border-bottom: 6px solid #f3f4f6;
    }
    .mob-section:last-child { border-bottom: none; }
    .mob-section-title {
        font-size: 13.5px;
        color: #0F3460;
        font-weight: 700;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: .3px;
    }
}

/* ── Spec grid ── */
@media (max-width: 1023px) {
    .mob-spec-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }
    .mob-spec-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .mob-spec-icon {
        width: 28px; height: 28px;
        border-radius: 8px;
        background: #ebf3ff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }
    .mob-spec-lbl {
        font-size: 11px;
        color: #6b7280;
    }
    .mob-spec-val {
        font-size: 12.5px;
        font-weight: 600;
        color: #1f2937;
    }
}

/* ── Chips ── */
@media (max-width: 1023px) {
    .mob-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .mob-chip {
        font-size: 11.5px;
        color: #0F3460;
        background: #ebf3ff;
        padding: 7px 12px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 5px;
        font-weight: 600;
    }
}

/* ── Description clamp ── */
@media (max-width: 1023px) {
    .mob-desc-text {
        font-size: 12.5px;
        color: #1f2937;
        line-height: 1.65;
    }
    .mob-desc-text.mob-clamped {
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .mob-see-more-btn {
        margin-top: 8px;
        background: none;
        border: none;
        color: #1A56A8;
        font-size: 12.5px;
        font-weight: 700;
        padding: 0;
        display: flex;
        align-items: center;
        gap: 3px;
        cursor: pointer;
    }
}

/* ── Map placeholder ── */
@media (max-width: 1023px) {
    .mob-map-box {
        height: 120px;
        border-radius: 12px;
        background:
            linear-gradient(0deg, rgba(26,86,168,.08) 1px, transparent 1px),
            linear-gradient(90deg, rgba(26,86,168,.08) 1px, transparent 1px), #eef2f8;
        background-size: 20px 20px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: opacity .2s;
        overflow: hidden;
    }
    .mob-map-box:hover { opacity: .85; }
    .mob-map-pin { font-size: 28px; }
    .mob-map-label {
        position: absolute;
        bottom: 8px; left: 8px;
        background: #fff;
        font-size: 10.5px;
        padding: 4px 8px;
        border-radius: 6px;
        color: #0F3460;
        font-weight: 600;
        box-shadow: 0 1px 4px rgba(0,0,0,.1);
    }
}

/* ── Agent card mobile ── */
@media (max-width: 1023px) {
    .mob-agent-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .mob-avatar {
        width: 44px; height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 16px;
        flex-shrink: 0;
    }
    .mob-agent-name {
        font-size: 13px;
        font-weight: 700;
        color: #1f2937;
    }
    .mob-agent-sub {
        font-size: 11px;
        color: #6b7280;
        margin-top: 1px;
    }
    .mob-agent-badge {
        font-size: 9.5px;
        background: #fef3c7;
        color: #92400e;
        padding: 2px 6px;
        border-radius: 5px;
        font-weight: 700;
        margin-left: 4px;
    }
    .mob-schedule-btn {
        width: 100%;
        margin-top: 12px;
        background: #ebf3ff;
        color: #1A56A8;
        border: 1px solid #c7d9f0;
        border-radius: 10px;
        padding: 10px;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        transition: background .2s;
    }
    .mob-schedule-btn:hover { background: #dbeafe; }
}

/* ── Similar horizontal scroll ── */
@media (max-width: 1023px) {
    .mob-similar-scroll {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 4px;
        scrollbar-width: none;
    }
    .mob-similar-scroll::-webkit-scrollbar { display: none; }
    .mob-sim-card {
        min-width: 145px;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        flex-shrink: 0;
        text-decoration: none;
        display: block;
        background: #fff;
        transition: box-shadow .2s;
    }
    .mob-sim-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.1); }
    .mob-sim-img {
        height: 90px;
        overflow: hidden;
        background: #dbeafe;
    }
    .mob-sim-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .mob-sim-body { padding: 8px; }
    .mob-sim-price {
        font-size: 12px;
        font-weight: 700;
        color: #0F3460;
    }
    .mob-sim-title {
        font-size: 10.5px;
        color: #1f2937;
        margin-top: 2px;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
}

/* ── Sticky bottom bar ── */
@media (max-width: 1023px) {
    .mob-bottom-bar {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        z-index: 999;
        background: #fff;
        border-top: 1px solid #e5e7eb;
        padding: 10px 14px;
        padding-bottom: max(14px, env(safe-area-inset-bottom));
        display: flex;
        gap: 10px;
        box-shadow: 0 -4px 12px rgba(0,0,0,.06);
    }
    .mob-btn-save {
        width: 44px; height: 44px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
        background: #fff;
        cursor: pointer;
        transition: all .2s;
        color: #6b7280;
    }
    .mob-btn-save:hover, .mob-btn-save.saved {
        border-color: #fca5a5;
        color: #ef4444;
    }
    .mob-btn-call {
        flex: 1; height: 44px;
        border-radius: 12px;
        border: none;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        background: #0d9488;
        color: #fff;
        cursor: pointer;
        text-decoration: none;
        transition: opacity .2s;
    }
    .mob-btn-call:hover { opacity: .88; color: #fff; }
    .mob-btn-zalo {
        flex: 1; height: 44px;
        border-radius: 12px;
        border: 1.5px solid #99f6e4;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        background: #f0fdfa;
        color: #0f766e;
        cursor: pointer;
        text-decoration: none;
        transition: background .2s;
    }
    .mob-btn-zalo:hover { background: #ccfbf1; }
    /* Push content above sticky bar */
    .mob-bottom-spacer { height: 80px; }
}
@media (min-width: 1024px) {
    .mob-only { display: none !important; }
    .mob-bottom-bar { display: none !important; }
}
</style>

{{-- ────────────────────────── BREADCRUMB ────────────────────────── --}}
<div class="bg-white border-b border-gray-100 py-3">
    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">
        <nav class="flex items-center gap-1.5 text-xs text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-teal-600 transition-colors">Trang chủ</a>
            <i class="fas fa-chevron-right text-[9px] text-gray-300"></i>
            <a href="{{ route('rent.list') }}" class="hover:text-teal-600 transition-colors">Thuê nhà</a>
            @if($property->district)
                <i class="fas fa-chevron-right text-[9px] text-gray-300"></i>
                <span class="text-gray-800 font-medium">{{ $property->district }}</span>
            @endif
        </nav>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     MOBILE LAYOUT (< lg) — hidden on desktop
══════════════════════════════════════════════════ --}}
<div class="lg:hidden mob-only" id="mob-layout">

    {{-- ── 1. MOBILE CAROUSEL ── --}}
    <div class="mob-carousel" id="mobCarousel">

        {{-- Appbar overlay --}}
        <div class="mob-carousel-appbar">
            <a href="{{ route('rent.list') }}" class="mob-appbar-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="mob-appbar-right">
                <button class="mob-appbar-btn" id="mob-wishlist-appbar" onclick="mobToggleWishlist()">♡</button>
                <button class="mob-appbar-btn" onclick="mobShare()">
                    <i class="fas fa-share-alt" style="font-size:13px"></i>
                </button>
            </div>
        </div>

        {{-- Carousel track --}}
        <div class="mob-carousel-track" id="mobTrack">
            @foreach($allImages as $idx => $img)
                <div class="mob-carousel-slide">
                    <img src="{{ $img }}" alt="Ảnh {{ $idx + 1 }}" loading="{{ $idx === 0 ? 'eager' : 'lazy' }}">
                </div>
            @endforeach
        </div>

        {{-- Dots --}}
        <div class="mob-carousel-dots" id="mobDots">
            @foreach($allImages as $idx => $img)
                <div class="mob-dot {{ $idx === 0 ? 'active' : '' }}"></div>
            @endforeach
        </div>

        {{-- Counter --}}
        <div class="mob-carousel-count" id="mobCounter">1/{{ $allImages->count() }}</div>
    </div>

    {{-- ── 2. PRICE & TITLE BLOCK ── --}}
    <div class="mob-price-block">
        <div class="mob-price-row">
            <span class="mob-price-big">{{ $priceFullLabel }}</span>
            <span class="mob-price-badge">✓ Đã xác thực</span>
        </div>
        <div class="mob-title">{{ $property->title }}</div>
        <div class="mob-addr">
            <span>📍</span>
            <span>{{ $property->address ? $property->address.', ' : '' }}{{ $locationStr }}</span>
        </div>

        {{-- Quick stats --}}
        <div class="mob-stats-row">
            <div class="mob-stat-item">
                <div class="mob-stat-val">{{ $property->area }} m²</div>
                <div class="mob-stat-lbl">Diện tích</div>
            </div>
            <div class="mob-stat-sep"></div>
            <div class="mob-stat-item">
                <div class="mob-stat-val">{{ $property->bedrooms ?? '—' }}</div>
                <div class="mob-stat-lbl">Phòng ngủ</div>
            </div>
            <div class="mob-stat-sep"></div>
            <div class="mob-stat-item">
                <div class="mob-stat-val">{{ $property->bathrooms ?? '—' }}</div>
                <div class="mob-stat-lbl">WC</div>
            </div>
            @if($property->distance_to_beach)
                <div class="mob-stat-sep"></div>
                <div class="mob-stat-item">
                    <div class="mob-stat-val">{{ number_format($property->distance_to_beach) }}m</div>
                    <div class="mob-stat-lbl">Tới biển</div>
                </div>
            @endif
        </div>
    </div>

    {{-- ── 3. ĐẶC ĐIỂM BẤT ĐỘNG SẢN ── --}}
    <div class="mob-section">
        <div class="mob-section-title">Đặc điểm bất động sản</div>
        <div class="mob-spec-grid">
            <div class="mob-spec-item">
                <div class="mob-spec-icon">📐</div>
                <div>
                    <div class="mob-spec-lbl">Diện tích</div>
                    <div class="mob-spec-val">{{ $property->area }} m²</div>
                </div>
            </div>
            @if($property->bedrooms)
            <div class="mob-spec-item">
                <div class="mob-spec-icon">🛏️</div>
                <div>
                    <div class="mob-spec-lbl">Phòng ngủ</div>
                    <div class="mob-spec-val">{{ $property->bedrooms }} phòng</div>
                </div>
            </div>
            @endif
            @if($property->bathrooms)
            <div class="mob-spec-item">
                <div class="mob-spec-icon">🚿</div>
                <div>
                    <div class="mob-spec-lbl">Phòng tắm</div>
                    <div class="mob-spec-val">{{ $property->bathrooms }} phòng</div>
                </div>
            </div>
            @endif
            <div class="mob-spec-item">
                <div class="mob-spec-icon">🏠</div>
                <div>
                    <div class="mob-spec-lbl">Loại BĐS</div>
                    <div class="mob-spec-val">{{ $typeLabel }}</div>
                </div>
            </div>
            @if($property->transaction_type)
            <div class="mob-spec-item">
                <div class="mob-spec-icon">📋</div>
                <div>
                    <div class="mob-spec-lbl">Hình thức</div>
                    <div class="mob-spec-val">{{ $property->transaction_type === 'rent' ? 'Cho thuê' : 'Mua bán' }}</div>
                </div>
            </div>
            @endif
            @if($property->min_rent_period)
            <div class="mob-spec-item">
                <div class="mob-spec-icon">📅</div>
                <div>
                    <div class="mob-spec-lbl">Thuê tối thiểu</div>
                    <div class="mob-spec-val">{{ $property->min_rent_period }} tháng</div>
                </div>
            </div>
            @endif
            @if($property->distance_to_beach)
            <div class="mob-spec-item">
                <div class="mob-spec-icon">🌊</div>
                <div>
                    <div class="mob-spec-lbl">Cách biển</div>
                    <div class="mob-spec-val">{{ number_format($property->distance_to_beach) }}m</div>
                </div>
            </div>
            @endif
            @if($property->project)
            <div class="mob-spec-item">
                <div class="mob-spec-icon">🏗️</div>
                <div>
                    <div class="mob-spec-lbl">Dự án</div>
                    <div class="mob-spec-val">{{ $property->project }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── 4. MÔ TẢ ── --}}
    @if($property->description)
    <div class="mob-section">
        <div class="mob-section-title">Mô tả</div>
        <div class="mob-desc-text mob-clamped" id="mobDescText">
            {{ $property->description }}
        </div>
        <button class="mob-see-more-btn" id="mobSeeMoreBtn" onclick="mobToggleDesc()">
            Xem thêm <span id="mobSeeMoreIcon">⌄</span>
        </button>
    </div>
    @endif

    {{-- ── 5. TIỆN ÍCH ── --}}
    @if($property->utilities->isNotEmpty())
    <div class="mob-section">
        <div class="mob-section-title">Tiện ích</div>
        <div class="mob-chips">
            @foreach($property->utilities as $util)
                <div class="mob-chip">
                    @if($util->icon_name)
                        <i class="{{ $util->icon_name }}" style="font-size:12px"></i>
                    @else
                        ✓
                    @endif
                    {{ $util->name }}
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── 6. VỊ TRÍ ── --}}
    <div class="mob-section">
        <div class="mob-section-title">Vị trí</div>
        @if($property->lat && $property->lng)
            {{-- Has coordinates: show map with tap overlay --}}
            <div style="position:relative; border-radius:12px; overflow:hidden; height:140px;">
                <iframe
                    src="https://www.openstreetmap.org/export/embed.html?bbox={{ $property->lng - 0.012 }},{{ $property->lat - 0.009 }},{{ $property->lng + 0.012 }},{{ $property->lat + 0.009 }}&layer=mapnik&marker={{ $property->lat }},{{ $property->lng }}"
                    style="width:100%;height:100%;border:0;" loading="lazy" title="Vị trí">
                </iframe>
                {{-- Tap-to-open overlay --}}
                <a href="https://maps.google.com/?q={{ $property->lat }},{{ $property->lng }}" target="_blank"
                   style="position:absolute;inset:0;z-index:5;display:flex;align-items:flex-end;justify-content:flex-end;padding:8px;text-decoration:none;">
                    <span style="background:#fff;color:#0F3460;font-size:10.5px;font-weight:700;padding:4px 10px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.15);">
                        🗺️ Mở Google Maps
                    </span>
                </a>
            </div>
        @else
            {{-- No coordinates: placeholder --}}
            <div class="mob-map-box"
                 onclick="window.open('https://maps.google.com/?q={{ urlencode(($property->address ?? '').' '.($locationStr ?? '')) }}', '_blank')">
                <span class="mob-map-pin">📍</span>
                <span class="mob-map-label">Chạm để mở bản đồ</span>
            </div>
        @endif
    </div>

    {{-- ── 7. MÔI GIỚI ── --}}
    <div class="mob-section">
        <div class="mob-section-title">Môi giới</div>
        <div class="mob-agent-row">
            <div class="mob-avatar">{{ $agentInitial }}</div>
            <div style="flex:1">
                <div class="mob-agent-name">
                    {{ $agentName }}
                    <span class="mob-agent-badge">SILVER</span>
                </div>
                <div class="mob-agent-sub">
                    @if($property->view_count > 0)
                        {{ number_format($property->view_count) }} lượt xem ·
                    @endif
                    Phản hồi nhanh
                </div>
            </div>
        </div>
        <button class="mob-schedule-btn" onclick="document.getElementById('mob-booking-modal').classList.remove('hidden')">
            🗓️ Đặt lịch xem nhà
        </button>
    </div>

    {{-- ── 8. TIN TƯƠNG TỰ ── --}}
    @if($similar->isNotEmpty())
    <div class="mob-section" style="border-bottom:none">
        <div class="mob-section-title">Tin tương tự</div>
        <div class="mob-similar-scroll">
            @foreach($similar as $prop)
                @php
                    $sImg = $prop->cover_image_url ?? '/images/hero-nhatrang.jpg';
                    $sPrc = $prop->monthly_price
                        ? number_format($prop->monthly_price / 1000000, 1).' triệu/th'
                        : number_format($prop->price).' đ';
                @endphp
                <a href="{{ route('rent.detail', $prop->slug) }}" class="mob-sim-card">
                    <div class="mob-sim-img">
                        <img src="{{ $sImg }}" alt="{{ $prop->title }}">
                    </div>
                    <div class="mob-sim-body">
                        <div class="mob-sim-price">{{ $sPrc }}</div>
                        <div class="mob-sim-title">{{ $prop->title }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Spacer above sticky bar --}}
    <div class="mob-bottom-spacer"></div>
</div>
{{-- END MOBILE LAYOUT --}}

{{-- ══════════════════════════════════════════════════
     DESKTOP LAYOUT (>= lg) — hidden on mobile
══════════════════════════════════════════════════ --}}
<div class="bg-gray-50 min-h-screen hidden lg:block">
<div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-6">

{{-- ────────────────────────── GALLERY ────────────────────────── --}}
<div class="mb-6">
    {{-- Desktop: main image + 2×2 thumbnails side by side --}}
    <div class="hidden lg:flex gap-3" style="height:390px">

        {{-- Main image --}}
        <div class="relative flex-1 min-w-0 rounded-2xl overflow-hidden bg-gray-200 cursor-pointer"
             onclick="openGallery(currentMainIdx)">
            <img id="main-gallery-img"
                 src="{{ $allImages->first() }}"
                 alt="{{ $property->title }}"
                 class="w-full h-full object-cover transition-all duration-400 hover:scale-[1.02]">
        </div>

        {{-- 2 × 2 thumbnail grid --}}
        <div class="shrink-0 grid grid-cols-2 gap-2.5" style="width:268px; grid-template-rows:1fr 1fr">
            @foreach($allImages->take(4) as $idx => $img)
                <div class="relative rounded-xl overflow-hidden bg-gray-200 cursor-pointer group thumbnail-wrap
                            {{ $idx === 0 ? 'ring-[2.5px] ring-teal-400 ring-offset-1' : '' }}"
                     data-idx="{{ $idx }}"
                     onclick="switchMain(this, {{ $idx }})">
                    <img src="{{ $img }}" alt="Ảnh {{ $idx + 1 }}"
                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    @if($idx === 3 && $allImages->count() > 4)
                        <div class="absolute inset-0 bg-black/55 flex items-center justify-center rounded-xl"
                             onclick="openGallery(3); event.stopPropagation()">
                            <span class="text-white font-bold text-2xl">+{{ $allImages->count() - 4 }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
            {{-- Empty slots --}}
            @for($e = min($allImages->count(), 4); $e < 4; $e++)
                <div class="rounded-xl bg-gray-100 border-2 border-dashed border-gray-200 flex items-center justify-center">
                    <i class="fas fa-image text-gray-300 text-xl"></i>
                </div>
            @endfor
        </div>
    </div>
</div>

{{-- ────────────────────────── MAIN LAYOUT ────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-8 items-start">

    {{-- ═══════════════ LEFT COLUMN ═══════════════ --}}
    <div>

        {{-- Badges --}}
        <div class="flex flex-wrap gap-2 mb-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-50 border border-teal-100 text-teal-700 text-xs font-semibold">
                <i class="fas fa-circle-check text-teal-500 text-[10px]"></i> Tin đã xác thực
            </span>
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold">
                {{ $typeLabel }}
            </span>
            @if($property->view_count > 0)
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-orange-50 text-orange-600 text-xs font-medium">
                    <i class="fas fa-eye text-[10px]"></i> {{ number_format($property->view_count) }} lượt xem
                </span>
            @endif
        </div>

        {{-- Title --}}
        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight mb-2">
            {{ $property->title }}
        </h1>

        {{-- Location --}}
        <p class="flex items-center gap-1.5 text-sm text-gray-500 mb-5">
            <i class="fas fa-map-marker-alt text-teal-500 text-xs"></i>
            {{ $property->address ? $property->address.', ' : '' }}{{ $locationStr }}
        </p>

        {{-- Price --}}
        <div class="flex items-baseline gap-2 mb-6">
            <span class="text-3xl font-extrabold text-teal-600">{{ $priceBig }}</span>
            @if($priceSub)
                <span class="text-gray-500 text-base font-normal">{{ $priceSub }}</span>
            @endif
        </div>

        {{-- Stats: 4 boxes --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
            <div class="flex flex-col items-center justify-center bg-white rounded-xl border border-gray-100 shadow-sm py-4 px-2 gap-2">
                <i class="fas fa-bed text-teal-500 text-xl"></i>
                <span class="text-sm font-semibold text-gray-700 text-center">{{ $property->bedrooms }} phòng ngủ</span>
            </div>
            <div class="flex flex-col items-center justify-center bg-white rounded-xl border border-gray-100 shadow-sm py-4 px-2 gap-2">
                <i class="fas fa-bath text-teal-500 text-xl"></i>
                <span class="text-sm font-semibold text-gray-700 text-center">{{ $property->bathrooms }} phòng tắm</span>
            </div>
            <div class="flex flex-col items-center justify-center bg-white rounded-xl border border-gray-100 shadow-sm py-4 px-2 gap-2">
                <i class="fas fa-vector-square text-teal-500 text-xl"></i>
                <span class="text-sm font-semibold text-gray-700 text-center">{{ $property->area }} m²</span>
            </div>
            @if($property->distance_to_beach)
                <div class="flex flex-col items-center justify-center bg-white rounded-xl border border-gray-100 shadow-sm py-4 px-2 gap-2">
                    <i class="fas fa-water text-teal-500 text-xl"></i>
                    <span class="text-sm font-semibold text-gray-700 text-center">{{ number_format($property->distance_to_beach) }}m tới biển</span>
                </div>
            @endif
        </div>

        {{-- Action buttons --}}
        <div class="flex flex-wrap gap-3 mb-8 pb-8 border-b border-gray-200">
            <button id="btn-wishlist"
                    onclick="toggleWishlist(this)"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 hover:border-red-300 hover:text-red-500 font-medium text-sm transition-all shadow-sm">
                <i class="far fa-heart"></i> Lưu tin
            </button>
            <button onclick="shareProperty()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 hover:border-teal-400 hover:text-teal-600 font-medium text-sm transition-all shadow-sm">
                <i class="fas fa-share-alt"></i> Chia sẻ
            </button>
        </div>

        {{-- Description --}}
        @if($property->description)
            <div class="mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-3">Mô tả</h2>
                <div class="text-gray-600 text-sm leading-relaxed" id="desc-content">
                    {!! nl2br(e(Str::limit($property->description, 400))) !!}
                    @if(strlen($property->description) > 400)
                        <button onclick="expandDesc()" id="desc-btn"
                                class="text-teal-600 hover:underline font-medium ml-1">Xem thêm</button>
                        <span id="desc-full" class="hidden">{!! nl2br(e(substr($property->description, 400))) !!}</span>
                    @endif
                </div>
            </div>
        @endif

        {{-- Utilities --}}
        @if($property->utilities->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-3">Tiện ích</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-y-3 gap-x-4">
                    @foreach($property->utilities as $util)
                        <div class="flex items-center gap-2 text-sm text-gray-700">
                            <i class="fas fa-check text-teal-500 shrink-0 text-xs"></i>
                            <span>{{ $util->name }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Map --}}
        @if($property->lat && $property->lng)
            <div class="mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-3">Vị trí</h2>
                <div class="rounded-2xl overflow-hidden border border-gray-100 shadow-sm" style="height:280px">
                    <iframe
                        src="https://www.openstreetmap.org/export/embed.html?bbox={{ $property->lng - 0.012 }},{{ $property->lat - 0.009 }},{{ $property->lng + 0.012 }},{{ $property->lat + 0.009 }}&layer=mapnik&marker={{ $property->lat }},{{ $property->lng }}"
                        class="w-full h-full border-0" loading="lazy" title="Vị trí">
                    </iframe>
                </div>
                <p class="mt-2 text-xs text-gray-400">
                    Bản đồ © <a href="https://www.openstreetmap.org/copyright" target="_blank" class="text-teal-600 hover:underline">OpenStreetMap</a> contributors
                </p>
            </div>
        @endif

    </div>{{-- end left column --}}

    {{-- ═══════════════ RIGHT SIDEBAR ═══════════════ --}}
    <aside class="space-y-4 lg:sticky lg:top-6">

        {{-- Agent card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-white text-lg font-bold shrink-0 shadow">
                    {{ $agentInitial }}
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900 leading-tight">{{ $agentName }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Silver Agent</p>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <a href="tel:{{ preg_replace('/[^0-9]/', '', $agentPhone) }}"
                   class="flex items-center justify-center gap-2 w-full bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm py-2.5 rounded-xl transition-colors shadow-sm">
                    <i class="fas fa-phone text-sm"></i>
                    Hotline: {{ $agentPhone ?: 'Chưa cập nhật' }}
                </a>
                <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $agentPhone) }}" target="_blank"
                   class="flex items-center justify-center gap-2 w-full border border-gray-200 hover:border-teal-400 hover:text-teal-600 text-gray-700 font-semibold text-sm py-2.5 rounded-xl transition-all">
                    <i class="fas fa-comment-dots text-sm"></i> Zalo
                </a>
            </div>
        </div>

        {{-- Booking card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-2 mb-1">
                <i class="far fa-calendar-check text-teal-500"></i>
                <h3 class="text-sm font-bold text-gray-900">Đặt lịch xem nhà</h3>
            </div>
            <p class="text-xs text-gray-400 mb-4 leading-relaxed">Chọn ngày trống và khung giờ phù hợp.</p>

            {{-- Date strip --}}
            <div class="overflow-x-auto -mx-1 pb-0.5 mb-4">
                <div class="flex gap-2 px-1 w-max">
                    @php
                        $activeDate = isset($userBooking) ? $userBooking->scheduled_date->format('Y-m-d') : now()->format('Y-m-d');
                    @endphp
                    @for($d = 0; $d < 7; $d++)
                        @php
                            $dt = now()->addDays($d);
                            $dn = ['CN','T2','T3','T4','T5','T6','T7'][$dt->dayOfWeek];
                            $dateStr = $dt->format('Y-m-d');
                            $isActiveDate = ($dateStr === $activeDate);
                        @endphp
                        <button type="button"
                                onclick="selectDate(this,'{{ $dateStr }}')"
                                data-date="{{ $dateStr }}"
                                class="date-btn shrink-0 flex flex-col items-center w-11 py-2.5 rounded-xl border text-xs font-medium transition-all
                                       {{ $isActiveDate
                                            ? 'bg-teal-600 border-teal-600 text-white'
                                            : 'border-gray-200 text-gray-600 hover:border-teal-400 hover:text-teal-600' }}">
                            <span class="text-[9px] font-normal opacity-75 uppercase">{{ $dn }}</span>
                            <span class="text-sm font-bold mt-0.5">{{ $dt->format('d') }}</span>
                            <span class="text-[9px] opacity-75">{{ $dt->format('M') }}</span>
                        </button>
                    @endfor
                </div>
            </div>

            {{-- Time slots --}}
            <div class="grid grid-cols-2 gap-2 mb-4">
                @php
                    $activeTime = isset($userBooking) && $userBooking->scheduled_time ? \Carbon\Carbon::parse($userBooking->scheduled_time)->format('H:i') : '';
                @endphp
                @foreach(['09:00','10:30','14:00','15:30','17:00'] as $t)
                    @php $isActiveTime = ($t === $activeTime); @endphp
                    <button type="button"
                            onclick="selectTime(this,'{{ $t }}')"
                            class="time-btn py-2 rounded-xl border {{ $isActiveTime ? 'bg-teal-600 border-teal-600 text-white' : 'border-gray-200 text-gray-600 hover:border-teal-400 hover:text-teal-600' }} text-xs font-medium transition-all">
                        {{ $t }}
                    </button>
                @endforeach
            </div>

            {{-- Contact form --}}
            <form id="booking-form" class="space-y-3">
                @csrf
                <input type="hidden" name="property_id" value="{{ $property->id }}">
                <input type="hidden" name="booking_date" id="booking_date" value="{{ $activeDate }}">
                <input type="hidden" name="booking_time" id="booking_time" value="{{ $activeTime }}">
                <input type="text" name="contact_name" placeholder="Họ tên của bạn" value="{{ $userBooking->renter_name ?? '' }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-teal-400 transition-colors placeholder-gray-400">
                <input type="tel" name="contact_phone" placeholder="Số điện thoại" value="{{ $userBooking->renter_phone ?? '' }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-teal-400 transition-colors placeholder-gray-400">
                <button type="submit"
                        class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 rounded-xl text-sm transition-colors shadow-sm">
                    {{ isset($userBooking) ? 'Cập nhật lịch hẹn' : 'Gửi yêu cầu đặt lịch' }}
                </button>
            </form>
        </div>

    </aside>

</div>{{-- end grid --}}

{{-- ────────────────────────── SIMILAR PROPERTIES ────────────────────────── --}}
@if($similar->isNotEmpty())
    <section class="mt-14 pb-10">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-6">Tin tương tự</h2>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($similar as $prop)
                @php
                    $sImg   = $prop->cover_image_url ?? '/images/hero-nhatrang.jpg';
                    $sPrc   = $prop->monthly_price
                        ? number_format($prop->monthly_price / 1000000, 1).' triệu/tháng'
                        : number_format($prop->price).' đ';
                    $sLoc   = ($prop->district ? $prop->district.', ' : '').'Nha Trang';
                    $sType  = $prop->type_label;
                @endphp
                <a href="{{ route('rent.detail', $prop->slug) }}"
                   class="group rounded-2xl border border-gray-100 bg-white overflow-hidden shadow-sm hover:shadow-lg hover:border-teal-100 transition-all duration-300 flex flex-col hover:-translate-y-0.5">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100 shrink-0">
                        <img src="{{ $sImg }}" alt="{{ $prop->title }}"
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <div class="absolute top-2.5 left-2.5 flex gap-1.5 z-10">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white/90 backdrop-blur-sm text-teal-700 text-[10px] font-bold shadow-sm border border-teal-100">
                                <i class="fas fa-circle-check text-teal-500"></i> Đã xác thực
                            </span>
                        </div>
                        @if($prop->distance_to_beach)
                            <div class="absolute bottom-2.5 left-2.5 z-10">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-black/45 backdrop-blur-md text-white text-[10px] font-medium">
                                    <i class="fas fa-water text-cyan-300 text-[9px]"></i> {{ number_format($prop->distance_to_beach) }}m tới biển
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="p-4 flex flex-col gap-1.5">
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-teal-600 font-extrabold text-base leading-tight">{{ $sPrc }}</span>
                            <span class="shrink-0 text-[10px] font-semibold text-teal-700 bg-teal-50 border border-teal-100 px-2 py-0.5 rounded-md mt-0.5">{{ $sType }}</span>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900 line-clamp-2 min-h-[2.75rem] group-hover:text-teal-600 transition-colors leading-snug" title="{{ $prop->title }}">{{ $prop->title }}</h3>
                        <p class="text-[11px] text-gray-500 flex items-center gap-1 truncate">
                            <i class="fas fa-map-marker-alt text-gray-400 text-[10px]"></i>{{ $sLoc }}
                        </p>
                        <hr class="border-gray-100 my-1">
                        <div class="flex items-center gap-3 text-[11px] text-gray-600 font-medium">
                            <span class="flex items-center gap-1"><i class="fas fa-bed text-gray-400 text-[10px]"></i>{{ $prop->bedrooms }} PN</span>
                            <span class="flex items-center gap-1"><i class="fas fa-bath text-gray-400 text-[10px]"></i>{{ $prop->bathrooms }} WC</span>
                            <span class="flex items-center gap-1"><i class="fas fa-vector-square text-gray-400 text-[10px]"></i>{{ $prop->area }} m²</span>
                        </div>
                        @if($prop->utilities->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5 mt-1">
                                @foreach($prop->utilities->take(2) as $u)
                                    <span class="inline-flex items-center gap-1 bg-sky-50 text-sky-700 text-[10px] font-medium px-2 py-0.5 rounded-md border border-sky-100">{{ $u->name }}</span>
                                @endforeach
                                @if($prop->utilities->count() > 2)
                                    <span class="inline-flex items-center bg-gray-50 text-gray-400 text-[10px] px-2 py-0.5 rounded-md border border-gray-100">+{{ $prop->utilities->count()-2 }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif

</div>{{-- end container --}}
</div>{{-- end desktop bg-gray-50 --}}

{{-- ────────────────────────── LIGHTBOX ────────────────────────── --}}
<div id="gallery-modal" class="fixed inset-0 z-50 bg-black/92 hidden items-center justify-center"
     onclick="if(event.target===this) closeGallery()">
    <button onclick="closeGallery()"
            class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xl transition">
        <i class="fas fa-times"></i>
    </button>
    <button onclick="prevImg()"
            class="absolute left-3 md:left-6 top-1/2 -translate-y-1/2 z-10 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xl transition">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button onclick="nextImg()"
            class="absolute right-3 md:right-6 top-1/2 -translate-y-1/2 z-10 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xl transition">
        <i class="fas fa-chevron-right"></i>
    </button>
    <div class="max-w-4xl w-full px-16 md:px-20" onclick="event.stopPropagation()">
        <img id="modal-img" src="" alt="Gallery"
             class="w-full max-h-[82vh] object-contain rounded-xl">
        <p id="modal-counter" class="text-center text-white/50 text-sm mt-3 font-medium"></p>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     MOBILE STICKY BOTTOM BAR
══════════════════════════════════════════════════ --}}
<div class="mob-bottom-bar lg:hidden" id="mobBottomBar">
    <button class="mob-btn-save" id="mobSaveBtn" onclick="mobToggleWishlist()" title="Lưu tin">
        <span id="mobSaveIcon">♡</span>
    </button>
    <a href="tel:{{ preg_replace('/[^0-9]/', '', $agentPhone) }}" class="mob-btn-call">
        📞 Gọi điện
    </a>
    <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $agentPhone) }}" target="_blank" class="mob-btn-zalo">
        💬 Chat Zalo
    </a>
</div>

{{-- ══════════════════════════════════════════════════
     MOBILE BOOKING MODAL
══════════════════════════════════════════════════ --}}
<div id="mob-booking-modal" class="hidden lg:hidden fixed inset-0 z-[1000] flex items-end">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50" onclick="document.getElementById('mob-booking-modal').classList.add('hidden')"></div>

    {{-- Sheet --}}
    <div class="relative w-full bg-white rounded-t-2xl p-5 pb-safe z-10" style="max-height:90vh; overflow-y:auto; padding-bottom: max(20px, env(safe-area-inset-bottom));">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-900 text-base">🗓️ Đặt lịch xem nhà</h3>
            <button onclick="document.getElementById('mob-booking-modal').classList.add('hidden')"
                    style="width:32px;height:32px;border-radius:50%;background:#f3f4f6;border:none;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
        </div>

        {{-- Date strip --}}
        <div style="overflow-x:auto;margin-bottom:16px;">
            <div style="display:flex;gap:8px;width:max-content;">
                @for($d = 0; $d < 7; $d++)
                    @php
                        $dt2 = now()->addDays($d);
                        $dn2 = ['CN','T2','T3','T4','T5','T6','T7'][$dt2->dayOfWeek];
                        $dateStr2 = $dt2->format('Y-m-d');
                        $isAD2 = ($dateStr2 === (isset($activeDate) ? $activeDate : now()->format('Y-m-d')));
                    @endphp
                    <button type="button"
                            onclick="mobSelectDate(this,'{{ $dateStr2 }}')"
                            data-date="{{ $dateStr2 }}"
                            style="flex-shrink:0;display:flex;flex-direction:column;align-items:center;width:44px;padding:10px 0;border-radius:12px;border:1px solid {{ $isAD2 ? '#0d9488' : '#e5e7eb' }};background:{{ $isAD2 ? '#0d9488' : 'transparent' }};color:{{ $isAD2 ? '#fff' : '#4b5563' }};font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;"
                            class="mob-date-btn">
                        <span style="font-size:9px;opacity:.75;text-transform:uppercase;">{{ $dn2 }}</span>
                        <span style="font-size:14px;font-weight:800;margin-top:2px;">{{ $dt2->format('d') }}</span>
                        <span style="font-size:9px;opacity:.75;">{{ $dt2->format('M') }}</span>
                    </button>
                @endfor
            </div>
        </div>

        {{-- Time slots --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px;">
            @foreach(['09:00','10:30','14:00','15:30','17:00'] as $t)
                <button type="button"
                        onclick="mobSelectTime(this,'{{ $t }}')"
                        style="padding:8px;border-radius:12px;border:1px solid #e5e7eb;background:transparent;color:#4b5563;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;"
                        class="mob-time-btn">
                    {{ $t }}
                </button>
            @endforeach
        </div>

        {{-- Form --}}
        <form id="mob-booking-form" style="display:flex;flex-direction:column;gap:10px;">
            @csrf
            <input type="hidden" name="property_id" value="{{ $property->id }}">
            <input type="hidden" name="booking_date" id="mob_booking_date" value="{{ isset($activeDate) ? $activeDate : now()->format('Y-m-d') }}">
            <input type="hidden" name="booking_time" id="mob_booking_time" value="">
            <input type="text" name="contact_name" placeholder="Họ tên của bạn"
                   style="width:100%;padding:10px 14px;border:1px solid #e5e7eb;border-radius:12px;font-size:13px;outline:none;box-sizing:border-box;">
            <input type="tel" name="contact_phone" placeholder="Số điện thoại"
                   style="width:100%;padding:10px 14px;border:1px solid #e5e7eb;border-radius:12px;font-size:13px;outline:none;box-sizing:border-box;">
            <button type="submit"
                    style="width:100%;background:#0d9488;color:#fff;font-weight:700;padding:12px;border-radius:12px;border:none;font-size:14px;cursor:pointer;transition:opacity .2s;">
                Gửi yêu cầu đặt lịch
            </button>
        </form>
    </div>
</div>

<script>
// ── Gallery (Desktop) ────────────────────────────────────
const galleryImages = @json($allImages->values());
let currentImg     = 0;
let currentMainIdx = 0;

function switchMain(el, idx) {
    currentMainIdx = idx;
    const main = document.getElementById('main-gallery-img');
    if (main) main.src = galleryImages[idx];
    document.querySelectorAll('.thumbnail-wrap').forEach(t => {
        t.classList.remove('ring-[2.5px]', 'ring-teal-400', 'ring-offset-1');
    });
    document.querySelectorAll(`.thumbnail-wrap[data-idx="${idx}"]`).forEach(t => {
        t.classList.add('ring-[2.5px]', 'ring-teal-400', 'ring-offset-1');
    });
}

function openGallery(idx) {
    currentImg = idx;
    document.getElementById('gallery-modal').classList.replace('hidden','flex');
    updateModal();
}
function closeGallery() {
    document.getElementById('gallery-modal').classList.replace('flex','hidden');
}
function prevImg() { currentImg = (currentImg - 1 + galleryImages.length) % galleryImages.length; updateModal(); }
function nextImg() { currentImg = (currentImg + 1) % galleryImages.length; updateModal(); }
function updateModal() {
    document.getElementById('modal-img').src = galleryImages[currentImg];
    document.getElementById('modal-counter').textContent = (currentImg + 1) + ' / ' + galleryImages.length;
}
document.addEventListener('keydown', e => {
    if (document.getElementById('gallery-modal').classList.contains('flex')) {
        if (e.key==='ArrowLeft') prevImg();
        if (e.key==='ArrowRight') nextImg();
        if (e.key==='Escape') closeGallery();
    }
});

// ── Mobile Carousel ──────────────────────────────────────
(function() {
    const track  = document.getElementById('mobTrack');
    if (!track) return;
    const dots   = document.querySelectorAll('.mob-dot');
    const counter = document.getElementById('mobCounter');
    const total  = galleryImages.length;

    function updateDots(idx) {
        dots.forEach((d, i) => d.classList.toggle('active', i === idx));
        if (counter) counter.textContent = (idx + 1) + '/' + total;
    }

    track.addEventListener('scroll', () => {
        const idx = Math.round(track.scrollLeft / track.clientWidth);
        updateDots(idx);
    }, { passive: true });
})();

// ── Mobile description toggle ────────────────────────────
function mobToggleDesc() {
    const desc = document.getElementById('mobDescText');
    const icon = document.getElementById('mobSeeMoreIcon');
    const btn  = document.getElementById('mobSeeMoreBtn');
    const expanded = desc.classList.toggle('mob-clamped');
    // classList.toggle returns the NEW state; true = class was ADDED (now clamped)
    if (expanded) {
        icon.textContent = '⌄';
        btn.childNodes[0].textContent = 'Xem thêm ';
    } else {
        icon.textContent = '⌃';
        btn.childNodes[0].textContent = 'Thu gọn ';
    }
}

// ── Desktop Date & Time picker ───────────────────────────
function selectDate(btn, date) {
    document.querySelectorAll('.date-btn').forEach(b => {
        b.classList.remove('bg-teal-600','border-teal-600','text-white');
        b.classList.add('border-gray-200','text-gray-600');
    });
    btn.classList.add('bg-teal-600','border-teal-600','text-white');
    btn.classList.remove('border-gray-200','text-gray-600');
    document.getElementById('booking_date').value = date;
}
function selectTime(btn, time) {
    document.querySelectorAll('.time-btn').forEach(b => {
        b.classList.remove('bg-teal-600','border-teal-600','text-white');
    });
    btn.classList.add('bg-teal-600','border-teal-600','text-white');
    document.getElementById('booking_time').value = time;
}

// ── Mobile Date & Time picker ────────────────────────────
function mobSelectDate(btn, date) {
    document.querySelectorAll('.mob-date-btn').forEach(b => {
        b.style.background = 'transparent';
        b.style.borderColor = '#e5e7eb';
        b.style.color = '#4b5563';
    });
    btn.style.background = '#0d9488';
    btn.style.borderColor = '#0d9488';
    btn.style.color = '#fff';
    document.getElementById('mob_booking_date').value = date;
}
function mobSelectTime(btn, time) {
    document.querySelectorAll('.mob-time-btn').forEach(b => {
        b.style.background = 'transparent';
        b.style.borderColor = '#e5e7eb';
        b.style.color = '#4b5563';
    });
    btn.style.background = '#0d9488';
    btn.style.borderColor = '#0d9488';
    btn.style.color = '#fff';
    document.getElementById('mob_booking_time').value = time;
}

// ── Wishlist ─────────────────────────────────────────────
let _wishlisted = false;

function toggleWishlist(btn) {
    const icon = btn.querySelector('i');
    const saved = icon.classList.contains('fas');
    if (saved) {
        btn.innerHTML = '<i class="far fa-heart"></i> Lưu tin';
        btn.classList.remove('text-red-500','border-red-300');
    } else {
        btn.innerHTML = '<i class="fas fa-heart text-red-500"></i> <span class="text-red-500">Đã lưu</span>';
        btn.classList.add('border-red-300');
    }
    _doWishlistFetch();
}

function mobToggleWishlist() {
    _wishlisted = !_wishlisted;
    const icon = document.getElementById('mobSaveIcon');
    const btn  = document.getElementById('mobSaveBtn');
    if (_wishlisted) {
        icon.textContent = '❤️';
        btn.classList.add('saved');
    } else {
        icon.textContent = '♡';
        btn.classList.remove('saved');
    }
    _doWishlistFetch();
}

function _doWishlistFetch() {
    fetch('{{ route("wishlist.toggle") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({ property_id: {{ $property->id }} })
    }).then(r => r.json()).then(d => {
        if (!d.success && d.redirect) window.location.href = d.redirect;
    });
}

// ── Share ─────────────────────────────────────────────────
function shareProperty() { mobShare(); }
function mobShare() {
    if (navigator.share) {
        navigator.share({ title: '{{ addslashes($property->title) }}', url: window.location.href });
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => {
            // Simple toast
            const toast = document.createElement('div');
            toast.textContent = '✓ Đã sao chép link!';
            toast.style.cssText = 'position:fixed;bottom:90px;left:50%;transform:translateX(-50%);background:#0F3460;color:#fff;padding:8px 18px;border-radius:20px;font-size:13px;font-weight:600;z-index:9999;transition:opacity .3s;';
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 2000);
        });
    }
}

// ── Expand description (Desktop) ─────────────────────────
function expandDesc() {
    document.getElementById('desc-full').classList.remove('hidden');
    document.getElementById('desc-btn').remove();
}

// ── Desktop Booking form ──────────────────────────────────
document.getElementById('booking-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const name  = formData.get('contact_name').trim();
    const phone = formData.get('contact_phone').trim();
    const time  = formData.get('booking_time');
    if (!name || !phone) {
        Swal.fire({ title: 'Lỗi', text: 'Vui lòng nhập họ tên và số điện thoại.', icon: 'error', confirmButtonText: 'Đóng',
            customClass: { confirmButton: 'bg-primary text-white px-6 py-2.5 rounded-xl font-bold hover:opacity-90 transition-opacity' }, buttonsStyling: false });
        return;
    }
    if (!time) {
        Swal.fire({ title: 'Lỗi', text: 'Vui lòng chọn khung giờ xem nhà.', icon: 'error', confirmButtonText: 'Đóng',
            customClass: { confirmButton: 'bg-primary text-white px-6 py-2.5 rounded-xl font-bold hover:opacity-90 transition-opacity' }, buttonsStyling: false });
        return;
    }
    const btn = this.querySelector('button[type=submit]');
    const oldText = btn.textContent;
    btn.disabled = true; btn.textContent = 'Đang gửi...';
    fetch('{{ route("booking.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: formData
    }).then(r => r.json()).then(data => {
        btn.disabled = false; btn.textContent = oldText;
        if (data.success) {
            Swal.fire({ title: 'Đã gửi yêu cầu đặt lịch!', text: data.message, icon: 'success', confirmButtonText: 'Đóng',
                customClass: { confirmButton: 'bg-primary text-white px-6 py-2.5 rounded-xl font-bold hover:opacity-90 transition-opacity' }, buttonsStyling: false });
            this.reset();
        } else {
            Swal.fire({ title: 'Lỗi', text: data.message || 'Có lỗi xảy ra.', icon: 'error', confirmButtonText: 'Đóng',
                customClass: { confirmButton: 'bg-primary text-white px-6 py-2.5 rounded-xl font-bold hover:opacity-90 transition-opacity' }, buttonsStyling: false });
        }
    }).catch(() => {
        btn.disabled = false; btn.textContent = oldText;
        Swal.fire({ title: 'Lỗi', text: 'Không thể kết nối đến máy chủ.', icon: 'error', confirmButtonText: 'Đóng',
            customClass: { confirmButton: 'bg-primary text-white px-6 py-2.5 rounded-xl font-bold hover:opacity-90 transition-opacity' }, buttonsStyling: false });
    });
});

// ── Mobile Booking form ───────────────────────────────────
document.getElementById('mob-booking-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const name  = formData.get('contact_name').trim();
    const phone = formData.get('contact_phone').trim();
    const time  = formData.get('booking_time');
    if (!name || !phone || !time) {
        alert('Vui lòng nhập đầy đủ họ tên, số điện thoại và chọn khung giờ.');
        return;
    }
    const btn = this.querySelector('button[type=submit]');
    const oldText = btn.textContent;
    btn.disabled = true; btn.textContent = 'Đang gửi...';
    fetch('{{ route("booking.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: formData
    }).then(r => r.json()).then(data => {
        btn.disabled = false; btn.textContent = oldText;
        if (data.success) {
            document.getElementById('mob-booking-modal').classList.add('hidden');
            const toast = document.createElement('div');
            toast.textContent = '✓ Đã gửi yêu cầu đặt lịch!';
            toast.style.cssText = 'position:fixed;bottom:90px;left:50%;transform:translateX(-50%);background:#0d9488;color:#fff;padding:10px 20px;border-radius:20px;font-size:13px;font-weight:600;z-index:9999;';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
            this.reset();
        } else {
            alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
        }
    }).catch(() => {
        btn.disabled = false; btn.textContent = oldText;
        alert('Không thể kết nối đến máy chủ.');
    });
});
</script>
@endsection
