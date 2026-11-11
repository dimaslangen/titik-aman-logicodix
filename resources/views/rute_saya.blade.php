<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TitikAman - Navigasi Rute SafePass</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Leaflet CSS (Untuk Peta) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: #ffffff;
            min-height: 100vh;
            border-right: 1px solid #e2e8f0;
        }
        .nav-link {
            color: #4a5568;
            font-weight: 500;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.2s ease;
        }
        .nav-link:hover {
            background-color: #e6fffa;
            color: #0d9488;
        }
        .nav-link.active {
            background-color: #e6fffa;
            color: #0d9488;
            font-weight: 600;
        }
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
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            
            <!-- SIDEBAR KIRI -->
            <div class="col-md-3 col-lg-2 px-3 py-4 sidebar d-flex flex-column justify-content-between">
                <div>
                    <!-- Logo / Brand -->
                    <div class="d-flex align-items-center mb-4 px-2">
                        <div class="bg-success text-white p-2 rounded-3 me-2">
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">TitikAman</h5>
                            <small class="text-muted" style="font-size: 11px;">CIVIC SAFETY GRID</small>
                        </div>
                    </div>

                    <!-- Kotak Informasi Singkat -->
                    <div class="p-3 bg-light rounded-4 mb-4 border-0">
                        <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">Navigasi SafePass</small>
                        <p class="fw-semibold text-dark small mt-1 mb-0">Prioritaskan jalan berpenerangan baik & dekat Safety Hub.</p>
                    </div>

                    <!-- Menu Navigasi -->
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="/app" class="nav-link py-2 px-3">
                                <i class="bi bi-map me-2"></i> Peta Aman
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/rute-saya" class="nav-link active py-2 px-3">
                                <i class="bi bi-signpost-split me-2"></i> Rute Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/lapor-kondisi" class="nav-link py-2 px-3">
                                <i class="bi bi-exclamation-triangle me-2"></i> Lapor Kondisi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/admin" class="nav-link py-2 px-3">
                                <i class="bi bi-shield-lock me-2"></i> Admin
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Footer Status -->
                <div class="px-2 pt-3 border-top">
                    <div class="d-flex align-items-center">
                        <span class="spinner-grow spinner-grow-sm text-success me-2" role="status"></span>
                        <div style="font-size: 12px;">
                            <span class="fw-semibold text-dark d-block">SafePass Engine Aktif</span>
                            <span class="text-muted" style="font-size: 10px;">OSRM + Spatial Scoring</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KONTEN UTAMA KANAN (Komparasi Rute) -->
            <div class="col-md-9 col-lg-10 p-4">
                
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
                <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-3 rounded-4 shadow-sm flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <h4 class="fw-bold mb-0 text-dark me-3">Bandingkan Rute SafePass</h4>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                            <i class="bi bi-shield-shaded me-1"></i> Anti-Gelap & Area Sepi
                        </span>
                    </div>
                    <div id="authHeader" class="d-flex align-items-center gap-2">
                        <span id="userInfo" class="badge bg-light text-dark border px-3 py-2 rounded-pill d-none align-items-center gap-1">
                            <i class="bi bi-person-fill text-success"></i> <span id="userName">Warga</span>
                        </span>
                        <button id="btnLogout" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold d-none" onclick="logoutRoute()">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar
                        </button>
                        <a id="btnLogin" href="/masuk" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                        </a>
                    </div>
                </div>

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
                            <i class="bi bi-cursor me-1"></i> Klik titik di peta untuk pilih
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

            </div>

        </div>
    </div>

    <!-- Floating SOS Emergency Button (PRD FR-01.6) -->
    <button type="button" class="btn btn-danger rounded-pill shadow-lg position-fixed d-flex align-items-center gap-2 px-3 py-2" style="bottom: 24px; right: 24px; z-index: 1050; font-weight: 700; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.45);" onclick="openSosModalRoute()">
        <i class="bi bi-telephone-fill"></i> SOS 110
    </button>

    <!-- Modal SOS Emergency -->
    <div class="modal fade" id="sosModalRoute" tabindex="-1" aria-labelledby="sosModalRouteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header bg-danger text-white border-0 rounded-top-4">
                    <h5 class="modal-title fw-bold" id="sosModalRouteLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i> Panggilan Darurat SOS</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="bg-danger-subtle text-danger p-3 rounded-circle d-inline-flex mb-3">
                        <i class="bi bi-telephone-outbound-fill fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Butuh Bantuan Mendesak?</h5>
                    <p class="text-muted small mb-3">
                        Tombol ini akan segera menghubungkan panggilan darurat kepolisian <strong>110 (bebas pulsa)</strong> dan mengirimkan koordinat lokasi Anda ke dashboard Command Center Petugas.
                    </p>
                    <div id="sosStatusMsg" class="alert d-none small py-2 mb-3"></div>
                    <button type="button" class="btn btn-danger w-100 py-3 rounded-pill fw-bold shadow-sm mb-2" onclick="triggerSosEmergencyRoute()">
                        <i class="bi bi-telephone-fill me-1"></i> Hubungi 110 & Kirim Alert Sinyal
                    </button>
                    <button type="button" class="btn btn-light w-100 rounded-pill text-muted small" data-bs-dismiss="modal">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle & Leaflet JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const routeApi = '/api/v1';
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
        let sosModalInstance = null;
        let shareModalInstance = null;

        const $route = id => document.getElementById(id);
        const showRouteStatus = (message, type = 'danger') => {
            const element = $route('routeStatus');
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

        // Render rute lengkap + marker Safety Hub & Laporan yang dihindari (FR-01.4 & FR-01.5)
        function drawRoute(data) {
            routeLayer.clearLayers();
            markersLayer.clearLayers();

            const safe = toLeafletCoordinates(data.safepass);
            const fastest = toLeafletCoordinates(data.fastest);
            const points = [...safe, ...fastest];

            // 1. Gambar Polyline Rute Tercepat (Kuning Putus-putus) & SafePass (Hijau Tebal)
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

            // 2. Marker Asal & Tujuan
            const origin = [Number(lastRouteRequest.origin_lat), Number(lastRouteRequest.origin_lng)];
            const destination = [Number(lastRouteRequest.destination_lat), Number(lastRouteRequest.destination_lng)];
            
            L.circleMarker(origin, {
                radius: 8, color: '#fff', weight: 2, fillColor: '#16a34a', fillOpacity: 1
            }).bindPopup('<strong>Titik Awal (Asal)</strong>').addTo(markersLayer);

            L.circleMarker(destination, {
                radius: 8, color: '#fff', weight: 2, fillColor: '#dc2626', fillOpacity: 1
            }).bindPopup('<strong>Titik Akhir (Tujuan)</strong>').addTo(markersLayer);

            // 3. Marker Safety Hubs yang Dilewati (Biru)
            const hubsPassed = data.safepass?.hubs_passed || [];
            hubsPassed.forEach(hub => {
                if (hub.lat && hub.lng) {
                    L.circleMarker([Number(hub.lat), Number(hub.lng)], {
                        radius: 7, color: '#fff', weight: 2, fillColor: '#2563eb', fillOpacity: 0.95
                    }).bindPopup(`<strong>Safety Hub: ${hub.name}</strong><br>Tipe: ${hub.type}<br>Dilewati dalam radius ${Math.round(hub.distance_m || 0)} m`).addTo(markersLayer);
                }
            });

            // 4. Marker Laporan yang Dihindari oleh SafePass (Kuning/Merah)
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

        // Pembuatan tautan Live Companion (POST /api/v1/shared-routes)
        async function shareRoute() {
            if (!lastRouteRequest || !lastRouteData) return;
            const token = localStorage.getItem('ta_token');
            if (!token) {
                if (confirm('Silakan masuk terlebih dahulu untuk membagikan rute Live Companion. Masuk sekarang?')) {
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
                // Buat tautan web yang langsung dapat dibuka di browser
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

        // Penampil Tautan Live Companion yang Dibagikan (GET /api/v1/shared-routes/{token})
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

                // Tampilkan banner Live Companion
                const banner = document.getElementById('sharedRouteBanner');
                if (banner) banner.classList.remove('d-none');
                
                if (shared.expires_at) {
                    document.getElementById('sharedRouteExpires').textContent = `Berlaku hingga: ${new Date(shared.expires_at).toLocaleTimeString('id-ID')}`;
                }

                // Isi koordinat form
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

        // SOS EMERGENCY DI RUTE (FR-01.6)
        function openSosModalRoute() {
            if (!sosModalInstance) {
                sosModalInstance = new bootstrap.Modal(document.getElementById('sosModalRoute'));
            }
            document.getElementById('sosStatusMsg').classList.add('d-none');
            sosModalInstance.show();
        }

        async function triggerSosEmergencyRoute() {
            const statusBox = document.getElementById('sosStatusMsg');
            if (statusBox) {
                statusBox.className = 'alert alert-info small py-2 mb-3';
                statusBox.textContent = 'Mencatat sinyal darurat ke Command Center...';
                statusBox.classList.remove('d-none');
            }

            const activeReg = $route('pilihanAsal').value;
            let lat = Number($route('originLat').value) || null;
            let lng = Number($route('originLng').value) || null;

            if (navigator.geolocation) {
                try {
                    const pos = await new Promise((resolve, reject) => {
                        navigator.geolocation.getCurrentPosition(resolve, reject, { timeout: 3000, enableHighAccuracy: true });
                    });
                    lat = pos.coords.latitude;
                    lng = pos.coords.longitude;
                } catch (e) {}
            }

            const payload = {
                region_slug: activeReg,
                message: 'Panggilan darurat SOS dari layar Navigasi Rute'
            };
            if (lat !== null && lng !== null) {
                payload.lat = lat;
                payload.lng = lng;
            }

            try {
                const token = localStorage.getItem('ta_token');
                const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json' };
                if (token) headers['Authorization'] = `Bearer ${token}`;

                await fetch(`${routeApi}/sos`, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(payload)
                });
            } catch (e) {
                console.warn('Gagal mencatat alert SOS:', e);
            }

            window.location.href = 'tel:110';
        }

        async function checkAuthStatus() {
            const token = localStorage.getItem('ta_token');
            const userInfo = document.getElementById('userInfo');
            const userName = document.getElementById('userName');
            const btnLogout = document.getElementById('btnLogout');
            const btnLogin = document.getElementById('btnLogin');

            if (!token) {
                if (btnLogin) btnLogin.classList.remove('d-none');
                if (userInfo) userInfo.classList.add('d-none');
                if (btnLogout) btnLogout.classList.add('d-none');
                return;
            }

            try {
                const res = await fetch(`${routeApi}/me`, {
                    headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` }
                });
                if (res.ok) {
                    const json = await res.json();
                    if (userName) userName.textContent = json.data?.name || 'Warga';
                    if (userInfo) userInfo.classList.remove('d-none');
                    if (btnLogout) btnLogout.classList.remove('d-none');
                    if (btnLogin) btnLogin.classList.add('d-none');
                } else {
                    localStorage.removeItem('ta_token');
                    if (btnLogin) btnLogin.classList.remove('d-none');
                    if (userInfo) userInfo.classList.add('d-none');
                    if (btnLogout) btnLogout.classList.add('d-none');
                }
            } catch (err) {}
        }

        async function logoutRoute() {
            const token = localStorage.getItem('ta_token');
            if (token) {
                try {
                    await fetch(`${routeApi}/logout`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` }
                    });
                } catch (e) {}
            }
            localStorage.removeItem('ta_token');
            window.location.reload();
        }

        async function loadRouteRegions() {
            const response = await fetch(`${routeApi}/regions`, {headers: {Accept: 'application/json'}});
            if (!response.ok) throw new Error('Daftar region gagal dimuat.');
            const payload = await response.json();
            const regions = Array.isArray(payload.data) ? payload.data : [];
            routeRegions = Object.fromEntries(regions.map(region => [region.slug, region]));
            setRegionDefaults();
        }

        // Klik pada peta untuk memilih koordinat tujuan
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

        checkAuthStatus();
        loadRouteRegions()
            .then(() => checkSharedRouteFromUrl())
            .catch(error => showRouteStatus(error.message));
    </script>
</body>
</html>
