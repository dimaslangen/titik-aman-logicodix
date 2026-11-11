<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TitikAman - Komparasi Rute</title>
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
            height: 420px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .route-option-card {
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
            cursor: pointer;
        }
        .route-option-card:hover {
            border-color: #0d9488;
            background-color: #f0fdfa;
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
                        <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">Malam Ini</small>
                        <p class="fw-semibold text-dark small mt-1 mb-0">Ruang kota lebih aman, satu laporan pada satu waktu.</p>
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
                            <span class="fw-semibold text-dark d-block">Sistem terhubung</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KONTEN UTAMA KANAN (Komparasi Rute) -->
            <div class="col-md-9 col-lg-10 p-4">
                
                <!-- Bar Input Asal & Tujuan dengan Pilihan Wilayah -->
                <div class="bg-white p-3 rounded-4 shadow-sm mb-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
                        <div class="input-group input-group-sm" style="max-width: 300px;">
                            <span class="input-group-text bg-success-subtle text-success border-0"><i class="bi bi-geo-alt-fill"></i></span>
                            <select class="form-select bg-light border-0 fw-semibold" id="pilihanAsal">
                                <option value="unesa-lidah-wetan">UNESA Lidah Wetan, Surabaya</option>
                                <option value="unesa-ketintang">UNESA Ketintang, Surabaya</option>
                                <option value="rawamangun">Rawamangun, Jakarta Timur</option>
                            </select>
                        </div>
                        <i class="bi bi-arrow-right text-muted"></i>
                        <div class="input-group input-group-sm flex-grow-1" style="max-width: 320px;">
                            <span class="input-group-text bg-danger-subtle text-danger border-0"><i class="bi bi-flag-fill"></i></span>
                            <input type="number" step="any" class="form-control bg-light border-0" id="destinationLat" placeholder="Tujuan latitude">
                            <input type="number" step="any" class="form-control bg-light border-0" id="destinationLng" placeholder="Tujuan longitude">
                        </div>
                    </div>
                    <div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                            <i class="bi bi-broadcast me-1"></i> Live Grid
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
                        <div class="col-md-3"><input type="number" step="any" class="form-control" id="originLat" placeholder="Asal latitude"></div>
                        <div class="col-md-3"><input type="number" step="any" class="form-control" id="originLng" placeholder="Asal longitude"></div>
                        <div class="col-md-3">
                            <div class="btn-group w-100" role="group" aria-label="Moda transportasi">
                                <button type="button" class="btn btn-outline-success mode-tab" data-mode="mobil">Mobil</button>
                                <button type="button" class="btn btn-success mode-tab active" data-mode="motor">Motor</button>
                                <button type="button" class="btn btn-outline-success mode-tab" data-mode="jalan_kaki">Jalan kaki</button>
                            </div>
                            <input type="hidden" id="routeMode" value="motor">
                        </div>
                        <div class="col-md-3"><button type="button" class="btn btn-success w-100" id="compareRoutesButton">Cari / Bandingkan</button></div>
                    </div>
                    <div id="routeStatus" class="alert d-none py-2"></div>
                    <dialog id="shareDialog" class="rounded-4 border-0 shadow p-4">
                        <h5 class="fw-bold">Bagikan Rute</h5>
                        <p class="text-muted small">Tautan Live Companion berlaku selama 6 jam.</p>
                        <input id="shareUrl" class="form-control mb-3" readonly>
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-outline-secondary" id="closeShareDialog">Tutup</button>
                            <button type="button" class="btn btn-success" id="copyShareUrl">Salin Tautan</button>
                        </div>
                    </dialog>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">Pilih Rute Perjalanan (Dua Opsi Ditemukan)</h6>
                        <button type="button" class="btn btn-outline-success btn-sm" id="shareRouteButton" disabled>Bagikan Rute</button>
                    </div>

                    <div class="row g-3">
                        <!-- Opsi 1: SafePass (Rute Aman) -->
                        <div class="col-md-6">
                            <div class="p-3 rounded-4 route-option-card border-success bg-success-subtle bg-opacity-10">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-success text-white px-3 py-1 rounded-pill">SafePass (Rekomendasi)</span>
                                    <span class="fw-bold text-success" id="safeDuration">-</span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">SafePass</h6>
                                <p class="text-muted small mb-3" id="safeDetails">Belum ada hasil rute.</p>
                                <button type="button" class="btn btn-success btn-sm w-100 rounded-pill fw-semibold shadow-sm" onclick="pilihRute('SafePass')">
                                    Gunakan Rute Ini <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Opsi 2: Rute Tercepat / Alternatif -->
                        <div class="col-md-6">
                            <div class="p-3 rounded-4 route-option-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge text-white px-3 py-1 rounded-pill" style="background-color: #0d9488;">Tercepat</span>
                                    <span class="fw-bold text-dark" id="fastDuration">-</span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Rute tercepat</h6>
                                <p class="text-muted small mb-3" id="fastDetails">Belum ada hasil rute.</p>
                                <button type="button" class="btn btn-outline-success btn-sm w-100 rounded-pill fw-semibold shadow-sm" onclick="pilihRute('Tercepat')">
                                    Gunakan Rute Ini <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const routeApi = '/api/v1';
        const mapRute = L.map('mapRute').setView([0, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(mapRute);
        const routeLayer = L.layerGroup().addTo(mapRute);
        let routeRegions = {};
        let lastRouteRequest = null;
        let lastRouteData = null;

        const $route = id => document.getElementById(id);
        const showRouteStatus = (message, type = 'danger') => {
            const element = $route('routeStatus');
            element.className = `alert alert-${type} py-2`;
            element.textContent = message;
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
            $route('destinationLat').value = center.lat;
            $route('destinationLng').value = center.lng;
            mapRute.setView([center.lat, center.lng], 15);
        }

        function drawRoute(data) {
            routeLayer.clearLayers();
            const safe = toLeafletCoordinates(data.safepass);
            const fastest = toLeafletCoordinates(data.fastest);
            const points = [...safe, ...fastest];
            if (fastest.length) L.polyline(fastest, {color: '#eab308', weight: 5, dashArray: '10 8'}).bindPopup('Rute tercepat').addTo(routeLayer);
            if (safe.length) L.polyline(safe, {color: '#16a34a', weight: 8}).bindPopup('SafePass').addTo(routeLayer);
            if (points.length) mapRute.fitBounds(points, {padding: [28, 28], maxZoom: 17});
            const origin = [Number(lastRouteRequest.origin_lat), Number(lastRouteRequest.origin_lng)];
            const destination = [Number(lastRouteRequest.destination_lat), Number(lastRouteRequest.destination_lng)];
            L.marker(origin).bindPopup('Lokasi asal').addTo(routeLayer);
            L.marker(destination).bindPopup('Lokasi tujuan').addTo(routeLayer);
        }

        function routeSummary(route) {
            const avoided = (route?.avoided_reports || []).length;
            const hubs = (route?.hubs_passed || []).length;
            return `${formatDistance(route?.distance)} · penalti ${route?.penalty_seconds || 0} dtk · ${avoided} laporan dihindari · ${hubs} safety hub`;
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
            button.textContent = 'Memuat...';
            try {
                const response = await fetch(`${routeApi}/routes/compare`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', Accept: 'application/json'},
                    body: JSON.stringify(body)
                });
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(payload.message || 'Rute tidak dapat dihitung. Periksa region dan koordinat.');
                lastRouteRequest = body;
                lastRouteData = payload.data;
                drawRoute(payload.data);
                $route('safeDuration').textContent = formatDuration(payload.data.safepass?.duration);
                $route('fastDuration').textContent = formatDuration(payload.data.fastest?.duration);
                $route('safeDetails').textContent = routeSummary(payload.data.safepass);
                $route('fastDetails').textContent = `${formatDistance(payload.data.fastest?.distance)} · skor ${payload.data.fastest?.score_seconds || 0} detik`;
                $route('shareRouteButton').disabled = false;
                showRouteStatus(payload.data.note || 'Rute berhasil dimuat.', 'success');
            } catch (error) {
                showRouteStatus(error.message);
            } finally {
                button.disabled = false;
                button.textContent = 'Cari / Bandingkan';
            }
        }

        async function shareRoute() {
            if (!lastRouteRequest || !lastRouteData) return;
            const token = localStorage.getItem('ta_token');
            if (!token) {
                alert('Silakan masuk terlebih dahulu untuk membagikan rute.');
                return;
            }
            try {
                const response = await fetch(`${routeApi}/shared-routes`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', Accept: 'application/json', Authorization: `Bearer ${token}`},
                    body: JSON.stringify({...lastRouteRequest, route_data: lastRouteData})
                });
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(payload.message || 'Rute gagal dibagikan.');
                const shareUrl = payload.data.share_url;
                $route('shareUrl').value = shareUrl;
                $route('shareDialog').showModal();
            } catch (error) {
                if (error.message.includes('Unauthenticated') || error.message.includes('401')) {
                    alert('Sesi login berakhir. Silakan masuk kembali untuk membagikan rute.');
                    return;
                }
                alert(error.message);
            }
        }

        async function loadRouteRegions() {
            const response = await fetch(`${routeApi}/regions`, {headers: {Accept: 'application/json'}});
            if (!response.ok) throw new Error('Daftar region gagal dimuat.');
            const payload = await response.json();
            const regions = Array.isArray(payload.data) ? payload.data : [];
            routeRegions = Object.fromEntries(regions.map(region => [region.slug, region]));
            setRegionDefaults();
        }

        $route('pilihanAsal').addEventListener('change', setRegionDefaults);
        document.querySelectorAll('.mode-tab').forEach(button => button.addEventListener('click', () => {
            document.querySelectorAll('.mode-tab').forEach(tab => {
                tab.className = tab === button ? 'btn btn-success mode-tab active' : 'btn btn-outline-success mode-tab';
            });
            $route('routeMode').value = button.dataset.mode;
        }));
        $route('compareRoutesButton').addEventListener('click', compareRoutes);
        $route('shareRouteButton').addEventListener('click', shareRoute);
        $route('closeShareDialog').addEventListener('click', () => $route('shareDialog').close());
        $route('copyShareUrl').addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText($route('shareUrl').value);
                $route('copyShareUrl').textContent = 'Tersalin';
            } catch (error) {
                alert(`Tautan tidak dapat disalin otomatis: ${error.message}`);
            }
        });
        loadRouteRegions().catch(error => showRouteStatus(error.message));

        function ubahWilayahRute() {
            setRegionDefaults();
        }

        function pilihRute(namaRute) {
            if (!lastRouteData) {
                showRouteStatus('Bandingkan rute terlebih dahulu.');
                return;
            }
            alert(`Rute ${namaRute} dipilih.`);
        }
    </script>
</body>
</html>