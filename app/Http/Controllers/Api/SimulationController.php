<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Setting; use Illuminate\Http\Request;
class SimulationController extends Controller {
    public function status(){ return response()->json(['success'=>true,'data'=>['simulation_mode'=>Setting::simulationEnabled(),'note'=>'Mode Simulasi Lokasi Uji Juri (FR-02.1): kalau aktif, geofence 20 m dilewati. Demo/juri only.']]); }
    public function toggle(Request $r){ // admin only
        $d=$r->validate(['enabled'=>'required|boolean']);
        Setting::updateOrCreate(['key'=>'simulation_mode'],['value'=>$d['enabled']?'1':'0']);
        return response()->json(['success'=>true,'data'=>['simulation_mode'=>$d['enabled']]]);
    }
}
