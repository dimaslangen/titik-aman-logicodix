<button type="button" class="ta-sos-button btn btn-danger position-fixed shadow-lg d-flex align-items-center gap-2 px-3 py-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#sosEmergencyModal" aria-label="Buka sinyal darurat SOS">
    <i class="bi bi-exclamation-octagon-fill fs-5"></i><span>SOS 110</span>
</button>
@include('components.sos-modal')
<style>
    .ta-sos-button {
        bottom: 20px;
        right: 20px;
        z-index: 1040;
        font-weight: bold;
        animation: taPulseSos 2s infinite;
    }
    @keyframes taPulseSos {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, .7); }
        70% { box-shadow: 0 0 0 14px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
</style>
