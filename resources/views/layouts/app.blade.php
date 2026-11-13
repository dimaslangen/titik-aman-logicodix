<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f766e">
    <title>@yield('title', 'TitikAman - Civic Safety Grid')</title>

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
            --ta-primary: #0f766e;
        }
        html, body {
            min-height: 100%;
            margin: 0;
            background: var(--ta-background);
            color: var(--ta-ink);
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .ta-shell { min-height: 100vh; }
        .ta-desktop-nav { 
            width: 17rem; 
            flex: 0 0 17rem; 
            background: #ffffff; 
            border-right: 1px solid #e2e8f0;
        }
        .ta-main {
            min-width: 0;
            padding: 1rem;
            padding-bottom: 6rem;
        }
        .ta-content { width: 100%; max-width: 1440px; margin: 0 auto; }
        .ta-card { background: var(--ta-surface); border: 1px solid #e2e8f0; border-radius: 1rem; }
        .ta-map { min-height: 300px; }
        
        /* Styling Logo Header Sidebar */
        .ta-brand-header {
            padding: 1.25rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .ta-brand-logo {
            width: 38px;
            height: 38px;
            object-fit: contain;
            border-radius: 8px;
        }
        
        @media (min-width: 768px) {
            .ta-shell { display: flex; }
            .ta-main { flex: 1 1 auto; height: 100vh; overflow-y: auto; padding: 1.5rem; }
            .ta-map { min-height: 440px; }
        }
        @media (max-width: 374px) {
            .ta-main { padding-left: .75rem; padding-right: .75rem; }
        }
        .leaflet-container { z-index: 0; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="ta-shell">
        <!-- Sidebar Desktop -->
        <aside class="ta-desktop-nav d-none d-md-block">
            <!-- Header Logo Brand TitikAman -->
            <div class="ta-brand-header">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo TitikAman" class="ta-brand-logo shadow-sm">
                <div>
                    <h5 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.3px; line-height: 1.1;">TitikAman</h5>
                    <small class="text-muted fw-semibold d-block" style="font-size: 10px; letter-spacing: 0.8px; margin-top: 2px;">CIVIC SAFETY GRID</small>
                </div>
            </div>

            <!-- Menu Navigasi Komponen Navbar -->
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
                                <span class="rounded-circle bg-success text-white d-inline-flex justify-content-center align-items-center fw-bold" style="width: 24px; height: 24px; font-size: 11px;">${initial}</span>
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