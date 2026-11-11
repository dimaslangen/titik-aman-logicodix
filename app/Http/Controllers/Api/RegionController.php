<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Region;
class RegionController extends Controller {
    public function index(){ return response()->json(['success'=>true,'data'=>Region::all()->map(fn($r)=>['slug'=>$r->slug,'name'=>$r->name,'group'=>$r->group,'center'=>['lat'=>(float)$r->center_lat,'lng'=>(float)$r->center_lng],'bbox'=>['min_lat'=>(float)$r->min_lat,'max_lat'=>(float)$r->max_lat,'min_lng'=>(float)$r->min_lng,'max_lng'=>(float)$r->max_lng]]) ]); }
    public function show($slug){ $r=Region::where('slug',$slug)->firstOrFail(); return response()->json(['success'=>true,'data'=>$r]); }
}
