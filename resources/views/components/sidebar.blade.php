@props(['active' => null])

@php
    $active = $active ?? (
        request()->routeIs('rute') ? 'rute' :
        (request()->routeIs('hubs') || request()->is('safety-hubs-ui') ? 'hubs' :
        (request()->routeIs('lapor') ? 'lapor' : 'app'))
    );
@endphp

<aside id="taCivicSidebar" class="ta-sidebar overflow-x-hidden" aria-label="Navigasi utama">
    <div class="d-flex align-items-start justify-content-between gap-2 mb-4">
        <div>
            <x-brand-logo theme="light" size="md" />
            <small class="text-uppercase text-muted fw-bold d-block mt-2" style="font-size: 10px; letter-spacing: .12em;">Civic Safety Grid</small>
        </div>
        <button type="button" class="ta-sidebar-close sidebar-close-btn btn btn-light d-lg-none lg:hidden rounded-circle ta-circle ta-circle-40" onclick="window.taToggleSidebar(false)" aria-label="Tutup menu navigasi">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <nav class="flex-grow-1" aria-label="Menu warga">
        <ul class="nav flex-column gap-1">
            <li class="nav-item">
                <a href="/app" class="nav-link py-2 px-3 {{ $active === 'app' ? 'active' : '' }}" onclick="window.taToggleSidebar(false)">
                    <i class="bi bi-map me-2"></i>Peta Aman
                </a>
            </li>
            <li class="nav-item">
                <a href="/rute-saya" class="nav-link py-2 px-3 {{ $active === 'rute' ? 'active' : '' }}" onclick="window.taToggleSidebar(false)">
                    <i class="bi bi-signpost-split me-2"></i>Rute Saya
                </a>
            </li>
            <li class="nav-item">
                <a href="/safety-hubs-ui" class="nav-link py-2 px-3 {{ $active === 'hubs' ? 'active' : '' }}" onclick="window.taToggleSidebar(false)">
                    <i class="bi bi-building me-2"></i>Safety Hub
                </a>
            </li>
            <li class="nav-item">
                <a href="/lapor-kondisi" class="nav-link py-2 px-3 {{ $active === 'lapor' ? 'active' : '' }}" onclick="return handleSidebarLaporClick(event)">
                    <i class="bi bi-exclamation-triangle me-2"></i>Lapor Kondisi
                </a>
            </li>
            <li class="nav-item d-none" id="sidebarAdminNav" data-admin-nav="true">
                <a href="/admin" class="nav-link py-2 px-3 {{ $active === 'admin' ? 'active' : '' }}" onclick="window.taToggleSidebar(false)">
                    <i class="bi bi-shield-lock me-2"></i>Admin
                </a>
            </li>
        </ul>

        <a href="{{ route('tentang') }}" class="nav-link py-2 px-3 mt-3 text-secondary" onclick="window.taToggleSidebar(false)">
            <i class="bi bi-info-circle me-2"></i>Tentang TitikAman
        </a>
    </nav>

    <div class="pt-3 mt-3 border-top">
        <div data-ta-auth class="ta-sidebar-account">
            <span class="spinner-border spinner-border-sm text-secondary" id="authSpinner" role="status" style="width: 14px; height: 14px;"></span>
        </div>
    </div>
</aside>

<script>
    function handleSidebarLaporClick(event) {
        const token = localStorage.getItem('ta_token');
        if (!token) {
            event.preventDefault();
            window.taToggleSidebar(false);
            if (typeof window.taPromptGuest === 'function') {
                window.taPromptGuest(
                    'Masuk untuk Melaporkan Kondisi',
                    'Untuk membuat laporan kondisi jalan atau bahaya di sekitar Anda, silakan masuk ke akun terlebih dahulu agar dapat diverifikasi komunitas.'
                );
            } else {
                window.location.href = '/masuk';
            }
            return false;
        }
        return true;
    }
</script>
