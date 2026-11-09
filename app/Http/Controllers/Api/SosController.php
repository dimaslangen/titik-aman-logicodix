<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\SosAlert; use App\Services\GeoService;
use Illuminate\Http\Request;
class SosController extends Controller {
    // POST /api/v1/sos — frontend memakai tel:110 untuk panggilan; backend hanya mencatat alert untuk admin (FR-01.6/FR-04.4)
    public function store(Request $r){
        $d=$r->validate(['lat'=>'nullable|numeric','lng'=>'nullable|numeric','region_slug'=>'nullable|string','message'=>'nullable|string|max:300']);
        if (($d['lat'] ?? null) === null xor ($d['lng'] ?? null) === null) {
            return response()->json(['success'=>false,'message'=>'lat dan lng harus dikirim berpasangan.'],422);
        }
        $region=$d['region_slug'] ?? ($d['lat'] ?? null ? GeoService::regionForPoint($d['lat'],$d['lng']) : null);
        if (($d['region_slug'] ?? null) !== null && !array_key_exists($d['region_slug'], config('titikaman.regions', []))) {
            return response()->json(['success'=>false,'message'=>'region_slug SOS tidak dikenal.'],422);
        }
        if (($d['lat'] ?? null) !== null && (!$region || GeoService::regionForPoint($d['lat'],$d['lng']) !== $region)) {
            return response()->json(['success'=>false,'message'=>'Koordinat SOS berada di luar region yang dipilih atau region tidak valid.'],422);
        }
        $a=SosAlert::create(['user_id'=>$r->user()?->id,'region_slug'=>$region,'lat'=>$d['lat']??null,'lng'=>$d['lng']??null,'message'=>$d['message']??'SOS dari aplikasi TitikAman','status'=>'open']);
        return response()->json(['success'=>true,'message'=>'SOS tercatat. Untuk darurat segera hubungi 110 (tel:110). Alert ini diteruskan ke dashboard admin.','data'=>$a],201);
    }
}
