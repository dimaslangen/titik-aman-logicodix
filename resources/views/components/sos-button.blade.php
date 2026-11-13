<button type="button" class="ta-sos-button btn btn-danger position-fixed shadow-lg d-inline-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#sosEmergencyModal" aria-label="Buka sinyal darurat SOS">
    <i class="bi bi-exclamation-octagon-fill fs-5"></i><span class="ta-sos-label">SOS 110</span>
</button>
@include('components.sos-modal')
<style>
    .ta-sos-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1040;
        height: 48px;
        min-width: 48px;
        padding: 0 14px;
        border-radius: 9999px !important;
        background-color: #DC2626;
        color: #FFFFFF;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        overflow: hidden;
        box-shadow: 0 6px 16px rgba(220, 38, 38, 0.28);
        transition: padding 0.4s ease, background-color 0.3s ease;
    }

    .ta-sos-label {
        display: inline-block;
        max-width: 0;
        margin-left: 0;
        overflow: hidden;
        white-space: nowrap;
        transition: max-width 0.4s ease, margin-left 0.4s ease;
    }

    .ta-sos-button:hover,
    .ta-sos-button:focus-visible,
    .ta-sos-button:active {
        padding: 0 18px;
        background-color: #B91C1C;
    }

    .ta-sos-button:hover .ta-sos-label,
    .ta-sos-button:focus-visible .ta-sos-label,
    .ta-sos-button:active .ta-sos-label {
        max-width: 85px;
        margin-left: 8px;
    }
    @keyframes taPulseSos {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, .7); }
        70% { box-shadow: 0 0 0 14px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
</style>
