@php
    $links = [
        ['route' => 'app', 'label' => 'Peta', 'icon' => 'bi-map', 'url' => route('app')],
        ['route' => 'rute', 'label' => 'Rute', 'icon' => 'bi-signpost-split', 'url' => route('rute')],
        ['route' => 'lapor', 'label' => 'Lapor', 'icon' => 'bi-exclamation-triangle', 'url' => route('lapor')],
        ['route' => 'admin', 'label' => 'Admin', 'icon' => 'bi-shield-lock', 'url' => route('admin')],
    ];
@endphp

<nav class="d-md-none position-fixed bottom-0 start-0 end-0 bg-white border-top shadow-lg d-flex justify-content-around px-1 py-2" style="z-index: 1030;" aria-label="Navigasi mobile">
    @foreach($links as $link)
        <a href="{{ $link['url'] }}" data-admin-nav="{{ $link['route'] === 'admin' ? 'true' : 'false' }}" class="flex-fill text-center text-decoration-none small {{ $link['route'] === 'admin' ? 'd-none' : '' }} {{ request()->routeIs($link['route']) ? 'text-success fw-bold' : 'text-secondary' }}">
            <i class="bi {{ $link['icon'] }} d-block fs-5"></i>
            <span>{{ $link['label'] }}</span>
        </a>
    @endforeach
</nav>
