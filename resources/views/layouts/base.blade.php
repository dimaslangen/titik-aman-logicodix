<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TitikAman - Civic Safety Grid')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Mencegah window utama ikut scroll */
            background-color: #f4f7f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .app-wrapper {
            display: flex;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
        }
        .app-sidebar {
            width: 260px;
            min-width: 260px;
            max-width: 260px;
            height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.5rem 1rem;
            overflow-y: auto;
            flex-shrink: 0;
            z-index: 1030;
            transition: left 0.3s ease;
        }
        .app-main {
            flex: 1 1 auto;
            height: 100vh;
            overflow-y: auto; /* Konten utama bergulir mandiri */
            overflow-x: hidden;
            padding: 1.5rem;
            position: relative;
            background-color: #f4f7f6;
        }
        .nav-link {
            color: #4a5568;
            font-weight: 500;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.2s ease;
        }
        .nav-link:hover, .nav-link.active {
            background-color: #e6fffa;
            color: #0d9488;
        }
        .nav-link.active {
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .app-sidebar {
                position: fixed;
                left: -260px;
                top: 0;
                bottom: 0;
            }
            .app-sidebar.show {
                left: 0;
                box-shadow: 0 0 25px rgba(0,0,0,0.25);
            }
            .sidebar-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1025;
            }
            .sidebar-backdrop.show {
                display: block;
            }
            .app-main {
                padding: 1rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <div class="app-wrapper">
        <!-- Backdrop mobile -->
        <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="window.taToggleSidebar()"></div>

        <!-- Sidebar Reusable -->
        <aside class="app-sidebar" id="appSidebar">
            @include('components.sidebar', ['active' => $active ?? 'app'])
        </aside>

        <!-- Konten Utama Kanan (Scroll Mandiri) -->
        <main class="app-main" id="appMain">
            @yield('content')
        </main>
    </div>

    <!-- Modal SOS 110 Darurat Terpusat -->
    @include('components.sos-modal')

    <!-- Modal Ajakan Masuk untuk Tamu -->
    @include('components.guest-prompt-modal')

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        window.taApiBase = '/api/v1';
        window.taCurrentUser = null;

        // Toggle Sidebar Mobile
        window.taToggleSidebar = function() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.toggle('show');
            if (backdrop) backdrop.classList.toggle('show');
        };

        // Modal Ajakan Masuk untuk Tamu
        window.taPromptGuest = function(title, desc) {
            if (title) {
                const el = document.getElementById('guestPromptTitle');
                if (el) el.textContent = title;
            }
            if (desc) {
                const el = document.getElementById('guestPromptDesc');
                if (el) el.textContent = desc;
            }
            const modalEl = document.getElementById('guestPromptModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        };

        // Global Auth State Handler
        async function taInitAuth() {
            const container = document.getElementById('globalAuthContainer');
            const token = localStorage.getItem('ta_token');
            if (!container) return;

            if (!token) {
                container.innerHTML = `
                    <a href="/masuk" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                    </a>
                `;
                return;
            }

            try {
                const res = await fetch(`${window.taApiBase}/me`, {
                    headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` }
                });
                if (!res.ok) throw new Error('Token expired');
                const json = await res.json();
                window.taCurrentUser = json.data;
                const user = json.data;
                const initial = (user.name || 'U').charAt(0).toUpperCase();

                container.innerHTML = `
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light rounded-pill px-3 py-1 d-flex align-items-center gap-2 border shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <span class="rounded-circle bg-success text-white d-inline-flex justify-content-center align-items-center fw-bold" style="width: 24px; height: 24px; font-size: 11px;">
                                ${initial}
                            </span>
                            <span class="fw-semibold text-dark small">${escapeHtml(user.name)}</span>
                            ${user.role === 'admin' ? '<span class="badge bg-danger rounded-pill" style="font-size: 9px;">Admin</span>' : ''}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-1">
                            <li><h6 class="dropdown-header small text-truncate" style="max-width: 200px;">${escapeHtml(user.email)}</h6></li>
                            ${user.role === 'admin' ? '<li><a class="dropdown-item small" href="/admin"><i class="bi bi-shield-lock me-2"></i>Dashboard Admin</a></li>' : ''}
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item small text-danger" onclick="window.taLogout()"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button></li>
                        </ul>
                    </div>
                `;
            } catch (err) {
                localStorage.removeItem('ta_token');
                container.innerHTML = `
                    <a href="/masuk" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                    </a>
                `;
            }
        }

        // Global Logout Handler
        window.taLogout = async function() {
            const token = localStorage.getItem('ta_token');
            if (token) {
                try {
                    await fetch(`${window.taApiBase}/logout`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` }
                    });
                } catch (e) {}
            }
            localStorage.removeItem('ta_token');
            localStorage.removeItem('ta_admin_token');
            window.location.reload();
        };

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, char => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
            }[char]));
        }

        document.addEventListener('DOMContentLoaded', taInitAuth);
    </script>
    @stack('scripts')
</body>
</html>
