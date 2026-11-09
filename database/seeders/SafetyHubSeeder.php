<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder; use App\Models\SafetyHub;
class SafetyHubSeeder extends Seeder {
    public function run(): void {
        $hubs = [
            // Rawamangun (pusat -6.1928, 106.89763) — DUMMY, koordinat realistis di sekitar pusat
            ['rawamangun','Pos Polisi Rawamangun','polisi',-6.1920,106.8970,'Jl. Pemuda Raya, Rawamangun',true,'110'],
            ['rawamangun','Pos Satpam Perumahan Rawamangun','satpam',-6.1955,106.9005,'Jl. Rawamangun Muka',true,null],
            ['rawamangun','RS Persahabatan (Faskes 24 Jam)','faskes',-6.1885,106.8885,'Jl. Persahabatan Raya No.1',true,'(021) 4890000'],
            ['rawamangun','Halte TransJakarta Rawamangun','halte',-6.1942,106.8940,'Jl. Pemuda',true,null],
            ['rawamangun','Pos Ronda RW 04 Jati','satpam',-6.1900,106.9020,'Jl. Jati, Rawamangun',true,null],
            // UNESA Lidah Wetan (pusat -7.30071, 112.67289)
            ['unesa-lidah-wetan','Pos Satpam UNESA Lidah Wetan','satpam',-7.3005,112.6725,'Kampus UNESA Lidah Wetan',true,null],
            ['unesa-lidah-wetan','Pos Polisi Lakarsantri','polisi',-7.2980,112.6750,'Jl. Raya Lakarsantri',true,'110'],
            ['unesa-lidah-wetan','Klinik UNESA Lidah Wetan (Faskes 24 Jam)','faskes',-7.3015,112.6740,'Kampus UNESA Lidah Wetan',true,null],
            ['unesa-lidah-wetan','Halte Kampus UNESA Lidah Wetan','halte',-7.2995,112.6710,'Jl. Lidah Wetan',true,null],
            // UNESA Ketintang (pusat -7.31143, 112.72937)
            ['unesa-ketintang','Pos Satpam UNESA Ketintang','satpam',-7.3110,112.7290,'Kampus UNESA Ketintang',true,null],
            ['unesa-ketintang','Pos Polisi Gayungan','polisi',-7.3135,112.7320,'Jl. Gayungan',true,'110'],
            ['unesa-ketintang','Klinik UNESA Ketintang (Faskes 24 Jam)','faskes',-7.3100,112.7280,'Kampus UNESA Ketintang',true,null],
            ['unesa-ketintang','Halte Kampus UNESA Ketintang','halte',-7.3125,112.7305,'Jl. Ketintang',true,null],
        ];
        foreach ($hubs as [$region,$name,$type,$lat,$lng,$addr,$h24,$phone]) {
            SafetyHub::updateOrCreate(['name'=>$name], ['region_slug'=>$region,'type'=>$type,'lat'=>$lat,'lng'=>$lng,'address'=>$addr,'is_24h'=>$h24,'phone'=>$phone]);
        }
    }
}
