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
            max-width: 100%;
            margin: 0;
            overflow-x: hidden;
            background: var(--ta-background);
            color: var(--ta-ink);
            font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        body.ta-sidebar-open { overflow: hidden; }
        .ta-shell { min-height: 100vh; }
        .ta-main {
            min-width: 0;
            padding: 1rem;
            padding-bottom: 1rem;
        }
        .ta-sidebar {
            background: #fff;
            border-right: 1px solid #e2e8f0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            left: 0;
            overflow-y: auto;
            padding: 1.25rem;
            position: fixed;
            top: 0;
            transform: translateX(-100%);
            transition: transform .28s ease;
            width: 275px;
            z-index: 1060;
        }
        .ta-sidebar.is-open { transform: translateX(0); }
        .ta-sidebar .nav-link {
            border-radius: 10px;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 4px;
        }
        .ta-sidebar .nav-link:hover,
        .ta-sidebar .nav-link.active {
            background: var(--ta-active-bg);
            color: var(--ta-primary-deep);
        }
        .ta-sidebar .nav-link.active {
            border: 1px solid var(--ta-active-border);
            font-weight: 600;
        }
        .ta-sidebar-backdrop {
            background: rgba(15, 23, 42, .45);
            inset: 0;
            opacity: 0;
            pointer-events: none;
            position: fixed;
            transition: opacity .28s ease;
            z-index: 1050;
        }
        .ta-sidebar-backdrop.is-visible {
            opacity: 1;
            pointer-events: auto;
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
        .ta-topbar-actions {
            min-width: 0;
        }
        @media (min-width: 992px) {
            .ta-shell { display: block; }
            .ta-sidebar {
                transform: translateX(0);
                width: 260px;
            }
            .ta-sidebar-close { display: none; }
            .ta-main { height: 100vh; margin-left: 260px; overflow-y: auto; padding: 1.5rem; }
            .ta-map { min-height: 440px; }
        }
        @media (max-width: 575.98px) {
            .ta-topbar {
                align-items: flex-start !important;
            }
            .ta-topbar-heading {
                min-width: 0;
                flex: 1 1 auto;
            }
            .ta-topbar-heading > div {
                min-width: 0;
            }
            .ta-topbar-heading h4 {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .ta-topbar-actions {
                flex: 1 1 100%;
                justify-content: flex-start !important;
            }
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
        .form-select,
        select.form-control {
            font-size: 0.875rem !important;
            line-height: 1.35 !important;
            padding: 0.45rem 2.1rem 0.45rem 0.9rem !important;
            min-height: 38px !important;
            height: auto !important;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="ta-shell">
        <x-sidebar />
        <div class="ta-sidebar-backdrop" id="taSidebarBackdrop" onclick="window.taToggleSidebar()"></div>

        <main class="ta-main" id="appMain">
            <div class="ta-content">
                @yield('content')
            </div>
        </main>
    </div>

    <x-sos-button />
    <x-alert-toast />
    <x-confirm-modal />
    @include('components.guest-prompt-modal')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        window.taApiBase = '/api/v1';
        window.taCurrentUser = null;
        document.querySelectorAll('[data-admin-nav="true"]').forEach(item => item.classList.add('d-none'));

        window.taToggleSidebar = function(forceState) {
            const sidebar = document.getElementById('taCivicSidebar');
            const backdrop = document.getElementById('taSidebarBackdrop');
            if (!sidebar || !backdrop) return;
            const shouldOpen = typeof forceState === 'boolean'
                ? forceState
                : !sidebar.classList.contains('is-open');
            sidebar.classList.toggle('is-open', shouldOpen);
            backdrop.classList.toggle('is-visible', shouldOpen);
            document.body.classList.toggle('ta-sidebar-open', shouldOpen);
        };

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') window.taToggleSidebar(false);
        });

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
                    container.innerHTML = `
                        <div class="d-flex align-items-center gap-2">
                            <span class="ta-circle ta-circle-40 bg-light text-secondary">
                                <i class="bi bi-person"></i>
                            </span>
                            <div class="min-width-0 flex-grow-1">
                                <span class="fw-semibold text-dark small d-block text-truncate">Mode Tamu</span>
                                <small class="text-muted d-block text-truncate">Belum masuk</small>
                            </div>
                            <a href="/masuk" class="btn btn-sm btn-outline-success rounded-pill px-2 fw-semibold">Masuk</a>
                        </div>`;
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
                        <div class="d-flex align-items-center gap-2">
                            <span class="ta-circle ta-circle-40 bg-success text-white fw-bold" style="font-size: 12px;">${initial}</span>
                            <div class="min-width-0 flex-grow-1">
                                <span class="fw-semibold text-dark small d-block text-truncate">${escapeHtml(user.name)}</span>
                                <small class="text-muted d-block text-truncate">Warga Terverifikasi</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger-subtle text-danger rounded-circle ta-circle ta-circle-38" onclick="window.taLogout()" aria-label="Keluar">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        </div>`;
                });
            } catch (error) {
                localStorage.removeItem('ta_token');
                containers.forEach(container => {
                    container.innerHTML = `
                        <div class="d-flex align-items-center gap-2">
                            <span class="ta-circle ta-circle-40 bg-light text-secondary"><i class="bi bi-person"></i></span>
                            <div class="min-width-0 flex-grow-1">
                                <span class="fw-semibold text-dark small d-block text-truncate">Mode Tamu</span>
                                <small class="text-muted d-block text-truncate">Belum masuk</small>
                            </div>
                            <a href="/masuk" class="btn btn-sm btn-outline-success rounded-pill px-2 fw-semibold">Masuk</a>
                        </div>`;
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
