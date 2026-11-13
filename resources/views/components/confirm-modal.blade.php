<style>
    #taConfirmModal .ta-confirm-icon {
        align-items: center;
        aspect-ratio: 1 / 1;
        border-radius: 50%;
        display: inline-flex;
        flex-shrink: 0;
        height: 52px;
        justify-content: center;
        width: 52px;
    }
    #taConfirmModal .ta-confirm-icon-success { background: #e8f5ed; color: #11653a; }
    #taConfirmModal .ta-confirm-icon-danger { background: #fef2f2; color: #dc2626; }
    #taConfirmModal .ta-confirm-icon-warning { background: #fff7ed; color: #d97706; }
    #taConfirmModal .ta-confirm-icon-info { background: #eff6ff; color: #0284c7; }
</style>

<div class="modal fade" id="taConfirmModal" tabindex="-1" aria-labelledby="taConfirmTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <span id="taConfirmIcon" class="ta-confirm-icon ta-confirm-icon-success"><i class="bi bi-check-lg fs-4"></i></span>
                    <div class="flex-grow-1">
                        <h5 id="taConfirmTitle" class="fw-bold mb-2">Konfirmasi</h5>
                        <p id="taConfirmMessage" class="text-muted mb-0"></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" id="taConfirmCancel">Batal</button>
                    <button type="button" class="btn btn-success rounded-pill px-4" id="taConfirmSubmit">Ya, Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const modalElement = document.getElementById('taConfirmModal');
        if (!modalElement) return;

        let activeResolve = null;
        let completed = false;
        const variants = {
            success: { icon: 'bi-check-lg', button: 'btn-success' },
            danger: { icon: 'bi-exclamation-triangle-fill', button: 'btn-danger' },
            warning: { icon: 'bi-exclamation-lg', button: 'btn-warning' },
            info: { icon: 'bi-info-lg', button: 'btn-primary' }
        };

        const finish = result => {
            if (completed) return;
            completed = true;
            const resolve = activeResolve;
            activeResolve = null;
            resolve?.(result);
        };

        document.getElementById('taConfirmSubmit')?.addEventListener('click', () => {
            finish(true);
            bootstrap.Modal.getOrCreateInstance(modalElement).hide();
        });
        document.getElementById('taConfirmCancel')?.addEventListener('click', () => {
            finish(false);
            bootstrap.Modal.getOrCreateInstance(modalElement).hide();
        });
        modalElement.addEventListener('hidden.bs.modal', () => finish(false));

        window.taConfirm = function({
            title = 'Konfirmasi',
            message = 'Apakah Anda ingin melanjutkan?',
            confirmText = 'Ya, Lanjutkan',
            cancelText = 'Batal',
            variant = 'success'
        } = {}) {
            if (activeResolve) finish(false);
            const safeVariant = variants[variant] ? variant : 'success';
            const config = variants[safeVariant];
            const icon = document.getElementById('taConfirmIcon');
            const submit = document.getElementById('taConfirmSubmit');
            document.getElementById('taConfirmTitle').textContent = title;
            document.getElementById('taConfirmMessage').textContent = message;
            document.getElementById('taConfirmCancel').textContent = cancelText;
            submit.textContent = confirmText;
            icon.className = `ta-confirm-icon ta-confirm-icon-${safeVariant}`;
            icon.innerHTML = `<i class="bi ${config.icon} fs-4"></i>`;
            submit.className = `btn ${config.button} rounded-pill px-4`;
            completed = false;

            return new Promise(resolve => {
                activeResolve = resolve;
                bootstrap.Modal.getOrCreateInstance(modalElement).show();
            });
        };
    })();
</script>
