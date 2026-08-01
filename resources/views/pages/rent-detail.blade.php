@extends('layouts.app')

@php
    /* ── Share / SEO preview ── */
    $shareTitle = trim((string) ($property->title ?? ''));
    if (
        mb_strlen($shareTitle, 'UTF-8') > 6
        && $shareTitle === mb_strtoupper($shareTitle, 'UTF-8')
        && preg_match('/\p{L}/u', $shareTitle)
    ) {
        $lowerTitle = mb_strtolower($shareTitle, 'UTF-8');
        $shareTitle = mb_strtoupper(mb_substr($lowerTitle, 0, 1, 'UTF-8'), 'UTF-8')
            .mb_substr($lowerTitle, 1, null, 'UTF-8');
    }

    $shareLocation = trim((string) ($property->address ?? ''));
    if ($shareLocation === '') {
        $shareLocation = trim(($property->district ? $property->district.', ' : '').'Nha Trang');
    }

    if ($property->monthly_price) {
        $sharePrice = number_format($property->monthly_price / 1000000, 0, ',', '.').' triệu/tháng';
    } elseif ($property->price) {
        $sharePrice = number_format($property->price).' đ';
    } else {
        $sharePrice = '';
    }

    $shareSpecs = collect([
        $property->area ? (rtrim(rtrim(number_format((float) $property->area, 1, ',', '.'), '0'), ',').' m²') : null,
        isset($property->bedrooms) ? ($property->bedrooms.' phòng ngủ') : null,
        isset($property->bathrooms) ? ($property->bathrooms.' WC') : null,
        $property->type_label ?: null,
    ])->filter()->implode(' · ');

    $shareDescSnippet = Str::limit(
        preg_replace('/\s+/u', ' ', trim(strip_tags((string) ($property->description ?? '')))) ?: '',
        140,
        '…'
    );

    $shareMetaDescription = collect([$shareLocation, $sharePrice, $shareSpecs, $shareDescSnippet])
        ->filter()
        ->implode(' — ');

    $shareCoverPath = $property->cover_image_url
        ?: optional($property->media->where('media_type', 'image')->sortBy('display_order')->first())->file_url
        ?: '/images/hero-nhatrang.jpg';
    $shareCoverUrl = preg_match('#^https?://#i', $shareCoverPath)
        ? $shareCoverPath
        : url($shareCoverPath);
    // Relative path for JS (tránh APP_URL lệch domain local)
    $shareCoverRel = preg_match('#^https?://#i', $shareCoverPath)
        ? $shareCoverPath
        : (str_starts_with($shareCoverPath, '/') ? $shareCoverPath : '/'.$shareCoverPath);
    $sharePageUrl = route('rent.detail', $property->slug);

    $shareTextLines = array_filter([
        '🏠 '.$shareTitle,
        $shareLocation !== '' ? '📍 '.$shareLocation : null,
        $sharePrice !== '' ? '💰 '.$sharePrice : null,
        $shareSpecs !== '' ? '📐 '.$shareSpecs : null,
        $shareDescSnippet !== '' ? "\n".$shareDescSnippet : null,
        "\nXem chi tiết trên LANDTEK:",
    ]);
    $shareText = implode("\n", $shareTextLines);
    $shareShortText = collect([
        $shareLocation !== '' ? $shareLocation : null,
        $sharePrice !== '' ? $sharePrice : null,
        $shareSpecs !== '' ? $shareSpecs : null,
        $shareDescSnippet !== '' ? $shareDescSnippet : null,
    ])->filter()->implode(' · ');
@endphp

@section('title', $shareTitle.' — LANDTEK')
@section('description', Str::limit($shareMetaDescription, 160))

@section('meta')
    <link rel="canonical" href="{{ $sharePageUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="LANDTEK">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:url" content="{{ $sharePageUrl }}">
    <meta property="og:title" content="{{ $shareTitle }}{{ $sharePrice ? ' | '.$sharePrice : '' }}">
    <meta property="og:description" content="{{ Str::limit($shareMetaDescription, 200) }}">
    <meta property="og:image" content="{{ $shareCoverUrl }}">
    <meta property="og:image:alt" content="{{ $shareTitle }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $shareTitle }}{{ $sharePrice ? ' | '.$sharePrice : '' }}">
    <meta name="twitter:description" content="{{ Str::limit($shareMetaDescription, 200) }}">
    <meta name="twitter:image" content="{{ $shareCoverUrl }}">
