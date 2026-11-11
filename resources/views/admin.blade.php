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
                            <a href="#" class="nav-link py-2 px-3 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-exclamation-triangle me-2"></i> Moderasi</span>
                                <span class="badge bg-danger rounded-pill" style="font-size: 10px;">8</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/app" class="nav-link py-2 px-3">
                                <i class="bi bi-map me-2"></i> Patrol Grid
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/rute-saya" class="nav-link py-2 px-3">
                                <i class="bi bi-building me-2"></i> Safety Hub
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/lapor-kondisi" class="nav-link py-2 px-3">
                                <i class="bi bi-people me-2"></i> Komunitas
                            </a>
                        </li>
                    </ul>

                    <!-- Card Bantuan / Support -->
                    <div class="p-3 bg-light rounded-4 border-0 mb-3">
                        <small class="fw-bold text-dark d-block">Butuh bantuan?</small>
                        <small class="text-muted" style="font-size: 11px;">Pusat kendali 24/7</small>
                    </div>
                </div>

                <!-- Footer Admin Profile -->
                <div class="px-2 pt-3 border-top">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" style="width: 35px; height: 35px; font-size: 12px;">
                                AP
                            </div>
                            <div style="font-size: 12px;">
                                <span class="fw-semibold text-dark d-block mb-0">Admin Patroli</span>
                                <small class="text-muted" style="font-size: 10px;">admin@titikaman.id</small>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </div>
                </div>
            </div>

            <!-- KONTEN UTAMA KANAN (Command Center) -->
            <div class="col-md-9 col-lg-10 p-4">
                
                <!-- Sub-Header & Location Toggles -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <small class="text-uppercase text-muted fw-bold" style="font-size: 11px;">CIVIC SAFETY COMMAND CENTER</small>
                        <h3 class="fw-bold text-dark mb-0">Selamat malam, Admin</h3>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-white p-1 rounded-pill shadow-sm border d-flex gap-1">
                            <button id="btnAdminUnesa" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold" onclick="switchAdminLoc('unesa')">UNESA</button>
                            <button id="btnAdminRawamangun" class="btn btn-sm btn-light text-secondary rounded-pill px-3 fw-semibold" onclick="switchAdminLoc('rawamangun')">Rawamangun</button>
                        </div>
                        <button class="btn btn-outline-danger btn-sm rounded-circle p-2" title="Peringatan Darurat">
                            <i class="bi bi-exclamation-octagon-fill"></i>
                        </button>
                    </div>
                </div>

                <!-- 4 KARTU METRIK UTAMA -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="metric-card p-3 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Laporan Aktif</span>
                                <div class="icon-box bg-warning-subtle text-warning">
                                    <i class="bi bi-file-earmark-text-fill"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold text-dark mb-0">24</h2>
                            <small class="text-success fw-semibold" style="font-size: 11px;">+6 hari ini</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="metric-card p-3 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Verified</span>
                                <div class="icon-box bg-success-subtle text-success">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold text-dark mb-0">87%</h2>
                            <small class="text-success fw-semibold" style="font-size: 11px;">+12% minggu ini</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="metric-card p-3 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Waktu Respons</span>
                                <div class="icon-box bg-info-subtle text-info">
                                    <i class="bi bi-clock-fill"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold text-dark mb-0">18m</h2>
                            <small class="text-success fw-semibold" style="font-size: 11px;">4m lebih cepat</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="metric-card p-3 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Safety Hub Aktif</span>
                                <div class="icon-box bg-primary-subtle text-primary">
                                    <i class="bi bi-shield-fill-check"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold text-dark mb-0">12</h2>
                            <small class="text-muted fw-semibold" style="font-size: 11px;">Semua online</small>
                        </div>
                    </div>
                </div>

                <!-- MAIN SECTION: HEATMAP PETA & LIVE FEED -->
                <div class="row g-3 mb-4">
                    <div class="col-lg-8">
                        <div class="bg-white p-3 rounded-4 border shadow-sm h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">INTENSITAS LAPORAN</small>
                                    <h6 class="fw-bold text-dark mb-0">Patrol Heatmap • <span id="judulLokasiMap">UNESA</span></h6>
                                </div>
                                <div>
                                    <select class="form-select form-select-sm bg-light border-0 fw-semibold rounded-pill px-3">
                                        <option value="6">6 jam terakhir</option>
                                        <option value="12">12 jam terakhir</option>
                                        <option value="24">24 jam terakhir</option>
                                    </select>
                                </div>
                            </div>
                            <div class="map-heatmap-container">
                                <div id="adminMap" style="width: 100%; height: 100%;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="bg-white p-3 rounded-4 border shadow-sm h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">LIVE FEED</small>
                                    <h6 class="fw-bold text-dark mb-0">Aktivitas Terbaru</h6>
                                </div>
                                <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
                            </div>

                            <div class="live-feed-list pt-2">
                                <div class="live-feed-item d-flex align-items-start gap-3">
                                    <div class="bg-warning-subtle text-warning p-2 rounded-circle mt-1">
                                        <i class="bi bi-exclamation-diamond-fill"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="fw-bold text-dark small mb-0">Laporan baru diterima</h6>
                                            <span class="badge bg-success-subtle text-success rounded-circle p-1" style="width: 8px; height: 8px;"></span>
                                        </div>
                                        <small class="text-muted d-block" style="font-size: 11px;">Lidah Wetan • 2 menit lalu</small>
                                    </div>
                                </div>
                                <div class="live-feed-item d-flex align-items-start gap-3">
                                    <div class="bg-success-subtle text-success p-2 rounded-circle mt-1">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="fw-bold text-dark small mb-0">Laporan terverifikasi</h6>
                                            <span class="badge bg-success-subtle text-success rounded-circle p-1" style="width: 8px; height: 8px;"></span>
                                        </div>
                                        <small class="text-muted d-block" style="font-size: 11px;">Rawamangun • 8 menit lalu</small>
                                    </div>
                                </div>
                                <div class="live-feed-item d-flex align-items-start gap-3">
                                    <div class="bg-info-subtle text-info p-2 rounded-circle mt-1">
                                        <i class="bi bi-shield-shaded"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="fw-bold text-dark small mb-0">Petugas merespons</h6>
                                            <span class="badge bg-success-subtle text-success rounded-circle p-1" style="width: 8px; height: 8px;"></span>
                                        </div>
                                        <small class="text-muted d-block" style="font-size: 11px;">Ketintang • 14 menit lalu</small>
                                    </div>
                                </div>
                                <div class="live-feed-item d-flex align-items-start gap-3">
                                    <div class="bg-primary-subtle text-primary p-2 rounded-circle mt-1">
                                        <i class="bi bi-clock-history"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="fw-bold text-dark small mb-0">Hambatan diselesaikan</h6>
                                            <span class="badge bg-success-subtle text-success rounded-circle p-1" style="width: 8px; height: 8px;"></span>
                                        </div>
                                        <small class="text-muted d-block" style="font-size: 11px;">Jl. Pemuda • 22 menit lalu</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION BARU: ANTRIAN PEMERIKSAAN - MODERASI LAPORAN -->
                <div class="bg-white p-4 rounded-4 border shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <div>
                            <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">ANTRIAN PEMERIKSAAN</small>
                            <h5 class="fw-bold text-dark mb-0">Moderasi laporan</h5>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control bg-light border-0" placeholder="Cari laporan">
                            </div>
                            <select class="form-select form-select-sm bg-light border-0 fw-semibold" style="width: 140px;">
                                <option value="all">Semua status</option>
                                <option value="verified">Verified</option>
                                <option value="review">Review</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="text-muted border-bottom" style="font-size: 12px; background-color: #fafbfc;">
                                <tr>
                                    <th class="py-3">LAPORAN</th>
                                    <th class="py-3">LOKASI</th>
                                    <th class="py-3">KATEGORI</th>
                                    <th class="py-3">VALIDASI</th>
                                    <th class="py-3">STATUS</th>
                                    <th class="py-3 text-end">AKSI CEPAT</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px;">
                                <!-- Baris 1 -->
                                <tr>
                                    <td class="py-3">
                                        <span class="fw-bold text-dark">TA-1842</span><br>
                                        <small class="text-muted" style="font-size: 11px;">12m lalu</small>
                                    </td>
                                    <td>Lidah Wetan</td>
                                    <td>Penerangan Padam</td>
                                    <td><span class="badge bg-success-subtle text-success px-2 py-1 rounded">3/3</span></td>
                                    <td><span class="badge bg-success text-white px-3 py-1 rounded-pill" style="font-size: 11px;">VERIFIED</span></td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <button class="btn btn-light btn-sm rounded-circle p-2" title="Setujui"><i class="bi bi-check-lg text-success"></i></button>
                                            <button class="btn btn-light btn-sm rounded-circle p-2" title="Periksa"><i class="bi bi-shield-check text-muted"></i></button>
                                            <button class="btn btn-light btn-sm rounded-circle p-2" title="Komentar"><i class="bi bi-chat text-muted"></i></button>
                                            <button class="btn btn-light btn-sm rounded-circle p-2" title="Peringatan"><i class="bi bi-exclamation-triangle text-muted"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Baris 2 -->
                                <tr>
                                    <td class="py-3">
                                        <span class="fw-bold text-dark">TA-1839</span><br>
                                        <small class="text-muted" style="font-size: 11px;">34m lalu</small>
                                    </td>
                                    <td>Jl. Pemuda</td>
                                    <td>Hambatan Fisik Jalan</td>
                                    <td><span class="badge bg-warning-subtle text-warning px-2 py-1 rounded">2/3</span></td>
                                    <td><span class="badge bg-warning text-dark px-3 py-1 rounded-pill" style="font-size: 11px;">REVIEW</span></td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <button class="btn btn-light btn-sm rounded-circle p-2" title="Setujui"><i class="bi bi-check-lg text-success"></i></button>
                                            <button class="btn btn-light btn-sm rounded-circle p-2" title="Periksa"><i class="bi bi-shield-check text-muted"></i></button>
                                            <button class="btn btn-light btn-sm rounded-circle p-2" title="Komentar"><i class="bi bi-chat text-muted"></i></button>
                                            <button class="btn btn-light btn-sm rounded-circle p-2" title="Peringatan"><i class="bi bi-exclamation-triangle text-muted"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Baris 3 -->
                                <tr>
                                    <td class="py-3">
                                        <span class="fw-bold text-dark">TA-1833</span><br>
                                        <small class="text-muted" style="font-size: 11px;">1j lalu</small>
                                    </td>
                                    <td>Rawamangun Muka</td>
                                    <td>Area Sepi Perlu Perhatian</td>
                                    <td><span class="badge bg-success-subtle text-success px-2 py-1 rounded">3/3</span></td>
                                    <td><span class="badge bg-success text-white px-3 py-1 rounded-pill" style="font-size: 11px;">VERIFIED</span></td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <button class="btn btn-light btn-sm rounded-circle p-2" title="Setujui"><i class="bi bi-check-lg text-success"></i></button>
                                            <button class="btn btn-light btn-sm rounded-circle p-2" title="Periksa"><i class="bi bi-shield-check text-muted"></i></button>
                                            <button class="btn btn-light btn-sm rounded-circle p-2" title="Komentar"><i class="bi bi-chat text-muted"></i></button>
                                            <button class="btn btn-light btn-sm rounded-circle p-2" title="Peringatan"><i class="bi bi-exclamation-triangle text-muted"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Baris 4 -->
                                <tr>
                                    <td class="py-3">
                                        <span class="fw-bold text-dark">TA-1828</span><br>
                                        <small class="text-muted" style="font-size: 11px;">2j lalu</small>
                                    </td>
                                    <td>Ketintang</td>
                                    <td>Penerangan Padam</td>
                                    <td><span class="badge bg-danger-subtle text-danger px-2 py-1 rounded">1/3</span></td>
                                    <td><span class="badge bg-warning text-dark px-3 py-1 rounded-pill" style="font-size: 11px;">REVIEW</span></td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <button class="btn btn-light btn-sm rounded-circle p-2" title="Setujui"><i class="bi bi-check-lg text-success"></i></button>
                                            <button class="btn btn-light btn-sm rounded-circle p-2" title="Periksa"><i class="bi bi-shield-check text-muted"></i></button>
                                            <button class="btn btn-light btn-sm rounded-circle p-2" title="Komentar"><i class="bi bi-chat text-muted"></i></button>
                                            <button class="btn btn-light btn-sm rounded-circle p-2" title="Peringatan"><i class="bi bi-exclamation-triangle text-muted"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var adminMap = L.map('adminMap').setView([-7.2819, 112.7388], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(adminMap);

        var adminMarker = L.marker([-7.2819, 112.7388]).addTo(adminMap)
            .bindPopup('<b>Safety Hub UNESA</b><br>Area aktif terverifikasi.').openPopup();

        var heatCircle = L.circle([-7.2819, 112.7388], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.35,
            radius: 400
        }).addTo(adminMap);

        function switchAdminLoc(loc) {
            const btnU = document.getElementById('btnAdminUnesa');
            const btnR = document.getElementById('btnAdminRawamangun');
            const judul = document.getElementById('judulLokasiMap');

            if(loc === 'rawamangun') {
                btnR.className = "btn btn-sm btn-success rounded-pill px-3 fw-semibold";
                btnU.className = "btn btn-sm btn-light text-secondary rounded-pill px-3 fw-semibold";
                judul.innerText = "Rawamangun";

                adminMap.setView([-6.1913, 106.8856], 14);
                adminMarker.setLatLng([-6.1913, 106.8856]).bindPopup('<b>Safety Hub Rawamangun</b><br>Area aktif terverifikasi.').openPopup();
                heatCircle.setLatLng([-6.1913, 106.8856]);
            } else {
                btnU.className = "btn btn-sm btn-success rounded-pill px-3 fw-semibold";
                btnR.className = "btn btn-sm btn-light text-secondary rounded-pill px-3 fw-semibold";
                judul.innerText = "UNESA";

                adminMap.setView([-7.2819, 112.7388], 14);
                adminMarker.setLatLng([-7.2819, 112.7388]).bindPopup('<b>Safety Hub UNESA</b><br>Area aktif terverifikasi.').openPopup();
                heatCircle.setLatLng([-7.2819, 112.7388]);
            }
        }
    </script>
</body>
</html>