<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Report; use App\Models\ReportUpvote; use App\Models\Setting;
use App\Services\AiPhotoCheckService; use App\Services\GeoService;
use Illuminate\Http\Request; use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
class ReportController extends Controller {
    public function index(Request $r){
        $q=Report::active()->orderByDesc('created_at');
        if($r->region_slug) $q->where('region_slug',$r->region_slug);
        if($r->status) $q->where('status',$r->status);
        if($r->category) $q->where('category',$r->category);
        $items=$q->paginate($r->get('per_page',20));
        // Samarkan identitas pelapor di peta publik (PRD Etika): tanpa user_id/nama
        $items->getCollection()->transform(fn($rep)=>$rep->toPublicArray());
        return response()->json(['success'=>true,'data'=>$items->items(),'meta'=>['current_page'=>$items->currentPage(),'total'=>$items->total()]]);
    }
    public function show($id){ $rep=Report::active()->findOrFail($id); return response()->json(['success'=>true,'data'=>$rep->toPublicArray()]); }
    public function store(Request $r, AiPhotoCheckService $ai){
        $d=$r->validate([
            'region_slug'=>'required|string','category'=>'required|in:penerangan_padam,area_sepi,hambatan_fisik',
            'lat'=>'required|numeric|between:-90,90','lng'=>'required|numeric|between:-180,180',
            'user_lat'=>'required|numeric|between:-90,90','user_lng'=>'required|numeric|between:-180,180',
            'description'=>'nullable|string|max:500','photo'=>'nullable|image|max:5120',
        ]);
        $region=config('titikaman.regions')[$d['region_slug']] ?? null;
        if(!$region) return response()->json(['success'=>false,'message'=>'region_slug tidak dikenal.'],422);
        if(!GeoService::inBbox($d['lat'],$d['lng'],$region))
            return response()->json(['success'=>false,'message'=>'Titik laporan di luar bounding box region '.$d['region_slug'].' (hard-locked).','bbox'=>$region],422);
        $sim = Setting::simulationEnabled();
        $dist = GeoService::distanceM($d['user_lat'],$d['user_lng'],$d['lat'],$d['lng']);
        if(!$sim && $dist > config('titikaman.geofence_m',20))
            return response()->json(['success'=>false,'message'=>'Jarak Anda ke titik laporan '.round($dist,1).' m, melebihi geofence 20 m (FR-02.1). Dekati titiknya atau aktifkan Mode Simulasi Lokasi (demo juri).','distance_m'=>round($dist,1),'geofence_m'=>20,'simulation_mode'=>$sim],422);
        // Rate limit: maks 3 laporan/jam per user (PRD NFR) — dihitung di sini agar pesannya jelas
        $recent = Report::where('user_id',$r->user()->id)->where('created_at','>=',now()->subHour())->count();
        if($recent >= config('titikaman.report_rate_limit_per_hour',3))
            return response()->json(['success'=>false,'message'=>'Rate limit: maksimal 3 laporan per jam. Coba lagi nanti.'],429);
        // Duplikat <=20m kategori sama & masih aktif -> arahkan upvote (FR-02.4)
        $dup = Report::active()->where('region_slug',$d['region_slug'])->where('category',$d['category'])->get()
            ->first(fn($rep)=> GeoService::distanceM($d['lat'],$d['lng'],(float)$rep->lat,(float)$rep->lng) <= config('titikaman.duplicate_radius_m',20));
        if($dup) return response()->json(['success'=>false,'message'=>'Laporan duplikat: sudah ada laporan kategori sama dalam radius 20 m. Beri Up-Check pada laporan tersebut.','duplicate_report_id'=>$dup->id,'duplicate_report'=>$dup->toPublicArray()],409);
        $photoPath=null;
        if($r->hasFile('photo')) $photoPath=$r->file('photo')->store('reports','public');
        // AI filter (STUB sampai Rafa pasang Gemini — FR-02.3/FR-02.7)
        $aiResult=$ai->check($photoPath,['category'=>$d['category']]);
        if(isset($aiResult['has_face']) && $aiResult['has_face'] || isset($aiResult['has_plate']) && $aiResult['has_plate'])
            return response()->json(['success'=>false,'message'=>'Foto ditolak AI: menampilkan wajah / plat nomor secara jelas (privasi, FR-02.3). Ambil ulang tanpa wajah/plat.','ai_check'=>$aiResult],422);
        $status = ($aiResult['source'] ?? 'stub')==='stub' ? 'PENDING_MODERATION' : 'UNVERIFIED';
        // Catatan: stub = belum ada cek Gemini sungguhan, jadi status awal PENDING_MODERATION (FR-02.7).
        $rep=Report::create([
            'user_id'=>$r->user()->id,'region_slug'=>$d['region_slug'],'category'=>$d['category'],
            'lat'=>$d['lat'],'lng'=>$d['lng'],'user_lat'=>$d['user_lat'],'user_lng'=>$d['user_lng'],
            'description'=>$d['description']??null,'photo_path'=>$photoPath,'status'=>$status,
            'ai_check'=>$aiResult,'is_simulation'=>$sim,'expires_at'=>now()->addHours(config('titikaman.ttl_hours',6)),
        ]);
        $rep->refresh();
        return response()->json(['success'=>true,'message'=>'Laporan dibuat. Status awal '.$status.'. Butuh >=3 upvote warga atau 1 verifikasi admin untuk jadi VERIFIED.','data'=>$rep->toPublicArray(),'ai_check'=>$aiResult],201);
    }
    public function upvote(Request $r, $id){
        $rep = DB::transaction(function () use ($r, $id) {
            $rep = Report::active()->whereKey($id)->lockForUpdate()->firstOrFail();
            if($rep->status==='VERIFIED') abort(409, 'Laporan sudah VERIFIED.');

            $exists=ReportUpvote::where('report_id',$rep->id)->where('user_id',$r->user()->id)->exists();
            if($exists) abort(409, 'Anda sudah memberi Up-Check pada laporan ini.');

            ReportUpvote::create(['report_id'=>$rep->id,'user_id'=>$r->user()->id]);
            $rep->increment('upvotes_count');
            $rep->refresh();
            if($rep->upvotes_count >= config('titikaman.verify_upvotes',3)){
                $rep->update(['status'=>'VERIFIED','verified_at'=>now()]);
            }
            return $rep->fresh();
        });
        return response()->json(['success'=>true,'message'=>'Up-Check tercatat.','data'=>$rep->toPublicArray()]);
    }
    public function aiCheck(Request $r, $id, AiPhotoCheckService $ai){
        // Endpoint kontrak untuk Rafa: POST /api/v1/reports/{id}/ai-check (login wajib)
        // Saat ini memanggil STUB. Rafa mengganti StubAiPhotoCheckService dengan implementasi Gemini.
        $rep=Report::findOrFail($id);
        if ($r->user()->role !== 'admin' && $rep->user_id !== $r->user()->id) {
            return response()->json(['success'=>false,'message'=>'AI-check hanya boleh dilakukan oleh pemilik laporan atau admin.'],403);
        }
        $result=$ai->check($rep->photo_path,['category'=>$rep->category,'report_id'=>$rep->id]);
        $rep->update(['ai_check'=>$result]);
        return response()->json(['success'=>true,'message'=>'AI check (STUB) selesai. Implementasi Gemini asli oleh Rafa — lihat API.md Kontrak Gemini.','data'=>['report_id'=>$rep->id,'ai_check'=>$result,'report_status'=>$rep->status]]);
    }
}
