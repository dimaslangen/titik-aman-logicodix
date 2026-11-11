<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TitikAman - Safe Navigation App</title>
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
        .nav-link:hover, .nav-link.active {
            background-color: #e6fffa;
            color: #0d9488;
        }
        .map-container {
            height: 500px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            
            <!-- SIDEBAR KIRI (Menu Navigasi) -->
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
                        <hr class="my-2">
                        <small class="text-muted" style="font-size: 11px;">Data komunitas aktif di UNESA Surabaya dan Rawamangun, Jakarta.</small>
                    </div>

                    <!-- Menu Navigasi -->
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="/app" class="nav-link active py-2 px-3">
                                <i class="bi bi-map me-2"></i> Peta Aman
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/rute-saya" class="nav-link py-2 px-3">
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

                <!-- Footer Status Terhubung -->
                <div class="px-2 pt-3 border-top">
                    <div class="d-flex align-items-center">
                        <span class="spinner-grow spinner-grow-sm text-success me-2" role="status"></span>
                        <div style="font-size: 12px;">
                            <span class="fw-semibold text-dark d-block">Sistem terhubung</span>
                            <span class="text-muted" style="font-size: 10px;">Grid diperbarui langsung</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KONTEN UTAMA KANAN (Peta & Aksi) -->
            <div class="col-md-9 col-lg-10 p-4">
                
                <!-- Topbar Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm">
                    <div class="d-flex align-items-center">
                        <h4 class="fw-bold mb-0 text-dark me-3">TitikAman Safe Navigation App</h4>
                        <span class="badge bg-light text-secondary border">v1.0</span>
                    </div>
                    <div id="authHeader" class="d-flex align-items-center gap-2">
                        <span id="userInfo" class="badge bg-light text-dark border px-3 py-2 rounded-pill d-none align-items-center gap-1">
                            <i class="bi bi-person-fill text-success"></i> <span id="userName">Warga</span>
                        </span>
                        <button id="btnLogout" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold d-none" onclick="logoutApp()">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar
                        </button>
                        <a id="btnLogin" href="/masuk" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                        </a>
                    </div>
                </div>

                <!-- Filter / Quick Bar di atas Peta -->
                <div class="bg-white p-3 rounded-4 shadow-sm mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex gap-2">
                        <button id="btn-unesa" data-region="unesa-lidah-wetan" class="btn btn-sm btn-outline-success active">UNESA Lidah Wetan</button>
                        <button id="btn-ketintang" data-region="unesa-ketintang" class="btn btn-sm btn-outline-secondary">UNESA Ketintang</button>
                        <button id="btn-rawamangun" data-region="rawamangun" class="btn btn-sm btn-outline-secondary">Rawamangun</button>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                            <i class="bi bi-geo-alt-fill me-1"></i> GPS Aktif
                        </span>
                    </div>
                </div>

                <!-- Area Peta Interaktif (Leaflet JS) -->
                <div class="map-container mb-3 position-relative">
                    <div id="map" style="width: 100%; height: 100%;"></div>
                </div>

                <!-- Banner Bawah (Mau ke mana malam ini?) -->
                <div class="bg-teal text-white p-4 rounded-4 shadow-sm d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);">
                    <div>
                        <span class="badge bg-white text-teal mb-1 fw-bold" style="color: #0d9488;">NAVIGASI MALAM BERBASIS KOMUNITAS</span>
                        <h5 class="fw-bold mb-0 text-white">Mau ke mana malam ini?</h5>
                    </div>
                    <a href="/rute-saya" class="btn btn-light text-teal fw-bold px-4 rounded-pill shadow-sm text-decoration-none">
                        Cari Rute Aman <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                <!-- Floating SOS Emergency Button (PRD FR-01.6) -->
                <button type="button" class="btn btn-danger rounded-pill shadow-lg position-fixed d-flex align-items-center gap-2 px-3 py-2" style="bottom: 24px; right: 24px; z-index: 1050; font-weight: 700; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.45);" onclick="openSosModal()">
                    <i class="bi bi-telephone-fill"></i> SOS 110
                </button>

                <!-- Modal SOS Emergency -->
                <div class="modal fade" id="sosModal" tabindex="-1" aria-labelledby="sosModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow">
                            <div class="modal-header bg-danger text-white border-0 rounded-top-4">
                                <h5 class="modal-title fw-bold" id="sosModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i> Panggilan Darurat SOS</h5>
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
                                <button type="button" class="btn btn-danger w-100 py-3 rounded-pill fw-bold shadow-sm mb-2" id="btnHubungi110" onclick="triggerSosEmergency()">
                                    <i class="bi bi-telephone-fill me-1"></i> Hubungi 110 & Kirim Alert Sinyal
                                </button>
                                <button type="button" class="btn btn-light w-100 rounded-pill text-muted small" data-bs-dismiss="modal">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle & Leaflet JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const appApi = '/api/v1';
        let appRegions = {};
        let activeRegion = 'unesa-lidah-wetan';
        let mapLayers = L.layerGroup();
        let sosModalInstance = null;
        const map = L.map('map').setView([0, 0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        mapLayers.addTo(map);

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, char => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
            }[char]));
        }

        function regionCenter(region) {
            const center = region?.center || [region?.center_lat, region?.center_lng];
            return Array.isArray(center) ? center : [Number(center?.lat), Number(center?.lng)];
        }

        function reportColor(category) {
            return category === 'hambatan_fisik' ? '#f59e0b' : '#dc2626';
        }

        async function loadAppRegions() {
            const response = await fetch(`${appApi}/regions`, {headers: {Accept: 'application/json'}});
            if (!response.ok) throw new Error('Daftar region tidak dapat dimuat.');
            const payload = await response.json();
            const regions = Array.isArray(payload.data) ? payload.data : payload.data?.regions;
            if (!Array.isArray(regions)) throw new Error('Format data region tidak valid.');
            appRegions = Object.fromEntries(regions.map(region => [region.slug, region]));
        }

        async function loadAppMap(regionSlug) {
            const region = appRegions[regionSlug];
            if (!region) throw new Error('Region tidak tersedia.');
            activeRegion = regionSlug;
            const center = regionCenter(region);
            map.setView(center, 15);
            mapLayers.clearLayers();
            const [reportsResponse, hubsResponse] = await Promise.all([
                fetch(`${appApi}/reports?region_slug=${encodeURIComponent(regionSlug)}&per_page=100`, {headers: {Accept: 'application/json'}}),
                fetch(`${appApi}/safety-hubs?region_slug=${encodeURIComponent(regionSlug)}`, {headers: {Accept: 'application/json'}})
            ]);
            if (!reportsResponse.ok || !hubsResponse.ok) throw new Error('Data laporan atau safety hub gagal dimuat.');
            const reportsPayload = await reportsResponse.json();
            const hubsPayload = await hubsResponse.json();
            const reports = Array.isArray(reportsPayload.data) ? reportsPayload.data : [];
            const hubs = Array.isArray(hubsPayload.data) ? hubsPayload.data : [];

            reports.forEach(report => {
                const isVerified = report.status === 'VERIFIED';
                const badgeClass = isVerified ? 'bg-success' : (report.status === 'PENDING_MODERATION' ? 'bg-warning text-dark' : 'bg-secondary');
                const upvotes = Number(report.upvotes_count || 0);

                const popupContent = `
                    <div style="min-width: 210px;" class="py-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-dark">${escapeHtml(report.label || report.category)}</strong>
                        </div>
                        <span class="badge ${badgeClass} mb-2">${escapeHtml(report.status)}</span>
                        <p class="small text-muted mb-2">${escapeHtml(report.description || 'Tidak ada keterangan tambahan.')}</p>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="small fw-semibold text-secondary">
                                <i class="bi bi-hand-thumbs-up me-1"></i> ${upvotes} Up-Check
                            </span>
                            ${!isVerified ? `
                                <button class="btn btn-sm btn-success py-1 px-2 rounded-pill fw-semibold shadow-sm" style="font-size: 11px;" onclick="upvoteReport(${report.id})">
                                    <i class="bi bi-hand-thumbs-up-fill me-1"></i> Up-Check
                                </button>
                            ` : `
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Terverifikasi</span>
                            `}
                        </div>
                    </div>
                `;

                L.circleMarker([Number(report.lat), Number(report.lng)], {
                    radius: 8, color: '#fff', weight: 2, fillColor: reportColor(report.category), fillOpacity: .95
                }).bindPopup(popupContent).addTo(mapLayers);
            });

            hubs.forEach(hub => {
                L.circleMarker([Number(hub.lat), Number(hub.lng)], {
                    radius: 7, color: '#fff', weight: 2, fillColor: '#2563eb', fillOpacity: .95
                }).bindPopup(`<strong>${escapeHtml(hub.name)}</strong><br>Safety Hub · ${escapeHtml(hub.type)}`).addTo(mapLayers);
            });
        }

        async function upvoteReport(reportId) {
            const token = localStorage.getItem('ta_token');
            if (!token) {
                if (confirm('Anda perlu masuk (login) terlebih dahulu untuk memberikan Up-Check konfirmasi laporan. Masuk sekarang?')) {
                    window.location.href = '/masuk';
                }
                return;
            }

            try {
                const response = await fetch(`${appApi}/reports/${reportId}/upvote`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });
                const data = await response.json().catch(() => ({}));

                if (response.status === 401) {
                    alert('Sesi login telah berakhir. Silakan masuk kembali.');
                    localStorage.removeItem('ta_token');
                    window.location.href = '/masuk';
                    return;
                }

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal memberikan Up-Check.');
                }

                alert(data.message || 'Up-Check berhasil dicatat!');
                loadAppMap(activeRegion).catch(console.error);
            } catch (err) {
                alert(err.message);
            }
        }

        function openSosModal() {
            if (!sosModalInstance) {
                sosModalInstance = new bootstrap.Modal(document.getElementById('sosModal'));
            }
            const statusBox = document.getElementById('sosStatusMsg');
            if (statusBox) statusBox.classList.add('d-none');
            sosModalInstance.show();
        }

        async function triggerSosEmergency() {
            const statusBox = document.getElementById('sosStatusMsg');
            if (statusBox) {
                statusBox.className = 'alert alert-info small py-2 mb-3';
                statusBox.textContent = 'Mencatat sinyal darurat ke Command Center...';
                statusBox.classList.remove('d-none');
            }

            const region = appRegions[activeRegion];
            const center = region ? regionCenter(region) : null;
            let lat = center ? Number(center[0]) : null;
            let lng = center ? Number(center[1]) : null;

            if (navigator.geolocation) {
                try {
                    const pos = await new Promise((resolve, reject) => {
                        navigator.geolocation.getCurrentPosition(resolve, reject, { timeout: 3000, enableHighAccuracy: true });
                    });
                    lat = pos.coords.latitude;
                    lng = pos.coords.longitude;
                } catch (e) {
                    // Fallback menggunakan pusat region
                }
            }

            const payload = {
                region_slug: activeRegion,
                message: 'Panggilan darurat SOS dari Peta Aman'
            };
            if (lat !== null && lng !== null) {
                payload.lat = lat;
                payload.lng = lng;
            }

            try {
                const token = localStorage.getItem('ta_token');
                const headers = {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                };
                if (token) headers['Authorization'] = `Bearer ${token}`;

                await fetch(`${appApi}/sos`, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(payload)
                });
            } catch (e) {
                console.warn('Gagal mencatat alert SOS ke backend:', e);
            }

            // Panggilan darurat 110 (PRD FR-01.6)
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
                const res = await fetch(`${appApi}/me`, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });
                if (res.ok) {
                    const json = await res.json();
                    const user = json.data;
                    if (userName) userName.textContent = user.name || 'Warga';
                    if (userInfo) userInfo.classList.remove('d-none');
                    if (btnLogout) btnLogout.classList.remove('d-none');
                    if (btnLogin) btnLogin.classList.add('d-none');
                } else {
                    localStorage.removeItem('ta_token');
                    if (btnLogin) btnLogin.classList.remove('d-none');
                    if (userInfo) userInfo.classList.add('d-none');
                    if (btnLogout) btnLogout.classList.add('d-none');
                }
            } catch (err) {
                console.warn('Gagal memverifikasi token auth:', err);
            }
        }

        async function logoutApp() {
            const token = localStorage.getItem('ta_token');
            if (token) {
                try {
                    await fetch(`${appApi}/logout`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`
                        }
                    });
                } catch (e) {}
            }
            localStorage.removeItem('ta_token');
            window.location.reload();
        }

        function selectAppRegion(button) {
            document.querySelectorAll('[data-region]').forEach(item => {
                item.className = item === button
                    ? 'btn btn-sm btn-outline-success active'
                    : 'btn btn-sm btn-outline-secondary';
            });
            loadAppMap(button.dataset.region).catch(error => alert(error.message));
        }

        document.querySelectorAll('[data-region]').forEach(button => button.addEventListener('click', () => selectAppRegion(button)));
        checkAuthStatus();
        loadAppRegions()
            .then(() => loadAppMap(activeRegion))
            .catch(error => alert(`Gagal memuat peta: ${error.message}`));
    </script>
</body>
</html>
