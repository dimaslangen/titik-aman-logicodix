<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; use App\Models\SharedRoute; use Illuminate\Support\Str; use App\Services\GeoService;
class SharedRouteController extends Controller {
    public function store(Request $r){
        $d=$r->validate(['region_slug'=>'required|string','mode'=>'required|in:mobil,motor,jalan_kaki','origin_lat'=>'required|numeric','origin_lng'=>'required|numeric','destination_lat'=>'required|numeric','destination_lng'=>'required|numeric','route_data'=>'nullable|array']);
        $region=config('titikaman.regions')[$d['region_slug']] ?? null;
        if (!$region || !GeoService::inBbox($d['origin_lat'],$d['origin_lng'],$region) || !GeoService::inBbox($d['destination_lat'],$d['destination_lng'],$region)) {
            return response()->json(['success'=>false,'message'=>'Asal dan tujuan harus berada di dalam bounding box region yang dipilih.'],422);
        }
        $s=SharedRoute::create(array_merge($d,['token'=>Str::random(12),'user_id'=>$r->user()?->id,'expires_at'=>now()->addHours(6)]));
        return response()->json(['success'=>true,'data'=>['token'=>$s->token,'share_url'=>url('/api/v1/shared-routes/'.$s->token),'expires_at'=>$s->expires_at]],201);
    }
    public function show($token){ $s=SharedRoute::where('token',$token)->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at','>',now()); })->firstOrFail(); return response()->json(['success'=>true,'data'=>$s]); }
}
