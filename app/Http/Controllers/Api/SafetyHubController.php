<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\SafetyHub;
use Illuminate\Http\Request;
class SafetyHubController extends Controller {
    public function index(Request $r){
        $q=SafetyHub::query();
        if($r->region_slug) $q->where('region_slug',$r->region_slug);
        if($r->type) $q->where('type',$r->type);
        return response()->json(['success'=>true,'data'=>$q->orderBy('name')->get()]);
    }
}
