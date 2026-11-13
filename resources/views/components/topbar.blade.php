@props([
    'title' => 'TitikAman',
    'subtitle' => 'Civic Safety Grid',
    'backUrl' => null,
])

<div class="ta-topbar d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm flex-wrap gap-2">
    <div class="ta-topbar-heading d-flex align-items-center min-width-0">
        <button type="button" class="ta-sidebar-toggle btn btn-light d-lg-none rounded-circle ta-circle ta-circle-40 me-2" onclick="window.taToggleSidebar()" aria-label="Buka menu navigasi">
            <i class="bi bi-list fs-5"></i>
        </button>
        @if($backUrl)
            <a href="{{ $backUrl }}" class="btn btn-light btn-sm rounded-circle ta-circle ta-circle-38 me-2 text-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
        @endif

        <div>
            <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">{{ $subtitle }}</small>
            <h4 class="fw-bold mb-0 text-dark">{{ $title }}</h4>
        </div>
    </div>

    <div class="ta-topbar-actions d-flex align-items-center gap-2 ms-auto flex-wrap justify-content-end">
        {{ $slot ?? '' }}
    </div>
</div>
