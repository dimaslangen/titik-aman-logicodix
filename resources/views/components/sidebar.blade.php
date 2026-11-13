<div>
    <!-- Logo / Brand -->
    <div class="d-flex align-items-center mb-4 px-2">
        <x-brand-logo theme="light" size="md" />
    </div>

    <!-- Kotak Informasi Singkat -->
    <div class="p-3 bg-light rounded-4 mb-4 border-0">
        <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">Malam Ini</small>
        <p class="fw-semibold text-dark small mt-1 mb-0">Ruang kota lebih aman, satu laporan pada satu waktu.</p>
    </div>

    <!-- Menu Navigasi Bersama -->
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="/app" class="nav-link py-2 px-3 {{ ($active ?? 'app') === 'app' ? 'active' : '' }}">
                <i class="bi bi-map me-2"></i> Peta Aman
            </a>
        </li>
        <li class="nav-item">
            <a href="/rute-saya" class="nav-link py-2 px-3 {{ ($active ?? '') === 'rute' ? 'active' : '' }}">
                <i class="bi bi-signpost-split me-2"></i> Rute Saya
            </a>
        </li>
        <li class="nav-item">
            <a href="/safety-hubs-ui" class="nav-link py-2 px-3 {{ ($active ?? '') === 'hubs' ? 'active' : '' }}">
                <i class="bi bi-building me-2"></i> Safety Hub
            </a>
        </li>
        <li class="nav-item">
            <a href="/lapor-kondisi" class="nav-link py-2 px-3 {{ ($active ?? '') === 'lapor' ? 'active' : '' }}" onclick="return handleSidebarLaporClick(event)">
                <i class="bi bi-exclamation-triangle me-2"></i> Lapor Kondisi
            </a>
        </li>
        <li class="nav-item d-none" id="sidebarAdminNav">
            <a href="/admin" class="nav-link py-2 px-3 {{ ($active ?? '') === 'admin' ? 'active' : '' }}">
                <i class="bi bi-shield-lock me-2"></i> Admin Panel
            </a>
        </li>
    </ul>
    <a href="{{ route('tentang') }}" class="nav-link py-2 px-3 mt-3 text-secondary">
        <i class="bi bi-info-circle me-2"></i> Tentang TitikAman
    </a>
</div>

<!-- Footer Status -->
<div class="px-2 pt-3 border-top mt-auto">
    <div class="d-flex align-items-center">
        <span class="spinner-grow spinner-grow-sm text-success me-2" role="status"></span>
        <div style="font-size: 12px;">
            <span class="fw-semibold text-dark d-block">Sistem Terhubung</span>
            <span class="text-muted" style="font-size: 10px;">13 Safety Hub Aktif</span>
        </div>
    </div>
</div>

<script>
    function handleSidebarLaporClick(e) {
        const token = localStorage.getItem('ta_token');
        if (!token) {
            e.preventDefault();
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
