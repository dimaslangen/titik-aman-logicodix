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
        <span class="bg-success text-white p-2 rounded-3 shadow-sm"><i class="bi bi-shield-check fs-4"></i></span>
        <span>
            <strong class="d-block text-dark fs-5">TitikAman</strong>
            <small class="text-muted fw-semibold">CIVIC SAFETY GRID</small>
        </span>
    </a>
    <p class="text-uppercase text-muted fw-bold small mb-2">Navigasi</p>
    <div class="d-grid gap-2">
        @foreach($links as $link)
            <a href="{{ $link['url'] }}" data-admin-nav="{{ $link['route'] === 'admin' ? 'true' : 'false' }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none {{ $link['route'] === 'admin' ? 'd-none' : '' }} {{ request()->routeIs($link['route']) ? 'bg-success-subtle text-success fw-semibold' : 'text-secondary' }}">
                <i class="bi {{ $link['icon'] }}"></i>{{ $link['label'] }}
            </a>
        @endforeach
    </div>
    <div class="mt-auto pt-4 border-top">
        <div data-ta-auth class="d-flex justify-content-start"></div>
        <small class="text-muted d-block mt-3">Sistem terhubung · Safety Hub aktif</small>
    </div>
</nav>
