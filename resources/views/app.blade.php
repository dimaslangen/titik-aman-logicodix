@extends('layouts.app')

@section('title', 'TitikAman - Safe Navigation App')

@push('styles')
<style>
    .map-container {
        height: 520px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
    }
</style>
@endpush

@section('content')
    <!-- Topbar Header -->
    <x-topbar title="Peta TitikAman" subtitle="Navigasi Komunitas">
        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill">v1.0</span>
    </x-topbar>

    <!-- Filter / Quick Bar di atas Peta -->
    <div class="bg-white p-3 rounded-4 shadow-sm mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2 overflow-hidden">
        <x-region-switcher />
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                <i class="bi bi-geo-alt-fill me-1"></i> GPS Aktif
            </span>
        </div>
    </div>

    <!-- Area Peta Interaktif (Leaflet JS) -->
    <div class="map-container mb-3 position-relative">
        <div id="map" style="width: 100%; height: 100%;"></div>
    </div>

    <!-- Banner Bawah (Mau ke mana malam ini?) -->
    <div class="bg-teal text-white p-4 rounded-4 shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4" style="background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);">
        <div>
            <span class="badge bg-white text-teal mb-1 fw-bold" style="color: #0d9488;">NAVIGASI MALAM BERBASIS KOMUNITAS</span>
            <h5 class="fw-bold mb-0 text-white">Mau ke mana malam ini?</h5>
            <small class="text-white-50">Cari jalur teraman dengan rute menghindari area sepi dan lampu padam.</small>
        </div>
        <a href="/rute-saya" class="btn btn-light text-teal fw-bold px-4 rounded-pill shadow-sm text-decoration-none" style="color: #0d9488;">
            Cari Rute Aman <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
@endsection

