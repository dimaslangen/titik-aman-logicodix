@extends('layouts.app')

@section('title', 'TitikAman - Lapor Kondisi')

@push('styles')
<style>
    .camera-box {
        border: 2px dashed #cbd5e1;
        background-color: #f8fafc;
        border-radius: 12px;
        transition: all 0.2s;
        cursor: pointer;
    }
    .camera-box:hover {
        border-color: #0d9488;
        background-color: #f0fdfa;
    }
</style>
@endpush

@section('content')
    <!-- Topbar Header -->
    <x-topbar title="Lapor Kondisi" subtitle="Laporan Komunitas" back-url="/app">
        <span id="badgeSimulationMode" class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill d-none">
            <i class="bi bi-cpu-fill me-1"></i> SIMULASI JURI AKTIF (BYPASS RADIUS)
        </span>
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
            <i class="bi bi-geo-alt-fill me-1"></i> GPS VALID
        </span>
    </x-topbar>

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
                <label class="form-label fw-bold text-dark small mb-0"><span class="badge bg-dark rounded-circle ta-circle ta-circle-24 me-1">1</span> Ambil Foto Kondisi</label>
                <!-- Tombol Ganti Kamera (Depan/Belakang) -->
                <button type="button" id="btnSwitchKamera" class="btn btn-outline-secondary btn-sm rounded-pill d-none" onclick="gantiKamera()">
                    <i class="bi bi-camera-recess me-1"></i> Ganti Kamera
                </button>
            </div>
            <p class="text-muted small mb-2">Aktifkan kamera langsung untuk validasi keamanan.</p>
            
            <!-- Kotak Utama Kamera / Tombol Buka -->
            <div id="wrapperBukaKamera" class="camera-box p-5 text-center position-relative" onclick="nyalakanKamera()">
                <div class="py-4">
                    <div class="bg-success text-white rounded-circle ta-circle ta-circle-56 d-inline-flex p-3 mb-2 shadow-sm">
                        <i class="bi bi-camera-fill fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Nyalakan Kamera Langsung</h6>
                    <p class="text-muted small mb-0">Ketuk untuk mengaktifkan kamera depan/belakang</p>
                </div>
            </div>

            <!-- Area Tampilan Video Kamera -->
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

            <!-- Canvas Tersembunyi untuk Capture Foto -->
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

        <!-- 3. Keterangan / Form Laporan -->
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
            <label for="deskripsiLaporan" class="form-label fw-bold text-dark small"><span class="badge bg-dark rounded-circle ta-circle ta-circle-24 me-1">2</span> Keterangan Kondisi</label>
            <textarea class="form-control rounded-3" id="deskripsiLaporan" rows="3" placeholder="Contoh: Lampu penerangan jalan mati total, jalan gelap dan rawan..." required></textarea>
        </div>

        <!-- Tombol Kirim Laporan -->
        <button type="button" class="btn btn-success w-100 py-3 rounded-pill fw-bold shadow-sm" id="btnKirimLaporan">
            <i class="bi bi-upload me-2"></i> Kirim Laporan
        </button>
        <div id="statusKirimLaporan" class="small text-center text-muted mt-2" role="status"></div>

    </div>

    <div class="modal fade" id="aiResultModal" tabindex="-1" aria-labelledby="aiResultModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="aiResultModalLabel">Hasil Verifikasi AI Gemini</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span id="aiResultIcon" class="ta-circle ta-circle-56 bg-success-subtle text-success">
                            <i class="bi bi-shield-check fs-3"></i>
                        </span>
                        <div>
                            <span id="aiResultBadge" class="badge bg-success rounded-pill mb-1">VERIFIED</span>
                            <h6 id="aiResultTitle" class="fw-bold mb-0">Laporan berhasil diproses</h6>
                        </div>
                    </div>
                    <div class="bg-light rounded-3 p-3 border">
                        <div class="small text-muted fw-semibold mb-2"><i class="bi bi-stars me-1"></i>Ringkasan Analisis AI Gemini</div>
                        <p id="aiResultSummary" class="small text-dark mb-0" style="white-space: pre-line;"></p>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4 d-flex flex-column flex-sm-row">
                    <a href="{{ route('app') }}" class="btn btn-success rounded-pill px-4 flex-grow-1">
                        <i class="bi bi-map me-1"></i>Lihat di Peta Aman
                    </a>
                    <button type="button" class="btn btn-outline-success rounded-pill px-4 flex-grow-1" id="btnAiNewReport">
                        <i class="bi bi-plus-lg me-1"></i>Buat Laporan Lain
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let mediaStream = null;
    let useFrontCamera = false;
    let currentLatitude = null;
    let currentLongitude = null;
    let capturedPhoto = null;

    async function nyalakanKamera() {
        const wrapperBuka = document.getElementById('wrapperBukaKamera');
        const wrapperVideo = document.getElementById('wrapperVideoKamera');
        const btnSwitch = document.getElementById('btnSwitchKamera');
        const video = document.getElementById('videoKamera');

        if (!video) return;

        try {
            matikanKameraStreamSaja();
            const constraints = {
                video: { facingMode: useFrontCamera ? "user" : "environment" },
                audio: false
            };
            mediaStream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = mediaStream;

            if (wrapperBuka) wrapperBuka.classList.add('d-none');
            if (wrapperVideo) wrapperVideo.classList.remove('d-none');
            if (btnSwitch) btnSwitch.classList.remove('d-none');
        } catch (err) {
            console.error("Gagal mengakses kamera:", err);
            window.taShowAlert("Tidak dapat mengakses kamera. Pastikan izin kamera di browser sudah diaktifkan.", 'danger');
        }
    }

    async function gantiKamera() {
        useFrontCamera = !useFrontCamera;
        matikanKameraStreamSaja();
        await nyalakanKamera(); 
    }

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

        let imageDataURL = canvas.toDataURL('image/png');
        imgPreview.src = imageDataURL;
        canvas.toBlob(blob => { capturedPhoto = blob; }, 'image/png');

        matikanKameraStreamSaja();

        if (wrapperVideo) wrapperVideo.classList.add('d-none');
        if (btnSwitch) btnSwitch.classList.add('d-none');
        if (previewContainer) previewContainer.classList.remove('d-none');
    }

    function matikanKameraStreamSaja() {
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
            mediaStream = null;
        }
    }

    function matikanKamera() {
        matikanKameraStreamSaja();
        const wrapperVideo = document.getElementById('wrapperVideoKamera');
        const btnSwitch = document.getElementById('btnSwitchKamera');
        const wrapperBuka = document.getElementById('wrapperBukaKamera');

        if (wrapperVideo) wrapperVideo.classList.add('d-none');
        if (btnSwitch) btnSwitch.classList.add('d-none');
        if (wrapperBuka) wrapperBuka.classList.remove('d-none');
    }

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
            if (typeof window.taPromptGuest === 'function') {
                window.taPromptGuest(
                    'Masuk untuk Mengirim Laporan',
                    'Anda perlu masuk atau mendaftar terlebih dahulu agar laporan kondisi jalan dapat diproses dan diverifikasi oleh komunitas.'
                );
            } else {
                window.taShowAlert('Silakan masuk terlebih dahulu untuk mengirim laporan.', 'danger');
                window.location.href = '/masuk';
            }
            return;
        }
        
        if (!description || !capturedPhoto || currentLatitude === null || currentLongitude === null) {
            window.taShowAlert('Lengkapi deskripsi, foto kamera, dan lokasi GPS terlebih dahulu.', 'danger');
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
        status.className = 'small text-center mt-2 p-3 bg-success-subtle border border-success-subtle rounded-4 text-success';
        status.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Mengirim foto &amp; memverifikasi kondisi lapangan dengan AI Gemini...';

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
                const confirmUpcheck = await window.taConfirm({
                    title: 'Laporan Serupa Terdeteksi',
                    message: `Laporan serupa sudah dilaporkan dalam radius ≤ 20m: ${desc}\n\nApakah Anda ingin memberikan Up-Check (konfirmasi) untuk laporan yang sudah ada tersebut?`,
                    confirmText: 'Tetap Kirim Laporan',
                    variant: 'warning'
                });
                if (confirmUpcheck) {
                    status.textContent = 'Mengirimkan Up-Check untuk laporan terdekat...';
                    const upResponse = await fetch(`/api/v1/reports/${payload.duplicate_report_id}/upvote`, {
                        method: 'POST',
                        headers: {Accept: 'application/json', Authorization: `Bearer ${token}`}
                    });
                    const upPayload = await upResponse.json().catch(() => ({}));
                    if (!upResponse.ok) throw new Error(upPayload.message || 'Gagal memberikan Up-Check.');
                    window.taShowAlert(upPayload.message || 'Berhasil memberikan Up-Check pada laporan yang ada!', 'success');
                    window.location.href = '/app';
                    return;
                } else {
                    status.textContent = 'Pengiriman laporan dibatalkan (terdeteksi duplikat).';
                    return;
                }
            }

            if (!response.ok) throw new Error(payload.message || 'Laporan gagal dikirim.');

            const reportStatus = payload.data?.status || payload.status;
            const aiCheck = payload.data?.ai_check || payload.ai_check;

            const isPending = reportStatus === 'PENDING_MODERATION';
            const aiResultIcon = document.getElementById('aiResultIcon');
            const aiResultBadge = document.getElementById('aiResultBadge');
            const aiResultTitle = document.getElementById('aiResultTitle');
            const aiResultSummary = document.getElementById('aiResultSummary');
            if (aiResultIcon && aiResultBadge && aiResultTitle && aiResultSummary) {
                aiResultIcon.className = `ta-circle ta-circle-56 ${isPending ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success'}`;
                aiResultIcon.innerHTML = `<i class="bi ${isPending ? 'bi-hourglass-split' : 'bi-shield-check'} fs-3"></i>`;
                aiResultBadge.className = `badge ${isPending ? 'bg-warning text-dark' : 'bg-success'} rounded-pill mb-1`;
                aiResultBadge.textContent = isPending ? 'MENUNGGU MODERASI' : 'VERIFIED';
                aiResultTitle.textContent = isPending ? 'Laporan masuk antrean moderasi admin' : 'Laporan langsung terverifikasi valid';
                aiResultSummary.textContent = [
                    payload.message,
                    aiCheck?.reason ? `Analisis Gemini: ${aiCheck.reason}` : null
                ].filter(Boolean).join('\n\n') || 'Laporan berhasil diproses oleh sistem TitikAman.';
                bootstrap.Modal.getOrCreateInstance(document.getElementById('aiResultModal')).show();
            } else {
                window.taShowAlert(payload.message || 'Laporan berhasil dikirim.', 'success');
            }
        } catch (error) {
            status.className = 'small text-center text-danger mt-2';
            status.textContent = '';
            window.taShowAlert(error.message, 'danger');
        } finally {
            button.disabled = false;
        }
    }

    function resetLaporanForm() {
        document.getElementById('deskripsiLaporan').value = '';
        capturedPhoto = null;
        const previewContainer = document.getElementById('previewContainer');
        const imgPreview = document.getElementById('imgPreview');
        if (previewContainer) previewContainer.classList.add('d-none');
        if (imgPreview) imgPreview.removeAttribute('src');
        const status = document.getElementById('statusKirimLaporan');
        if (status) {
            status.className = 'small text-center text-muted mt-2';
            status.textContent = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const token = localStorage.getItem('ta_token');
        if (!token) {
            setTimeout(() => {
                if (typeof window.taPromptGuest === 'function') {
                    window.taPromptGuest(
                        'Perhatian: Anda Belum Masuk',
                        'Anda dapat melihat formulir ini, namun perlu masuk atau membuat akun terlebih dahulu sebelum dapat mengirimkan laporan ke komunitas.'
                    );
                }
            }, 500);
        }

        const btnKirim = document.getElementById('btnKirimLaporan');
        if (btnKirim) {
            btnKirim.addEventListener('click', kirimLaporan);
        }
        document.getElementById('btnAiNewReport')?.addEventListener('click', () => {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('aiResultModal')).hide();
            resetLaporanForm();
        });

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

        deteksiLokasiGPS();
    });

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

                if (boxLokasi) boxLokasi.className = "d-flex align-items-center justify-content-between p-3 rounded-3 bg-success-subtle border border-success-subtle mb-4";
                if (iconLokasi) iconLokasi.className = "bi bi-geo-alt-fill text-success fs-4 me-3";
                if (statusTeks) statusTeks.innerHTML = 'Lokasi Terverifikasi <span class="badge bg-success text-white ms-2" style="font-size: 10px;">GPS VALID</span>';

                try {
                    let response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
                    let data = await response.json();
                    if (data && data.display_name) {
                        lokasiTeks.innerText = data.display_name;
                    }
                } catch (e) {}

            }, function(error) {
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
@endpush
