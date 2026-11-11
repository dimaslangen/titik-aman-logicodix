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
        /* Sidebar khusus tampilan desktop */
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
                            <a href="#" class="nav-link active py-2 px-3">
                                <i class="bi bi-map me-2"></i> Peta Aman
                            </a>
                        </li>
                        <li class="nav-item">
        <!-- UBAH DI SINI: Dari href="#" jadi href="/rute-saya" -->
        <a href="/rute-saya" class="nav-link py-2 px-3">
            <i class="bi bi-signpost-split me-2"></i> Rute Saya
        </a>
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
                            <span class="text-muted" style="font-size: 10px;">Grid diperbarui 1 menit lalu</span>
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
                    <div>
                        <button class="btn btn-outline-dark btn-sm rounded-circle"><i class="bi bi-bell"></i></button>
                        <button class="btn btn-outline-dark btn-sm rounded-circle ms-2"><i class="bi bi-person"></i></button>
                    </div>
                </div>

               <!-- Filter / Quick Bar di atas Peta -->
<div class="bg-white p-3 rounded-4 shadow-sm mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
    <div class="d-flex gap-2">
        <button id="btn-unesa" class="btn btn-sm btn-outline-success active">UNESA</button>
        <button id="btn-rawamangun" class="btn btn-sm btn-outline-secondary">Rawamangun</button>
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
                    <button class="btn btn-light text-teal fw-bold px-4 rounded-pill shadow-sm">
                        Cari Rute Aman <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>

            </div>

        </div>
    </div>

   <!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Koordinat lokasi
    var unesaLatLon = [-7.2819, 112.7388]; // UNESA Surabaya
    var rawamangunLatLon = [-6.1913, 106.8856]; // Rawamangun Jakarta

    // Inisialisasi Peta (Default ke UNESA)
    var map = L.map('map').setView(unesaLatLon, 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var currentMarker = L.marker(unesaLatLon).addTo(map)
        .bindPopup('<b>Area UNESA Surabaya</b><br>Titik Aman / Safety Hub terdekat.')
        .openPopup();

    // Ambil elemen tombol berdasarkan ID
    const btnUnesa = document.getElementById('btn-unesa');
    const btnRawamangun = document.getElementById('btn-rawamangun');

    // Event ketika tombol UNESA diklik
    btnUnesa.addEventListener('click', function() {
        map.setView(unesaLatLon, 15);
        map.removeLayer(currentMarker);
        currentMarker = L.marker(unesaLatLon).addTo(map)
            .bindPopup('<b>Area UNESA Surabaya</b><br>Titik Aman / Safety Hub terdekat.')
            .openPopup();

        btnUnesa.className = "btn btn-sm btn-outline-success active";
        btnRawamangun.className = "btn btn-sm btn-outline-secondary";
    });

    // Event ketika tombol Rawamangun diklik
    btnRawamangun.addEventListener('click', function() {
        map.setView(rawamangunLatLon, 15);
        map.removeLayer(currentMarker);
        currentMarker = L.marker(rawamangunLatLon).addTo(map)
            .bindPopup('<b>Area Rawamangun Jakarta</b><br>Titik Aman / Safety Hub terdekat.')
            .openPopup();

        btnRawamangun.className = "btn btn-sm btn-outline-success active";
        btnUnesa.className = "btn btn-sm btn-outline-secondary";
    });
</script>
</body>
</html>