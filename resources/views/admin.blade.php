<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TitikAman - Command Center Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Leaflet CSS (Untuk Patrol Heatmap) -->
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
            border-radius: 10px;
            margin-bottom: 4px;
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
        .metric-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .icon-box {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .map-heatmap-container {
            height: 380px;
            border-radius: 16px;
            overflow: hidden;
        }
        .live-feed-list {
            max-height: 340px;
            overflow-y: auto;
        }
        .live-feed-item {
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }
        .live-feed-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            
            <!-- SIDEBAR ADMIN KIRI -->
            <div class="col-md-3 col-lg-2 px-3 py-4 sidebar d-flex flex-column justify-content-between">
                <div>
                    <!-- Logo / Brand -->
                    <div class="d-flex align-items-center mb-4 px-2">
                        <div class="bg-success text-white p-2 rounded-3 me-2">
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">TitikAman</h5>
                            <small class="text-muted" style="font-size: 10px;">CIVIC SAFETY GRID</small>
                        </div>
                    </div>

                    <!-- Menu Navigasi Admin -->
                    <ul class="nav flex-column mb-4">
                        <li class="nav-item">
                            <a href="/admin" class="nav-link active py-2 px-3">
                                <i class="bi bi-bar-chart-line me-2"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#moderasiSection" class="nav-link py-2 px-3 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-exclamation-triangle me-2"></i> Moderasi</span>
                                <span class="badge bg-danger rounded-pill" style="font-size: 10px;" id="badgePendingModeration">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/app" class="nav-link py-2 px-3">
                                <i class="bi bi-map me-2"></i> Peta Warga
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/rute-saya" class="nav-link py-2 px-3">
                                <i class="bi bi-signpost-split me-2"></i> Navigasi Rute
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/lapor-kondisi" class="nav-link py-2 px-3">
                                <i class="bi bi-camera me-2"></i> Form Laporan
                            </a>
                        </li>
                    </ul>

                    <!-- Card Bantuan / Status Grid -->
                    <div class="p-3 bg-light rounded-4 border-0 mb-3">
                        <small class="fw-bold text-dark d-block">Status API & Grid</small>
                        <small class="text-success fw-semibold" style="font-size: 11px;">
                            <span class="spinner-grow spinner-grow-sm text-success me-1" role="status" style="width: 8px; height: 8px;"></span> Live terhubung
                        </small>
                    </div>
                </div>

                <!-- Footer Admin Profile -->
                <div class="px-2 pt-3 border-top">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" style="width: 35px; height: 35px; font-size: 12px;" id="adminAvatar">
                                AD
                            </div>
                            <div style="font-size: 12px; max-width: 110px; overflow: hidden;">
                                <span class="fw-semibold text-dark d-block mb-0 text-truncate" id="adminName">Admin</span>
                                <small class="text-muted d-block text-truncate" style="font-size: 10px;" id="adminEmail">admin@titikaman.id</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm p-1 rounded-circle" onclick="adminLogout()" title="Keluar">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- KONTEN UTAMA KANAN (Command Center) -->
            <div class="col-md-9 col-lg-10 p-4">
                
                <!-- Sub-Header & Location Toggles -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <small class="text-uppercase text-muted fw-bold" style="font-size: 11px;">CIVIC SAFETY COMMAND CENTER</small>
                        <h3 class="fw-bold text-dark mb-0">Selamat datang, <span id="adminGreeting">Admin</span></h3>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-white p-1 rounded-pill shadow-sm border d-flex gap-1" id="regionBtnGroup">
                            <button class="btn btn-sm btn-success rounded-pill px-3 fw-semibold region-toggle" data-region="unesa-lidah-wetan">UNESA Lidah Wetan</button>
                            <button class="btn btn-sm btn-light text-secondary rounded-pill px-3 fw-semibold region-toggle" data-region="unesa-ketintang">UNESA Ketintang</button>
                            <button class="btn btn-sm btn-light text-secondary rounded-pill px-3 fw-semibold region-toggle" data-region="rawamangun">Rawamangun</button>
                        </div>
                        <div id="sosAlertBadge" class="d-none">
                            <span class="badge bg-danger rounded-pill px-3 py-2 animate-pulse">
                                <i class="bi bi-exclamation-octagon-fill me-1"></i> SOS AKTIF
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 4 KARTU METRIK UTAMA (DINAMIS DARI GET /api/v1/admin/dashboard) -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="metric-card p-3 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Laporan Wilayah</span>
                                <div class="icon-box bg-warning-subtle text-warning">
                                    <i class="bi bi-file-earmark-text-fill"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold text-dark mb-0" id="statTotalReports">-</h2>
                            <small class="text-muted fw-semibold" style="font-size: 11px;" id="statTotalReportsSub">Memuat...</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="metric-card p-3 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Terverifikasi</span>
                                <div class="icon-box bg-success-subtle text-success">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold text-dark mb-0" id="statVerified">-</h2>
                            <small class="text-success fw-semibold" style="font-size: 11px;" id="statVerifiedSub">-</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="metric-card p-3 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Perlu Moderasi</span>
                                <div class="icon-box bg-danger-subtle text-danger">
                                    <i class="bi bi-shield-exclamation"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold text-dark mb-0" id="statPending">-</h2>
                            <small class="text-secondary fw-semibold" style="font-size: 11px;" id="statPendingSub">-</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="metric-card p-3 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Alert SOS Terbuka</span>
                                <div class="icon-box bg-danger-subtle text-danger">
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold text-dark mb-0" id="statSosOpen">-</h2>
                            <small class="text-danger fw-semibold" style="font-size: 11px;" id="statSosOpenSub">-</small>
                        </div>
                    </div>
                </div>

                <!-- MAIN SECTION: HEATMAP PETA & LIVE FEED -->
                <div class="row g-3 mb-4">
                    <div class="col-lg-8">
                        <div class="bg-white p-3 rounded-4 border shadow-sm h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">INTENSITAS LAPORAN LAPANGAN</small>
                                    <h6 class="fw-bold text-dark mb-0">Patrol Heatmap • <span id="judulLokasiMap">UNESA Lidah Wetan</span></h6>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-secondary border" id="heatmapCellCount">0 grid aktif</span>
                                </div>
                            </div>
                            <div class="map-heatmap-container">
                                <div id="adminMap" style="width: 100%; height: 100%;"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                <div class="d-flex align-items-center gap-3 small text-muted">
                                    <span><i class="bi bi-circle-fill text-danger me-1"></i> Densitas Tinggi (≥3)</span>
                                    <span><i class="bi bi-circle-fill text-warning me-1"></i> Densitas Sedang (1-2)</span>
                                    <span><i class="bi bi-circle-fill text-primary me-1"></i> Safety Hub</span>
                                </div>
                                <small class="text-muted" id="lastUpdatedHeatmap">Diperbarui baru saja</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="bg-white p-3 rounded-4 border shadow-sm h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">LIVE FEED</small>
                                        <h6 class="fw-bold text-dark mb-0">Aktivitas Terbaru</h6>
                                    </div>
                                    <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
                                </div>

                                <div class="live-feed-list pt-2" id="liveFeedContainer">
                                    <div class="text-center py-4 text-muted small">
                                        <span class="spinner-border spinner-border-sm me-1" role="status"></span> Memuat aktivitas...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION: ANTRIAN PEMERIKSAAN - MODERASI LAPORAN (Tahap 3 Preview) -->
                <div class="bg-white p-4 rounded-4 border shadow-sm" id="moderasiSection">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <div>
                            <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">ANTRIAN PEMERIKSAAN LAPORAN</small>
                            <h5 class="fw-bold text-dark mb-0">Laporan Terbaru Wilayah</h5>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill small">
                                <i class="bi bi-info-circle me-1"></i> Aksi moderasi lengkap di Tahap 3
                            </span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="text-muted border-bottom" style="font-size: 12px; background-color: #fafbfc;">
                                <tr>
                                    <th class="py-3">ID LAPORAN</th>
                                    <th class="py-3">WILAYAH</th>
                                    <th class="py-3">KATEGORI</th>
                                    <th class="py-3">VALIDASI KOMUNITAS</th>
                                    <th class="py-3">STATUS</th>
                                    <th class="py-3 text-end">WAKTU</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px;" id="reportsTableBody">
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted small">
                                        <span class="spinner-border spinner-border-sm me-1" role="status"></span> Memuat daftar laporan...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle & Leaflet JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const adminApi = '/api/v1';
        const adminToken = localStorage.getItem('ta_admin_token');

        // Guard autentikasi admin: jika tidak ada token, arahkan ke login admin
        if (!adminToken) {
            window.location.href = '/admin/login';
        }

        const regionConfigs = {
            'unesa-lidah-wetan': { name: 'UNESA Lidah Wetan', center: [-7.30071, 112.67289], zoom: 15 },
            'unesa-ketintang': { name: 'UNESA Ketintang', center: [-7.31143, 112.72937], zoom: 15 },
            'rawamangun': { name: 'Rawamangun', center: [-6.1928, 106.89763], zoom: 15 }
        };

        let activeRegion = 'unesa-lidah-wetan';
        const adminMap = L.map('adminMap').setView(regionConfigs[activeRegion].center, regionConfigs[activeRegion].zoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(adminMap);

        const heatmapLayer = L.layerGroup().addTo(adminMap);
        const hubsLayer = L.layerGroup().addTo(adminMap);

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, char => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
            }[char]));
        }

        function formatTimeAgo(isoString) {
            if (!isoString) return '-';
            const diffSeconds = Math.floor((new Date() - new Date(isoString)) / 1000);
            if (diffSeconds < 60) return `${diffSeconds} dtk lalu`;
            const diffMinutes = Math.floor(diffSeconds / 60);
            if (diffMinutes < 60) return `${diffMinutes} mnt lalu`;
            const diffHours = Math.floor(diffMinutes / 60);
            if (diffHours < 24) return `${diffHours} jam lalu`;
            return `${Math.floor(diffHours / 24)} hari lalu`;
        }

        async function loadAdminProfile() {
            try {
                const response = await fetch(`${adminApi}/me`, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${adminToken}`
                    }
                });

                if (response.status === 401 || response.status === 403) {
                    localStorage.removeItem('ta_admin_token');
                    window.location.href = '/admin/login';
                    return;
                }

                if (response.ok) {
                    const json = await response.json();
                    const admin = json.data;
                    document.getElementById('adminName').textContent = admin.name || 'Admin';
                    document.getElementById('adminGreeting').textContent = admin.name || 'Admin';
                    document.getElementById('adminEmail').textContent = admin.email || 'admin@titikaman.id';
                    
                    const initials = (admin.name || 'AD').split(' ').map(s => s[0]).slice(0, 2).join('').toUpperCase();
                    document.getElementById('adminAvatar').textContent = initials;
                }
            } catch (err) {
                console.warn('Gagal memuat profil admin:', err);
            }
        }

        async function loadAdminDashboard(regionSlug) {
            activeRegion = regionSlug;
            const regionInfo = regionConfigs[regionSlug] || { name: regionSlug, center: [-7.30071, 112.67289], zoom: 14 };
            document.getElementById('judulLokasiMap').textContent = regionInfo.name;
            adminMap.setView(regionInfo.center, regionInfo.zoom);

            try {
                const [dashboardResponse, hubsResponse] = await Promise.all([
                    fetch(`${adminApi}/admin/dashboard?region_slug=${encodeURIComponent(regionSlug)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${adminToken}`
                        }
                    }),
                    fetch(`${adminApi}/safety-hubs?region_slug=${encodeURIComponent(regionSlug)}`, {
                        headers: { 'Accept': 'application/json' }
                    })
                ]);

                if (dashboardResponse.status === 401 || dashboardResponse.status === 403) {
                    localStorage.removeItem('ta_admin_token');
                    window.location.href = '/admin/login';
                    return;
                }

                if (!dashboardResponse.ok) {
                    throw new Error('Gagal mengambil data dashboard admin.');
                }

                const dashboardPayload = await dashboardResponse.json();
                const data = dashboardPayload.data || {};
                const totals = data.totals || {};

                // 1. Perbarui 4 Kartu Metrik
                const totalReports = Number(totals.reports || 0);
                const verifiedReports = Number(totals.verified || 0);
                const pendingModeration = Number(totals.pending_moderation || 0);
                const unverifiedReports = Number(totals.unverified || 0);
                const sosOpen = Number(totals.sos_open || 0);

                document.getElementById('statTotalReports').textContent = totalReports;
                document.getElementById('statTotalReportsSub').textContent = `${unverifiedReports} belum verifikasi`;

                document.getElementById('statVerified').textContent = verifiedReports;
                const verifiedPct = totalReports > 0 ? Math.round((verifiedReports / totalReports) * 100) : 0;
                document.getElementById('statVerifiedSub').textContent = `${verifiedPct}% dari total laporan`;

                document.getElementById('statPending').textContent = pendingModeration;
                document.getElementById('statPendingSub').textContent = pendingModeration > 0 ? 'Perlu tindakan moderator' : 'Semua tertinjau';
                document.getElementById('badgePendingModeration').textContent = pendingModeration;

                document.getElementById('statSosOpen').textContent = sosOpen;
                document.getElementById('statSosOpenSub').textContent = sosOpen > 0 ? 'Darurat aktif!' : 'Tidak ada sinyal aktif';

                const sosBadge = document.getElementById('sosAlertBadge');
                if (sosOpen > 0) {
                    sosBadge.classList.remove('d-none');
                } else {
                    sosBadge.classList.add('d-none');
                }

                // 2. Render Patrol Heatmap Kerapatan Laporan (FR-04.2)
                heatmapLayer.clearLayers();
                const heatmapCells = Array.isArray(data.heatmap) ? data.heatmap : [];
                document.getElementById('heatmapCellCount').textContent = `${heatmapCells.length} grid intensitas`;

                heatmapCells.forEach(cellItem => {
                    const coords = String(cellItem.cell || '').split(',').map(Number);
                    if (coords.length === 2 && !isNaN(coords[0]) && !isNaN(coords[1])) {
                        const count = Number(cellItem.count || 1);
                        const verified = Number(cellItem.verified || 0);
                        const radius = Math.min(350, 100 + count * 60);
                        const color = count >= 3 ? '#ef4444' : (count === 2 ? '#f97316' : '#f59e0b');

                        L.circle(coords, {
                            radius: radius,
                            color: color,
                            fillColor: color,
                            fillOpacity: 0.35,
                            weight: 2
                        }).bindPopup(`
                            <div class="p-1">
                                <strong>Patrol Grid: ${escapeHtml(cellItem.cell)}</strong><br>
                                <span class="badge bg-dark mt-1 mb-1">${count} Laporan di grid ini</span><br>
                                <small class="text-success"><i class="bi bi-check-circle me-1"></i>${verified} Terverifikasi</small>
                            </div>
                        `).addTo(heatmapLayer);
                    }
                });

                // 3. Render Safety Hubs pada Peta Admin
                hubsLayer.clearLayers();
                if (hubsResponse.ok) {
                    const hubsPayload = await hubsResponse.json();
                    const hubs = Array.isArray(hubsPayload.data) ? hubsPayload.data : [];
                    hubs.forEach(hub => {
                        L.circleMarker([Number(hub.lat), Number(hub.lng)], {
                            radius: 7,
                            color: '#ffffff',
                            weight: 2,
                            fillColor: '#2563eb',
                            fillOpacity: 0.95
                        }).bindPopup(`<strong>${escapeHtml(hub.name)}</strong><br>Safety Hub · ${escapeHtml(hub.type)}`).addTo(hubsLayer);
                    });
                }

                // 4. Render Live Feed (recent_reports & sos_alerts)
                renderLiveFeed(data.recent_reports || [], data.sos_alerts || []);

                // 5. Render Tabel Laporan Terbaru
                renderReportsTable(data.recent_reports || []);

                document.getElementById('lastUpdatedHeatmap').textContent = `Diperbarui ${new Date().toLocaleTimeString('id-ID')}`;

            } catch (err) {
                console.error('Error saat memuat dashboard admin:', err);
                alert(`Gagal memuat data dashboard: ${err.message}`);
            }
        }

        function renderLiveFeed(recentReports, sosAlerts) {
            const container = document.getElementById('liveFeedContainer');
            if (!container) return;

            let html = '';

            // Tampilkan SOS Alerts jika ada
            sosAlerts.slice(0, 3).forEach(sos => {
                const isOpen = sos.status === 'open';
                html += `
                    <div class="live-feed-item d-flex align-items-start gap-3">
                        <div class="bg-danger-subtle text-danger p-2 rounded-circle mt-1">
                            <i class="bi bi-exclamation-octagon-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold text-danger small mb-0">Alert SOS Darurat</h6>
                                <span class="badge ${isOpen ? 'bg-danger' : 'bg-secondary'} rounded-pill" style="font-size: 9px;">${escapeHtml(sos.status)}</span>
                            </div>
                            <small class="text-dark d-block fw-semibold" style="font-size: 11px;">${escapeHtml(sos.message || 'Panggilan SOS')}</small>
                            <small class="text-muted d-block" style="font-size: 10px;">${escapeHtml(sos.region_slug || '-')} • ${formatTimeAgo(sos.created_at)}</small>
                        </div>
                    </div>
                `;
            });

            // Tampilkan Recent Reports
            if (recentReports.length === 0 && sosAlerts.length === 0) {
                container.innerHTML = '<div class="text-center py-4 text-muted small">Belum ada aktivitas laporan baru.</div>';
                return;
            }

            recentReports.slice(0, 6).forEach(rep => {
                const iconClass = rep.category === 'penerangan_padam' ? 'bi-lightbulb-fill text-warning' : (rep.category === 'area_sepi' ? 'bi-eye-fill text-danger' : 'bi-cone-striped text-warning');
                const bgClass = rep.category === 'area_sepi' ? 'bg-danger-subtle' : 'bg-warning-subtle';
                
                html += `
                    <div class="live-feed-item d-flex align-items-start gap-3">
                        <div class="${bgClass} p-2 rounded-circle mt-1">
                            <i class="bi ${iconClass}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold text-dark small mb-0">${escapeHtml(rep.label || rep.category)}</h6>
                                <span class="badge ${rep.status === 'VERIFIED' ? 'bg-success' : (rep.status === 'PENDING_MODERATION' ? 'bg-warning text-dark' : 'bg-secondary')} rounded-pill" style="font-size: 9px;">${escapeHtml(rep.status)}</span>
                            </div>
                            <small class="text-muted d-block" style="font-size: 11px;">${escapeHtml(rep.region_slug)} • ${formatTimeAgo(rep.created_at)}</small>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function renderReportsTable(reports) {
            const tbody = document.getElementById('reportsTableBody');
            if (!tbody) return;

            if (reports.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted small">Tidak ada laporan pada wilayah ini.</td></tr>';
                return;
            }

            let html = '';
            reports.forEach(rep => {
                const badgeClass = rep.status === 'VERIFIED' ? 'bg-success' : (rep.status === 'PENDING_MODERATION' ? 'bg-warning text-dark' : 'bg-secondary');
                html += `
                    <tr>
                        <td class="py-3">
                            <span class="fw-bold text-dark">#${rep.id}</span>
                        </td>
                        <td><span class="badge bg-light text-dark border">${escapeHtml(rep.region_slug)}</span></td>
                        <td>${escapeHtml(rep.label || rep.category)}</td>
                        <td>
                            <span class="badge bg-success-subtle text-success px-2 py-1 rounded">
                                <i class="bi bi-hand-thumbs-up me-1"></i>${rep.upvotes_count || 0} Up-Check
                            </span>
                        </td>
                        <td><span class="badge ${badgeClass} px-3 py-1 rounded-pill" style="font-size: 11px;">${escapeHtml(rep.status)}</span></td>
                        <td class="text-end text-muted small">${formatTimeAgo(rep.created_at)}</td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        async function adminLogout() {
            if (confirm('Apakah Anda yakin ingin keluar dari Command Center Admin?')) {
                try {
                    await fetch(`${adminApi}/logout`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${adminToken}`
                        }
                    });
                } catch (e) {}
                localStorage.removeItem('ta_admin_token');
                window.location.href = '/admin/login';
            }
        }

        // Event listener pilihan wilayah
        document.querySelectorAll('.region-toggle').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.region-toggle').forEach(b => {
                    b.className = 'btn btn-sm btn-light text-secondary rounded-pill px-3 fw-semibold region-toggle';
                });
                this.className = 'btn btn-sm btn-success rounded-pill px-3 fw-semibold region-toggle';
                loadAdminDashboard(this.dataset.region);
            });
        });

        // Inisialisasi awal
        loadAdminProfile();
        loadAdminDashboard(activeRegion);
    </script>
</body>
</html>
