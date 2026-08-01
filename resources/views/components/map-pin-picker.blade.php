@php
    $mapProperty = $targetProp ?? $property ?? null;
    $lat = old('lat', $lat ?? ($mapProperty->lat ?? null));
    $lng = old('lng', $lng ?? ($mapProperty->lng ?? null));
    $defaultLat = 12.2388;
    $defaultLng = 109.1967;
    $mapId = 'map-pin-' . uniqid();
    $googleApiKey = config('services.google.maps_api_key');
    $areaHints = collect(config('nhatrang_areas', []))
        ->filter(fn ($a) => ($a['value'] ?? '') !== 'Khác')
        ->pluck('label')
        ->values()
        ->all();
@endphp

@once
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
        <style>
            /* Leaflet preflight fixes */
            .leaflet-container { width: 100%; height: 100%; z-index: 0; background: #e8eef5; }
            .leaflet-container img { max-width: none !important; }
            .leaflet-pane, .leaflet-tile, .leaflet-marker-icon, .leaflet-marker-shadow {
                max-width: none !important;
            }
            /* Google Map controls styling */
            .gm-style-iw { font-family: inherit; }
        </style>
    @endpush
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
        @if (!empty($googleApiKey))
            <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleApiKey }}&libraries=places&language=vi&region=VN" async defer></script>
        @endif
        <script>
            window.LandtekMapPin = {
                waitForLeaflet(cb, tries) {
                    tries = tries || 0;
                    if (typeof L !== 'undefined') return cb();
                    if (tries > 40) return console.error('Leaflet failed to load');
                    setTimeout(() => window.LandtekMapPin.waitForLeaflet(cb, tries + 1), 100);
                },
                async searchOSM(query) {
                    const q = (query || '').trim();
                    if (q.length < 2) return [];
                    const params = new URLSearchParams({
                        q: q.includes('Nha Trang') ? q : (q + ', Nha Trang, Việt Nam'),
                        format: 'json',
                        addressdetails: '1',
                        limit: '6',
                        countrycodes: 'vn',
                        'accept-language': 'vi',
                        viewbox: '109.05,12.40,109.35,12.10',
                        bounded: '0'
                    });
                    const res = await fetch('https://nominatim.openstreetmap.org/search?' + params.toString(), {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (!res.ok) throw new Error('Geocode failed');
                    return await res.json();
                }
            };
        </script>
    @endpush
@endonce

<div
    class="space-y-3"
    x-data="{
        lat: {{ $lat !== null && $lat !== '' ? (float) $lat : 'null' }},
        lng: {{ $lng !== null && $lng !== '' ? (float) $lng : 'null' }},
        defaultLat: {{ $defaultLat }},
        defaultLng: {{ $defaultLng }},
        map: null,
        marker: null,
        isGoogle: false,
        status: 'Đang tải bản đồ…',
        query: '',
        results: [],
        searching: false,
        searchError: '',
        showResults: false,
        hints: @js($areaHints),
        get coordLabel() {
            if (this.lat == null || this.lng == null) return '';
            return Number(this.lat).toFixed(5) + ', ' + Number(this.lng).toFixed(5);
        },
        get googleMapsUrl() {
            if (this.lat == null || this.lng == null) return '#';
            return 'https://www.google.com/maps?q=' + this.lat + ',' + this.lng;
        },
        init() {
            this.$nextTick(() => {
                let attempts = 0;
                const checkEngine = () => {
                    const el = document.getElementById(@js($mapId));
                    if (!el) { this.status = 'Không tìm thấy khung bản đồ'; return; }
                    
                    if (typeof window.google !== 'undefined' && typeof window.google.maps !== 'undefined') {
                        this.isGoogle = true;
                        this.mountGoogle(el);
                    } else if (attempts < 15 && @js(!empty($googleApiKey))) {
                        attempts++;
                        setTimeout(checkEngine, 150);
                    } else {
                        this.isGoogle = false;
                        if (typeof window.LandtekMapPin === 'undefined' || typeof L === 'undefined') {
                            window.LandtekMapPin
                                ? window.LandtekMapPin.waitForLeaflet(() => this.mountLeaflet(el))
                                : setTimeout(() => this.init(), 150);
                        } else {
                            this.mountLeaflet(el);
                        }
                    }
                };
                checkEngine();
            });
        },
        mountGoogle(el) {
            if (this.map) return;
            try {
                const startLat = this.lat ?? this.defaultLat;
                const startLng = this.lng ?? this.defaultLng;
                
                this.map = new google.maps.Map(el, {
                    center: { lat: startLat, lng: startLng },
                    zoom: this.lat != null ? 16 : 13,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: true,
                    zoomControl: true,
                });

                if (this.lat != null && this.lng != null) {
                    this.setMarker(this.lat, this.lng, false);
                    this.reverseGeocode(this.lat, this.lng);
                } else {
                    this.status = 'Tìm địa chỉ hoặc click/kéo ghim trên bản đồ Google Maps';
                }

                this.map.addListener('click', (e) => {
                    const clickLat = e.latLng.lat();
                    const clickLng = e.latLng.lng();
                    this.setMarker(clickLat, clickLng, true);
                    this.reverseGeocode(clickLat, clickLng);
                });
            } catch (err) {
                console.error('Google Maps init error, falling back to Leaflet', err);
                this.isGoogle = false;
                this.mountLeaflet(el);
            }
        },
        mountLeaflet(el) {
            if (this.map) return;
            try {
                const startLat = this.lat ?? this.defaultLat;
                const startLng = this.lng ?? this.defaultLng;
                const lmap = L.map(el, { scrollWheelZoom: false }).setView([startLat, startLng], this.lat ? 16 : 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(lmap);
                setTimeout(() => lmap.invalidateSize(), 100);
                setTimeout(() => lmap.invalidateSize(), 400);

                this.map = lmap;
                if (this.lat != null && this.lng != null) {
                    this.setMarker(this.lat, this.lng, false);
                    this.reverseGeocode(this.lat, this.lng);
                } else {
                    this.status = 'Tìm địa chỉ hoặc click bản đồ để ghim';
                }
                this.map.on('click', (e) => {
                    const clickLat = e.latlng.lat;
                    const clickLng = e.latlng.lng;
                    this.setMarker(clickLat, clickLng, true);
                    this.reverseGeocode(clickLat, clickLng);
                });
            } catch (err) {
                console.error(err);
                this.status = 'Lỗi khởi tạo bản đồ';
            }
        },
        async runSearch() {
            const q = this.query.trim();
            if (q.length < 2) {
                this.searchError = 'Nhập ít nhất 2 ký tự';
                this.results = [];
                this.showResults = true;
                return;
            }
            this.searching = true;
            this.searchError = '';
            this.showResults = true;

            if (this.isGoogle && window.google && window.google.maps) {
                const fullQuery = q.includes('Nha Trang') || q.includes('Khánh Hòa') ? q : (q + ', Nha Trang, Việt Nam');
                
                // Try Places Autocomplete Service first for rich location suggestions
                if (google.maps.places && google.maps.places.AutocompleteService) {
                    const service = new google.maps.places.AutocompleteService();
                    service.getPlacePredictions({
                        input: fullQuery,
                        componentRestrictions: { country: 'vn' }
                    }, (predictions, status) => {
                        this.searching = false;
                        if (status === google.maps.places.PlacesServiceStatus.OK && predictions && predictions.length) {
                            this.results = predictions.map(p => ({
                                place_id: p.place_id,
                                label: p.description
                            }));
                        } else {
                            // Fallback to Google Geocoder
                            this.runGoogleGeocodeSearch(fullQuery);
                        }
                    });
                    return;
                } else {
                    this.runGoogleGeocodeSearch(fullQuery);
                    return;
                }
            }

            // Fallback Leaflet / OSM search
            try {
                const data = await window.LandtekMapPin.searchOSM(q);
                this.results = (data || []).map(item => ({
                    lat: parseFloat(item.lat),
                    lng: parseFloat(item.lon),
                    label: item.display_name
                }));
                if (!this.results.length) {
                    this.searchError = 'Không tìm thấy địa điểm phù hợp';
                }
            } catch (e) {
                console.error(e);
                this.results = [];
                this.searchError = 'Không tìm được địa điểm, thử lại sau';
            } finally {
                this.searching = false;
            }
        },
        runGoogleGeocodeSearch(query) {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ address: query, componentRestrictions: { country: 'VN' } }, (results, status) => {
                this.searching = false;
                if (status === 'OK' && results && results.length) {
                    this.results = results.map(r => ({
                        lat: r.geometry.location.lat(),
                        lng: r.geometry.location.lng(),
                        place_id: r.place_id,
                        label: r.formatted_address
                    }));
                } else {
                    this.results = [];
                    this.searchError = 'Không tìm thấy địa điểm trên Google Maps';
                }
            });
        },
        async pickResult(item) {
            this.showResults = false;
            if (item.label) {
                this.query = item.label;
            }

            if (item.lat != null && item.lng != null) {
                this.setMarker(item.lat, item.lng, true);
                if (this.isGoogle) {
                    this.reverseGeocode(item.lat, item.lng);
                }
                return;
            }

            // If selected item has a Google place_id, fetch details
            if (item.place_id && this.isGoogle && window.google && window.google.maps) {
                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ placeId: item.place_id }, (results, status) => {
                    if (status === 'OK' && results && results[0]) {
                        const loc = results[0].geometry.location;
                        const lat = loc.lat();
                        const lng = loc.lng();
                        this.setMarker(lat, lng, true);
                        const addr = results[0].formatted_address;
                        this.status = 'Đã ghim: ' + addr;
                        this.query = addr;
                    }
                });
            }
        },
        useHint(name) {
            this.query = name + ', Nha Trang';
            this.runSearch();
        },
        setMarker(lat, lng, pan) {
            this.lat = Number(Number(lat).toFixed(8));
            this.lng = Number(Number(lng).toFixed(8));
            this.status = 'Đã ghim · ' + this.coordLabel;
            if (!this.map) return;

            if (this.isGoogle) {
                const latLng = { lat: this.lat, lng: this.lng };
                if (this.marker) {
                    this.marker.setPosition(latLng);
                } else {
                    this.marker = new google.maps.Marker({
                        position: latLng,
                        map: this.map,
                        draggable: true,
                        title: 'Vị trí bất động sản',
                        animation: google.maps.Animation.DROP
                    });
                    this.marker.addListener('dragend', (e) => {
                        const p = e.latLng;
                        const newLat = p.lat();
                        const newLng = p.lng();
                        this.setMarker(newLat, newLng, false);
                        this.reverseGeocode(newLat, newLng);
                    });
                }
                if (pan && this.map) {
                    this.map.panTo(latLng);
                    if (this.map.getZoom() < 16) this.map.setZoom(16);
                }
            } else {
                if (this.marker) {
                    this.marker.setLatLng([this.lat, this.lng]);
                } else {
                    this.marker = L.marker([this.lat, this.lng], { draggable: true }).addTo(this.map);
                    this.marker.on('dragend', (e) => {
                        const p = e.target.getLatLng();
                        const newLat = Number(p.lat.toFixed(8));
                        const newLng = Number(p.lng.toFixed(8));
                        this.setMarker(newLat, newLng, false);
                        this.reverseGeocode(newLat, newLng);
                    });
                }
                if (pan) this.map.setView([this.lat, this.lng], Math.max(this.map.getZoom(), 16));
            }
        },
        reverseGeocode(lat, lng) {
            if (this.isGoogle && window.google && window.google.maps) {
                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ location: { lat: lat, lng: lng } }, (results, status) => {
                    if (status === 'OK' && results && results[0]) {
                        const addr = results[0].formatted_address;
                        this.status = 'Đã ghim: ' + addr;
                        this.query = addr;
                    }
                });
            } else {
                this.reverseGeocodeOSM(lat, lng);
            }
        },
        async reverseGeocodeOSM(lat, lng) {
            try {
                const params = new URLSearchParams({
                    lat: lat,
                    lon: lng,
                    format: 'json',
                    'accept-language': 'vi'
                });
                const res = await fetch('https://nominatim.openstreetmap.org/reverse?' + params.toString(), {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data && data.display_name) {
                        this.status = 'Đã ghim: ' + data.display_name;
                        this.query = data.display_name;
                    }
                }
            } catch (err) {
                console.error('OSM reverse geocode error', err);
            }
        },
        clearPin() {
            this.lat = null;
            this.lng = null;
            this.query = '';
            this.status = this.isGoogle ? 'Tìm địa chỉ hoặc click/kéo ghim trên bản đồ Google Maps' : 'Tìm địa chỉ hoặc click bản đồ để ghim';
            if (this.marker) {
                if (this.isGoogle) {
                    this.marker.setMap(null);
                } else if (this.map) {
                    this.map.removeLayer(this.marker);
                }
                this.marker = null;
            }
        }
    }"
    @keydown.escape.window="showResults = false"
