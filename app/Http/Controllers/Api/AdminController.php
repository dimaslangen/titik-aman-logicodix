<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Report; use App\Models\SosAlert; use App\Models\User;
use Illuminate\Http\Request;
class AdminController extends Controller {
    public function dashboard(Request $r){
        $region=$r->region_slug;
        $q=Report::query(); if($region) $q->where('region_slug',$region);
        $pendingAllRegions=Report::query()->whereRaw('UPPER(status) IN (?, ?)', ['PENDING_MODERATION','PENDING'])->count();
        // Heatmap = kerapatan laporan per grid ~0.005 derajat (FR-04.2)
        $heat=Report::active()->when($region,fn($x)=>$x->where('region_slug',$region))->get()
            ->groupBy(fn($rep)=> round((float)$rep->lat,3).','.round((float)$rep->lng,3))
            ->map(fn($g,$k)=>['cell'=>$k,'count'=>$g->count(),'verified'=>$g->where('status','VERIFIED')->count()])->values();
        return response()->json(['success'=>true,'data'=>[
            'totals'=>['reports'=>$q->count(),'verified'=>(clone $q)->where('status','VERIFIED')->count(),'unverified'=>(clone $q)->where('status','UNVERIFIED')->count(),'pending_moderation'=>(clone $q)->whereRaw('UPPER(status) IN (?, ?)', ['PENDING_MODERATION','PENDING'])->count(),'pending_all_regions'=>$pendingAllRegions,'sos_open'=>SosAlert::where('status','open')->when($region,fn($x)=>$x->where('region_slug',$region))->count()],
            'heatmap'=>$heat,
            'by_category'=>(clone $q)->selectRaw('category, COUNT(*) c')->groupBy('category')->pluck('c','category'),
            'by_region'=>Report::selectRaw('region_slug, COUNT(*) c')->groupBy('region_slug')->pluck('c','region_slug'),
            'recent_reports'=>Report::orderByDesc('created_at')->limit(10)->get()->map(fn($x)=>$x->toPublicArray()),
            'sos_alerts'=>SosAlert::orderByDesc('created_at')->limit(10)->get(),
        ]]);
    }
    public function verify($id){ $rep=Report::findOrFail($id); $rep->update(['status'=>'VERIFIED','verified_at'=>now()]); return response()->json(['success'=>true,'message'=>'Official Verification diberikan.','data'=>$rep->toPublicArray()]); }
    public function resolve($id){ $rep=Report::findOrFail($id); $rep->update(['status'=>'RESOLVED']); return response()->json(['success'=>true,'message'=>'Laporan ditandai RESOLVED (selesai ditangani).','data'=>$rep->toPublicArray()]); }
    public function hide($id){ $rep=Report::findOrFail($id); $rep->update(['status'=>'HIDDEN']); return response()->json(['success'=>true,'message'=>'Laporan disembunyikan (spam/hoax).','data'=>$rep->toPublicArray()]); }
    public function sosList(){ return response()->json(['success'=>true,'data'=>SosAlert::orderByDesc('created_at')->paginate(20)]); }
    public function sosResolve($id){ $a=SosAlert::findOrFail($id); $a->update(['status'=>'resolved']); return response()->json(['success'=>true,'data'=>$a]); }
    public function blockUser($id){ $u=User::findOrFail($id); if($u->role==='admin') return response()->json(['success'=>false,'message'=>'Tidak bisa memblokir admin lain.'],422); $u->update(['is_blocked'=>true]); return response()->json(['success'=>true,'message'=>'Akun diblokir.','data'=>['id'=>$u->id,'email'=>$u->email,'is_blocked'=>true]]); }
    public function unblockUser($id){ $u=User::findOrFail($id); $u->update(['is_blocked'=>false]); return response()->json(['success'=>true,'data'=>['id'=>$u->id,'is_blocked'=>false]]); }
    public function waFollowup(Request $r, $id){ // FR-04.4: pesan tindak lanjut siap kirim ke WA RT/Kelurahan/Satpam
        $rep=Report::findOrFail($id);
        $label=\App\Models\Report::labels()[$rep->category] ?? $rep->category;
        $msg="Halo, dari aplikasi TitikAman. Ada laporan kondisi jalan: $label di {$rep->region_slug} (https://www.google.com/maps?q={$rep->lat},{$rep->lng}). Status: {$rep->status}, upvote: {$rep->upvotes_count}. Mohon tindak lanjut. Terima kasih.";
        return response()->json(['success'=>true,'data'=>['report_id'=>$rep->id,'wa_text'=>$msg,'wa_url'=>'https://wa.me/?text='.urlencode($msg)]]);
    }
}
