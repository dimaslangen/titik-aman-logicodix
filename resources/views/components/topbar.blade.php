@props([
    'title' => 'TitikAman',
    'subtitle' => 'Civic Safety Grid',
    'backUrl' => null,
])

<div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm flex-wrap gap-2">
    <div class="d-flex align-items-center">
        @if($backUrl)
            <a href="{{ $backUrl }}" class="btn btn-light btn-sm rounded-circle ta-circle ta-circle-38 me-3 text-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
        @endif

        <div>
            <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">{{ $subtitle }}</small>
            <h4 class="fw-bold mb-0 text-dark">{{ $title }}</h4>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 ms-auto flex-wrap">
        {{ $slot ?? '' }}

        <!-- Wadah Global Status Auth (Masuk / Profil Avatar Dropdown) -->
        <div data-ta-auth class="d-flex align-items-center gap-2">
            <span class="spinner-border spinner-border-sm text-secondary" id="authSpinner" role="status" style="width: 14px; height: 14px;"></span>
        </div>
    </div>
</div>