@push('scripts')
<script>
    const appApi = window.taApiBase || '/api/v1';
    let appRegions = {};
    let activeRegion = 'unesa-lidah-wetan';
    let mapLayers = L.layerGroup();
    const map = L.map('map').setView([0, 0], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    mapLayers.addTo(map);

    function regionCenter(region) {
        const center = region?.center || [region?.center_lat, region?.center_lng];
        return Array.isArray(center) ? center : [Number(center?.lat), Number(center?.lng)];
    }

    function reportColor(category) {
        return category === 'hambatan_fisik' ? '#f59e0b' : '#dc2626';
    }

    async function loadAppRegions() {
        const response = await fetch(`${appApi}/regions`, {headers: {Accept: 'application/json'}});
        if (!response.ok) throw new Error('Daftar region tidak dapat dimuat.');
        const payload = await response.json();
        const regions = Array.isArray(payload.data) ? payload.data : payload.data?.regions;
        if (!Array.isArray(regions)) throw new Error('Format data region tidak valid.');
        appRegions = Object.fromEntries(regions.map(region => [region.slug, region]));
    }

    async function loadAppMap(regionSlug) {
        const region = appRegions[regionSlug];
        if (!region) throw new Error('Region tidak tersedia.');
        activeRegion = regionSlug;
        const center = regionCenter(region);
        map.setView(center, 15);
        mapLayers.clearLayers();

        const [reportsResponse, hubsResponse] = await Promise.all([
            fetch(`${appApi}/reports?region_slug=${encodeURIComponent(regionSlug)}&per_page=100`, {headers: {Accept: 'application/json'}}),
            fetch(`${appApi}/safety-hubs?region_slug=${encodeURIComponent(regionSlug)}`, {headers: {Accept: 'application/json'}})
        ]);

        if (!reportsResponse.ok || !hubsResponse.ok) throw new Error('Data laporan atau safety hub gagal dimuat.');
        const reportsPayload = await reportsResponse.json();
        const hubsPayload = await hubsResponse.json();
        const reports = Array.isArray(reportsPayload.data) ? reportsPayload.data : [];
        const hubs = Array.isArray(hubsPayload.data) ? hubsPayload.data : [];

        reports.forEach(report => {
            const isVerified = report.status === 'VERIFIED';
            const badgeClass = isVerified ? 'bg-success' : (report.status === 'PENDING_MODERATION' ? 'bg-warning text-dark' : 'bg-secondary');
            const upvotes = Number(report.upvotes_count || 0);

            const popupContent = `
                <div style="min-width: 210px;" class="py-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-dark">${escapeHtml(report.label || report.category)}</strong>
                    </div>
                    <span class="badge ${badgeClass} mb-2">${escapeHtml(report.status)}</span>
                    <p class="small text-muted mb-2">${escapeHtml(report.description || 'Tidak ada keterangan tambahan.')}</p>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="small fw-semibold text-secondary">
                            <i class="bi bi-hand-thumbs-up me-1"></i> ${upvotes} Up-Check
                        </span>
                        ${!isVerified ? `
                            <button class="btn btn-sm btn-success py-1 px-2 rounded-pill fw-semibold shadow-sm" style="font-size: 11px;" onclick="upvoteReport(${report.id})">
                                <i class="bi bi-hand-thumbs-up-fill me-1"></i> Up-Check
                            </button>
                        ` : `
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Terverifikasi</span>
                        `}
                    </div>
                </div>
            `;

            L.circleMarker([Number(report.lat), Number(report.lng)], {
                radius: 8, color: '#fff', weight: 2, fillColor: reportColor(report.category), fillOpacity: .95
            }).bindPopup(popupContent).addTo(mapLayers);
        });

        hubs.forEach(hub => {
            L.circleMarker([Number(hub.lat), Number(hub.lng)], {
                radius: 7, color: '#fff', weight: 2, fillColor: '#2563eb', fillOpacity: .95
            }).bindPopup(`<strong>${escapeHtml(hub.name)}</strong><br>Safety Hub · ${escapeHtml(hub.type)}`).addTo(mapLayers);
        });
    }

    async function upvoteReport(reportId) {
        const token = localStorage.getItem('ta_token');
        if (!token) {
            if (typeof window.taPromptGuest === 'function') {
                window.taPromptGuest(
                    'Masuk untuk Memberikan Up-Check',
                    'Anda perlu masuk atau mendaftar terlebih dahulu untuk mengonfirmasi (Up-Check) laporan kondisi jalan dari sesama warga.'
                );
            } else {
                window.location.href = '/masuk';
            }
            return;
        }

        try {
            const response = await fetch(`${appApi}/reports/${reportId}/upvote`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            const data = await response.json().catch(() => ({}));

            if (response.status === 401) {
                window.taShowAlert('Sesi login telah berakhir. Silakan masuk kembali.', 'danger');
                localStorage.removeItem('ta_token');
                window.location.href = '/masuk';
                return;
            }

            if (!response.ok) {
                throw new Error(data.message || 'Gagal memberikan Up-Check.');
            }

            window.taShowAlert(data.message || 'Up-Check berhasil dicatat!', 'success');
            loadAppMap(activeRegion).catch(console.error);
        } catch (err) {
            window.taShowAlert(err.message, 'danger');
        }
    }

    function selectAppRegion(button) {
        document.querySelectorAll('[data-region]').forEach(item => {
            item.className = item === button
                ? 'btn btn-sm btn-outline-success active rounded-pill px-3 fw-semibold'
                : 'btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold';
        });
        loadAppMap(button.dataset.region).catch(error => window.taShowAlert(error.message, 'danger'));
    }

    document.querySelectorAll('[data-region]').forEach(button => button.addEventListener('click', () => selectAppRegion(button)));

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            map.invalidateSize();
        }, 300);

        loadAppRegions()
            .then(() => loadAppMap(activeRegion))
            .catch(error => window.taShowAlert(`Gagal memuat peta: ${error.message}`, 'danger'));
    });
</script>
@endpush
