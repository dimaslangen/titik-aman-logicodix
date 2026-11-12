<!-- Floating Tombol Darurat SOS 110 -->
<button type="button" 
        class="btn btn-danger position-fixed shadow-lg d-flex align-items-center justify-content-center gap-2 px-3 py-2 rounded-pill"
        style="bottom: 24px; right: 24px; z-index: 1040; font-weight: bold; font-size: 14px; animation: taPulseSos 2s infinite;"
        data-bs-toggle="modal" data-bs-target="#sosEmergencyModal">
    <i class="bi bi-exclamation-octagon-fill fs-5"></i>
    <span>🚨 SOS 110</span>
</button>

<style>
@keyframes taPulseSos {
    0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
    70% { box-shadow: 0 0 0 14px rgba(220, 53, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}
</style>

<!-- Modal Darurat SOS 110 -->
<div class="modal fade" id="sosEmergencyModal" tabindex="-1" aria-labelledby="sosEmergencyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white border-0 rounded-top-4 p-3">
                <h5 class="modal-title fw-bold" id="sosEmergencyModalLabel">
                    <i class="bi bi-shield-exclamation me-2"></i> Panggilan & Sinyal Darurat 110
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-danger border-0 rounded-3 small mb-3">
                    <strong>PENTING:</strong> Layanan darurat Polisi Nasional adalah <strong>110</strong> (Bebas Pulsa). Tombol ini memancarkan sinyal darurat langsung ke sistem patroli.
                </div>
                <div class="d-grid mb-3">
                    <a href="tel:110" class="btn btn-danger btn-lg py-3 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-telephone-fill me-2"></i> PANGGIL POLISI 110 SEKARANG
                    </a>
                </div>
                <hr>
                <h6 class="fw-bold text-dark small mb-2">Pancarkan Sinyal SOS ke Petugas Grid:</h6>
                <div class="mb-3">
                    <label class="form-label text-muted small mb-1">Pilih Wilayah Anda:</label>
                    <select class="form-select form-select-sm rounded-3" id="taSosRegionSelect">
                        <option value="unesa-lidah-wetan">UNESA Lidah Wetan</option>
                        <option value="unesa-ketintang">UNESA Ketintang</option>
                        <option value="rawamangun">Rawamangun</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small mb-1">Catatan Darurat (Opsional):</label>
                    <textarea class="form-control form-control-sm rounded-3" id="taSosNotesInput" rows="2" placeholder="Contoh: Butuh pertolongan darurat di dekat gerbang..."></textarea>
                </div>
                <button type="button" class="btn btn-outline-danger w-100 rounded-pill fw-semibold py-2" id="btnTaKirimSos" onclick="window.taKirimSosGrid()">
                    <i class="bi bi-broadcast me-1"></i> Pancarkan Sinyal Darurat ke Grid
                </button>
                <div id="taSosStatusMsg" class="small text-center mt-2 text-muted"></div>
            </div>
        </div>
    </div>
</div>

<script>
    window.taKirimSosGrid = async function() {
        const btn = document.getElementById('btnTaKirimSos');
        const statusEl = document.getElementById('taSosStatusMsg');
        const region = document.getElementById('taSosRegionSelect')?.value || 'unesa-lidah-wetan';
        const notes = document.getElementById('taSosNotesInput')?.value || '';
        const token = localStorage.getItem('ta_token');

        if (!confirm('Peringatan: Apakah Anda benar-benar dalam keadaan darurat dan ingin menyiarkan sinyal SOS ke seluruh petugas patroli?')) {
            return;
        }

        btn.disabled = true;
        statusEl.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mendapatkan lokasi GPS darurat...';

        const sendPayload = async (lat, lng) => {
            try {
                statusEl.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menghubungkan ke server darurat...';
                const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
                if (token) headers['Authorization'] = `Bearer ${token}`;

                const res = await fetch(`${window.taApiBase}/sos`, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify({
                        region_slug: region,
                        lat: lat,
                        lng: lng,
                        notes: notes
                    })
                });
                const payload = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(payload.message || 'Gagal memancarkan sinyal darurat.');

                statusEl.innerHTML = '<span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i> Sinyal darurat berhasil disiarkan ke petugas patroli!</span>';
                alert('🚨 Sinyal darurat berhasil disiarkan! Petugas dan sistem patroli telah menerima koordinat Anda.');
            } catch (err) {
                statusEl.innerHTML = `<span class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i> ${err.message}</span>`;
            } finally {
                btn.disabled = false;
            }
        };

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => sendPayload(pos.coords.latitude, pos.coords.longitude),
                () => {
                    const fallbackCoords = {
                        'unesa-lidah-wetan': [-7.30071, 112.67289],
                        'unesa-ketintang': [-7.31143, 112.72937],
                        'rawamangun': [-6.1928, 106.89763]
                    };
                    const [defLat, defLng] = fallbackCoords[region] || [-7.30071, 112.67289];
                    sendPayload(defLat, defLng);
                },
                { timeout: 7000 }
            );
        } else {
            sendPayload(-7.30071, 112.67289);
        }
    };
</script>
