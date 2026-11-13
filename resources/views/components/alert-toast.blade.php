<style>
    #taAlertToast {
        position: fixed;
        top: 84px;
        right: 24px;
        z-index: 1090;
        width: min(390px, calc(100vw - 48px));
    }
    #taAlertToast .ta-toast {
        --ta-toast-accent: #0284c7;
        align-items: flex-start;
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-left: 4px solid var(--ta-toast-accent);
        border-radius: 14px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.14);
        color: #0f172a;
        display: flex;
        gap: 12px;
        margin-bottom: 10px;
        padding: 14px 14px 14px 12px;
    }
    #taAlertToast .ta-toast-success { --ta-toast-accent: #11653a; }
    #taAlertToast .ta-toast-danger { --ta-toast-accent: #dc2626; }
    #taAlertToast .ta-toast-warning { --ta-toast-accent: #d97706; }
    #taAlertToast .ta-toast-info { --ta-toast-accent: #0284c7; }
    #taAlertToast .ta-toast-icon,
    #taAlertToast .ta-toast-close {
        align-items: center;
        aspect-ratio: 1 / 1;
        border-radius: 50%;
        display: inline-flex;
        flex-shrink: 0;
        justify-content: center;
    }
    #taAlertToast .ta-toast-icon {
        background: color-mix(in srgb, var(--ta-toast-accent) 12%, white);
        color: var(--ta-toast-accent);
        height: 34px;
        width: 34px;
    }
    #taAlertToast .ta-toast-message {
        flex: 1 1 auto;
        font-size: 13px;
        line-height: 1.5;
        padding-top: 5px;
    }
    #taAlertToast .ta-toast-close {
        background: transparent;
        border: 0;
        color: #64748b;
        height: 28px;
        width: 28px;
    }
    @media (max-width: 767.98px) {
        #taAlertToast {
            left: 12px;
            right: 12px;
            top: 12px;
            width: auto;
        }
    }
</style>

<div id="taAlertToast" aria-live="polite" aria-atomic="true">
    @foreach (['success' => session('success'), 'danger' => session('error')] as $type => $message)
        @if($message)
            <div class="ta-toast ta-toast-{{ $type }}" role="alert">
                <span class="ta-toast-icon"><i class="bi {{ $type === 'success' ? 'bi-check-lg' : 'bi-exclamation-lg' }}"></i></span>
                <div class="ta-toast-message">{{ $message }}</div>
                <button type="button" class="ta-toast-close" aria-label="Tutup notifikasi"><i class="bi bi-x-lg"></i></button>
            </div>
        @endif
    @endforeach
</div>

<script>
    (() => {
        const allowedTypes = new Set(['success', 'danger', 'warning', 'info']);
        const icons = {
            success: 'bi-check-lg',
            danger: 'bi-exclamation-lg',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-lg'
        };

        const removeToast = toast => {
            if (!toast) return;
            toast.classList.add('opacity-0');
            window.setTimeout(() => toast.remove(), 150);
        };

        const bindToast = toast => {
            toast.querySelector('.ta-toast-close')?.addEventListener('click', () => removeToast(toast));
            window.setTimeout(() => removeToast(toast), 4500);
        };

        document.querySelectorAll('#taAlertToast .ta-toast').forEach(bindToast);

        window.taShowAlert = function(message, type = 'info') {
            const container = document.getElementById('taAlertToast');
            if (!container) return;

            const safeType = allowedTypes.has(type) ? type : 'info';
            const toast = document.createElement('div');
            toast.className = `ta-toast ta-toast-${safeType}`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <span class="ta-toast-icon"><i class="bi ${icons[safeType]}"></i></span>
                <div class="ta-toast-message"></div>
                <button type="button" class="ta-toast-close" aria-label="Tutup notifikasi"><i class="bi bi-x-lg"></i></button>
            `;
            toast.querySelector('.ta-toast-message').textContent = String(message ?? '');
            container.appendChild(toast);
            bindToast(toast);
        };
    })();
</script>
