@extends('layouts.app')

@section('title', 'TitikAman - Direktori Safety Hub')

@push('styles')
<style>
    .hub-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .hub-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.06);
        border-color: #0d9488;
    }
</style>
@endpush

@section('content')
    <!-- Topbar Header -->
    <x-topbar title="Direktori Safety Hub" subtitle="Civic Safety Grid" back-url="/app">
        <select class="form-select form-select-sm bg-light border-0 fw-semibold rounded-pill px-3" id="filterRegion" onchange="loadSafetyHubs()">
            <option value="">Semua Wilayah</option>
            <option value="unesa-lidah-wetan">UNESA Lidah Wetan</option>
            <option value="unesa-ketintang">UNESA Ketintang</option>
            <option value="rawamangun">Rawamangun</option>
        </select>
        <select class="form-select form-select-sm bg-light border-0 fw-semibold rounded-pill px-3" id="filterType" onchange="loadSafetyHubs()">
            <option value="">Semua Tipe Hub</option>
            <option value="polisi">Pos Polisi</option>
            <option value="satpam">Pos Satpam</option>
            <option value="faskes">Fasilitas Kesehatan</option>
            <option value="halte">Halte Aktif</option>
        </select>
    </x-topbar>

    <!-- Info Banner -->
    <div class="alert border-0 rounded-4 shadow-sm p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: #e8f5ed; color: #11653A;" role="status">
        <div class="d-flex align-items-center">
            <div class="text-white p-2 rounded-circle ta-circle ta-circle-40 me-3" style="background: #11653A;">
                <i class="bi bi-shield-shaded fs-5"></i>
            </div>
            <div>
                <strong class="d-block" style="color: #11653A;">Safety Hub adalah zona aman terverifikasi</strong>
                <small class="text-secondary">Rute SafePass memprioritaskan jalur yang melewati titik-titik ini (radius 100 m).</small>
            </div>
        </div>
        <a href="/rute-saya" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
            Cari Rute Lewat Hub <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <!-- Container Grid Hub -->
    <div class="row g-3" id="hubsContainer">
        <div class="col-12 text-center py-5 text-muted small">
            <span class="spinner-border spinner-border-sm me-1" role="status"></span> Memuat daftar Safety Hub...
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const apiBase = window.taApiBase || '/api/v1';

    function getHubBadge(type) {
        switch(type) {
            case 'polisi': return '<span class="badge bg-primary rounded-pill px-3 py-1"><i class="bi bi-shield-fill me-1"></i>Polisi</span>';
            case 'satpam': return '<span class="badge bg-success rounded-pill px-3 py-1"><i class="bi bi-person-badge-fill me-1"></i>Satpam</span>';
            case 'faskes': return '<span class="badge bg-danger rounded-pill px-3 py-1"><i class="bi bi-hospital-fill me-1"></i>Faskes 24 Jam</span>';
            case 'halte': return '<span class="badge bg-warning text-dark rounded-pill px-3 py-1"><i class="bi bi-bus-front-fill me-1"></i>Halte</span>';
            default: return '<span class="badge bg-secondary rounded-pill px-3 py-1">Hub</span>';
        }
    }

    async function loadSafetyHubs() {
        const container = document.getElementById('hubsContainer');
        const reg = document.getElementById('filterRegion')?.value || '';
        const type = document.getElementById('filterType')?.value || '';

        if (!container) return;
        container.innerHTML = '<div class="col-12 text-center py-5 text-muted small"><span class="spinner-border spinner-border-sm me-1" role="status"></span> Memuat data...</div>';

        let url = `${apiBase}/safety-hubs?`;
        if (reg) url += `region_slug=${encodeURIComponent(reg)}&`;
        if (type) url += `type=${encodeURIComponent(type)}&`;

        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('Gagal memuat data Safety Hub.');
            const payload = await res.json();
            const list = Array.isArray(payload.data) ? payload.data : [];

            if (list.length === 0) {
                container.innerHTML = '<div class="col-12 text-center py-5 text-muted">Tidak ada Safety Hub yang sesuai dengan filter.</div>';
                return;
            }

            let html = '';
            list.forEach(hub => {
                html += `
                    <div class="col-md-6 col-lg-4">
                        <div class="hub-card p-3 shadow-sm h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    ${getHubBadge(hub.type)}
                                    ${hub.is_24h ? '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">24 Jam</span>' : ''}
                                </div>
                                <h6 class="fw-bold text-dark mb-1">${escapeHtml(hub.name)}</h6>
                                <small class="text-muted d-block mb-3">${escapeHtml(hub.address || 'Alamat tidak tersedia.')}</small>
                            </div>
                            <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                                <small class="text-secondary fw-semibold">
                                    <i class="bi bi-geo-alt me-1"></i>${escapeHtml(hub.region_slug)}
                                </small>
                                ${hub.phone ? `
                                    <a href="tel:${escapeHtml(hub.phone)}" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1" style="font-size: 11px;">
                                        <i class="bi bi-telephone-fill me-1"></i>${escapeHtml(hub.phone)}
                                    </a>
                                ` : '<span class="text-muted small" style="font-size: 11px;">Tidak ada kontak</span>'}
                            </div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        } catch (err) {
            container.innerHTML = `
                <div class="col-12 text-center py-5 text-danger small" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i>${escapeHtml(err.message)}
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill ms-2 px-3" onclick="loadSafetyHubs()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Muat Ulang
                    </button>
                </div>`;
        }
    }

    document.addEventListener('DOMContentLoaded', loadSafetyHubs);
</script>
@endpush
