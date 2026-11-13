<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/TitikAman(no-teks).png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <title>TitikAman - Command Center Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Leaflet CSS (Untuk Patrol Heatmap) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        :root {
            --ta-primary: #15803D;
            --ta-primary-deep: #11653A;
            --ta-lime: #65A30D;
            --ta-lime-bright: #80C826;
            --ta-active-bg: rgba(21, 128, 61, 0.10);
            --ta-active-border: rgba(21, 128, 61, 0.22);
        }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #f8fafc;
            color: #0f172a;
            font-family: 'Plus Jakarta Sans', 'Instrument Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .app-wrapper {
            display: flex;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
        }
        .sidebar {
            width: 260px;
            min-width: 260px;
            max-width: 260px;
            height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            overflow-y: auto;
            flex-shrink: 0;
        }
        .main-content {
            flex: 1 1 auto;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1.5rem;
        }
        .nav-link {
            color: #4a5568;
            font-weight: 500;
            border-radius: 10px;
            margin-bottom: 4px;
            transition: all 0.2s ease;
        }
        .nav-link:hover {
            background-color: var(--ta-active-bg);
            color: var(--ta-primary-deep);
        }
        .nav-link.active {
            background-color: var(--ta-active-bg);
            border: 1px solid var(--ta-active-border);
            color: var(--ta-primary-deep);
            font-weight: 600;
        }
        .nav-group-label {
            color: #94a3b8;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .14em;
            margin: 1.25rem .75rem .5rem;
        }
        .preview-link { color: #64748b; }
        .preview-link:hover { color: var(--ta-primary-deep); background-color: #f8fafc; }
        .btn-success {
            background: linear-gradient(135deg, var(--ta-primary-deep), #2fa33e);
            border-color: var(--ta-primary-deep);
        }
        .btn-success:hover, .btn-success:focus {
            background: linear-gradient(135deg, #0d5130, var(--ta-primary));
            border-color: var(--ta-primary-deep);
        }
        .text-success { color: var(--ta-primary-deep) !important; }
        .badge.bg-success { background-color: var(--ta-primary) !important; }
        .form-control:focus, .form-select:focus, .form-check-input:focus {
            border-color: var(--ta-primary);
            box-shadow: 0 0 0 .25rem rgba(21, 128, 61, .15);
        }
        .moderation-search { min-width: 220px; }
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
            min-width: 42px;
            aspect-ratio: 1 / 1;
            flex: 0 0 auto;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .ta-circle {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            flex-shrink: 0;
            aspect-ratio: 1 / 1;
            border-radius: 50% !important;
        }
        .ta-circle-36 { width: 36px !important; height: 36px !important; min-width: 36px; }
        .ta-circle-40 { width: 40px !important; height: 40px !important; min-width: 40px; }
        .patrol-heatmap-card { scroll-margin-top: 20px; }
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

    <div class="app-wrapper">
        
        <!-- SIDEBAR ADMIN KIRI -->
        <div class="sidebar px-3 py-4 d-flex flex-column justify-content-between">
            <div>
                <!-- Logo / Brand -->
                <div class="mb-4 px-2">
                    <x-brand-logo id="admin-sidebar-logo" theme="light" size="md" badge="ADMIN" />
                    <small class="text-muted d-block mt-2" style="font-size: 10px; letter-spacing: 0.08em;">CIVIC SAFETY COMMAND CENTER</small>
                </div>

                <!-- Menu Navigasi Admin -->
                <p class="nav-group-label">COMMAND CENTER</p>
                <ul class="nav flex-column mb-2 admin-section-nav">
                    <li class="nav-item">
                        <a href="#adminTop" class="nav-link active py-2 px-3" data-admin-nav-target="top">
                            <i class="bi bi-bar-chart-line me-2"></i> Overview
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#moderasiSection" class="nav-link py-2 px-3 d-flex justify-content-between align-items-center" data-admin-nav-target="moderasiSection">
                            <span><i class="bi bi-exclamation-triangle me-2"></i> Moderasi</span>
                            <span class="badge bg-danger rounded-pill" style="font-size: 10px;" id="badgePendingModeration">0</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#patrolHeatmapCard" class="nav-link py-2 px-3" data-admin-nav-target="patrolHeatmapCard">
                            <i class="bi bi-activity me-2"></i> Patrol Heatmap
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#adminSosModal" class="nav-link py-2 px-3" data-bs-toggle="modal" data-bs-target="#adminSosModal">
                            <i class="bi bi-telephone me-2"></i> Alert SOS
                        </a>
                    </li>
                </ul>

                <p class="nav-group-label">PRATINJAU WARGA</p>
                <ul class="nav flex-column mb-4">
                    <li class="nav-item">
                        <a href="/app" class="nav-link preview-link py-2 px-3 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-map me-2"></i> Peta Warga</span><i class="bi bi-box-arrow-up-right small"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/rute-saya" class="nav-link preview-link py-2 px-3 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-signpost-split me-2"></i> Navigasi Rute</span><i class="bi bi-box-arrow-up-right small"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/safety-hubs-ui" class="nav-link preview-link py-2 px-3 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-building me-2"></i> Direktori Hub</span><i class="bi bi-box-arrow-up-right small"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/lapor-kondisi" class="nav-link preview-link py-2 px-3 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-camera me-2"></i> Form Laporan</span><i class="bi bi-box-arrow-up-right small"></i>
                        </a>
                    </li>
                </ul>

                    <!-- Card Status Grid & API -->
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
                            <div class="bg-success text-white rounded-circle ta-circle ta-circle-36 d-flex align-items-center justify-content-center fw-bold me-2" style="font-size: 12px;" id="adminAvatar">
                                AD
                            </div>
                            <div style="font-size: 12px; max-width: 110px; overflow: hidden;">
                                <span class="fw-semibold text-dark d-block mb-0 text-truncate" id="adminName">Admin</span>
                                <small class="text-muted d-block text-truncate" style="font-size: 10px;" id="adminEmail">admin@titikaman.id</small>
                            </div>
                        </div>
                        <button type="button" id="adminLogoutBtn" class="btn btn-outline-danger btn-sm p-1 rounded-circle ta-circle ta-circle-36" onclick="adminLogout()" title="Keluar">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- KONTEN UTAMA KANAN (Command Center) -->
            <div class="main-content">
                
                <!-- Sub-Header & Location Toggles -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <small class="text-uppercase text-muted fw-bold" style="font-size: 11px;">CIVIC SAFETY COMMAND CENTER</small>
                        <h3 class="fw-bold text-dark mb-0">Selamat datang, <span id="adminGreeting">Admin</span></h3>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <!-- Switch Mode Simulasi Lokasi Uji Juri (FR-02.1) -->
                        <div class="form-check form-switch bg-white px-3 py-2 rounded-pill shadow-sm border d-flex align-items-center gap-2 mb-0" title="Aktifkan agar juri dapat menguji fitur pelaporan tanpa batasan radius geofence 20 meter">
                            <input class="form-check-input ms-0 mt-0 cursor-pointer" type="checkbox" role="switch" id="switchSimulation" onchange="toggleSimulationMode(this.checked)">
                            <label class="form-check-label small fw-semibold text-secondary" for="switchSimulation" id="labelSimulation">Simulasi: ...</label>
                        </div>

                        <!-- Tombol Buka Panel Pemantauan SOS (FR-04.4) -->
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-1" onclick="openAdminSosModal()">
                            <i class="bi bi-telephone-fill"></i> Panel SOS <span class="badge bg-danger rounded-pill ms-1" id="sosCountBtn">0</span>
                        </button>

                        <!-- Pilihan Wilayah -->
                        <div class="bg-white p-1 rounded-pill shadow-sm border d-flex gap-1" id="regionBtnGroup">
                            <button class="btn btn-sm btn-success rounded-pill px-3 fw-semibold region-toggle" data-region="unesa-lidah-wetan">UNESA Lidah Wetan</button>
                            <button class="btn btn-sm btn-light text-secondary rounded-pill px-3 fw-semibold region-toggle" data-region="unesa-ketintang">UNESA Ketintang</button>
                            <button class="btn btn-sm btn-light text-secondary rounded-pill px-3 fw-semibold region-toggle" data-region="rawamangun">Rawamangun</button>
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
                    <div class="col-6 col-lg-3" style="cursor: pointer;" onclick="openAdminSosModal()" title="Klik untuk membuka panel SOS">
                        <div class="metric-card p-3 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Alert SOS Terbuka</span>
                                <div class="icon-box bg-danger-subtle text-danger">
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold text-danger mb-0" id="statSosOpen">-</h2>
                            <small class="text-danger fw-semibold" style="font-size: 11px;" id="statSosOpenSub">-</small>
                        </div>
                    </div>
                </div>

                <!-- MAIN SECTION: HEATMAP PETA & LIVE FEED -->
                <div class="row g-3 mb-4">
                    <div class="col-lg-8">
                        <div id="patrolHeatmapCard" class="bg-white p-3 rounded-4 border shadow-sm h-100 patrol-heatmap-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">INTENSITAS LAPORAN LAPANGAN</small>
                                    <h6 class="fw-bold text-dark mb-0">Patrol Heatmap • <span id="judulLokasiMap">UNESA Lidah Wetan</span></h6>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-secondary border" id="heatmapCellCount">0 grid intensitas</span>
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

                <!-- SECTION: ANTRIAN PEMERIKSAAN & AKSI MODERASI (FR-04.3 & FR-04.4) -->
                <div class="bg-white p-4 rounded-4 border shadow-sm" id="moderasiSection">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <div>
                            <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">ANTRIAN PEMERIKSAAN LAPORAN</small>
                            <h5 class="fw-bold text-dark mb-0">Moderasi Laporan Warga</h5>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                            <label for="searchModerasiInput" class="visually-hidden">Cari laporan</label>
                            <input type="search" class="form-control form-control-sm rounded-pill moderation-search" id="searchModerasiInput" placeholder="Cari laporan..." autocomplete="off">
                            <label for="filterStatus" class="small fw-semibold text-secondary mb-0">Filter Status:</label>
                            <select class="form-select form-select-sm bg-light border-0 fw-semibold rounded-pill px-3" id="filterStatus" onchange="filterReportsByStatus()" style="width: 180px;">
                                <option value="all">Semua Status</option>
                                <option value="PENDING_MODERATION">Perlu Moderasi</option>
                                <option value="UNVERIFIED">Belum Verifikasi</option>
                                <option value="VERIFIED">Terverifikasi</option>
                                <option value="RESOLVED">Selesai Ditangani</option>
                                <option value="HIDDEN">Disembunyikan</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="text-muted border-bottom" style="font-size: 12px; background-color: #fafbfc;">
                                <tr>
                                    <th class="py-3">LAPORAN</th>
                                    <th class="py-3">WILAYAH</th>
                                    <th class="py-3">KATEGORI</th>
                                    <th class="py-3">STATUS & UP-CHECK</th>
                                    <th class="py-3 text-end" style="min-width: 280px;">AKSI CEPAT MODERASI (1-KLIK)</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px;" id="reportsTableBody">
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted small">
                                        <span class="spinner-border spinner-border-sm me-1" role="status"></span> Memuat daftar laporan...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    <!-- Modal Panel Pemantauan & Penanganan SOS Admin (FR-04.4) -->
    <div class="modal fade" id="adminSosModal" tabindex="-1" aria-labelledby="adminSosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header bg-danger text-white border-0 rounded-top-4">
                    <h5 class="modal-title fw-bold" id="adminSosModalLabel">
                        <i class="bi bi-exclamation-octagon-fill me-2"></i> Pemantauan Sinyal Darurat SOS (FR-04.4)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2 small mb-3 border-0 bg-warning-subtle text-warning-emphasis">
                        <i class="bi bi-info-circle-fill me-1"></i> Alert ini dikirim oleh warga dari peta navigasi. Pastikan tindak lanjut koordinasi darurat ke pihak berwajib atau pos satpam terdekat.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="text-muted border-bottom small">
                                <tr>
                                    <th>WAKTU</th>
                                    <th>WILAYAH</th>
                                    <th>PESAN / KOORDINAT</th>
                                    <th>STATUS</th>
                                    <th class="text-end">TINDAKAN</th>
                                </tr>
                            </thead>
                            <tbody id="sosTableBody">
                                <tr><td colspan="5" class="text-center py-4 text-muted small"><span class="spinner-border spinner-border-sm me-1"></span> Memuat sinyal SOS...</td></tr>
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
        const adminToken = localStorage.getItem('ta_admin_token') || localStorage.getItem('ta_token');

        // Guard autentikasi admin: jika tidak ada token, arahkan ke halaman login
        if (!adminToken) {
            window.location.href = '/masuk';
        }

        const regionConfigs = {
            'unesa-lidah-wetan': { name: 'UNESA Lidah Wetan', center: [-7.30071, 112.67289], zoom: 15 },
            'unesa-ketintang': { name: 'UNESA Ketintang', center: [-7.31143, 112.72937], zoom: 15 },
            'rawamangun': { name: 'Rawamangun', center: [-6.1928, 106.89763], zoom: 15 }
        };

        let activeRegion = 'unesa-lidah-wetan';
        let rawReportsList = [];
        let sosModalInstance = null;
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
                    localStorage.removeItem('ta_token');
                    window.location.href = '/masuk';
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

        // 1. Cek & Toggle Mode Simulasi Juri (FR-02.1)
        async function loadSimulationStatus() {
            try {
                const res = await fetch(`${adminApi}/simulation`, { headers: { 'Accept': 'application/json' } });
                if (res.ok) {
                    const json = await res.json();
                    const enabled = Boolean(json.data?.simulation_mode);
                    const sw = document.getElementById('switchSimulation');
                    const lbl = document.getElementById('labelSimulation');
                    if (sw) sw.checked = enabled;
                    if (lbl) {
                        lbl.textContent = enabled ? 'Simulasi: AKTIF' : 'Simulasi: Nonaktif';
                        lbl.className = enabled ? 'form-check-label small fw-semibold text-success' : 'form-check-label small fw-semibold text-secondary';
                    }
                }
            } catch (e) {
                console.warn('Gagal memuat status simulasi:', e);
            }
        }

        async function toggleSimulationMode(checked) {
            const lbl = document.getElementById('labelSimulation');
            if (lbl) lbl.textContent = 'Menyimpan...';

            try {
                const res = await fetch(`${adminApi}/admin/simulation`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${adminToken}`
                    },
                    body: JSON.stringify({ enabled: checked })
                });

                const json = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(json.message || 'Gagal mengubah status simulasi.');

                loadSimulationStatus();
                alert(checked ? 'Mode Simulasi Lokasi Uji Juri telah DIAKTIFKAN. Geofence 20 m dilewati untuk pengujian juri.' : 'Mode Simulasi Lokasi dinonaktifkan.');
            } catch (err) {
                alert(err.message);
                loadSimulationStatus();
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
                    localStorage.removeItem('ta_token');
                    window.location.href = '/masuk';
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
                document.getElementById('statSosOpenSub').textContent = sosOpen > 0 ? 'Darurat aktif! Klik untuk buka' : 'Tidak ada alert aktif';
                document.getElementById('sosCountBtn').textContent = sosOpen;

                // 2. Render Heatmap Kerapatan Laporan (FR-04.2)
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

                // 4. Render Live Feed
                renderLiveFeed(data.recent_reports || [], data.sos_alerts || []);

                // 5. Simpan & Render Tabel Laporan
                rawReportsList = data.recent_reports || [];
                filterReportsByStatus();

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

            sosAlerts.slice(0, 3).forEach(sos => {
                const isOpen = sos.status === 'open';
                html += `
                    <div class="live-feed-item d-flex align-items-start gap-3">
                        <div class="bg-danger-subtle text-danger p-2 rounded-circle ta-circle ta-circle-40 mt-1">
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

            if (recentReports.length === 0 && sosAlerts.length === 0) {
                container.innerHTML = '<div class="text-center py-4 text-muted small">Belum ada aktivitas laporan baru.</div>';
                return;
            }

            recentReports.slice(0, 6).forEach(rep => {
                const iconClass = rep.category === 'penerangan_padam' ? 'bi-lightbulb-fill text-warning' : (rep.category === 'area_sepi' ? 'bi-eye-fill text-danger' : 'bi-cone-striped text-warning');
                const bgClass = rep.category === 'area_sepi' ? 'bg-danger-subtle' : 'bg-warning-subtle';
                
                html += `
                    <div class="live-feed-item d-flex align-items-start gap-3">
                        <div class="${bgClass} p-2 rounded-circle ta-circle ta-circle-40 mt-1">
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

        // Filter tabel moderasi
        function filterReportsByStatus() {
            const filter = document.getElementById('filterStatus')?.value || 'all';
            const search = document.getElementById('searchModerasiInput')?.value.trim().toLowerCase() || '';
            let filtered = rawReportsList;
            if (filter !== 'all') {
                filtered = filtered.filter(r => r.status === filter);
            }
            if (search) {
                filtered = filtered.filter(r => [
                    r.id,
                    r.title,
                    r.description,
                    r.region_slug,
                    r.category,
                    r.status
                ].some(value => String(value || '').toLowerCase().includes(search)));
            }
            renderReportsTable(filtered);
        }

        function renderReportsTable(reports) {
            const tbody = document.getElementById('reportsTableBody');
            if (!tbody) return;

            if (reports.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted small">Tidak ada laporan dengan filter status ini.</td></tr>';
                return;
            }

            let html = '';
            reports.forEach(rep => {
                const badgeClass = rep.status === 'VERIFIED' ? 'bg-success' : (rep.status === 'PENDING_MODERATION' ? 'bg-warning text-dark' : (rep.status === 'RESOLVED' ? 'bg-info text-dark' : (rep.status === 'HIDDEN' ? 'bg-dark' : 'bg-secondary')));
                
                html += `
                    <tr>
                        <td class="py-3">
                            <strong class="text-dark d-block">#${rep.id} · ${escapeHtml(rep.label || rep.category)}</strong>
                            <small class="text-muted d-block text-truncate" style="max-width: 250px;">${escapeHtml(rep.description || 'Tidak ada deskripsi.')}</small>
                        </td>
                        <td><span class="badge bg-light text-dark border">${escapeHtml(rep.region_slug)}</span></td>
                        <td><small class="text-secondary fw-semibold">${escapeHtml(rep.category)}</small></td>
                        <td>
                            <span class="badge ${badgeClass} px-2 py-1 rounded-pill mb-1" style="font-size: 10px;">${escapeHtml(rep.status)}</span><br>
                            <small class="text-muted"><i class="bi bi-hand-thumbs-up me-1"></i>${rep.upvotes_count || 0} Upvote</small>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1 flex-wrap">
                                ${rep.status !== 'VERIFIED' ? `
                                    <button class="btn btn-sm btn-outline-success rounded-pill px-2 py-1 fw-semibold" style="font-size: 11px;" onclick="verifyReport(${rep.id})" title="Official Verification (1-Klik)">
                                        <i class="bi bi-patch-check-fill me-1"></i>Verifikasi
                                    </button>
                                ` : ''}
                                ${rep.status !== 'RESOLVED' ? `
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 fw-semibold" style="font-size: 11px;" onclick="resolveReport(${rep.id})" title="Tandai Masalah Jalan Selesai Ditangani">
                                        <i class="bi bi-check-lg me-1"></i>Selesai
                                    </button>
                                ` : ''}
                                ${rep.status !== 'HIDDEN' ? `
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1 fw-semibold" style="font-size: 11px;" onclick="hideReport(${rep.id})" title="Sembunyikan Laporan Hoax/Spam">
                                        <i class="bi bi-eye-slash-fill me-1"></i>Hoax
                                    </button>
                                ` : ''}
                                <button class="btn btn-sm btn-success rounded-pill px-2 py-1 fw-semibold shadow-sm" style="font-size: 11px;" onclick="waFollowupReport(${rep.id})" title="Kirim Tindak Lanjut ke WhatsApp RT/Kelurahan/Satpam (FR-04.4)">
                                    <i class="bi bi-whatsapp me-1"></i>WA RT
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        // AKSI MODERASI (1-KLIK)
        async function verifyReport(reportId) {
            if (!confirm(`Berikan Official Verification badge pada laporan #${reportId}?`)) return;
            try {
                const res = await fetch(`${adminApi}/admin/reports/${reportId}/verify`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${adminToken}` }
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(json.message || 'Gagal memverifikasi laporan.');
                alert(json.message || 'Laporan berhasil diverifikasi!');
                loadAdminDashboard(activeRegion);
            } catch (err) {
                alert(err.message);
            }
        }

        async function resolveReport(reportId) {
            if (!confirm(`Tandai laporan #${reportId} sebagai RESOLVED (selesai ditangani)?`)) return;
            try {
                const res = await fetch(`${adminApi}/admin/reports/${reportId}/resolve`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${adminToken}` }
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(json.message || 'Gagal menandai laporan selesai.');
                alert(json.message || 'Laporan ditandai selesai.');
                loadAdminDashboard(activeRegion);
            } catch (err) {
                alert(err.message);
            }
        }

        async function hideReport(reportId) {
            if (!confirm(`Sembunyikan laporan #${reportId} (spam/hoax)? Laporan ini tidak akan tampil di publik.`)) return;
            try {
                const res = await fetch(`${adminApi}/admin/reports/${reportId}/hide`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${adminToken}` }
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(json.message || 'Gagal menyembunyikan laporan.');
                alert(json.message || 'Laporan berhasil disembunyikan.');
                loadAdminDashboard(activeRegion);
            } catch (err) {
                alert(err.message);
            }
        }

        // Tindak Lanjut WhatsApp RT/Kelurahan/Satpam (FR-04.4)
        async function waFollowupReport(reportId) {
            try {
                const res = await fetch(`${adminApi}/admin/reports/${reportId}/wa-followup`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${adminToken}` }
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(json.message || 'Gagal membuat pesan WhatsApp.');
                
                const waUrl = json.data?.wa_url;
                if (waUrl) {
                    window.open(waUrl, '_blank');
                } else {
                    alert('Teks WhatsApp:\n\n' + json.data?.wa_text);
                }
            } catch (err) {
                alert(err.message);
            }
        }

        // MODAL & PENANGANAN SOS (FR-04.4)
        function openAdminSosModal() {
            if (!sosModalInstance) {
                sosModalInstance = new bootstrap.Modal(document.getElementById('adminSosModal'));
            }
            loadSosAlertsList();
            sosModalInstance.show();
        }

        async function loadSosAlertsList() {
            const tbody = document.getElementById('sosTableBody');
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted small"><span class="spinner-border spinner-border-sm me-1"></span> Mengambil data SOS...</td></tr>';

            try {
                const res = await fetch(`${adminApi}/admin/sos`, {
                    headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${adminToken}` }
                });
                if (!res.ok) throw new Error('Gagal memuat daftar SOS.');
                const payload = await res.json();
                const list = payload.data?.data || payload.data || [];

                if (list.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted small">Tidak ada riwayat sinyal darurat SOS.</td></tr>';
                    return;
                }

                let html = '';
                list.forEach(item => {
                    const isOpen = item.status === 'open';
                    html += `
                        <tr>
                            <td class="small text-muted">${formatTimeAgo(item.created_at)}</td>
                            <td><span class="badge bg-light text-dark border">${escapeHtml(item.region_slug || 'Global')}</span></td>
                            <td>
                                <strong class="d-block small text-dark">${escapeHtml(item.message || 'SOS Emergency')}</strong>
                                <small class="text-muted">Koordinat: ${item.lat ? `${item.lat}, ${item.lng}` : 'Tidak ada koordinat'}</small>
                            </td>
                            <td>
                                <span class="badge ${isOpen ? 'bg-danger' : 'bg-success'} rounded-pill">${escapeHtml(item.status)}</span>
                            </td>
                            <td class="text-end">
                                ${isOpen ? `
                                    <button class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold" style="font-size: 11px;" onclick="resolveSosAlert(${item.id})">
                                        <i class="bi bi-check2-circle me-1"></i>Tandai Selesai
                                    </button>
                                ` : `
                                    <span class="text-muted small"><i class="bi bi-check-all text-success me-1"></i>Tertangani</span>
                                `}
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger small py-3">${err.message}</td></tr>`;
            }
        }

        async function resolveSosAlert(sosId) {
            if (!confirm(`Tutup dan tandai alert SOS #${sosId} sebagai selesai?`)) return;
            try {
                const res = await fetch(`${adminApi}/admin/sos/${sosId}/resolve`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${adminToken}` }
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(json.message || 'Gagal menyelesaikan SOS.');
                alert('Alert SOS telah diselesaikan.');
                loadSosAlertsList();
                loadAdminDashboard(activeRegion);
            } catch (err) {
                alert(err.message);
            }
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
                localStorage.removeItem('ta_token');
                window.location.href = '/masuk';
            }
        }

        // Event listener tombol pilihan wilayah
        document.querySelectorAll('.region-toggle').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.region-toggle').forEach(b => {
                    b.className = 'btn btn-sm btn-light text-secondary rounded-pill px-3 fw-semibold region-toggle';
                });
                this.className = 'btn btn-sm btn-success rounded-pill px-3 fw-semibold region-toggle';
                loadAdminDashboard(this.dataset.region);
            });
        });

        const searchModerasiInput = document.getElementById('searchModerasiInput');
        if (searchModerasiInput) {
            searchModerasiInput.addEventListener('input', filterReportsByStatus);
        }

        const adminNavLinks = document.querySelectorAll('[data-admin-nav-target]');
        const mainContent = document.querySelector('.main-content');
        const setActiveAdminNav = target => {
            adminNavLinks.forEach(link => {
                link.classList.toggle('active', link.dataset.adminNavTarget === target);
            });
        };
        adminNavLinks.forEach(link => {
            link.addEventListener('click', event => {
                const target = link.dataset.adminNavTarget;
                if (target === 'top') {
                    event.preventDefault();
                    mainContent?.scrollTo({ top: 0, behavior: 'smooth' });
                    setActiveAdminNav('top');
                }
            });
        });
        const adminNavObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) setActiveAdminNav(entry.target.id);
            });
        }, { root: mainContent, threshold: 0.15, rootMargin: '-8% 0px -65% 0px' });
        ['moderasiSection', 'patrolHeatmapCard'].forEach(id => {
            const target = document.getElementById(id);
            if (target) adminNavObserver.observe(target);
        });
        mainContent?.addEventListener('scroll', () => {
            if (mainContent.scrollTop < 120) setActiveAdminNav('top');
        }, { passive: true });

        // Inisialisasi awal
        loadAdminProfile();
        loadSimulationStatus();
        loadAdminDashboard(activeRegion);
    </script>
</body>
</html>
