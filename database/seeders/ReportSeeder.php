<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder; use App\Models\Report; use App\Models\ReportUpvote; use App\Models\User;
class ReportSeeder extends Seeder {
    public function run(): void {
        $warga = User::where('email','warga@titikaman.id')->first();
        $w2 = User::where('email','warga2@titikaman.id')->first();
        $w3 = User::where('email','warga3@titikaman.id')->first();
        if (!$warga) return;
        $data = [
            // Rawamangun — 1 VERIFIED (sudah 3 upvote), 1 UNVERIFIED
            ['rawamangun','penerangan_padam',-6.1935,106.8985,'Penerangan padam total di ruas ini, gelap banget malam hari.','VERIFIED',3],
            ['rawamangun','hambatan_fisik',-6.1910,106.8960,'Jalan berlubang cukup dalam, motor harus pelan.','UNVERIFIED',1],
            // UNESA Lidah Wetan — 1 VERIFIED
            ['unesa-lidah-wetan','area_sepi',-7.3010,112.6735,'Area sepi di sisi barat kampus malam hari.','VERIFIED',3],
            // UNESA Ketintang — 1 PENDING_MODERATION (stub Gemini)
            ['unesa-ketintang','hambatan_fisik',-7.3118,112.7300,'Genangan setelah hujan, jalan licin.','PENDING_MODERATION',0],
        ];
        foreach ($data as [$region,$cat,$lat,$lng,$desc,$status,$up]) {
            $rep = Report::updateOrCreate(
                ['region_slug'=>$region,'category'=>$cat,'lat'=>$lat,'lng'=>$lng],
                ['user_id'=>$warga->id,'user_lat'=>$lat,'user_lng'=>$lng,'description'=>$desc,'status'=>$status,'upvotes_count'=>$up,'expires_at'=>now()->addHours(4),'created_at'=>now()->subHours(2),'updated_at'=>now()->subHours(2),'verified_at'=>$status==='VERIFIED'?now():null,'ai_check'=>['source'=>'stub','reason'=>'Data dummy seeder.']]
            );
            // Upvote dummy agar count konsisten (warga, warga2, warga3)
            if ($up>=1 && $w2) ReportUpvote::firstOrCreate(['report_id'=>$rep->id,'user_id'=>$w2->id]);
            if ($up>=2 && $w3) ReportUpvote::firstOrCreate(['report_id'=>$rep->id,'user_id'=>$w3->id]);
            if ($up>=3) ReportUpvote::firstOrCreate(['report_id'=>$rep->id,'user_id'=>$warga->id]);
        }
    }
}
