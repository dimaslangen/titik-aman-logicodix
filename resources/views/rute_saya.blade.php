@extends('layouts.app')

@section('title', 'TitikAman - Navigasi Rute SafePass')

@push('styles')
<style>
    .map-container {
        height: 440px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .route-option-card {
        border: 2px solid #e2e8f0;
        transition: all 0.2s;
        cursor: pointer;
    }
    .route-option-card:hover {
        border-color: #0d9488;
        background-color: #f0fdfa;
    }
    .route-option-card.selected {
        border-color: #16a34a !important;
        background-color: #f0fdf4 !important;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.15);
    }
    .route-search-panel { width: 100%; max-width: none; }
    .route-mode-tabs { display: flex; gap: 8px; padding-bottom: 2px; }
    .route-mode-tabs .mode-tab {
        flex: 1 1 0;
        font-size: .82rem;
        justify-content: center;
        min-width: 0;
        padding: .45rem .5rem;
        white-space: nowrap;
    }
    .route-search-controls { align-items: center; display: flex; flex-wrap: nowrap; gap: 10px; min-width: 0; }
    .route-search-controls > .flex-grow-1 { flex: 1 1 auto !important; min-width: 0; }
    .route-search-row { position: relative; }
    .route-search-row .route-icon {
        width: 1.25rem;
        flex: 0 0 1.25rem;
        text-align: center;
    }
    .route-search-row input,
    .route-search-row select {
    min-width: 0;
    }
    .route-suggestions {
        position: absolute;
        z-index: 1000;
        left: 2.75rem;
        right: .75rem;
        top: calc(100% + .25rem);
    }
    .destination-pin {
        background: #dc2626;
        border: 3px solid #fff;
        border-radius: 50% 50% 50% 0;
        box-shadow: 0 2px 6px rgba(15, 23, 42, .35);
        height: 24px;
        transform: rotate(-45deg);
        width: 24px;
    }
    .destination-pin::after {
        background: #fff;
        border-radius: 50%;
        content: '';
        height: 6px;
        left: 6px;
        position: absolute;
        top: 6px;
        width: 6px;
    }
    @media (max-width: 767.98px) {
        .route-search-row {
            padding: .45rem .65rem !important;
        }
        .route-search-row input,
        .route-search-row select {
            font-size: .85rem !important;
        }
        .route-search-controls > #swapRoutePoints {
            width: 38px !important;
            height: 38px !important;
        }
        .route-option-card h6,
        .route-option-card p {
            overflow-wrap: anywhere;
        }
    }
</style>
@endpush

