<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
/**
 * SafePass Routing (FR-03). Kandidat rute dari OSRM publik (tanpa key).
 * Profil: mobil => driving, motor => cycling, jalan_kaki => foot.
 * Jika OSRM tidak terjangkau/timeout => fallback STUB (ditandai source='stub' di response).
 * Skor: duration + penalti laporan VERIFIED <=50m - bonus Safety Hub <=100m.
 * Penalti: penerangan_padam/area_sepi 300 detik, hambatan_fisik 180 detik.
 * Bonus hub: 60 detik per hub, maks 300 detik.
 * Kalau tidak ada laporan terverifikasi, SafePass = Fastest (FR sesuai jawaban Dimas 2026-11-09).
 */
class RoutingService {
    public function compare(float $oLat, float $oLng, float $dLat, float $dLng, string $mode, string $regionSlug): array {
        $profile = $this->profileForMode($mode);
        $routes = [];
        $source = 'osrm';

        // The public OSRM demo server only exposes driving. Use ORS for
        // walking/cycling when configured, otherwise try a compatible OSRM
        // profile before falling back to driving geometry.
        if ($profile !== 'driving' && filled(env('ORS_API_KEY'))) {
            $routes = $this->openRouteServiceRoutes($oLat, $oLng, $dLat, $dLng, $this->openRouteServiceProfile($mode));
            if (!empty($routes)) $source = 'openrouteservice';
        }
        if (empty($routes)) {
            $routes = $this->osrmRoutes($oLat, $oLng, $dLat, $dLng, $profile);
            $source = 'osrm';
        }
        if (empty($routes) && $profile !== 'driving') {
            $routes = $this->osrmRoutes($oLat, $oLng, $dLat, $dLng, 'driving');
            $source = 'osrm-driving-fallback';
        }
        if (empty($routes)) {
            $routes = $this->stubRoutes($oLat, $oLng, $dLat, $dLng, $mode);
            $source = 'stub';
        }

        // Provider durations are profile-dependent and may be driving
        // durations on fallback geometry, so normalize them by mode speed.
        $routes = array_map(function (array $route) use ($mode): array {
            $route['duration'] = $this->estimateDuration((float) $route['distance'], $mode);
            return $route;
        }, $routes);

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
            'region'=>$regionSlug, 'mode'=>$mode, 'route_profile'=>$profile, 'source'=>$source,
            'disclaimer'=>config('titikaman.disclaimer'),
            'fastest'=>$fastest, 'safepass'=>$safepass,
            'note'=> $this->routeNote($source, $mode, $profile),
        ];
    }

    private function profileForMode(string $mode): string {
        return match ($mode) {
            'jalan_kaki' => 'foot',
            'motor' => 'cycling',
            default => 'driving',
        };
    }

    private function openRouteServiceProfile(string $mode): string {
        return match ($mode) {
            'jalan_kaki' => 'foot-walking',
            'motor' => 'cycling-regular',
            default => 'driving-car',
        };
    }

    private function estimateDuration(float $distance, string $mode): int {
        $metersPerSecond = match ($mode) {
            'jalan_kaki' => 1.33,
            'motor' => 8.33,
            default => 6.11,
        };

        return (int) max(60, round($distance / $metersPerSecond));
    }

    private function routeNote(string $source, string $mode, string $profile): string {
        if ($source === 'stub') {
            return 'STUB: provider routing tidak terjangkau; rute dibuat dari garis lurus + offset (bukan jalan asli).';
        }
        if ($source === 'osrm-driving-fallback') {
            return "Profile {$profile} untuk mode {$mode} tidak tersedia di provider routing. Geometri OSRM driving digunakan sebagai fallback, tetapi duration dihitung ulang berdasarkan kecepatan mode.";
        }
        if ($source === 'openrouteservice') {
            return "Rute {$mode} menggunakan profile OpenRouteService {$this->openRouteServiceProfile($mode)}; duration dinormalisasi berdasarkan kecepatan rata-rata mode.";
        }

        return "Rute menggunakan profile OSRM {$profile}; duration dinormalisasi berdasarkan kecepatan rata-rata mode.";
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

    private function openRouteServiceRoutes(float $oLat, float $oLng, float $dLat, float $dLng, string $profile): array {
        try {
            $key = (string) env('ORS_API_KEY');
            $url = "https://api.openrouteservice.org/v2/directions/{$profile}/geojson";
            $res = Http::withHeaders([
                'Authorization' => $key,
                'Accept' => 'application/geo+json, application/json',
            ])->timeout(8)->post($url, [
                'coordinates' => [[$oLng, $oLat], [$dLng, $dLat]],
                'instructions' => false,
            ]);
            if (!$res->successful()) return [];

            $features = $res->json('features', []);
            $out = [];
            foreach ($features as $feature) {
                $summary = $feature['properties']['summary'] ?? [];
                $out[] = [
                    'duration' => (int) round($summary['duration'] ?? 0),
                    'distance' => (int) round($summary['distance'] ?? 0),
                    'geometry' => $feature['geometry'] ?? ['type' => 'LineString', 'coordinates' => []],
                ];
            }
            return $out;
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function stubRoutes(float $oLat, float $oLng, float $dLat, float $dLng, string $mode): array {
        // Dua polyline masuk akal: lurus, dan alternatif dengan offset 0.004 derajat.
        $mk = function($offLat, $offLng) use ($oLat,$oLng,$dLat,$dLng) {
            $pts=[]; for ($i=0;$i<=10;$i++){ $t=$i/10; $pts[]=[round($oLng+($dLng-$oLng)*$t + $offLng*sin($t*M_PI),6), round($oLat+($dLat-$oLat)*$t + $offLat*sin($t*M_PI),6)]; }
            return $pts;
        };
        $dist = (int) GeoService::distanceM($oLat,$oLng,$dLat,$dLng);
        $dur = $this->estimateDuration($dist, $mode);
        return [
            ['duration'=>$dur, 'distance'=>$dist, 'geometry'=>['type'=>'LineString','coordinates'=>$mk(0,0)]],
            ['duration'=>(int)($dur*1.25), 'distance'=>(int)($dist*1.15), 'geometry'=>['type'=>'LineString','coordinates'=>$mk(0.004,0.004)]],
        ];
    }
}