@endsection

@section('content')
@php
    /* ── Collect images only (videos shown separately) ── */
    $allImages = collect();
    if ($property->cover_image_url) $allImages->push($property->cover_image_url);
    foreach ($property->media->where('media_type', 'image')->sortBy('display_order') as $m) {
        if ($m->file_url && !$allImages->contains($m->file_url)) $allImages->push($m->file_url);
    }
    // Legacy: media without type or mistyped as image path still in gallery; skip video MIME extensions
    foreach ($property->media->where('media_type', '!=', 'video')->sortBy('display_order') as $m) {
        if ($m->media_type === 'image') continue;
        if (!$m->file_url || $allImages->contains($m->file_url)) continue;
        $ext = strtolower(pathinfo($m->file_url, PATHINFO_EXTENSION));
        if (in_array($ext, ['mp4', 'mov', 'avi', 'webm'], true)) continue;
        $allImages->push($m->file_url);
    }
    if ($allImages->isEmpty()) $allImages->push('/images/hero-nhatrang.jpg');

    $videos = $property->media->where('media_type', 'video')->sortBy('display_order')->values();
    // Also catch video files wrongly stored as image
    foreach ($property->media as $m) {
        if ($m->media_type === 'video') continue;
        $ext = strtolower(pathinfo($m->file_url ?? '', PATHINFO_EXTENSION));
        if (in_array($ext, ['mp4', 'mov', 'avi', 'webm'], true) && $m->file_url) {
            $videos->push($m);
        }
    }

    $hasMapPin = is_numeric($property->lat) && is_numeric($property->lng);
    $googleMapsLink = $hasMapPin
        ? 'https://www.google.com/maps?q=' . rawurlencode($property->lat . ',' . $property->lng)
        : 'https://maps.google.com/?q=' . rawurlencode(($property->address ?? '') . ' ' . (($property->district ? $property->district . ', ' : '') . 'Nha Trang'));

    /* ── Labels ── */
    $typeLabel   = $property->type_label;
    $locationStr = ($property->district ? $property->district.', ' : '').'Nha Trang';

    // Address without duplication (demo: "Phước Hải, Nha Trang" once)
    $rawAddress = trim((string) ($property->address ?? ''));
    $addrLower = mb_strtolower($rawAddress, 'UTF-8');
    $districtLower = mb_strtolower((string) ($property->district ?? ''), 'UTF-8');
    if ($rawAddress === '') {
        $displayAddress = $locationStr;
    } elseif (
        str_contains($addrLower, 'nha trang')
        || ($districtLower !== '' && str_contains($addrLower, $districtLower))
    ) {
        $displayAddress = $rawAddress;
    } else {
        $displayAddress = $rawAddress.', '.$locationStr;
    }

    // Soften ALL-CAPS titles for readability
    $displayTitle = trim((string) $property->title);
    if (
        mb_strlen($displayTitle, 'UTF-8') > 6
        && $displayTitle === mb_strtoupper($displayTitle, 'UTF-8')
        && preg_match('/\p{L}/u', $displayTitle)
    ) {
        $lower = mb_strtolower($displayTitle, 'UTF-8');
        $displayTitle = mb_strtoupper(mb_substr($lower, 0, 1, 'UTF-8'), 'UTF-8').mb_substr($lower, 1, null, 'UTF-8');
    }

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
    $agentName    = $property->user?->name ?? 'Môi giới LANDTEK';
    $agentPhone   = $property->user?->phone ?? '';
    $nameParts = preg_split('/\s+/u', trim($agentName)) ?: [];
    if (count($nameParts) >= 2) {
        $agentInitial = mb_strtoupper(
            mb_substr($nameParts[0], 0, 1, 'UTF-8').mb_substr(end($nameParts), 0, 1, 'UTF-8'),
            'UTF-8'
        );
    } else {
        $agentInitial = mb_strtoupper(mb_substr($agentName, 0, 2, 'UTF-8'), 'UTF-8');
    }
    $agentListingCount = $agentListingCount ?? 0;

    // Spec helpers from utilities (demo-style feature grid)
    $utilNames = $property->utilities->pluck('name')->map(fn ($n) => mb_strtolower((string) $n, 'UTF-8'));
    $hasElevator = $utilNames->contains(fn ($n) => str_contains($n, 'thang máy') || str_contains($n, 'thang may'));
    $hasParking  = $utilNames->contains(fn ($n) => str_contains($n, 'đậu') || str_contains($n, 'đỗ') || str_contains($n, 'parking') || str_contains($n, 'gara'));
    $hasWifi     = $utilNames->contains(fn ($n) => str_contains($n, 'wifi') || str_contains($n, 'wi-fi'));

    $specItems = collect([
        ['icon' => '📐', 'label' => 'Diện tích', 'value' => $property->area ? ($property->area.' m²') : null],
        ['icon' => '🛏️', 'label' => 'Phòng ngủ', 'value' => $property->bedrooms !== null ? ($property->bedrooms.' phòng') : null],
        ['icon' => '🚿', 'label' => 'WC', 'value' => $property->bathrooms !== null ? ($property->bathrooms.' phòng') : null],
        ['icon' => '🏠', 'label' => 'Loại BĐS', 'value' => $typeLabel],
        ['icon' => '🏗️', 'label' => 'Dự án', 'value' => $property->project ?: null],
        ['icon' => '🌊', 'label' => 'Cách biển', 'value' => $property->distance_to_beach ? (number_format($property->distance_to_beach).'m') : null],
        ['icon' => '📅', 'label' => 'Thuê tối thiểu', 'value' => $property->min_rent_period ? ($property->min_rent_period.' tháng') : null],
        ['icon' => '🛗', 'label' => 'Thang máy', 'value' => $hasElevator ? 'Có' : null],
        ['icon' => '🅿️', 'label' => 'Chỗ đậu xe', 'value' => $hasParking ? 'Có' : null],
        ['icon' => '📶', 'label' => 'Wifi', 'value' => $hasWifi ? 'Có' : null],
        ['icon' => '📋', 'label' => 'Hình thức', 'value' => $property->transaction_type === 'sale' ? 'Mua bán' : 'Cho thuê'],
        ['icon' => '✅', 'label' => 'Trạng thái', 'value' => $property->status_label ?? null],
    ])->filter(fn ($s) => filled($s['value']))->values();
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
    left: 50%;
    right: auto;
    transform: translateX(-50%);
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
        background: #0F3460;
        color: #fff;
        cursor: pointer;
        text-decoration: none;
        transition: opacity .2s;
    }
    .mob-btn-call:hover { opacity: .88; color: #fff; }
    .mob-btn-zalo {
        flex: 1; height: 44px;
        border-radius: 12px;
        border: 1.5px solid #B9D2F0;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        background: #EBF3FF;
        color: #0f766e;
        cursor: pointer;
        text-decoration: none;
        transition: background .2s;
    }
    .mob-btn-zalo:hover { background: #EBF3FF; }
    /* Push content above sticky bar */
    .mob-bottom-spacer { height: 80px; }
}
@media (min-width: 1024px) {
    .mob-only { display: none !important; }
    .mob-bottom-bar { display: none !important; }
}
</style>

{{-- ────────────────────────── BREADCRUMB (desktop) ────────────────────────── --}}
<div class="bg-white border-b border-gray-100 py-3 hidden lg:block">
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

@php
    $prevProperty = $prevProperty ?? null;
    $nextProperty = $nextProperty ?? null;
@endphp

{{-- ══════════════════════════════════════════════════
     MOBILE LAYOUT (< lg) — hidden on desktop
══════════════════════════════════════════════════ --}}
<div class="lg:hidden mob-only" id="mob-layout">

    {{-- ── 1. MOBILE CAROUSEL ── --}}
    <div class="mob-carousel group" id="mobCarousel">

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

        {{-- Listing Back / Next on main photo --}}
        <x-property-detail-nav
            variant="overlay"
            :prev-url="$prevProperty ? route('rent.detail', $prevProperty->slug) : null"
            :next-url="$nextProperty ? route('rent.detail', $nextProperty->slug) : null"
            :prev-title="$prevProperty?->title"
            :next-title="$nextProperty?->title"
        />

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

    @if($videos->isNotEmpty())
    <div class="mob-section" style="background:#0F3460;border-bottom:none;padding-top:14px;padding-bottom:16px">
        <div class="mob-section-title" style="color:#fff;display:flex;align-items:center;gap:8px;margin-bottom:4px">
            <span style="display:inline-flex;width:28px;height:28px;border-radius:8px;background:#F59E0B;color:#0F3460;align-items:center;justify-content:center;font-size:12px">▶</span>
            Video tham quan
        </div>
        <p style="font-size:11px;color:rgba(255,255,255,.55);margin:0 0 12px">Khối riêng — không nằm trong album ảnh phía trên</p>
        <div style="display:flex;flex-direction:column;gap:12px">
            @foreach($videos as $vid)
                <div style="border-radius:12px;overflow:hidden;background:#000;aspect-ratio:16/9">
                    <video src="{{ $vid->file_url }}" controls playsinline preload="metadata"
                           style="width:100%;height:100%;object-fit:contain;display:block;background:#000"></video>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── 2. PRICE & TITLE BLOCK ── --}}
    <div class="mob-price-block">
        <div class="mob-price-row">
            <span class="mob-price-big">{{ $priceFullLabel }}</span>
            <span class="mob-price-badge">✓ Đã xác thực</span>
        </div>
        <div class="mob-title">{{ $displayTitle }}</div>
        <div class="mob-addr">
            <span>📍</span>
            <span>{{ $displayAddress }}</span>
        </div>

        {{-- Quick stats --}}
        <div class="mob-stats-row">
            <div class="mob-stat-item">
                <div class="mob-stat-val">{{ $property->area ? $property->area.' m²' : '—' }}</div>
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
            @foreach($specItems as $spec)
                <div class="mob-spec-item">
                    <div class="mob-spec-icon">{{ $spec['icon'] }}</div>
                    <div>
                        <div class="mob-spec-lbl">{{ $spec['label'] }}</div>
                        <div class="mob-spec-val">{{ $spec['value'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── 4. MÔ TẢ ── --}}
    @if($property->description)
    <div class="mob-section">
        <div class="mob-section-title">Mô tả</div>
        <div class="mob-desc-text mob-clamped" id="mobDescText">{{ trim($property->description) }}</div>
        <button type="button" class="mob-see-more-btn" id="mobSeeMoreBtn" onclick="mobToggleDesc()">
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
        <p class="mb-2 text-[12px] text-gray-500">📍 {{ $displayAddress }}</p>
        <div style="position:relative;">
            <x-map-static-view :lat="$property->lat" :lng="$property->lng" :height="160" rounded="rounded-xl" />
            <a href="{{ $googleMapsLink }}" target="_blank" rel="noopener"
               style="position:absolute;left:8px;bottom:8px;z-index:500;background:#fff;color:#0F3460;font-size:10.5px;font-weight:700;padding:4px 10px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.15);text-decoration:none;">
                Mở Google Maps
            </a>
        </div>
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
                    @if($agentListingCount > 0)
                        {{ $agentListingCount }} tin đang đăng ·
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
        <div class="group relative flex-1 min-w-0 rounded-2xl overflow-hidden bg-gray-200 cursor-pointer"
             onclick="openGallery(currentMainIdx)">
            <img id="main-gallery-img"
                 src="{{ $allImages->first() }}"
                 alt="{{ $property->title }}"
                 class="w-full h-full object-cover transition-all duration-400 hover:scale-[1.02]">

            <x-property-detail-nav
                variant="overlay"
                :prev-url="$prevProperty ? route('rent.detail', $prevProperty->slug) : null"
                :next-url="$nextProperty ? route('rent.detail', $nextProperty->slug) : null"
                :prev-title="$prevProperty?->title"
                :next-title="$nextProperty?->title"
            />
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

@if($videos->isNotEmpty())
<div class="mb-6 rounded-2xl border border-navy/10 bg-navy p-5 md:p-6 text-white">
    <div class="mb-1 flex items-center gap-2.5">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-amber-brand text-navy">
            <i class="fas fa-play"></i>
        </span>
        <div>
            <h2 class="text-lg font-bold leading-tight">Video tham quan</h2>
            <p class="text-xs text-navy-muted">Khối riêng — không nằm trong album ảnh phía trên</p>
        </div>
    </div>
    <div class="mt-4 grid gap-4 {{ $videos->count() > 1 ? 'md:grid-cols-2' : '' }}">
        @foreach($videos as $vid)
            <div class="overflow-hidden rounded-xl bg-black aspect-video">
                <video src="{{ $vid->file_url }}" controls playsinline preload="metadata"
                       class="h-full w-full object-contain bg-black"></video>
            </div>
        @endforeach
    </div>
</div>
@endif

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
            {{ $displayTitle }}
        </h1>

        {{-- Location --}}
        <p class="flex items-center gap-1.5 text-sm text-gray-500 mb-5">
            <i class="fas fa-map-marker-alt text-teal-500 text-xs"></i>
            {{ $displayAddress }}
        </p>

        {{-- Price --}}
        <div class="flex items-baseline gap-2 mb-6">
            <span class="text-3xl font-extrabold text-navy">{{ $priceBig }}</span>
            @if($priceSub)
                <span class="text-gray-500 text-base font-normal">{{ $priceSub }}</span>
            @endif
            <span class="ml-2 inline-flex items-center gap-1 rounded-md bg-green-50 px-2 py-0.5 text-[11px] font-bold text-green-700">✓ Đã xác thực</span>
        </div>

        {{-- Stats: quick row --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
            <div class="flex flex-col items-center justify-center bg-white rounded-xl border border-gray-100 shadow-sm py-4 px-2 gap-2">
                <i class="fas fa-vector-square text-navy text-xl"></i>
                <span class="text-sm font-semibold text-gray-700 text-center">{{ $property->area ? $property->area.' m²' : '—' }}</span>
            </div>
            <div class="flex flex-col items-center justify-center bg-white rounded-xl border border-gray-100 shadow-sm py-4 px-2 gap-2">
                <i class="fas fa-bed text-navy text-xl"></i>
                <span class="text-sm font-semibold text-gray-700 text-center">{{ $property->bedrooms ?? '—' }} phòng ngủ</span>
            </div>
            <div class="flex flex-col items-center justify-center bg-white rounded-xl border border-gray-100 shadow-sm py-4 px-2 gap-2">
                <i class="fas fa-bath text-navy text-xl"></i>
                <span class="text-sm font-semibold text-gray-700 text-center">{{ $property->bathrooms ?? '—' }} WC</span>
            </div>
            @if($property->distance_to_beach)
                <div class="flex flex-col items-center justify-center bg-white rounded-xl border border-gray-100 shadow-sm py-4 px-2 gap-2">
                    <i class="fas fa-water text-navy text-xl"></i>
                    <span class="text-sm font-semibold text-gray-700 text-center">{{ number_format($property->distance_to_beach) }}m tới biển</span>
                </div>
            @else
                <div class="flex flex-col items-center justify-center bg-white rounded-xl border border-gray-100 shadow-sm py-4 px-2 gap-2">
                    <i class="fas fa-home text-navy text-xl"></i>
                    <span class="text-sm font-semibold text-gray-700 text-center">{{ $typeLabel }}</span>
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

        {{-- Đặc điểm BĐS --}}
        <div class="mb-8">
            <h2 class="text-lg font-bold text-gray-900 mb-3">Đặc điểm bất động sản</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($specItems as $spec)
                    <div class="flex items-center gap-2.5 rounded-xl border border-gray-100 bg-white px-3 py-2.5 shadow-sm">
                        <span class="text-base shrink-0">{{ $spec['icon'] }}</span>
                        <div class="min-w-0">
                            <div class="text-[11px] text-gray-400">{{ $spec['label'] }}</div>
                            <div class="text-sm font-semibold text-gray-800 truncate">{{ $spec['value'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Description --}}
        @if($property->description)
            <div class="mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-3">Mô tả</h2>
                <div class="text-gray-600 text-sm leading-relaxed" id="desc-content">
                    {!! nl2br(e(Str::limit($property->description, 400))) !!}
                    @if(mb_strlen($property->description) > 400)
                        <button onclick="expandDesc()" id="desc-btn"
                                class="text-navy hover:underline font-medium ml-1">Xem thêm</button>
                        <span id="desc-full" class="hidden">{!! nl2br(e(mb_substr($property->description, 400))) !!}</span>
                    @endif
                </div>
            </div>
        @endif

        {{-- Utilities --}}
        @if($property->utilities->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-3">Tiện ích</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($property->utilities as $util)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#EBF3FF] px-3 py-1.5 text-xs font-semibold text-navy">
                            @if($util->icon_name)
                                <i class="{{ $util->icon_name }} text-[11px]"></i>
                            @else
                                ✓
                            @endif
                            {{ $util->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Map --}}
        <div class="mb-8">
            <div class="mb-3 flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-gray-900">Vị trí</h2>
                <a href="{{ $googleMapsLink }}" target="_blank" rel="noopener"
                   class="text-xs font-semibold text-navy hover:underline">
                    Mở Google Maps <i class="fas fa-external-link-alt text-[10px]"></i>
                </a>
            </div>
            <p class="mb-2 text-sm text-gray-500">📍 {{ $displayAddress }}</p>
            <x-map-static-view :lat="$property->lat" :lng="$property->lng" :height="300" class="shadow-sm" />
        </div>

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
                    <p class="text-sm font-bold text-gray-900 leading-tight">{{ $agentName }} <span class="ml-1 rounded bg-amber-50 px-1.5 py-0.5 text-[9px] font-bold text-amber-800">SILVER</span></p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        @if($agentListingCount > 0)
                            {{ $agentListingCount }} tin đang đăng ·
                        @endif
                        Phản hồi nhanh
                    </p>
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
                            style="flex-shrink:0;display:flex;flex-direction:column;align-items:center;width:44px;padding:10px 0;border-radius:12px;border:1px solid {{ $isAD2 ? '#0F3460' : '#e5e7eb' }};background:{{ $isAD2 ? '#0F3460' : 'transparent' }};color:{{ $isAD2 ? '#fff' : '#4b5563' }};font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;"
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
                    style="width:100%;background:#0F3460;color:#fff;font-weight:700;padding:12px;border-radius:12px;border:none;font-size:14px;cursor:pointer;transition:opacity .2s;">
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
    if (!desc || !btn) return;
    const expanded = desc.classList.toggle('mob-clamped');
    if (expanded) {
        if (icon) icon.textContent = '⌄';
        btn.childNodes[0].textContent = 'Xem thêm ';
    } else {
        if (icon) icon.textContent = '⌃';
        btn.childNodes[0].textContent = 'Thu gọn ';
    }
}
document.addEventListener('DOMContentLoaded', () => {
    const desc = document.getElementById('mobDescText');
    const btn = document.getElementById('mobSeeMoreBtn');
    if (!desc || !btn) return;
    // Hide "Xem thêm" when text fits without clamping
    if (desc.scrollHeight <= desc.clientHeight + 2) {
        btn.style.display = 'none';
        desc.classList.remove('mob-clamped');
    }
});

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
    btn.style.background = '#0F3460';
    btn.style.borderColor = '#0F3460';
    btn.style.color = '#fff';
    document.getElementById('mob_booking_date').value = date;
}
function mobSelectTime(btn, time) {
    document.querySelectorAll('.mob-time-btn').forEach(b => {
        b.style.background = 'transparent';
        b.style.borderColor = '#e5e7eb';
        b.style.color = '#4b5563';
    });
    btn.style.background = '#0F3460';
    btn.style.borderColor = '#0F3460';
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
    const shareTitle = @json($shareTitle.($sharePrice ? ' | '.$sharePrice : ''));
    const shareText = @json($shareText);
    const shortText = @json($shareShortText);
    const coverUrl = (() => {
        const raw = @json($shareCoverRel);
        if (!raw) return '';
        if (/^https?:\/\//i.test(raw)) return raw;
        return window.location.origin + (raw.startsWith('/') ? raw : '/' + raw);
    })();
    const shareUrl = window.location.href;
    const fullMessage = shareText + "\n" + shareUrl;

    const showToast = (msg) => {
        const toast = document.createElement('div');
        toast.textContent = msg;
        toast.style.cssText = 'position:fixed;bottom:90px;left:50%;transform:translateX(-50%);background:#0F3460;color:#fff;padding:8px 18px;border-radius:20px;font-size:13px;font-weight:600;z-index:10001;transition:opacity .3s;';
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 2200);
    };

    const legacyCopy = (text) => {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.setAttribute('readonly', '');
        ta.style.cssText = 'position:fixed;left:-9999px;top:0;opacity:0;';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        ta.setSelectionRange(0, ta.value.length);
        let ok = false;
        try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
        document.body.removeChild(ta);
        return ok;
    };

    const copyShareText = async () => {
        if (navigator.clipboard && window.isSecureContext) {
            try {
                await navigator.clipboard.writeText(fullMessage);
                return true;
            } catch (e) {}
        }
        return legacyCopy(fullMessage);
    };

    let shareEscHandler = null;

    const closeShareModal = () => {
        if (shareEscHandler) {
            document.removeEventListener('keydown', shareEscHandler);
            shareEscHandler = null;
        }
        const el = document.getElementById('landtek-share-modal');
        if (el) el.remove();
        document.body.style.overflow = '';
    };

    const openShareModal = () => {
        closeShareModal();
        const u = encodeURIComponent(shareUrl);
        const t = encodeURIComponent(shareTitle);
        const txt = encodeURIComponent(fullMessage);
        const links = {
            facebook: 'https://www.facebook.com/sharer/sharer.php?u=' + u,
            messenger: 'https://www.facebook.com/dialog/send?link=' + u + '&app_id=966242223397117&redirect_uri=' + u,
            whatsapp: 'https://wa.me/?text=' + txt,
            telegram: 'https://t.me/share/url?url=' + u + '&text=' + t,
            twitter: 'https://twitter.com/intent/tweet?url=' + u + '&text=' + t,
            linkedin: 'https://www.linkedin.com/sharing/share-offsite/?url=' + u,
            email: 'mailto:?subject=' + t + '&body=' + txt,
        };
        const btnStyle = 'text-align:center;text-decoration:none;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:11px 8px;font-size:12px;font-weight:700;color:#334155;cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:6px;';

        const overlay = document.createElement('div');
        overlay.id = 'landtek-share-modal';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.style.cssText = 'position:fixed;inset:0;z-index:10000;background:rgba(15,23,42,.5);display:flex;align-items:center;justify-content:center;padding:16px;';
        overlay.innerHTML = `
            <div class="landtek-share-panel" style="width:100%;max-width:440px;background:#fff;border-radius:18px;overflow:auto;max-height:min(90vh,640px);box-shadow:0 16px 48px rgba(0,0,0,.25);animation:landtekShareIn .2s ease;">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px 8px;">
                    <strong style="font-size:16px;color:#0F3460;">Chia sẻ tin đăng</strong>
                    <button type="button" data-close style="width:32px;height:32px;border:0;border-radius:999px;background:#f1f5f9;color:#64748b;font-size:18px;line-height:1;cursor:pointer;">×</button>
                </div>
                <div style="display:flex;gap:12px;padding:8px 16px 14px;border-bottom:1px solid #f1f5f9;">
                    <img src="${coverUrl.replace(/"/g, '&quot;')}" alt="" style="width:88px;height:88px;object-fit:cover;border-radius:12px;background:#e2e8f0;flex-shrink:0;" onerror="this.style.display='none'">
                    <div style="min-width:0;flex:1;">
                        <div style="font-size:14px;font-weight:700;color:#0f172a;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">${shareTitle.replace(/</g,'&lt;')}</div>
                        <div style="margin-top:6px;font-size:12px;color:#64748b;line-height:1.4;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">${(shortText || shareText).replace(/</g,'&lt;')}</div>
                    </div>
                </div>
                <div style="padding:14px 16px 18px;display:grid;gap:10px;">
                    <button type="button" data-system style="display:none;width:100%;background:#0F3460;color:#fff;border:0;border-radius:12px;padding:12px;font-size:14px;font-weight:700;cursor:pointer;">
                        <i class="fas fa-share-alt" style="margin-right:6px;"></i> Chia sẻ qua Windows / thiết bị
                    </button>
                    <button type="button" data-copy style="width:100%;background:#EBF3FF;color:#0F3460;border:0;border-radius:12px;padding:12px;font-size:14px;font-weight:700;cursor:pointer;">
                        <i class="fas fa-link" style="margin-right:6px;"></i> Sao chép nội dung &amp; link
                    </button>
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;">
                        <button type="button" data-zalo style="${btnStyle}color:#0068ff;">
                            <span style="width:28px;height:28px;border-radius:8px;background:#0068ff;color:#fff;display:grid;place-items:center;font-size:11px;font-weight:800;">Z</span>
                            Zalo
                        </button>
                        <a href="${links.facebook}" target="_blank" rel="noopener" style="${btnStyle}color:#1d4ed8;">
                            <i class="fab fa-facebook" style="font-size:22px;color:#1877f2;"></i>Facebook
                        </a>
                        <a href="${links.messenger}" target="_blank" rel="noopener" style="${btnStyle}">
                            <i class="fab fa-facebook-messenger" style="font-size:22px;color:#0084ff;"></i>Messenger
                        </a>
                        <a href="${links.whatsapp}" target="_blank" rel="noopener" style="${btnStyle}">
                            <i class="fab fa-whatsapp" style="font-size:22px;color:#25d366;"></i>WhatsApp
                        </a>
                        <a href="${links.telegram}" target="_blank" rel="noopener" style="${btnStyle}">
                            <i class="fab fa-telegram" style="font-size:22px;color:#229ed9;"></i>Telegram
                        </a>
                        <a href="${links.twitter}" target="_blank" rel="noopener" style="${btnStyle}">
                            <i class="fab fa-x-twitter" style="font-size:20px;color:#111;"></i>X / Twitter
                        </a>
                        <a href="${links.linkedin}" target="_blank" rel="noopener" style="${btnStyle}">
                            <i class="fab fa-linkedin" style="font-size:22px;color:#0a66c2;"></i>LinkedIn
                        </a>
                        <a href="${links.email}" style="${btnStyle}">
                            <i class="fas fa-envelope" style="font-size:20px;color:#64748b;"></i>Email
                        </a>
                    </div>
                </div>
            </div>
            <style>
                @keyframes landtekShareIn{from{transform:translateY(12px);opacity:0}to{transform:translateY(0);opacity:1}}
                @media (max-width:1023px){
                    #landtek-share-modal{align-items:flex-end!important;padding:12px!important;padding-bottom:max(12px,env(safe-area-inset-bottom))!important}
                    #landtek-share-modal .landtek-share-panel{border-radius:18px 18px 14px 14px!important}
                }
            </style>
        `;

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay || e.target.closest('[data-close]')) closeShareModal();
        });
        shareEscHandler = (e) => {
            if (e.key === 'Escape' || e.key === 'Esc') {
                e.preventDefault();
                closeShareModal();
            }
        };
        document.addEventListener('keydown', shareEscHandler);

        overlay.querySelector('[data-copy]').addEventListener('click', async () => {
            const ok = await copyShareText();
            showToast(ok ? '✓ Đã sao chép nội dung chia sẻ!' : 'Không sao chép được — thử chọn text thủ công');
            if (ok) closeShareModal();
        });

        const sysBtn = overlay.querySelector('[data-system]');
        if (typeof navigator.share === 'function' && window.isSecureContext) {
            sysBtn.style.display = 'block';
            sysBtn.addEventListener('click', () => {
                navigator.share({ title: shareTitle, url: shareUrl }).then(closeShareModal).catch((err) => {
                    if (err && err.name === 'AbortError') return;
                    showToast('Thiết bị không mở được cửa sổ chia sẻ');
                });
            });
        }

        overlay.querySelector('[data-zalo]').addEventListener('click', async () => {
            await copyShareText();
            window.location.href = 'zalo://';
            showToast('Đã sao chép nội dung — dán vào Zalo để gửi');
        });

        document.body.style.overflow = 'hidden';
        document.body.appendChild(overlay);
        const closeBtn = overlay.querySelector('[data-close]');
        if (closeBtn) closeBtn.focus();
    };

    // Luôn mở modal LANDTEK (cover + Zalo/FB/WA...) — giống local.
    // Share Windows chỉ là nút phụ trong modal (HTTPS), không mở trước để tránh lệch layout production.
    openShareModal();
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
            toast.style.cssText = 'position:fixed;bottom:90px;left:50%;transform:translateX(-50%);background:#0F3460;color:#fff;padding:10px 20px;border-radius:20px;font-size:13px;font-weight:600;z-index:9999;';
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
