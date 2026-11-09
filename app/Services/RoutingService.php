<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
/**
 * SafePass Routing (FR-03). Kandidat rute dari OSRM publik (tanpa key).
 * Profil: mobil/motor => driving, jalan_kaki => foot.
 * Jika OSRM tidak terjangkau/timeout => fallback STUB (ditandai source='stub' di response).
 * Skor: duration + penalti laporan VERIFIED <=50m - bonus Safety Hub <=100m.
 * Penalti: penerangan_padam/area_sepi 300 detik, hambatan_fisik 180 detik.
 * Bonus hub: 60 detik per hub, maks 300 detik.
 * Kalau tidak ada laporan terverifikasi, SafePass = Fastest (FR sesuai jawaban Dimas 2026-11-09).
 */
class RoutingService {
    public function compare(float $oLat, float $oLng, float $dLat, float $dLng, string $mode, string $regionSlug): array {
        $profile = $mode === 'jalan_kaki' ? 'foot' : 'driving';
        $routes = $this->osrmRoutes($oLat, $oLng, $dLat, $dLng, $profile);
        $source = 'osrm';
        if (empty($routes)) { $routes = $this->stubRoutes($oLat, $oLng, $dLat, $dLng, $mode); $source = 'stub'; }
        // Data pembobot dari DB (MySQL spatial via GeoService di PHP agar portable)
        $verified = \App\Models\Report::active()->where('status','VERIFIED')->where('region_slug',$regionSlug)->get();
        $hubs = \App\Models\SafetyHub::where('region_slug',$regionSlug)->get();
        $scored = [];
        foreach ($routes as $r) {
            $coords = $r['geometry']['coordinates'] ?? [];
            $avoided = []; $penalty = 0;
            foreach ($verified as $rep) {
                $d = GeoService::minDistanceToPolyline((float)$rep->lat, (float)$rep->lng, $coords);
                if ($d <= config('titikaman.penalty_radius_m', 50)) {
                    $avoided[] = ['report_id'=>$rep->id,'category'=>$rep->category,'distance_m'=>round($d,1)];
                    $penalty += in_array($rep->category, ['penerangan_padam','area_sepi']) ? 300 : 180;
                }
            }
            $hubPassed = []; $bonus = 0;
            foreach ($hubs as $h) {
                $d = GeoService::minDistanceToPolyline((float)$h->lat, (float)$h->lng, $coords);
                if ($d <= config('titikaman.hub_bonus_radius_m', 100)) { $hubPassed[] = ['hub_id'=>$h->id,'name'=>$h->name,'type'=>$h->type,'distance_m'=>round($d,1)]; $bonus += 60; }
            }
            $bonus = min($bonus, 300);
            $scored[] = array_merge($r, [
                'penalty_seconds'=>$penalty, 'bonus_seconds'=>$bonus,
                'score_seconds'=> max(0, $r['duration'] + $penalty - $bonus),
                'avoided_reports'=>$avoided, 'hubs_passed'=>$hubPassed,
            ]);
        }
        usort($scored, fn($a,$b)=> $a['duration'] <=> $b['duration']);
        $fastest = $scored[0];
        usort($scored, fn($a,$b)=> $a['score_seconds'] <=> $b['score_seconds']);
        $safepass = $scored[0];
        $fastest['label']='fastest'; $safepass['label']='safepass';
        return [
            'region'=>$regionSlug, 'mode'=>$mode, 'source'=>$source,
            'disclaimer'=>config('titikaman.disclaimer'),
            'fastest'=>$fastest, 'safepass'=>$safepass,
            'note'=> $source==='stub' ? 'STUB: OSRM tidak terjangkau saat request ini, rute dibuat dari garis lurus + offset (bukan jalan asli). Cek koneksi ke router.project-osrm.org.' : 'Rute kandidat dari OSRM publik (router.project-osrm.org), pembobotan SafePass dihitung di Laravel.',
        ];
    }
    private function osrmRoutes(float $oLat, float $oLng, float $dLat, float $dLng, string $profile): array {
        try {
            $base = rtrim((string) env('OSRM_URL','https://router.project-osrm.org'), '/');
            $url = "$base/route/v1/$profile/$oLng,$oLat;$dLng,$dLat";
            $res = Http::timeout(6)->get($url, ['overview'=>'full','geometries'=>'geojson','alternatives'=>'true','steps'=>'false']);
            if (!$res->successful()) return [];
            $routes = $res->json('routes', []);
            $out = [];
            foreach ($routes as $r) {
                $out[] = ['duration'=>(int) round($r['duration'] ?? 0), 'distance'=>(int) round($r['distance'] ?? 0), 'geometry'=>$r['geometry'] ?? ['type'=>'LineString','coordinates'=>[]]];
            }
            return $out;
        } catch (\Throwable $e) { return []; }
    }
    private function stubRoutes(float $oLat, float $oLng, float $dLat, float $dLng, string $mode): array {
        // Dua polyline masuk akal: lurus, dan alternatif dengan offset 0.004 derajat.
        $mk = function($offLat, $offLng) use ($oLat,$oLng,$dLat,$dLng) {
            $pts=[]; for ($i=0;$i<=10;$i++){ $t=$i/10; $pts[]=[round($oLng+($dLng-$oLng)*$t + $offLng*sin($t*M_PI),6), round($oLat+($dLat-$oLat)*$t + $offLat*sin($t*M_PI),6)]; }
            return $pts;
        };
        $dist = (int) GeoService::distanceM($oLat,$oLng,$dLat,$dLng);
        $speed = $mode==='jalan_kaki' ? 1.4 : ($mode==='motor' ? 11 : 9); // m/detik kasar
        $dur = (int) max(60, $dist/$speed);
        return [
            ['duration'=>$dur, 'distance'=>$dist, 'geometry'=>['type'=>'LineString','coordinates'=>$mk(0,0)]],
            ['duration'=>(int)($dur*1.25), 'distance'=>(int)($dist*1.15), 'geometry'=>['type'=>'LineString','coordinates'=>$mk(0.004,0.004)]],
        ];
    }
}
