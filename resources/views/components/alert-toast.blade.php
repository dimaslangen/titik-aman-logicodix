<div id="taAlertToast" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;" aria-live="polite" aria-atomic="true">
    @if(session('success'))
        <div class="toast show text-bg-success border-0" role="alert"><div class="d-flex"><div class="toast-body">{{ session('success') }}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>
    @endif
    @if(session('error'))
        <div class="toast show text-bg-danger border-0" role="alert"><div class="d-flex"><div class="toast-body">{{ session('error') }}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>
    @endif
</div>
<script>
    window.taShowAlert = function(message, type = 'success') {
        const container = document.getElementById('taAlertToast');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = `toast show text-bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `<div class="d-flex"><div class="toast-body"></div><button type="button" class="btn-close btn-close-white me-2 m-auto"></button></div>`;
        toast.querySelector('.toast-body').textContent = message;
        toast.querySelector('.btn-close').addEventListener('click', () => toast.remove());
        container.appendChild(toast);
        window.setTimeout(() => toast.remove(), 5000);
    };
</script>
