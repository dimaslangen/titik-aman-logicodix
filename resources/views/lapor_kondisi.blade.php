<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TitikAman - Lapor Kondisi</title>
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
        .nav-link:hover {
            background-color: #e6fffa;
            color: #0d9488;
        }
        .nav-link.active {
            background-color: #e6fffa;
            color: #0d9488;
            font-weight: 600;
        }
        .camera-box {
            border: 2px dashed #cbd5e1;
            background-color: #f8fafc;
            border-radius: 12px;
            transition: all 0.2s;
        }
        .camera-box:hover {
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
                            <a href="/rute-saya" class="nav-link py-2 px-3">
                                <i class="bi bi-signpost-split me-2"></i> Rute Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/safety-hubs-ui" class="nav-link py-2 px-3">
                                <i class="bi bi-building me-2"></i> Safety Hub
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/lapor-kondisi" class="nav-link active py-2 px-3">
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

            <!-- KONTEN UTAMA KANAN (Form Lapor Kondisi) -->
            <div class="col-md-9 col-lg-10 p-4">
                
                <!-- Topbar Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm">
                    <div class="d-flex align-items-center">
                        <a href="/app" class="btn btn-light btn-sm rounded-circle me-3 text-secondary"><i class="bi bi-arrow-left"></i></a>
                        <div>
                            <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">Laporan Komunitas</small>
                            <h4 class="fw-bold mb-0 text-dark">Lapor Kondisi</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span id="badgeSimulationMode" class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill d-none">
                            <i class="bi bi-cpu-fill me-1"></i> SIMULASI JURI AKTIF (BYPASS RADIUS)
                        </span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                            <i class="bi bi-geo-alt-fill me-1"></i> GPS VALID
                        </span>
                    </div>
                </div>

                <!-- Form Utama Lapor Kondisi -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    
                  <!-- 1. Indikator Lokasi Terverifikasi (Dinamis GPS) -->
                    <div id="boxLokasi" class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-secondary-subtle border mb-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-geo-alt-fill text-secondary fs-4 me-3" id="iconLokasi"></i>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" id="statusLokasiTeks">Mendeteksi Lokasi GPS...</h6>
                                <small class="text-secondary" id="lokasi-teks">Meminta izin akses GPS perangkat...</small>
                            </div>
                        </div>
                        <span id="badgeAkurasi" class="badge bg-secondary text-white" style="font-size: 10px;">Menunggu...</span>
                    </div>

                    <!-- 2. Ambil Foto Kondisi (Kamera Langsung & Switch Depan/Belakang) -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-dark small mb-0"><span class="badge bg-dark rounded-circle me-1">1</span> Ambil Foto Kondisi</label>
                            <!-- Tombol Ganti Kamera (Depan/Belakang) -->
                            <button type="button" id="btnSwitchKamera" class="btn btn-outline-secondary btn-sm rounded-pill d-none" onclick="gantiKamera()">
                                <i class="bi bi-camera-recess me-1"></i> Ganti Kamera
                            </button>
                        </div>
                        <p class="text-muted small mb-2">Aktifkan kamera langsung untuk validasi keamanan.</p>
                        
                        <!-- Kotak Utama Kamera / Tombol Buka -->
                        <div id="wrapperBukaKamera" class="camera-box p-5 text-center cursor-pointer position-relative" onclick="nyalakanKamera()">
                            <div class="py-4">
                                <div class="bg-success text-white rounded-circle d-inline-flex p-3 mb-2 shadow-sm">
                                    <i class="bi bi-camera-fill fs-3"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Nyalakan Kamera Langsung</h6>
                                <p class="text-muted small mb-0">Ketuk untuk mengaktifkan kamera depan/belakang</p>
                            </div>
                        </div>

                        <!-- Area Tampilan Video Kamera (Ditambah style transform mirror) -->
<div id="wrapperVideoKamera" class="d-none text-center position-relative bg-black rounded-4 overflow-hidden shadow-sm p-2">
    <video id="videoKamera" autoplay playsinline class="w-100 rounded-3" style="max-height: 350px; object-fit: cover; transform: scaleX(-1);"></video>
    <div class="mt-3 pb-2">
        <button type="button" class="btn btn-light text-dark fw-bold px-4 rounded-pill shadow-sm me-2" onclick="ambilFoto()">
            <i class="bi bi-circle-fill text-danger me-1"></i> Jepret Foto
        </button>
        <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3" onclick="matikanKamera()">
            Tutup Kamera
        </button>
    </div>
</div>

                        <!-- Canvas Tersembunyi untuk Proses Capture Foto -->
                        <canvas id="canvasFoto" class="d-none"></canvas>

                        <!-- Preview Hasil Jepretan Foto -->
                        <div id="previewContainer" class="mt-3 d-none text-center">
                            <img id="imgPreview" src="" alt="Hasil Foto" class="img-fluid rounded-4 shadow-sm border" style="max-height: 250px;">
                            <div class="mt-2">
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="ulangiFoto()">
                                    <i class="bi bi-arrow-repeat me-1"></i> Ambil Ulang Foto
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Keterangan / Judul Laporan -->
                    <div class="mb-4">
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="regionLaporan" class="form-label fw-bold text-dark small">Region</label>
                                <select class="form-select" id="regionLaporan" required>
                                    <option value="unesa-lidah-wetan">UNESA Lidah Wetan</option>
                                    <option value="unesa-ketintang">UNESA Ketintang</option>
                                    <option value="rawamangun">Rawamangun</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="kategoriLaporan" class="form-label fw-bold text-dark small">Kategori</label>
                                <select class="form-select" id="kategoriLaporan" required>
                                    <option value="penerangan_padam">Penerangan Padam / Terbatas</option>
                                    <option value="area_sepi">Kondisi Area Sepi / Perlu Perhatian</option>
                                    <option value="hambatan_fisik">Hambatan Fisik Jalan</option>
                                </select>
                            </div>
                        </div>
                        <label for="deskripsiLaporan" class="form-label fw-bold text-dark small"><span class="badge bg-dark rounded-circle me-1">2</span> Keterangan Kondisi</label>
                        <textarea class="form-control rounded-3" id="deskripsiLaporan" rows="3" placeholder="Contoh: Lampu penerangan jalan mati total, jalan gelap dan rawan..." required></textarea>
                    </div>

                    <!-- Tombol Kirim Laporan -->
                    <button type="button" class="btn btn-success w-100 py-3 rounded-pill fw-bold shadow-sm" id="btnKirimLaporan">
                        <i class="bi bi-upload me-2"></i> Kirim Laporan
                    </button>
                    <div id="statusKirimLaporan" class="small text-center text-muted mt-2" role="status"></div>

                </div>

            </div>

        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
   <script>
    let mediaStream = null;
    let useFrontCamera = false; // Default pakai kamera belakang (environment) untuk foto kondisi jalan
    let currentLatitude = null;
    let currentLongitude = null;
    let capturedPhoto = null;

    // Fungsi Menyalakan Kamera (Diperbarui agar aman dari race condition stream)
    async function nyalakanKamera() {
        const wrapperBuka = document.getElementById('wrapperBukaKamera');
        const wrapperVideo = document.getElementById('wrapperVideoKamera');
        const btnSwitch = document.getElementById('btnSwitchKamera');
        const video = document.getElementById('videoKamera');

        if (!video) return;

        try {
            // Pastikan stream sebelumnya benar-benar bersih/mati dulu
            matikanKameraStreamSaja();

            // Konfigurasi constraints kamera depan/belakang
            const constraints = {
                video: {
                    facingMode: useFrontCamera ? "user" : "environment"
                },
                audio: false
            };

            mediaStream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = mediaStream;

            // Sembunyikan tombol awal, tampilkan area video & tombol switch
            if (wrapperBuka) wrapperBuka.classList.add('d-none');
            if (wrapperVideo) wrapperVideo.classList.remove('d-none');
            if (btnSwitch) btnSwitch.classList.remove('d-none');
        } catch (err) {
            console.error("Gagal mengakses kamera:", err);
            alert("Tidak dapat mengakses kamera. Pastikan izin kamera di browser sudah diaktifkan dan tidak sedang digunakan aplikasi lain.");
        }
    }

    // Fungsi Ganti Kamera (Depan <-> Belakang)
    async function gantiKamera() {
        useFrontCamera = !useFrontCamera;
        // Tunggu sebentar hingga stream benar-benar berhenti sebelum menyalakan ulang
        matikanKameraStreamSaja();
        await nyalakanKamera(); 
    }

    // Fungsi Jepret / Ambil Foto dari Video Stream
    function ambilFoto() {
        const video = document.getElementById('videoKamera');
        const canvas = document.getElementById('canvasFoto');
        const imgPreview = document.getElementById('imgPreview');
        const previewContainer = document.getElementById('previewContainer');
        const wrapperVideo = document.getElementById('wrapperVideoKamera');
        const btnSwitch = document.getElementById('btnSwitchKamera');

        if (!video || !canvas || !imgPreview) return;

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        
        let ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Ubah hasil canvas ke format gambar (Base64)
        let imageDataURL = canvas.toDataURL('image/png');
        imgPreview.src = imageDataURL;
        canvas.toBlob(blob => { capturedPhoto = blob; }, 'image/png');

        // Matikan stream kamera setelah dijepret
        matikanKameraStreamSaja();

        // Tampilkan hasil preview foto
        if (wrapperVideo) wrapperVideo.classList.add('d-none');
        if (btnSwitch) btnSwitch.classList.add('d-none');
        if (previewContainer) previewContainer.classList.remove('d-none');
    }

    // Matikan stream kamera
    function matikanKameraStreamSaja() {
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
            mediaStream = null;
        }
    }

    // Tutup kamera sepenuhnya kembali ke tombol awal
    function matikanKamera() {
        matikanKameraStreamSaja();
        const wrapperVideo = document.getElementById('wrapperVideoKamera');
        const btnSwitch = document.getElementById('btnSwitchKamera');
        const wrapperBuka = document.getElementById('wrapperBukaKamera');

        if (wrapperVideo) wrapperVideo.classList.add('d-none');
        if (btnSwitch) btnSwitch.classList.add('d-none');
        if (wrapperBuka) wrapperBuka.classList.remove('d-none');
    }

    // Ambil ulang foto
    function ulangiFoto() {
        const previewContainer = document.getElementById('previewContainer');
        if (previewContainer) previewContainer.classList.add('d-none');
        nyalakanKamera();
    }

    async function kirimLaporan() {
        const button = document.getElementById('btnKirimLaporan');
        const status = document.getElementById('statusKirimLaporan');
        const description = document.getElementById('deskripsiLaporan')?.value.trim();
        const token = localStorage.getItem('ta_token');
        if (!token) {
            alert('Silakan masuk terlebih dahulu untuk mengirim laporan.');
            return;
        }
        if (!description || !capturedPhoto || currentLatitude === null || currentLongitude === null) {
            alert('Lengkapi deskripsi, foto kamera, dan lokasi GPS terlebih dahulu.');
            return;
        }
        const formData = new FormData();
        formData.append('region_slug', document.getElementById('regionLaporan').value);
        formData.append('category', document.getElementById('kategoriLaporan').value);
        formData.append('lat', currentLatitude);
        formData.append('lng', currentLongitude);
        formData.append('user_lat', currentLatitude);
        formData.append('user_lng', currentLongitude);
        formData.append('description', description);
        formData.append('photo', capturedPhoto, 'laporan-kondisi.png');
        button.disabled = true;
        status.textContent = 'Mengirim foto dan menunggu verifikasi AI Gemini...';
        try {
            const response = await fetch('/api/v1/reports', {
                method: 'POST',
                headers: {Accept: 'application/json', Authorization: `Bearer ${token}`},
                body: formData
            });
            const payload = await response.json().catch(() => ({}));
            if (response.status === 401) throw new Error('Sesi login berakhir. Silakan masuk kembali.');

            // Handle 409 Conflict - Deteksi laporan duplikat dalam radius 20 meter
            if (response.status === 409 && payload.duplicate_report_id) {
                const desc = payload.duplicate_report?.description ? `"${payload.duplicate_report.description}"` : 'laporan di titik ini';
                const confirmUpcheck = confirm(
                    `Laporan serupa sudah dilaporkan dalam radius ≤ 20m:\n` +
                    `${desc}\n\n` +
                    `Apakah Anda ingin memberikan Up-Check (konfirmasi) untuk laporan yang sudah ada tersebut?`
                );
                if (confirmUpcheck) {
                    status.textContent = 'Mengirimkan Up-Check untuk laporan terdekat...';
                    const upResponse = await fetch(`/api/v1/reports/${payload.duplicate_report_id}/upvote`, {
                        method: 'POST',
                        headers: {Accept: 'application/json', Authorization: `Bearer ${token}`}
                    });
                    const upPayload = await upResponse.json().catch(() => ({}));
                    if (!upResponse.ok) throw new Error(upPayload.message || 'Gagal memberikan Up-Check.');
                    alert(upPayload.message || 'Berhasil memberikan Up-Check pada laporan yang ada!');
                    window.location.href = '/app';
                    return;
                } else {
                    status.textContent = 'Pengiriman laporan dibatalkan (terdeteksi duplikat).';
                    return;
                }
            }

            if (!response.ok) throw new Error(payload.message || 'Laporan gagal dikirim.');

            let successMsg = payload.message || 'Laporan berhasil dikirim.';
            if (payload.ai_check) {
                successMsg += `\n\n[Analisis AI Gemini]\nStatus: ${payload.ai_check.status}\nCatatan: ${payload.ai_check.notes || '-'}`;
            }
            alert(successMsg);
            window.location.href = '/app';
        } catch (error) {
            status.textContent = '';
            alert(error.message);
        } finally {
            button.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const btnKirim = document.getElementById('btnKirimLaporan');
        if (btnKirim) {
            btnKirim.addEventListener('click', kirimLaporan);
        }

        // Cek status mode simulasi juri
        fetch('/api/v1/simulation', {headers: {Accept: 'application/json'}})
            .then(res => res.json())
            .then(data => {
                if (data && data.simulation_mode) {
                    const simBadge = document.getElementById('badgeSimulationMode');
                    if (simBadge) simBadge.classList.remove('d-none');
                }
            })
            .catch(() => {});
        fetch('/api/v1/regions', {headers: {Accept: 'application/json'}})
            .then(response => response.ok ? response.json() : Promise.reject(new Error('Region gagal dimuat.')))
            .then(payload => {
                const regions = Array.isArray(payload.data) ? payload.data : [];
                const select = document.getElementById('regionLaporan');
                if (!select || !regions.length) return;
                select.innerHTML = '';
                regions.forEach(region => {
                    const option = document.createElement('option');
                    option.value = region.slug;
                    option.textContent = region.name;
                    select.appendChild(option);
                });
            })
            .catch(error => console.error('Gagal memuat region laporan:', error));

        // Jalankan fungsi deteksi GPS otomatis begitu halaman dibuka
        deteksiLokasiGPS();
    });

    // Fungsi Deteksi Lokasi GPS
    function deteksiLokasiGPS() {
        const statusTeks = document.getElementById('statusLokasiTeks');
        const lokasiTeks = document.getElementById('lokasi-teks');
        const badgeAkurasi = document.getElementById('badgeAkurasi');
        const boxLokasi = document.getElementById('boxLokasi');
        const iconLokasi = document.getElementById('iconLokasi');

        if (!lokasiTeks) return;

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(async function(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                currentLatitude = lat;
                currentLongitude = lon;
                const akurasi = Math.round(position.coords.accuracy);

                lokasiTeks.innerText = `Lat: ${lat.toFixed(5)}, Lon: ${lon.toFixed(5)}`;
                if (badgeAkurasi) badgeAkurasi.innerText = `± ${akurasi} M`;

                // Ubah style jadi hijau (Valid)
                if (boxLokasi) boxLokasi.className = "d-flex align-items-center justify-content-between p-3 rounded-3 bg-success-subtle border border-success-subtle mb-4";
                if (iconLokasi) iconLokasi.className = "bi bi-geo-alt-fill text-success fs-4 me-3";
                if (statusTeks) statusTeks.innerHTML = 'Lokasi Terverifikasi <span class="badge bg-success text-white ms-2" style="font-size: 10px;">GPS VALID</span>';

                // Coba ambil nama jalan asli menggunakan API Nominatim OpenStreetMap
                try {
                    let response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
                    let data = await response.json();
                    if (data && data.display_name) {
                        lokasiTeks.innerText = data.display_name;
                    }
                } catch (e) {
                    console.log("Gagal memuat nama jalan detail, tetap menggunakan koordinat GPS.");
                }

            }, function(error) {
                // Jika user menolak izin GPS atau GPS mati
                if (boxLokasi) boxLokasi.className = "d-flex align-items-center justify-content-between p-3 rounded-3 bg-danger-subtle border border-danger-subtle mb-4";
                if (iconLokasi) iconLokasi.className = "bi bi-exclamation-triangle-fill text-danger fs-4 me-3";
                if (statusTeks) statusTeks.innerText = "GPS Gagal / Tidak Diizinkan";
                lokasiTeks.innerText = "Mohon aktifkan izin lokasi di browser untuk melanjutkan.";
                if (badgeAkurasi) badgeAkurasi.innerText = "GAGAL";
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            });
        } else {
            if (statusTeks) statusTeks.innerText = "GPS Tidak Didukung";
            lokasiTeks.innerText = "Browser kamu tidak mendukung fitur geolokasi.";
        }
    }
</script>
</body>
</html>