>
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div>
            <label class="block text-xs font-semibold text-gray-700">Vị trí trên bản đồ</label>
            <p class="mt-0.5 text-[11px] text-gray-400">
                <template x-if="isGoogle">
                    <span>Gõ địa chỉ Google Maps hoặc click/kéo ghim để lấy tọa độ chính xác</span>
                </template>
                <template x-if="!isGoogle">
                    <span>Tìm địa chỉ hoặc click bản đồ để ghim — khách xem tin mở đúng điểm trên Google Maps</span>
                </template>
            </p>
        </div>
        <button type="button" @click="clearPin()" x-show="lat != null && lng != null"
                class="text-xs font-semibold text-gray-500 hover:text-red-600 underline">
            Xóa ghim
        </button>
    </div>

    {{-- Keep FormData values in sync (avoid Alpine null -> "null" string) --}}
    <input type="hidden" name="lat" :value="lat != null ? lat : ''">
    <input type="hidden" name="lng" :value="lng != null ? lng : ''">

    {{-- Search --}}
    <div class="relative" @click.outside="showResults = false">
        <div class="flex gap-2">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input
                    type="text"
                    x-model="query"
                    @keydown.enter.prevent="runSearch()"
                    @focus="if (results.length || searchError) showResults = true"
                    placeholder="VD: 18 Trần Phú, Vinpearl, Mường Thanh, Nha Trang..."
                    class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-700 outline-none focus:border-navy focus:ring-1 focus:ring-navy"
                    autocomplete="off"
                >
            </div>
            <button type="button" @click="runSearch()" :disabled="searching"
                    class="shrink-0 rounded-lg bg-navy px-4 py-2.5 text-sm font-semibold text-white hover:bg-navy/90 disabled:opacity-60">
                <span x-show="!searching">Tìm địa chỉ</span>
                <span x-show="searching"><i class="fas fa-spinner fa-spin"></i></span>
            </button>
        </div>

        <div x-show="hints.length" class="mt-2 flex flex-wrap gap-1.5">
            <template x-for="hint in hints" :key="hint">
                <button type="button" @click="useHint(hint)"
                        class="rounded-full border border-gray-200 bg-gray-50 px-2.5 py-1 text-[11px] font-medium text-gray-600 hover:border-navy hover:text-navy">
                    <span x-text="hint"></span>
                </button>
            </template>
        </div>

        <div x-show="showResults" x-cloak
             class="absolute z-[600] mt-1 max-h-56 w-full overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg">
            <template x-if="searchError && !results.length">
                <p class="px-3 py-2.5 text-xs text-gray-500" x-text="searchError"></p>
            </template>
            <template x-for="(item, idx) in results" :key="idx">
                <button type="button" @click="pickResult(item)"
                        class="block w-full border-b border-gray-50 px-3 py-2.5 text-left last:border-0 hover:bg-navy/5">
                    <span class="block text-sm font-medium text-gray-800 line-clamp-2" x-text="item.label"></span>
                </button>
            </template>
        </div>
    </div>

    <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-gray-100 shadow-sm">
        <div id="{{ $mapId }}" style="height:320px;width:100%;"></div>
        <div class="pointer-events-none absolute left-3 top-3 z-[500] max-w-[90%] rounded-lg bg-white/95 px-2.5 py-1.5 text-[11px] font-medium text-gray-700 shadow">
            <span x-text="status"></span>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3 text-[11px] text-gray-500">
        <a x-show="lat != null && lng != null" :href="googleMapsUrl" target="_blank" rel="noopener"
           class="inline-flex items-center gap-1.5 font-semibold text-navy hover:underline">
            <i class="fas fa-external-link-alt"></i> Mở ghim trên Google Maps
        </a>
        <span class="text-gray-400">Gõ địa chỉ → chọn gợi ý, hoặc click/kéo ghim trên bản đồ</span>
    </div>
</div>
