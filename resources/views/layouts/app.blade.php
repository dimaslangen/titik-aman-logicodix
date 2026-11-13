<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#11653A">
    <title>@yield('title', 'TitikAman - Civic Safety Grid')</title>

    <link rel="icon" type="image/png" href="{{ asset('images/TitikAman(no-teks).png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        :root {
            --ta-ink: #0f172a;
            --ta-muted: #64748b;
            --ta-surface: #ffffff;
            --ta-background: #f1f5f9;
            --ta-primary: #15803d;
            --ta-primary-deep: #11653a;
            --ta-lime: #65a30d;
            --ta-lime-bright: #80c826;
            --ta-active-bg: rgba(21, 128, 61, 0.10);
            --ta-active-border: rgba(21, 128, 61, 0.22);
        }
        html, body {
            min-height: 100%;
            margin: 0;
            background: var(--ta-background);
            color: var(--ta-ink);
            font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .ta-shell { min-height: 100vh; }
        .ta-main {
            min-width: 0;
            padding: 1rem;
            padding-bottom: 6rem;
        }
        .ta-content { width: 100%; max-width: 1440px; margin: 0 auto; }
        .ta-card { background: var(--ta-surface); border: 1px solid #e2e8f0; border-radius: 1rem; }
        .ta-map { min-height: 300px; }
        .ta-desktop-nav a.bg-success-subtle {
            background-color: var(--ta-active-bg) !important;
            border: 1px solid var(--ta-active-border);
        }
        .ta-desktop-nav a.text-success,
        nav[aria-label="Navigasi mobile"] a.text-success {
            color: var(--ta-primary-deep) !important;
        }
        @media (min-width: 768px) {
            .ta-shell { display: flex; }
            .ta-desktop-nav { width: 17rem; flex: 0 0 17rem; }
            .ta-main { flex: 1 1 auto; height: 100vh; overflow-y: auto; padding: 1.5rem; }
            .ta-map { min-height: 440px; }
        }
        @media (max-width: 374px) {
            .ta-main { padding-left: .75rem; padding-right: .75rem; }
        }
        .leaflet-container { z-index: 0; }
        .rounded-circle { flex-shrink: 0; aspect-ratio: 1 / 1; }
        .ta-circle {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            flex-shrink: 0;
            aspect-ratio: 1 / 1;
            border-radius: 50% !important;
        }
        .ta-circle-24 { width: 24px !important; height: 24px !important; min-width: 24px; }
        .ta-circle-38 { width: 38px !important; height: 38px !important; min-width: 38px; }
        .ta-circle-40 { width: 40px !important; height: 40px !important; min-width: 40px; }
        .ta-circle-56 { width: 56px !important; height: 56px !important; min-width: 56px; }
        .ta-circle-64 { width: 64px !important; height: 64px !important; min-width: 64px; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="ta-shell">
        <aside class="ta-desktop-nav d-none d-md-block">
            <x-navbar />
        </aside>

        <main class="ta-main" id="appMain">
            <div class="ta-content">
                @yield('content')
            </div>
        </main>
    </div>

    <x-bottom-nav />
    <x-sos-button />
    <x-alert-toast />
    @include('components.guest-prompt-modal')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        window.taApiBase = '/api/v1';
        window.taCurrentUser = null;
        document.querySelectorAll('[data-admin-nav="true"]').forEach(item => item.classList.add('d-none'));

        window.taToggleSidebar = function() {};

        window.taPromptGuest = function(title, desc) {
            const titleElement = document.getElementById('guestPromptTitle');
            const descriptionElement = document.getElementById('guestPromptDesc');
            if (titleElement && title) titleElement.textContent = title;
            if (descriptionElement && desc) descriptionElement.textContent = desc;
            const modalElement = document.getElementById('guestPromptModal');
            if (modalElement) bootstrap.Modal.getOrCreateInstance(modalElement).show();
        };

        async function taInitAuth() {
            const containers = document.querySelectorAll('[data-ta-auth]');
            const token = localStorage.getItem('ta_token');
            if (!containers.length) return;

            if (!token) {
                containers.forEach(container => {
                    container.innerHTML = '<a href="/masuk" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold">Masuk</a>';
                });
                return;
            }

            try {
                const response = await fetch(`${window.taApiBase}/me`, {
                    headers: {Accept: 'application/json', Authorization: `Bearer ${token}`}
                });
                if (!response.ok) throw new Error('Token expired');
                const payload = await response.json();
                window.taCurrentUser = payload.data;
                const user = payload.data;
                const initial = (user.name || 'U').charAt(0).toUpperCase();
                if (user.role === 'admin') {
                    document.querySelectorAll('[data-admin-nav="true"]').forEach(item => item.classList.remove('d-none'));
                }
                containers.forEach(container => {
                    container.innerHTML = `
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light rounded-pill px-3 py-1 d-flex align-items-center gap-2 border shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <span class="rounded-circle ta-circle ta-circle-24 bg-success text-white d-inline-flex justify-content-center align-items-center fw-bold" style="font-size: 11px;">${initial}</span>
                                <span class="fw-semibold text-dark small">${escapeHtml(user.name)}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                <li><button class="dropdown-item small text-danger" onclick="window.taLogout()">Keluar</button></li>
                            </ul>
                        </div>`;
                });
            } catch (error) {
                localStorage.removeItem('ta_token');
                containers.forEach(container => {
                    container.innerHTML = '<a href="/masuk" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold">Masuk</a>';
                });
            }
        }

        window.taLogout = async function() {
            const token = localStorage.getItem('ta_token');
            if (token) {
                await fetch(`${window.taApiBase}/logout`, {
                    method: 'POST',
                    headers: {Accept: 'application/json', Authorization: `Bearer ${token}`}
                }).catch(() => {});
            }
            localStorage.removeItem('ta_token');
            localStorage.removeItem('ta_admin_token');
            window.location.reload();
        };

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, character => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
            }[character]));
        }

        document.addEventListener('DOMContentLoaded', taInitAuth);
    </script>
    @stack('scripts')
</body>
</html>
