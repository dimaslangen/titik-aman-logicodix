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
                <div class="alert alert-danger border-0 rounded-3 small mb-3 sos-info-alert">
                    <strong>PENTING:</strong> Layanan darurat Polisi Nasional adalah <strong>110</strong> (Bebas Pulsa). Tombol ini memancarkan sinyal darurat langsung ke sistem patroli.
                </div>
                <div class="d-grid mb-3">
                    <a href="tel:110" class="btn btn-danger sos-call-button">
                        <i class="bi bi-telephone-fill me-2"></i>Panggil Polisi 110
                    </a>
                </div>
                <hr>
                <div class="sos-grid-form">
                    <h6 class="fw-bold text-dark small mb-3">Pancarkan Sinyal SOS ke Petugas Grid:</h6>
                    <div class="mb-3">
                        <label class="form-label sos-form-label mb-1" for="taSosRegionSelect">Pilih Wilayah Anda:</label>
                        <select class="form-select rounded-3" id="taSosRegionSelect">
                            <option value="unesa-lidah-wetan">UNESA Lidah Wetan</option>
                            <option value="unesa-ketintang">UNESA Ketintang</option>
                            <option value="rawamangun">Rawamangun</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label sos-form-label mb-1" for="taSosNotesInput">Catatan Darurat (Opsional):</label>
                        <textarea class="form-control" id="taSosNotesInput" rows="2" placeholder="Contoh: Butuh pertolongan darurat di dekat gerbang..."></textarea>
                    </div>
                    <button type="button" class="btn btn-outline-danger sos-grid-submit" id="btnTaKirimSos" onclick="window.taKirimSosGrid()">
                        <i class="bi bi-broadcast me-1"></i>Pancarkan Sinyal Darurat ke Grid
                    </button>
                </div>
                <div id="taSosStatusMsg" class="small text-center mt-2 text-muted"></div>
            </div>

            <style>
                .sos-info-alert {
                    border-radius: 12px !important;
                    font-size: .8125rem;
                    padding: .75rem .9rem;
                }
                .sos-call-button {
                    border-radius: 12px;
                    font-size: .9rem;
                    font-weight: 700;
                    min-height: 44px;
                    padding: .6rem 1rem;
                    width: 100%;
                }
                .sos-grid-form {
                    background: #f8fafc;
                    border: 1px solid #e2e8f0;
                    border-radius: 14px;
                    padding: 1rem;
                }
                .sos-form-label {
                    color: #334155;
                    font-size: .8rem;
                    font-weight: 600;
                }
                #taSosRegionSelect {
                    border-radius: 10px;
                    font-size: .875rem;
                    min-height: 38px;
                }
                #taSosNotesInput {
                    border-radius: 10px;
                    font-size: .85rem;
                    resize: none;
                }
                .sos-grid-submit {
                    border-radius: 12px;
                    font-size: .875rem;
                    font-weight: 600;
                    min-height: 42px;
                    width: 100%;
                }
            </style>
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

        const confirmed = await window.taConfirm({
            title: 'Kirim Sinyal Darurat SOS?',
            message: 'Pastikan Anda benar-benar dalam keadaan darurat sebelum menyiarkan sinyal ke seluruh petugas patroli.',
            confirmText: 'Kirim Sinyal SOS',
            variant: 'danger'
        });
        if (!confirmed) {
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
                window.taShowAlert('Sinyal darurat berhasil disiarkan! Petugas dan sistem patroli telah menerima koordinat Anda.', 'success');
            } catch (err) {
                statusEl.innerHTML = `<span class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i> ${err.message}</span>`;
                window.taShowAlert(err.message, 'danger');
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