@section('content')
    <!-- Banner Khusus Live Companion (Jika dibuka dari tautan terbagi) -->
    <div id="sharedRouteBanner" class="alert alert-success d-none align-items-center justify-content-between p-3 rounded-4 shadow-sm mb-3" role="alert">
        <div class="d-flex align-items-center">
            <div class="bg-success text-white p-2 rounded-circle ta-circle ta-circle-40 me-3">
                <i class="bi bi-broadcast fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-info-circle me-1"></i>Live Companion — Rute Perjalanan Terbagi</h6>
                <small class="text-muted" id="sharedRouteExpires">Tautan berlaku 6 jam sejak dibuat.</small>
            </div>
        </div>
        <a href="/rute-saya" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold">
            Cari Rute Baru
        </a>
    </div>

    <!-- Topbar Header -->
    <x-topbar title="Bandingkan Rute SafePass" subtitle="Navigasi Komparatif" back-url="/app">
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
            <i class="bi bi-shield-shaded me-1"></i> Anti-Gelap & Area Sepi
        </span>
    </x-topbar>

    <!-- Panel Pencarian Rute ala Google Maps -->
    <div class="bg-white p-3 p-md-4 rounded-4 shadow-sm mb-3 route-search-panel w-100">
        <div class="route-search-controls d-flex align-items-center gap-2">
            <div class="flex-grow-1">
                <div class="route-search-row d-flex align-items-center gap-2 border rounded-3 px-3 py-2 mb-2">
                    <i class="bi bi-circle-fill text-success route-icon"></i>
                    <select class="form-select form-select-sm border-0 shadow-none fw-semibold p-0" id="pilihanAsal" aria-label="Pilih region asal">
                        <option value="unesa-lidah-wetan">UNESA Lidah Wetan, Surabaya</option>
                        <option value="unesa-ketintang">UNESA Ketintang, Surabaya</option>
                        <option value="rawamangun">Rawamangun, Jakarta Timur</option>
                    </select>
                    <button type="button" class="btn btn-light btn-sm rounded-circle ta-circle ta-circle-38 flex-shrink-0" id="useCurrentLocation" title="Gunakan lokasi saya" aria-label="Gunakan lokasi saya"><i class="bi bi-crosshair"></i></button>
                </div>
                <div class="route-search-row d-flex align-items-center gap-2 border rounded-3 px-3 py-2">
                    <i class="bi bi-geo-alt-fill text-danger route-icon"></i>
                    <input type="search" class="form-control form-control-sm border-0 shadow-none p-0" id="destinationSearch" placeholder="Cari tujuan, misalnya UNJ atau RS Persahabatan" autocomplete="off">
                    <button type="button" class="btn btn-light btn-sm rounded-circle ta-circle ta-circle-38 flex-shrink-0" id="searchDestination" title="Cari tujuan" aria-label="Cari tujuan"><i class="bi bi-search"></i></button>
                    <div id="destinationSuggestions" class="route-suggestions bg-white border rounded-3 shadow-sm d-none"></div>
                </div>
                <small class="text-muted d-block mt-2"><i class="bi bi-info-circle me-1"></i> Klik peta untuk memilih titik tujuan.</small>
            </div>
            <button type="button" class="btn btn-outline-secondary rounded-circle ta-circle ta-circle-38 flex-shrink-0" id="swapRoutePoints" title="Tukar asal dan tujuan" aria-label="Tukar asal dan tujuan">⇅</button>
        </div>
        <input type="hidden" id="originLat">
        <input type="hidden" id="originLng">
        <input type="hidden" id="destinationLat">
        <input type="hidden" id="destinationLng">
        <input type="hidden" id="routeMode" value="motor">
    </div>

    <!-- Area Peta Leaflet Komparasi Rute -->
    <div class="map-container mb-3 position-relative">
        <div id="mapRute" style="width: 100%; height: 100%;"></div>
    </div>

    <!-- Panel Bawah: Pilihan Rute Perjalanan -->
    <div class="bg-white p-4 rounded-4 shadow-sm">
        <div class="route-mode-tabs mb-3" role="group" aria-label="Moda transportasi">
            <button type="button" class="btn btn-sm btn-outline-success mode-tab rounded-pill px-2" data-mode="mobil"><i class="bi bi-car-front-fill me-1"></i>Mobil</button>
            <button type="button" class="btn btn-sm btn-success mode-tab active rounded-pill px-2" data-mode="motor"><i class="bi bi-bicycle me-1"></i>Motor</button>
            <button type="button" class="btn btn-sm btn-outline-success mode-tab rounded-pill px-2" data-mode="jalan_kaki"><i class="bi bi-person-walking me-1"></i>Jalan kaki</button>
        </div>
        <div class="mb-3">
            <button type="button" class="btn btn-success w-100 py-2 fw-bold shadow-sm" id="compareRoutesButton">
                <i class="bi bi-search me-1"></i> Cari & Bandingkan Rute
            </button>
        </div>

        <div id="routeStatus" class="alert d-none py-2 mb-3" role="alert"></div>

        <!-- Modal Dialog Bagikan Rute Live Companion -->
        <div class="modal fade" id="shareRouteModal" tabindex="-1" aria-labelledby="shareRouteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="shareRouteModalLabel"><i class="bi bi-broadcast text-success me-2"></i>Bagikan Rute Live Companion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-3">Tautan Live Companion ini berlaku selama <strong>6 jam</strong>. Teman atau keluarga yang menerima tautan dapat melihat jalur perjalanan Anda di peta secara langsung.</p>
                        <div class="input-group mb-3">
                            <input id="shareUrl" class="form-control" readonly>
                            <button type="button" class="btn btn-success fw-semibold" id="copyShareUrl">
                                <i class="bi bi-clipboard me-1"></i> Salin
                            </button>
                        </div>
                        <small class="text-muted d-block" id="copyNotification"></small>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="fw-bold text-dark mb-0">Hasil Perbandingan Rute</h6>
            <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold shadow-sm" id="shareRouteButton" disabled>
                <i class="bi bi-share me-1"></i> Bagikan Rute (Live Companion)
            </button>
        </div>

        <div class="row g-3">
            <!-- Opsi 1: SafePass (Rute Aman) -->
            <div class="col-md-6">
                <div class="p-3 rounded-4 route-option-card border-success bg-success-subtle bg-opacity-10" id="cardSafePass">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-success text-white px-3 py-1 rounded-pill">
                            <i class="bi bi-shield-check me-1"></i> SafePass (Rekomendasi)
                        </span>
                        <span class="fw-bold text-success fs-5" id="safeDuration">-</span>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Rute Berpenerangan & Melalui Safety Hub</h6>
                    <p class="text-muted small mb-3" id="safeDetails">Lakukan pencarian rute untuk melihat detail pembobotan.</p>
                    <button type="button" class="btn btn-success btn-sm w-100 rounded-pill fw-semibold shadow-sm" onclick="pilihRute('SafePass')">
                        Gunakan Rute Ini <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            <!-- Opsi 2: Rute Tercepat / Alternatif -->
            <div class="col-md-6">
                <div class="p-3 rounded-4 route-option-card" id="cardTercepat">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge text-white px-3 py-1 rounded-pill" style="background-color: #0d9488;">
                            <i class="bi bi-lightning-charge-fill me-1"></i> Tercepat
                        </span>
                        <span class="fw-bold text-dark fs-5" id="fastDuration">-</span>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Rute Durasi Minimal (OSRM)</h6>
                    <p class="text-muted small mb-3" id="fastDetails">Lakukan pencarian rute untuk melihat perbandingan waktu.</p>
                    <button type="button" class="btn btn-outline-success btn-sm w-100 rounded-pill fw-semibold shadow-sm" onclick="pilihRute('Tercepat')">
                        Gunakan Rute Ini <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Panel Navigasi Aktif (Muncul saat rute dipilih) -->
        <div id="activeTripPanel" class="mt-4 p-3 bg-light rounded-4 border d-none">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
                    <div>
                        <strong class="text-dark d-block">Navigasi Perjalanan Aktif: <span id="activeRouteName">SafePass</span></strong>
                        <small class="text-muted">Tetap waspada dan perhatikan kondisi sekitar Anda.</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="selesaikanPerjalanan()">
                        Selesaikan Perjalanan
                    </button>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    const routeApi = window.taApiBase || '/api/v1';
    const mapRute = L.map('mapRute').setView([0, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(mapRute);

    const routeLayer = L.layerGroup().addTo(mapRute);
    const markersLayer = L.layerGroup().addTo(mapRute);
    const selectionLayer = L.layerGroup().addTo(mapRute);

    let routeRegions = {};
    let lastRouteRequest = null;
    let lastRouteData = null;
    let shareModalInstance = null;
    let originMarker = null;
    let destinationMarker = null;

    const $route = id => document.getElementById(id);
    const showRouteStatus = (message, type = 'danger') => {
        const element = $route('routeStatus');
        if (!element) return;
        element.className = `alert alert-${type} py-2 mb-3`;
        const icon = type === 'success' ? 'bi-check-circle' : (type === 'info' ? 'bi-info-circle' : 'bi-exclamation-triangle');
        element.innerHTML = `<i class="bi ${icon} me-2" aria-hidden="true"></i><span></span>`;
        element.querySelector('span').textContent = message;
        element.classList.remove('d-none');
    };
    const formatDuration = seconds => `${Math.round(Number(seconds || 0) / 60)} menit`;
    const formatDistance = meters => `${(Number(meters || 0) / 1000).toFixed(2)} km`;
    const toLeafletCoordinates = route => (route?.geometry?.coordinates || [])
        .filter(coordinate => Array.isArray(coordinate) && coordinate.length >= 2)
        .map(coordinate => [coordinate[1], coordinate[0]]);

    const destinationIcon = L.divIcon({
        className: '',
        html: '<div class="destination-pin"></div>',
        iconSize: [24, 24],
        iconAnchor: [12, 24],
        popupAnchor: [0, -22]
    });

    function currentRegion() {
        return routeRegions[$route('pilihanAsal').value];
    }

    function updateOriginLabel(label) {
        const option = $route('pilihanAsal').selectedOptions[0];
        if (option) option.textContent = label;
    }

    function renderSelectionMarkers() {
        selectionLayer.clearLayers();
        const originLat = Number($route('originLat').value);
        const originLng = Number($route('originLng').value);
        const destinationLat = Number($route('destinationLat').value);
        const destinationLng = Number($route('destinationLng').value);

        if (Number.isFinite(originLat) && Number.isFinite(originLng)) {
            originMarker = L.circleMarker([originLat, originLng], {
                radius: 8, color: '#fff', weight: 3, fillColor: '#16a34a', fillOpacity: 1
            }).bindPopup('<strong>Titik Asal</strong>').addTo(selectionLayer);
        }

        if (Number.isFinite(destinationLat) && Number.isFinite(destinationLng)) {
            const address = $route('destinationSearch').value || 'Titik tujuan';
            destinationMarker = L.marker([destinationLat, destinationLng], {icon: destinationIcon})
                .bindPopup(`<strong>Tujuan</strong><br>${escapeHtml(address)}`)
                .addTo(selectionLayer);
        }
    }

    function setDestinationPoint(lat, lng, address = '') {
        $route('destinationLat').value = Number(lat).toFixed(5);
        $route('destinationLng').value = Number(lng).toFixed(5);
        if (address) $route('destinationSearch').value = address;
        renderSelectionMarkers();
    }

    function regionViewbox() {
        const region = currentRegion();
        const center = region?.center || {lat: region?.center_lat, lng: region?.center_lng};
        const lat = Number(center?.lat);
        const lng = Number(center?.lng);
        const offset = 0.15;
        return `${lng - offset},${lat + offset},${lng + offset},${lat - offset}`;
    }

    async function reverseGeocode(lat, lng) {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}&zoom=18&addressdetails=1`, {
            headers: {'Accept': 'application/json', 'Accept-Language': 'id'}
        });
        if (!response.ok) throw new Error('Alamat titik tujuan tidak dapat dimuat.');
        const payload = await response.json();
        return payload.display_name || `${Number(lat).toFixed(5)}, ${Number(lng).toFixed(5)}`;
    }

    function renderDestinationSuggestions(results) {
        const container = $route('destinationSuggestions');
        container.innerHTML = '';
        results.slice(0, 5).forEach(result => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-light text-start w-100 border-0 rounded-0 small';
            button.textContent = result.display_name;
            button.addEventListener('click', () => {
                setDestinationPoint(result.lat, result.lon, result.display_name);
                mapRute.flyTo([Number(result.lat), Number(result.lon)], 16);
                container.classList.add('d-none');
            });
            container.appendChild(button);
        });
        container.classList.toggle('d-none', !results.length);
    }

    async function searchDestination() {
        const query = $route('destinationSearch').value.trim();
        if (!query) {
            showRouteStatus('Masukkan nama tempat atau alamat tujuan terlebih dahulu.');
            return;
        }

        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=5&viewbox=${encodeURIComponent(regionViewbox())}&bounded=1&q=${encodeURIComponent(query)}`, {
            headers: {'Accept': 'application/json', 'Accept-Language': 'id'}
        });
        if (!response.ok) throw new Error('Pencarian alamat tujuan gagal.');
        const results = await response.json();
        renderDestinationSuggestions(results);
        if (!results.length) {
            showRouteStatus('Alamat tujuan tidak ditemukan di sekitar region aktif.');
            return;
        }

        const first = results[0];
        setDestinationPoint(first.lat, first.lon, first.display_name);
        mapRute.flyTo([Number(first.lat), Number(first.lon)], 16);
        showRouteStatus('Tujuan ditemukan. Klik "Cari & Bandingkan Rute".', 'success');
    }

    function setRegionDefaults() {
        const region = routeRegions[$route('pilihanAsal').value];
        if (!region) return;
        const center = region.center;
        $route('originLat').value = center.lat;
        $route('originLng').value = center.lng;
        updateOriginLabel(region.name || $route('pilihanAsal').value);
        setDestinationPoint(Number(center.lat) + 0.005, Number(center.lng) + 0.005, 'Pilih tujuan di peta atau cari alamat');
        mapRute.setView([center.lat, center.lng], 15);
    }

    function drawRoute(data) {
        routeLayer.clearLayers();
        markersLayer.clearLayers();

        const safe = toLeafletCoordinates(data.safepass);
        const fastest = toLeafletCoordinates(data.fastest);
        const points = [...safe, ...fastest];

        if (fastest.length) {
            L.polyline(fastest, {
                color: '#eab308', weight: 5, dashArray: '10 8', opacity: 0.9
            }).bindPopup(`<strong>Rute Tercepat</strong><br>Durasi: ${formatDuration(data.fastest?.duration)} · ${formatDistance(data.fastest?.distance)}`).addTo(routeLayer);
        }

        if (safe.length) {
            L.polyline(safe, {
                color: '#16a34a', weight: 8, opacity: 0.95
            }).bindPopup(`<strong>Rute SafePass (Rekomendasi)</strong><br>Durasi: ${formatDuration(data.safepass?.duration)} · ${formatDistance(data.safepass?.distance)}`).addTo(routeLayer);
        }

        if (points.length) {
            mapRute.fitBounds(points, { padding: [35, 35], maxZoom: 17 });
        }

        renderSelectionMarkers();

        const hubsPassed = data.safepass?.hubs_passed || [];
        hubsPassed.forEach(hub => {
            if (hub.lat && hub.lng) {
                L.circleMarker([Number(hub.lat), Number(hub.lng)], {
                    radius: 7, color: '#fff', weight: 2, fillColor: '#2563eb', fillOpacity: 0.95
                }).bindPopup(`<strong>Safety Hub: ${hub.name}</strong><br>Tipe: ${hub.type}<br>Dilewati dalam radius ${Math.round(hub.distance_m || 0)} m`).addTo(markersLayer);
            }
        });

        const avoidedReports = data.safepass?.avoided_reports || [];
        avoidedReports.forEach(rep => {
            if (rep.lat && rep.lng) {
                L.circleMarker([Number(rep.lat), Number(rep.lng)], {
                    radius: 8, color: '#fff', weight: 2, fillColor: '#f59e0b', fillOpacity: 0.95
                }).bindPopup(`<strong>Laporan Dihindari: ${rep.category}</strong><br>Jarak ke rute: ${Math.round(rep.distance_m || 0)} m`).addTo(markersLayer);
            }
        });
    }

    function routeSummary(route) {
        const avoided = (route?.avoided_reports || []).length;
        const hubs = (route?.hubs_passed || []).length;
        return `${formatDistance(route?.distance)} · ${avoided} titik bahaya dihindari · ${hubs} Safety Hub dilewati`;
    }

    async function compareRoutes() {
        const button = $route('compareRoutesButton');
        const body = {
            origin_lat: Number($route('originLat').value),
            origin_lng: Number($route('originLng').value),
            destination_lat: Number($route('destinationLat').value),
            destination_lng: Number($route('destinationLng').value),
            mode: $route('routeMode').value,
            region_slug: $route('pilihanAsal').value
        };

        if (Object.values(body).some(value => value === '' || Number.isNaN(value))) {
            showRouteStatus('Lengkapi koordinat asal dan tujuan terlebih dahulu.');
            return;
        }

        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menghitung...';

        try {
            const response = await fetch(`${routeApi}/routes/compare`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', Accept: 'application/json'},
                body: JSON.stringify(body)
            });
            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(payload.message || 'Rute tidak dapat dihitung. Periksa koordinat dan wilayah.');
            }

            lastRouteRequest = body;
            lastRouteData = payload.data;
            drawRoute(payload.data);

            $route('safeDuration').textContent = formatDuration(payload.data.safepass?.duration);
            $route('fastDuration').textContent = formatDuration(payload.data.fastest?.duration);
            $route('safeDetails').textContent = routeSummary(payload.data.safepass);
            $route('fastDetails').textContent = `${formatDistance(payload.data.fastest?.distance)} · durasi tercepat tanpa penalti kondisi jalan`;
            $route('shareRouteButton').disabled = false;

            showRouteStatus(payload.data.note || 'Dua opsi rute berhasil dihitung.', 'success');
        } catch (error) {
            showRouteStatus(error.message);
        } finally {
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-search me-1"></i> Cari & Bandingkan';
        }
    }

    async function shareRoute() {
        if (!lastRouteRequest || !lastRouteData) return;
        const token = localStorage.getItem('ta_token');
        if (!token) {
            if (typeof window.taPromptGuest === 'function') {
                window.taPromptGuest(
                    'Masuk untuk Membagikan Rute',
                    'Fitur Live Companion membutuhkan akun agar Anda dapat membagikan perjalanan dengan aman kepada rekan atau keluarga.'
                );
            } else {
                window.location.href = '/masuk';
            }
            return;
        }

        try {
            const response = await fetch(`${routeApi}/shared-routes`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({...lastRouteRequest, route_data: lastRouteData})
            });

            const payload = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(payload.message || 'Rute gagal dibagikan.');

            const shareToken = payload.data.token;
            const webShareUrl = `${window.location.origin}/rute-saya?share=${encodeURIComponent(shareToken)}`;
            
            $route('shareUrl').value = webShareUrl;
            $route('copyNotification').textContent = '';

            if (!shareModalInstance) {
                shareModalInstance = new bootstrap.Modal(document.getElementById('shareRouteModal'));
            }
            shareModalInstance.show();
        } catch (error) {
            if (error.message.includes('Unauthenticated') || error.message.includes('401')) {
                window.taShowAlert('Sesi login berakhir. Silakan masuk kembali.', 'danger');
                localStorage.removeItem('ta_token');
                window.location.href = '/masuk';
                return;
            }
            window.taShowAlert(error.message, 'danger');
        }
    }

    async function checkSharedRouteFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        const shareToken = urlParams.get('share');
        if (!shareToken) return;

        try {
            const res = await fetch(`${routeApi}/shared-routes/${encodeURIComponent(shareToken)}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!res.ok) {
                throw new Error('Tautan Live Companion sudah kedaluwarsa (maksimal 6 jam) atau tidak valid.');
            }

            const json = await res.json();
            const shared = json.data;

            const banner = document.getElementById('sharedRouteBanner');
            if (banner) banner.classList.remove('d-none');
            
            if (shared.expires_at) {
                document.getElementById('sharedRouteExpires').textContent = `Berlaku hingga: ${new Date(shared.expires_at).toLocaleTimeString('id-ID')}`;
            }

            if (shared.region_slug) $route('pilihanAsal').value = shared.region_slug;
            $route('originLat').value = shared.origin_lat;
            $route('originLng').value = shared.origin_lng;
            $route('destinationLat').value = shared.destination_lat;
            $route('destinationLng').value = shared.destination_lng;
            updateOriginLabel('Lokasi asal rute dibagikan');
            $route('destinationSearch').value = 'Tujuan rute dibagikan';
            renderSelectionMarkers();

            lastRouteRequest = {
                origin_lat: shared.origin_lat,
                origin_lng: shared.origin_lng,
                destination_lat: shared.destination_lat,
                destination_lng: shared.destination_lng,
                mode: shared.mode || 'motor',
                region_slug: shared.region_slug
            };

            if (shared.route_data) {
                lastRouteData = shared.route_data;
                drawRoute(shared.route_data);
                $route('safeDuration').textContent = formatDuration(shared.route_data.safepass?.duration);
                $route('fastDuration').textContent = formatDuration(shared.route_data.fastest?.duration);
                $route('safeDetails').textContent = routeSummary(shared.route_data.safepass);
                $route('fastDetails').textContent = `${formatDistance(shared.route_data.fastest?.distance)} · rute alternatif`;
                showRouteStatus('Menampilkan rute perjalanan yang dibagikan via Live Companion.', 'success');
            } else {
                compareRoutes();
            }

        } catch (err) {
            showRouteStatus(err.message);
        }
    }

    function pilihRute(namaRute) {
        if (!lastRouteData) {
            showRouteStatus('Bandingkan rute terlebih dahulu.');
            return;
        }

        const cardSafe = document.getElementById('cardSafePass');
        const cardFast = document.getElementById('cardTercepat');
        const tripPanel = document.getElementById('activeTripPanel');
        const routeName = document.getElementById('activeRouteName');

        if (namaRute === 'SafePass') {
            cardSafe.classList.add('selected');
            cardFast.classList.remove('selected');
            routeName.textContent = 'SafePass (Rekomendasi Jalur Aman)';
        } else {
            cardFast.classList.add('selected');
            cardSafe.classList.remove('selected');
            routeName.textContent = 'Rute Tercepat';
        }

        tripPanel.classList.remove('d-none');
        tripPanel.scrollIntoView({ behavior: 'smooth' });
    }

    function selesaikanPerjalanan() {
        document.getElementById('cardSafePass').classList.remove('selected');
        document.getElementById('cardTercepat').classList.remove('selected');
        document.getElementById('activeTripPanel').classList.add('d-none');
        window.taShowAlert('Perjalanan telah diselesaikan. Tetap jaga keselamatan Anda!', 'success');
    }

    async function loadRouteRegions() {
        const response = await fetch(`${routeApi}/regions`, {headers: {Accept: 'application/json'}});
        if (!response.ok) throw new Error('Daftar region gagal dimuat.');
        const payload = await response.json();
        const regions = Array.isArray(payload.data) ? payload.data : [];
        routeRegions = Object.fromEntries(regions.map(region => [region.slug, region]));
        setRegionDefaults();
    }

    mapRute.on('click', function(e) {
        setDestinationPoint(e.latlng.lat, e.latlng.lng, 'Memuat alamat...');
        mapRute.flyTo(e.latlng, Math.max(mapRute.getZoom(), 15));
        reverseGeocode(e.latlng.lat, e.latlng.lng)
            .then(address => {
                setDestinationPoint(e.latlng.lat, e.latlng.lng, address);
                showRouteStatus('Alamat tujuan diperbarui dari peta. Klik "Cari & Bandingkan".', 'info');
            })
            .catch(error => {
                setDestinationPoint(e.latlng.lat, e.latlng.lng, `${e.latlng.lat.toFixed(5)}, ${e.latlng.lng.toFixed(5)}`);
                showRouteStatus(error.message);
            });
    });

    $route('pilihanAsal').addEventListener('change', setRegionDefaults);
    document.querySelectorAll('.mode-tab').forEach(button => button.addEventListener('click', () => {
        document.querySelectorAll('.mode-tab').forEach(tab => {
            tab.className = tab === button
                ? 'btn btn-sm btn-success mode-tab active rounded-pill px-3'
                : 'btn btn-sm btn-outline-success mode-tab rounded-pill px-3';
        });
        $route('routeMode').value = button.dataset.mode;
    }));

    $route('searchDestination').addEventListener('click', () => searchDestination().catch(error => showRouteStatus(error.message)));
    $route('destinationSearch').addEventListener('keydown', event => {
        if (event.key === 'Enter') {
            event.preventDefault();
            searchDestination().catch(error => showRouteStatus(error.message));
        }
    });
    $route('destinationSearch').addEventListener('focus', () => {
        if ($route('destinationSuggestions').children.length) $route('destinationSuggestions').classList.remove('d-none');
    });
    $route('useCurrentLocation').addEventListener('click', () => {
        if (!navigator.geolocation) {
            showRouteStatus('Browser tidak mendukung lokasi GPS.');
            return;
        }
        navigator.geolocation.getCurrentPosition(async position => {
            const {latitude, longitude} = position.coords;
            $route('originLat').value = latitude.toFixed(5);
            $route('originLng').value = longitude.toFixed(5);
            updateOriginLabel('Lokasi Saya (GPS)');
            renderSelectionMarkers();
            mapRute.flyTo([latitude, longitude], 16);
            try {
                updateOriginLabel(await reverseGeocode(latitude, longitude));
            } catch (error) {
                showRouteStatus(error.message);
            }
        }, () => showRouteStatus('Lokasi GPS tidak dapat diakses.'));
    });
    $route('swapRoutePoints').addEventListener('click', () => {
        const originLabel = $route('pilihanAsal').selectedOptions[0]?.textContent || 'Asal';
        const destinationLabel = $route('destinationSearch').value || 'Tujuan';
        const originLat = $route('originLat').value;
        const originLng = $route('originLng').value;
        $route('originLat').value = $route('destinationLat').value;
        $route('originLng').value = $route('destinationLng').value;
        $route('destinationLat').value = originLat;
        $route('destinationLng').value = originLng;
        updateOriginLabel(destinationLabel);
        $route('destinationSearch').value = originLabel;
        renderSelectionMarkers();
        mapRute.fitBounds([
            [Number($route('originLat').value), Number($route('originLng').value)],
            [Number($route('destinationLat').value), Number($route('destinationLng').value)]
        ], {padding: [35, 35]});
    });

    $route('compareRoutesButton').addEventListener('click', compareRoutes);
    $route('shareRouteButton').addEventListener('click', shareRoute);
    $route('copyShareUrl').addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText($route('shareUrl').value);
            $route('copyNotification').textContent = 'Tautan berhasil disalin ke clipboard!';
        } catch (error) {
            window.taShowAlert(`Tautan tidak dapat disalin otomatis: ${error.message}`, 'danger');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            mapRute.invalidateSize();
        }, 300);

        loadRouteRegions()
            .then(() => checkSharedRouteFromUrl())
            .catch(error => showRouteStatus(error.message));
    });
</script>
@endpush
