@php
    $links = [
        ['route' => 'app', 'label' => 'Peta Aman', 'icon' => 'bi-map', 'url' => route('app')],
        ['route' => 'rute', 'label' => 'Rute Saya', 'icon' => 'bi-signpost-split', 'url' => route('rute')],
        ['route' => 'lapor', 'label' => 'Lapor Kondisi', 'icon' => 'bi-exclamation-triangle', 'url' => route('lapor')],
        ['route' => 'admin', 'label' => 'Admin', 'icon' => 'bi-shield-lock', 'url' => route('admin')],
    ];
@endphp

<nav class="d-flex flex-column bg-white border-end min-vh-100 p-4" aria-label="Navigasi utama">
    <a href="{{ route('app') }}" class="d-flex align-items-center gap-2 text-decoration-none mb-5">
        <x-brand-logo theme="light" size="md" />
    </a>
    <p class="text-uppercase text-muted fw-bold small mb-2">Navigasi</p>
    <div class="d-grid gap-2">
        @foreach($links as $link)
            <a href="{{ $link['url'] }}" data-admin-nav="{{ $link['route'] === 'admin' ? 'true' : 'false' }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none {{ $link['route'] === 'admin' ? 'd-none' : '' }} {{ request()->routeIs($link['route']) ? 'bg-success-subtle text-success fw-semibold' : 'text-secondary' }}">
                <i class="bi {{ $link['icon'] }}"></i>{{ $link['label'] }}
            </a>
        @endforeach
    </div>
    <a href="{{ route('tentang') }}" class="d-flex align-items-center gap-3 mt-4 px-3 py-2 rounded-3 text-decoration-none text-secondary small">
        <i class="bi bi-info-circle"></i>Tentang TitikAman
    </a>
    <div class="mt-auto pt-4 border-top">
        <div data-ta-auth class="d-flex justify-content-start"></div>
        <small class="text-muted d-block mt-3">Sistem terhubung · Safety Hub aktif</small>
    </div>
</nav>
