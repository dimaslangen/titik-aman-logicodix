<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TitikAman - Direktori Safety Hub</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
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
        .hub-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .hub-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
            border-color: #0d9488;
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

                    <!-- Kotak Informasi -->
                    <div class="p-3 bg-light rounded-4 mb-4 border-0">
                        <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">Fasilitas Keamanan</small>
                        <p class="fw-semibold text-dark small mt-1 mb-0">Titik terang & pos keamanan 24 jam terverifikasi di sekitarmu.</p>
                    </div>

                    <!-- Menu Navigasi -->
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="/app" class="nav-link py-2 px-3">
                                <i class="bi bi-map me-2"></i> Peta Aman
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/rute-saya" class="nav-link py-2 px-3">
                                <i class="bi bi-signpost-split me-2"></i> Rute Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/safety-hubs-ui" class="nav-link active py-2 px-3">
                                <i class="bi bi-building me-2"></i> Safety Hub
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
                            <span class="fw-semibold text-dark d-block">13 Hub Aktif</span>
                            <span class="text-muted" style="font-size: 10px;">Polisi · Faskes · Satpam · Halte</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KONTEN UTAMA KANAN (Direktori Safety Hub) -->
            <div class="col-md-9 col-lg-10 p-4">
                
                <!-- Topbar Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <a href="/app" class="btn btn-light btn-sm rounded-circle me-3 text-secondary"><i class="bi bi-arrow-left"></i></a>
                        <div>
                            <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">Civic Safety Grid</small>
                            <h4 class="fw-bold mb-0 text-dark">Direktori Safety Hub</h4>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm bg-light border-0 fw-semibold rounded-pill px-3" id="filterRegion" onchange="loadSafetyHubs()">
                            <option value="">Semua Wilayah</option>
                            <option value="unesa-lidah-wetan">UNESA Lidah Wetan</option>
                            <option value="unesa-ketintang">UNESA Ketintang</option>
                            <option value="rawamangun">Rawamangun</option>
                        </select>
                        <select class="form-select form-select-sm bg-light border-0 fw-semibold rounded-pill px-3" id="filterType" onchange="loadSafetyHubs()">
                            <option value="">Semua Tipe Hub</option>
                            <option value="polisi">Pos Polisi</option>
                            <option value="satpam">Pos Satpam</option>
                            <option value="faskes">Fasilitas Kesehatan</option>
                            <option value="halte">Halte Aktif</option>
                        </select>
                    </div>
                </div>

                <!-- Info Banner -->
                <div class="alert alert-info border-0 rounded-4 shadow-sm p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white p-2 rounded-circle me-3">
                            <i class="bi bi-shield-shaded fs-5"></i>
                        </div>
                        <div>
                            <strong class="text-dark d-block">Safety Hub adalah zona aman terverifikasi</strong>
                            <small class="text-muted">Rute SafePass memprioritaskan jalur yang melewati titik-titik ini (radius 100 m).</small>
                        </div>
                    </div>
                    <a href="/rute-saya" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                        Cari Rute Lewat Hub <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                <!-- Container Grid Hub -->
                <div class="row g-3" id="hubsContainer">
                    <div class="col-12 text-center py-5 text-muted small">
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span> Memuat daftar Safety Hub...
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const apiBase = '/api/v1';

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, char => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
            }[char]));
        }

        function getHubBadge(type) {
            switch(type) {
                case 'polisi': return '<span class="badge bg-primary rounded-pill px-3 py-1"><i class="bi bi-shield-fill me-1"></i>Polisi</span>';
                case 'satpam': return '<span class="badge bg-success rounded-pill px-3 py-1"><i class="bi bi-person-badge-fill me-1"></i>Satpam</span>';
                case 'faskes': return '<span class="badge bg-danger rounded-pill px-3 py-1"><i class="bi bi-hospital-fill me-1"></i>Faskes 24 Jam</span>';
                case 'halte': return '<span class="badge bg-warning text-dark rounded-pill px-3 py-1"><i class="bi bi-bus-front-fill me-1"></i>Halte</span>';
                default: return '<span class="badge bg-secondary rounded-pill px-3 py-1">Hub</span>';
            }
        }

        async function loadSafetyHubs() {
            const container = document.getElementById('hubsContainer');
            const reg = document.getElementById('filterRegion').value;
            const type = document.getElementById('filterType').value;

            container.innerHTML = '<div class="col-12 text-center py-5 text-muted small"><span class="spinner-border spinner-border-sm me-1" role="status"></span> Memuat data...</div>';

            let url = `${apiBase}/safety-hubs?`;
            if (reg) url += `region_slug=${encodeURIComponent(reg)}&`;
            if (type) url += `type=${encodeURIComponent(type)}&`;

            try {
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Gagal memuat data Safety Hub.');
                const payload = await res.json();
                const list = Array.isArray(payload.data) ? payload.data : [];

                if (list.length === 0) {
                    container.innerHTML = '<div class="col-12 text-center py-5 text-muted">Tidak ada Safety Hub yang sesuai dengan filter.</div>';
                    return;
                }

                let html = '';
                list.forEach(hub => {
                    html += `
                        <div class="col-md-6 col-lg-4">
                            <div class="hub-card p-3 shadow-sm h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        ${getHubBadge(hub.type)}
                                        ${hub.is_24h ? '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">24 Jam</span>' : ''}
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">${escapeHtml(hub.name)}</h6>
                                    <small class="text-muted d-block mb-3">${escapeHtml(hub.address || 'Alamat tidak tersedia.')}</small>
                                </div>
                                <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                                    <small class="text-secondary fw-semibold">
                                        <i class="bi bi-geo-alt me-1"></i>${escapeHtml(hub.region_slug)}
                                    </small>
                                    ${hub.phone ? `
                                        <a href="tel:${escapeHtml(hub.phone)}" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1" style="font-size: 11px;">
                                            <i class="bi bi-telephone-fill me-1"></i>${escapeHtml(hub.phone)}
                                        </a>
                                    ` : '<span class="text-muted small" style="font-size: 11px;">Tidak ada kontak</span>'}
                                </div>
                            </div>
                        </div>
                    `;
                });

                container.innerHTML = html;
            } catch (err) {
                container.innerHTML = `<div class="col-12 text-center py-5 text-danger small">${escapeHtml(err.message)}</div>`;
            }
        }

        document.addEventListener('DOMContentLoaded', loadSafetyHubs);
    </script>
</body>
</html>
