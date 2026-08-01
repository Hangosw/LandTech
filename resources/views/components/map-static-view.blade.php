@props([
    'lat' => null,
    'lng' => null,
    'height' => 300,
    'class' => '',
    'rounded' => 'rounded-2xl',
    'marker' => null, // null = auto (pin when lat/lng valid)
])

@php
    $defaultLat = 12.2388;
    $defaultLng = 109.1967;
    $hasPin = is_numeric($lat) && is_numeric($lng);
    $viewLat = $hasPin ? (float) $lat : $defaultLat;
    $viewLng = $hasPin ? (float) $lng : $defaultLng;
    $showMarker = $marker === null ? $hasPin : (bool) $marker;
    $mapId = 'map-view-' . uniqid();
@endphp

@once
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
        <style>
            .leaflet-container { width: 100%; height: 100%; z-index: 0; background: #e8eef5; }
            .leaflet-container img { max-width: none !important; }
            .leaflet-pane, .leaflet-tile, .leaflet-marker-icon, .leaflet-marker-shadow {
                max-width: none !important;
            }
            .landtek-map-static { position: relative; overflow: hidden; border: 1px solid #f3f4f6; background: #f3f4f6; }
        </style>
    @endpush
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
        <script>
            (function () {
                function waitForLeaflet(cb, tries) {
                    tries = tries || 0;
                    if (typeof L !== 'undefined') return cb();
                    if (tries > 40) return;
                    setTimeout(function () { waitForLeaflet(cb, tries + 1); }, 100);
                }

                function mountMap(el) {
                    if (el.dataset.mapReady === '1') return;
                    var lat = parseFloat(el.dataset.lat);
                    var lng = parseFloat(el.dataset.lng);
                    if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
                    var zoom = parseInt(el.dataset.zoom || '16', 10);
                    var withMarker = el.dataset.marker === '1';

                    waitForLeaflet(function () {
                        if (el.dataset.mapReady === '1') return;
                        // Skip zero-size (hidden layout) — observer will retry when visible
                        if (el.offsetWidth < 10 || el.offsetHeight < 10) return;
                        el.dataset.mapReady = '1';
                        var map = L.map(el, {
                            scrollWheelZoom: false,
                            dragging: !L.Browser.mobile
                        }).setView([lat, lng], zoom);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; OpenStreetMap'
                        }).addTo(map);
                        if (withMarker) {
                            L.marker([lat, lng]).addTo(map);
                        }
                        el._landtekMap = map;
                        setTimeout(function () { map.invalidateSize(); }, 50);
                        setTimeout(function () { map.invalidateSize(); }, 300);
                    });
                }

                function watchMaps() {
                    document.querySelectorAll('[data-landtek-map-static]').forEach(function (el) {
                        if (el.dataset.mapObserved === '1') return;
                        el.dataset.mapObserved = '1';
                        if ('IntersectionObserver' in window) {
                            var io = new IntersectionObserver(function (entries) {
                                entries.forEach(function (entry) {
                                    if (entry.isIntersecting) {
                                        mountMap(el);
                                        if (el._landtekMap) el._landtekMap.invalidateSize();
                                    }
                                });
                            }, { rootMargin: '40px' });
                            io.observe(el);
                        } else {
                            mountMap(el);
                        }
                    });
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', watchMaps);
                } else {
                    watchMaps();
                }
                window.addEventListener('load', watchMaps);
                window.addEventListener('resize', function () {
                    document.querySelectorAll('[data-landtek-map-static]').forEach(function (el) {
                        mountMap(el);
                        if (el._landtekMap) el._landtekMap.invalidateSize();
                    });
                });
            })();
        </script>
    @endpush
@endonce

<div class="relative {{ $class }}">
    <div
        id="{{ $mapId }}"
        data-landtek-map-static
        data-lat="{{ $viewLat }}"
        data-lng="{{ $viewLng }}"
        data-zoom="{{ $hasPin ? 16 : 13 }}"
        data-marker="{{ $showMarker ? '1' : '0' }}"
        class="landtek-map-static {{ $rounded }}"
        style="height:{{ (int) $height }}px;width:100%;"
        role="img"
        aria-label="Bản đồ vị trí"
    ></div>
    @if(!$hasPin)
        <div class="pointer-events-none absolute inset-0 z-[400] flex items-center justify-center {{ $rounded }} bg-white/35">
            <span class="rounded-lg bg-white/95 px-3 py-1.5 text-xs font-semibold text-gray-600 shadow">
                Chưa ghim vị trí chính xác
            </span>
        </div>
    @endif
</div>
