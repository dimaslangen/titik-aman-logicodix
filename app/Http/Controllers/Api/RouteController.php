<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeoService;
use App\Services\RoutingService;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function compare(Request $r, RoutingService $svc)
    {
        $d = $r->validate([
            'origin_lat' => 'required|numeric|between:-90,90', 'origin_lng' => 'required|numeric|between:-180,180',
            'destination_lat' => 'required|numeric|between:-90,90', 'destination_lng' => 'required|numeric|between:-180,180',
            'mode' => 'required|in:car,motorcycle,foot,mobil,motor,jalan_kaki', 'region_slug' => 'required|string',
        ]);
        $d['mode'] = $svc->normalizeMode($d['mode']);
        $region = config('titikaman.regions')[$d['region_slug']] ?? null;
        if (! $region) {
            return response()->json(['success' => false, 'message' => 'region_slug tidak dikenal. Pilihan: '.implode(', ', array_keys(config('titikaman.regions')))], 422);
        }
        if (! GeoService::inBbox($d['origin_lat'], $d['origin_lng'], $region) || ! GeoService::inBbox($d['destination_lat'], $d['destination_lng'], $region)) {
            return response()->json(['success' => false, 'message' => 'Asal / tujuan di luar bounding box region '.$d['region_slug'].'. Region ini hard-locked (PRD Multi-Region Hard-Scope).', 'bbox' => $region], 422);
        }
        $out = $svc->compare($d['origin_lat'], $d['origin_lng'], $d['destination_lat'], $d['destination_lng'], $d['mode'], $d['region_slug']);

        return response()->json(['success' => true, 'data' => $out]);
    }
}
