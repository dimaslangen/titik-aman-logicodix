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
</style>
@endpush

@section('content')
    <!-- Banner Khusus Live Companion (Jika dibuka dari tautan terbagi) -->
    <div id="sharedRouteBanner" class="alert alert-success d-none align-items-center justify-content-between p-3 rounded-4 shadow-sm mb-3">
        <div class="d-flex align-items-center">
            <div class="bg-success text-white p-2 rounded-circle me-3">
                <i class="bi bi-broadcast fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0">Live Companion — Rute Perjalanan Terbagi</h6>
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

    <!-- Bar Input Asal & Tujuan dengan Pilihan Wilayah -->
    <div class="bg-white p-3 rounded-4 shadow-sm mb-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
            <div class="input-group input-group-sm" style="max-width: 320px;">
                <span class="input-group-text bg-success-subtle text-success border-0"><i class="bi bi-geo-alt-fill"></i></span>
                <select class="form-select bg-light border-0 fw-semibold" id="pilihanAsal">
                    <option value="unesa-lidah-wetan">UNESA Lidah Wetan, Surabaya</option>
                    <option value="unesa-ketintang">UNESA Ketintang, Surabaya</option>
                    <option value="rawamangun">Rawamangun, Jakarta Timur</option>
                </select>
            </div>
            <i class="bi bi-arrow-right text-muted"></i>
            <div class="input-group input-group-sm flex-grow-1" style="max-width: 340px;">
                <span class="input-group-text bg-danger-subtle text-danger border-0"><i class="bi bi-flag-fill"></i></span>
                <input type="number" step="any" class="form-control bg-light border-0" id="destinationLat" placeholder="Tujuan latitude">
                <input type="number" step="any" class="form-control bg-light border-0" id="destinationLng" placeholder="Tujuan longitude">
            </div>
        </div>
        <div>
            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill small">
                <i class="bi bi-cursor me-1"></i> Klik titik di peta untuk pilih tujuan
            </span>
        </div>
    </div>

    <!-- Area Peta Leaflet Komparasi Rute -->
    <div class="map-container mb-3 position-relative">
        <div id="mapRute" style="width: 100%; height: 100%;"></div>
    </div>

    <!-- Panel Bawah: Pilihan Rute Perjalanan -->
    <div class="bg-white p-4 rounded-4 shadow-sm">
        <div class="row g-2 mb-3">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Asal Latitude</label>
                <input type="number" step="any" class="form-control form-control-sm" id="originLat" placeholder="Asal latitude">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Asal Longitude</label>
                <input type="number" step="any" class="form-control form-control-sm" id="originLng" placeholder="Asal longitude">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Moda Transportasi</label>
                <div class="btn-group w-100" role="group" aria-label="Moda transportasi">
                    <button type="button" class="btn btn-sm btn-outline-success mode-tab" data-mode="mobil">Mobil</button>
                    <button type="button" class="btn btn-sm btn-success mode-tab active" data-mode="motor">Motor</button>
                    <button type="button" class="btn btn-sm btn-outline-success mode-tab" data-mode="jalan_kaki">Jalan kaki</button>
                </div>
                <input type="hidden" id="routeMode" value="motor">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-success btn-sm w-100 py-2 fw-bold shadow-sm" id="compareRoutesButton">
                    <i class="bi bi-search me-1"></i> Cari & Bandingkan
                </button>
            </div>
        </div>

        <div id="routeStatus" class="alert d-none py-2 mb-3"></div>

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

    let routeRegions = {};
    let lastRouteRequest = null;
    let lastRouteData = null;
    let shareModalInstance = null;

    const $route = id => document.getElementById(id);
    const showRouteStatus = (message, type = 'danger') => {
        const element = $route('routeStatus');
        if (!element) return;
        element.className = `alert alert-${type} py-2 mb-3`;
        element.textContent = message;
        element.classList.remove('d-none');
    };
    const formatDuration = seconds => `${Math.round(Number(seconds || 0) / 60)} menit`;
    const formatDistance = meters => `${(Number(meters || 0) / 1000).toFixed(2)} km`;
    const toLeafletCoordinates = route => (route?.geometry?.coordinates || [])
        .filter(coordinate => Array.isArray(coordinate) && coordinate.length >= 2)
        .map(coordinate => [coordinate[1], coordinate[0]]);

    function setRegionDefaults() {
        const region = routeRegions[$route('pilihanAsal').value];
        if (!region) return;
        const center = region.center;
        $route('originLat').value = center.lat;
        $route('originLng').value = center.lng;
        $route('destinationLat').value = (Number(center.lat) + 0.005).toFixed(5);
        $route('destinationLng').value = (Number(center.lng) + 0.005).toFixed(5);
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

        const origin = [Number(lastRouteRequest.origin_lat), Number(lastRouteRequest.origin_lng)];
        const destination = [Number(lastRouteRequest.destination_lat), Number(lastRouteRequest.destination_lng)];
        
        L.circleMarker(origin, {
            radius: 8, color: '#fff', weight: 2, fillColor: '#16a34a', fillOpacity: 1
        }).bindPopup('<strong>Titik Awal (Asal)</strong>').addTo(markersLayer);

        L.circleMarker(destination, {
            radius: 8, color: '#fff', weight: 2, fillColor: '#dc2626', fillOpacity: 1
        }).bindPopup('<strong>Titik Akhir (Tujuan)</strong>').addTo(markersLayer);

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
                alert('Sesi login berakhir. Silakan masuk kembali.');
                localStorage.removeItem('ta_token');
                window.location.href = '/masuk';
                return;
            }
            alert(error.message);
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
        alert('Perjalanan telah diselesaikan. Tetap jaga keselamatan Anda!');
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
        $route('destinationLat').value = e.latlng.lat.toFixed(5);
        $route('destinationLng').value = e.latlng.lng.toFixed(5);
        showRouteStatus('Titik tujuan diperbarui dari peta. Klik "Cari & Bandingkan".', 'info');
    });

    $route('pilihanAsal').addEventListener('change', setRegionDefaults);
    document.querySelectorAll('.mode-tab').forEach(button => button.addEventListener('click', () => {
        document.querySelectorAll('.mode-tab').forEach(tab => {
            tab.className = tab === button ? 'btn btn-sm btn-success mode-tab active' : 'btn btn-sm btn-outline-success mode-tab';
        });
        $route('routeMode').value = button.dataset.mode;
    }));

    $route('compareRoutesButton').addEventListener('click', compareRoutes);
    $route('shareRouteButton').addEventListener('click', shareRoute);
    $route('copyShareUrl').addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText($route('shareUrl').value);
            $route('copyNotification').textContent = 'Tautan berhasil disalin ke clipboard!';
        } catch (error) {
            alert(`Tautan tidak dapat disalin otomatis: ${error.message}`);
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
