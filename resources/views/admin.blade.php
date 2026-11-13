<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TitikAman - Panel Admin & Navigasi</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/512/3063/3063823.png">
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
        /* Sidebar Styling & Responsivitas Mobile */
        .sidebar {
            background: #ffffff;
            min-height: 100vh;
            border-right: 1px solid #e2e8f0;
            transition: all 0.3s ease;
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
        /* Penyesuaian Mobile */
        @media (max-width: 767.98px) {
            .sidebar {
                min-height: auto;
                border-right: none;
                border-bottom: 1px solid #e2e8f0;
            }
            .map-container {
                height: 300px;
            }
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            
            <!-- 2. & 3. KONSISTENSI SIDEBAR & LOGO BRAND TITIKAMAN -->
            <div class="col-md-3 col-lg-2 px-3 py-4 sidebar d-flex flex-column justify-content-between">
                <div>
                   <!-- Logo / Brand Resmi -->
<div class="d-flex align-items-center mb-4 px-2">
    <div class="me-2">
        <img src="images/logo.jpeg" alt="Logo TitikAman" class="rounded-3 shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
    </div>
    <div>
        <h5 class="fw-bold mb-0 text-dark">TitikAman</h5>
        <small class="text-muted" style="font-size: 11px;">CIVIC SAFETY GRID</small>
    </div>
</div>

                    <!-- Kotak Informasi Singkat -->
                    <div class="p-3 bg-light rounded-4 mb-4 border-0">
                        <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">Status Sistem</small>
                        <p class="fw-semibold text-dark small mt-1 mb-0">Zona aman aktif terpantau real-time.</p>
                    </div>

                    <!-- Menu Navigasi (Konsisten & Scope Terjaga) -->
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="/admin/peta" class="nav-link py-2 px-3">
                                <i class="bi bi-map me-2"></i> Peta Warga
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/admin/rute" class="nav-link active py-2 px-3">
                                <i class="bi bi-signpost-split me-2"></i> Navigasi Rute
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/admin/laporan" class="nav-link py-2 px-3">
                                <i class="bi bi-exclamation-triangle me-2"></i> Form Laporan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/admin/hub" class="nav-link py-2 px-3">
                                <i class="bi bi-shield-lock me-2"></i> Direktori Hub
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Footer Status -->
                <div class="px-2 pt-3 border-top mt-3">
                    <div class="d-flex align-items-center">
                        <span class="spinner-grow spinner-grow-sm text-success me-2" role="status"></span>
                        <div style="font-size: 12px;">
                            <span class="fw-semibold text-dark d-block">Admin Mode Aktif</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KONTEN UTAMA KANAN -->
            <div class="col-md-9 col-lg-10 p-3 p-md-4">
                
                <!-- 4. PERBAIKAN LAYOUT KOLOM PENCARIAN RUTE (Full-Width & Seimbang) -->
                <div class="bg-white p-3 rounded-4 shadow-sm mb-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
                        <!-- Input Asal -->
                        <div class="input-group input-group-sm flex-fill" style="min-width: 220px;">
                            <span class="input-group-text bg-success-subtle text-success border-0"><i class="bi bi-geo-alt-fill"></i></span>
                            <select class="form-select bg-light border-0 fw-semibold" id="pilihanAsal" onchange="ubahWilayahRute()">
                                <option value="unesa">UNESA Lidah Wetan, Surabaya</option>
                                <option value="rawamangun">Rawamangun, Jakarta Timur</option>
                            </select>
                        </div>
                        <i class="bi bi-arrow-right text-muted d-none d-sm-inline"></i>
                        <!-- Input Tujuan -->
                        <div class="input-group input-group-sm flex-fill" style="min-width: 220px;">
                            <span class="input-group-text bg-danger-subtle text-danger border-0"><i class="bi bi-flag-fill"></i></span>
                            <input type="text" class="form-control bg-light border-0" id="inputTujuan" value="Gerbang Utama / Titik Tujuan Aman" readonly>
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

                <!-- Panel Bawah: Pilihan Rute Perjalanan (Responsif Grid) -->
                <div class="bg-white p-3 p-md-4 rounded-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h6 class="fw-bold text-dark mb-0">Pilih Rute Perjalanan (Dua Opsi Ditemukan)</h6>
                        <small class="text-muted"><i class="bi bi-clock-history me-1"></i> Diperbarui 1m lalu</small>
                    </div>

                    <div class="row g-3">
                        <!-- Opsi 1: SafePass (Rute Aman) -->
                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded-4 route-option-card border-success bg-success-subtle bg-opacity-10 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-success text-white px-3 py-1 rounded-pill">SafePass (Rekomendasi)</span>
                                    <span class="fw-bold text-success">12 Menit</span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Jalur Utama Ber-CCTV & Terang</h6>
                                <p class="text-muted small mb-3">Melalui Pos Satpam utama, minim titik gelap, sangat aman untuk malam hari.</p>
                                <button type="button" class="btn btn-success btn-sm w-100 rounded-pill fw-semibold shadow-sm" onclick="pilihRute('SafePass (Rekomendasi)')">
                                    Gunakan Rute Ini <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Opsi 2: Rute Tercepat / Alternatif -->
                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded-4 route-option-card h-100">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge text-white px-3 py-1 rounded-pill" style="background-color: #0d9488;">Tercepat</span>
                                    <span class="fw-bold text-dark">9 Menit</span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Jalur Pintar / Alternatif</h6>
                                <p class="text-muted small mb-3">Lebih cepat sampai, namun terdapat beberapa titik lampu redup di pertigaan.</p>
                                <button type="button" class="btn btn-outline-success btn-sm w-100 rounded-pill fw-semibold shadow-sm" onclick="pilihRute('Jalur Pintar / Alternatif')">
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
        // Inisialisasi Peta Komparasi Rute
        var mapRute = L.map('mapRute').setView([-7.2819, 112.7388], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(mapRute);

        var markerAsal, markerTujuan, polylineAman, polylineTercepat;

        function updatePeta(latAsal, lonAsal, latTujuan, lonTujuan, jalurAmanCoords, jalurCepatCoords, namaTujuan) {
            if (markerAsal) mapRute.removeLayer(markerAsal);
            if (markerTujuan) mapRute.removeLayer(markerTujuan);
            if (polylineAman) mapRute.removeLayer(polylineAman);
            if (polylineTercepat) mapRute.removeLayer(polylineTercepat);

            mapRute.setView([latAsal, lonAsal], 15);

            markerAsal = L.marker([latAsal, lonAsal]).addTo(mapRute)
                .bindPopup('<b>Lokasi Asal</b>').openPopup();

            markerTujuan = L.marker([latTujuan, lonTujuan]).addTo(mapRute)
                .bindPopup('<b>' + namaTujuan + '</b>');

            polylineAman = L.polyline(jalurAmanCoords, {color: '#10b981', weight: 5, opacity: 0.8}).addTo(mapRute);
            polylineTercepat = L.polyline(jalurCepatCoords, {color: '#f59e0b', weight: 4, opacity: 0.7, dashArray: '5, 5'}).addTo(mapRute);
        }

        // Default: UNESA Surabaya
        updatePeta(
            -7.2819, 112.7388, 
            -7.2750, 112.7420, 
            [[-7.2819, 112.7388], [-7.2790, 112.7400], [-7.2750, 112.7420]],
            [[-7.2819, 112.7388], [-7.2830, 112.7410], [-7.2750, 112.7420]],
            "Gerbang Utama UNESA"
        );

        function ubahWilayahRute() {
            var pilihan = document.getElementById('pilihanAsal').value;
            var inputTujuan = document.getElementById('inputTujuan');

            if (pilihan === 'unesa') {
                inputTujuan.value = "Gerbang Utama / Titik Tujuan Aman";
                updatePeta(
                    -7.2819, 112.7388, 
                    -7.2750, 112.7420, 
                    [[-7.2819, 112.7388], [-7.2790, 112.7400], [-7.2750, 112.7420]],
                    [[-7.2819, 112.7388], [-7.2830, 112.7410], [-7.2750, 112.7420]],
                    "Gerbang Utama UNESA"
                );
            } else if (pilihan === 'rawamangun') {
                inputTujuan.value = "Velodrome / Titik Aman Rawamangun";
                updatePeta(
                    -6.1944, 106.8823, 
                    -6.1880, 106.8900, 
                    [[-6.1944, 106.8823], [-6.1910, 106.8860], [-6.1880, 106.8900]],
                    [[-6.1944, 106.8823], [-6.1960, 106.8880], [-6.1880, 106.8900]],
                    "Velodrome Rawamangun"
                );
            }
        }

        function pilihRute(namaRute) {
            alert("Rute dipilih: " + namaRute + ". Memulai navigasi aman...");
            window.location.href = '/admin/peta'; // Tetap dalam scope admin
        }
    </script>
</body>
</html>