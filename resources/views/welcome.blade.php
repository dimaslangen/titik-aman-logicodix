<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TitikAman - Safe & Civic Navigation</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .hero-section {
            background: linear-gradient(135deg, #0d9488 0%, #059669 100%);
            color: white;
            border-radius: 0 0 32px 32px;
            padding: 80px 0 60px 0;
        }
        .feature-card {
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            background: #ffffff;
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        .badge-preview {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            color: #ffffff;
            font-size: 13px;
        }
    </style>
</head>
<body>

    <!-- NAVBAR PREVIEW (TANPA WAJIB LOGIN) -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark" href="/">
                <div class="bg-success text-white p-2 rounded-3">
                    <i class="bi bi-shield-check fs-5"></i>
                </div>
                <span>TitikAman</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold" style="font-size: 15px;">
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="/app"><i class="bi bi-map me-1 text-success"></i> Peta Aman</a></li>
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="/rute-saya"><i class="bi bi-signpost-split me-1 text-success"></i> Bandingkan Rute</a></li>
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="/lapor-kondisi"><i class="bi bi-exclamation-triangle me-1 text-success"></i> Safety Hub / Lapor</a></li>
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="/admin"><i class="bi bi-shield-lock me-1 text-success"></i> Admin Center</a></li>
                </ul>
                <div class="d-flex gap-2 align-items-center">
                    <a href="/app" class="btn btn-success rounded-pill px-4 fw-semibold">Jelajahi Peta <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO BANNER PREVIEW -->
    <section class="hero-section text-center">
        <div class="container">
            <span class="badge badge-preview px-3 py-2 rounded-pill mb-3">
                <i class="bi bi-geo-alt-fill me-1"></i> Mode Preview Publik • Wilayah UNESA & Rawamangun
            </span>
            <h1 class="display-4 fw-bold mb-3">Navigasi Aman Real-Time & Evakuasi Komunitas</h1>
            <p class="lead mb-4 mx-auto style-desc" style="max-width: 650px; opacity: 0.95;">
                Pantau hambatan jalan, penerangan, dan titik keselamatan publik secara langsung tanpa hambatan login. Pilih wilayah dan mulai eksplorasi rute paling aman untuk kamu.
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="/app" class="btn btn-light btn-lg text-success fw-bold rounded-pill px-4 shadow">
                    <i class="bi bi-compass me-2"></i> Buka Peta Interaktif
                </a>
                <a href="/rute-saya" class="btn btn-outline-light btn-lg fw-semibold rounded-pill px-4">
                    <i class="bi bi-signpost-2 me-2"></i> Cek Opsi Rute
                </a>
            </div>
        </div>
    </section>

    <!-- SEKSI UTAMA: FITUR BEBAS AKSES -->
    <section class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <small class="text-uppercase text-success fw-bold">FITUR TERSEDIA SANGAT BEBAS</small>
                <h2 class="fw-bold text-dark mt-1">Jelajahi Fitur Utama Tanpa Batas</h2>
            </div>

            <div class="row g-4">
                <!-- Kartu 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card p-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="bg-success-subtle text-success p-3 rounded-4 d-inline-block mb-3">
                                <i class="bi bi-map-fill fs-3"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Peta Aman Live</h5>
                            <p class="text-muted small">Pantau status keselamatan wilayah UNESA Surabaya & Rawamangun Jakarta secara real-time.</p>
                        </div>
                        <a href="/app" class="btn btn-sm btn-outline-success rounded-pill fw-semibold mt-3">Buka Peta <i class="bi bi-chevron-right"></i></a>
                    </div>
                </div>

                <!-- Kartu 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card p-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="bg-warning-subtle text-warning p-3 rounded-4 d-inline-block mb-3">
                                <i class="bi bi-signpost-split-fill fs-3"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Komparasi Rute</h5>
                            <p class="text-muted small">Bandingkan opsi rute tercepat vs rute penerangan maksimal untuk perjalanan malam yang aman.</p>
                        </div>
                        <a href="/rute-saya" class="btn btn-sm btn-outline-warning text-dark rounded-pill fw-semibold mt-3">Cek Rute <i class="bi bi-chevron-right"></i></a>
                    </div>
                </div>

                <!-- Kartu 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card p-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="bg-danger-subtle text-danger p-3 rounded-4 d-inline-block mb-3">
                                <i class="bi bi-camera-fill fs-3"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Lapor Kondisi</h5>
                            <p class="text-muted small">Fitur laporan komunitas lengkap dengan modul kamera mirror & deteksi lokasi GPS terkini.</p>
                        </div>
                        <a href="/lapor-kondisi" class="btn btn-sm btn-outline-danger rounded-pill fw-semibold mt-3">Buat Laporan <i class="bi bi-chevron-right"></i></a>
                    </div>
                </div>

                <!-- Kartu 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card p-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="bg-primary-subtle text-primary p-3 rounded-4 d-inline-block mb-3">
                                <i class="bi bi-shield-lock-fill fs-3"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Command Center</h5>
                            <p class="text-muted small">Dashboard admin moderasi laporan publik, heatmap patroli, dan log aktivitas real-time.</p>
                        </div>
                        <a href="/admin" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold mt-3">Ke Admin <i class="bi bi-chevron-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-white border-top py-4 text-center">
        <div class="container">
            <small class="text-muted">&copy; 2026 TitikAman Civic Safety Grid. Bebas diakses publik.</small>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>