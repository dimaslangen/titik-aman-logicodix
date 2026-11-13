<?php

namespace App\Services;

use App\Models\Report;
use App\Models\SafetyHub;
use Illuminate\Support\Facades\Http;

/**
 * SafePass Routing (FR-03). Kandidat rute dari OSRM publik (tanpa key).
 * Profil: car => driving, motorcycle => bike, foot => foot.
 * OSRM diprioritaskan; provider lain dan geometri stub hanya menjadi fallback.
 * SafePass memprioritaskan laporan aktif dalam radius 120 m, lalu Safety Hub dan durasi.
 */
class RoutingService
{
    public function compare(float $oLat, float $oLng, float $dLat, float $dLng, string $mode, string $regionSlug): array
    {
        $mode = $this->normalizeMode($mode);
        $profile = $this->profileForMode($mode);
        $routingProfile = $profile;
        $routes = [];
        $source = 'osrm';

        $routes = $this->osrmRoutes($oLat, $oLng, $dLat, $dLng, $profile);
        $source = 'osrm';
        if (empty($routes) && $profile !== 'driving') {
            $routes = $this->osrmRoutes($oLat, $oLng, $dLat, $dLng, 'driving');
            $routingProfile = 'driving';
            $source = 'osrm-driving-fallback';
        }
        if (empty($routes) && $profile !== 'driving' && filled(env('ORS_API_KEY'))) {
            $routes = $this->openRouteServiceRoutes($oLat, $oLng, $dLat, $dLng, $this->openRouteServiceProfile($mode));
            if (! empty($routes)) {
                $source = 'openrouteservice-fallback';
            }
        }
        if (empty($routes)) {
            $routes = $this->stubRoutes($oLat, $oLng, $dLat, $dLng, $mode);
            $source = 'stub';
        }

        $routes = array_map(function (array $route) use ($mode): array {
            $route['duration'] = $this->estimateDuration((float) $route['distance'], $mode);

            return $route;
        }, $routes);

        $reports = Report::active()->where('region_slug', $regionSlug)->get();
        $hubs = SafetyHub::where('region_slug', $regionSlug)->get();
        $scored = array_map(fn (array $route) => $this->scoreRoute($route, $reports, $hubs, $mode), $routes);
        usort($scored, fn ($a, $b) => $a['duration'] <=> $b['duration']);
        $fastest = $scored[0];
        $fastestDistance = max(1, (float) $fastest['distance']);
        $fastest = $this->addDetourMetrics([$fastest], $fastestDistance)[0];
        $safeCandidates = [];
        foreach ($scored as $candidate) {
            if ($this->geometryKey($candidate) === $this->geometryKey($fastest)
                || $this->isUsableAlternative($candidate, $fastest, $oLat, $oLng, $dLat, $dLng)) {
                $safeCandidates[] = $this->addDetourMetrics([$candidate], $fastestDistance)[0];
            }
        }

        $dangerReports = $reports->where('status', 'VERIFIED');
        foreach ($this->candidateWaypoints($fastest, $dangerReports, $hubs, $oLat, $oLng, $dLat, $dLng, $mode) as $waypoint) {
            $waypointRoutes = $this->osrmRoutesWithWaypoint($oLat, $oLng, $waypoint[0], $waypoint[1], $dLat, $dLng, $routingProfile);
            foreach ($waypointRoutes as $route) {
                $route['duration'] = $this->estimateDuration((float) $route['distance'], $mode);
                $candidate = $this->scoreRoute($route, $reports, $hubs, $mode);
                if ($this->geometryKey($candidate) !== $this->geometryKey($fastest)
                    && $this->isUsableAlternative($candidate, $fastest, $oLat, $oLng, $dLat, $dLng)) {
                    $safeCandidates[] = $this->addDetourMetrics([$candidate], $fastestDistance)[0];
                }
            }
        }

        $safepass = $this->chooseSafeCandidate($safeCandidates, $fastest);
        $fastest['label'] = 'fastest';
        $safepass['label'] = 'safepass';

        return [
            'region' => $regionSlug, 'mode' => $mode, 'route_profile' => $profile, 'source' => $source,
            'disclaimer' => config('titikaman.disclaimer'),
            'fastest' => $fastest, 'safepass' => $safepass,
            'note' => $this->routeNote($source, $mode, $profile),
        ];
    }

    private function scoreRoute(array $route, $reports, $hubs, string $mode): array
    {
        $coords = $route['geometry']['coordinates'] ?? [];
        $riskReports = [];
        $dangerExposure = 0.0;
        $riskRadius = $this->riskRadiusForMode($mode);
        foreach ($reports as $report) {
            $distance = GeoService::minDistanceToPolyline((float) $report->lat, (float) $report->lng, $coords);
            if ($distance <= $riskRadius) {
                $proximity = max(0, 1 - ($distance / $riskRadius));
                $dangerExposure += $proximity * $proximity * 100;
                $riskReports[] = [
                    'report_id' => $report->id,
                    'category' => $report->category,
                    'lat' => (float) $report->lat,
                    'lng' => (float) $report->lng,
                    'distance_m' => round($distance, 1),
                ];
            }
        }

        $hubsPassed = [];
        foreach ($hubs as $hub) {
            $distance = GeoService::minDistanceToPolyline((float) $hub->lat, (float) $hub->lng, $coords);
            if ($distance <= 180) {
                $hubsPassed[] = [
                    'hub_id' => $hub->id,
                    'name' => $hub->name,
                    'type' => $hub->type,
                    'distance_m' => round($distance, 1),
                ];
            }
        }

        $route['danger_count'] = count($riskReports);
        $route['danger_exposure'] = round($dangerExposure, 2);
        $route['risk_count'] = $route['danger_count'];
        $route['hub_count'] = count($hubsPassed);
        $route['risk_reports'] = $riskReports;
        $route['avoided_reports'] = $riskReports;
        $route['hubs_passed'] = $hubsPassed;
        $route['safe_percentage'] = max(0, 100 - (count($riskReports) * 15));
        $route['score_seconds'] = ($route['danger_count'] * 100000)
            + ($route['danger_exposure'] * 100)
            - ($route['hub_count'] * 1000)
            + $route['duration'];

        return $route;
    }

    private function addDetourMetrics(array $routes, float $fastestDistance): array
    {
        return array_map(function (array $route) use ($fastestDistance): array {
            $route['detour_ratio'] = round((float) $route['distance'] / $fastestDistance, 4);
            $route['detour_valid'] = $route['detour_ratio'] <= 1.45;

            return $route;
        }, $routes);
    }

    private function chooseSafeCandidate(array $candidates, array $fastest): array
    {
        $valid = array_values(array_filter($candidates, fn (array $route) => $route['detour_valid']));
        $pool = $valid ?: $candidates;
        $distinct = array_values(array_filter($pool, fn (array $route) => $this->geometryKey($route) !== $this->geometryKey($fastest)));
        if ($distinct) {
            $pool = $distinct;
        }

        $bestDangerCount = min(array_column($pool, 'danger_count'));
        if ($bestDangerCount >= $fastest['danger_count']) {
            usort($pool, fn (array $a, array $b) => [$a['danger_exposure'], -$a['hub_count'], $a['detour_ratio'], $a['duration']]
                <=> [$b['danger_exposure'], -$b['hub_count'], $b['detour_ratio'], $b['duration']]);

            return $pool[0];
        }

        usort($pool, fn (array $a, array $b) => [$a['danger_count'], $a['danger_exposure'], -$a['hub_count'], $a['duration']]
            <=> [$b['danger_count'], $b['danger_exposure'], -$b['hub_count'], $b['duration']]);

        return $pool[0];
    }

    private function candidateWaypoints(array $fastest, $reports, $hubs, float $oLat, float $oLng, float $dLat, float $dLng, string $mode): array
    {
        $coords = $fastest['geometry']['coordinates'] ?? [];
        $waypoints = [];
        $criticalDanger = null;
        $criticalDistance = 150.0;
        foreach ($reports as $report) {
            $distance = GeoService::minDistanceToPolyline((float) $report->lat, (float) $report->lng, $coords);
            if ($distance <= $criticalDistance) {
                $criticalDanger = $report;
                $criticalDistance = $distance;
            }
        }

        $straightLine = [[$oLng, $oLat], [$dLng, $dLat]];
        $straightDistance = max(1, GeoService::distanceM($oLat, $oLng, $dLat, $dLng));
        if ($criticalDanger) {
            $projection = $this->projectPointOnSegment((float) $criticalDanger->lat, (float) $criticalDanger->lng, $oLat, $oLng, $dLat, $dLng);
            if ($projection['t'] >= 0.20 && $projection['t'] <= 0.85) {
                $normal = $this->perpendicularAwayFromDanger(
                    $projection['lat'],
                    $projection['lng'],
                    (float) $criticalDanger->lat,
                    (float) $criticalDanger->lng,
                    $oLat,
                    $oLng,
                    $dLat,
                    $dLng
                );
                foreach ([180, 280, 400, 550] as $offsetMeters) {
                    $waypoints[] = [
                        $projection['lat'] + ($normal['north'] * $offsetMeters / 111000),
                        $projection['lng'] + ($normal['east'] * $offsetMeters / (111000 * cos(deg2rad($projection['lat'])))),
                    ];
                }
            }
        }

        $hubCandidates = [];
        foreach ($hubs as $hub) {
            $hubProjection = $this->projectPointOnSegment((float) $hub->lat, (float) $hub->lng, $oLat, $oLng, $dLat, $dLng);
            if ($hubProjection['t'] < 0.20 || $hubProjection['t'] > 0.85) {
                continue;
            }
            if ($criticalDanger && GeoService::distanceM((float) $hub->lat, (float) $hub->lng, (float) $criticalDanger->lat, (float) $criticalDanger->lng) < 150) {
                continue;
            }
            $corridorDistance = GeoService::minDistanceToPolyline((float) $hub->lat, (float) $hub->lng, $straightLine);
            $viaHubDistance = GeoService::distanceM($oLat, $oLng, (float) $hub->lat, (float) $hub->lng)
                + GeoService::distanceM((float) $hub->lat, (float) $hub->lng, $dLat, $dLng);
            if ($corridorDistance <= ($mode === 'foot' ? 350 : 500) && $viaHubDistance / $straightDistance <= 1.25) {
                $hubCandidates[] = [$corridorDistance, (float) $hub->lat, (float) $hub->lng];
            }
        }
        usort($hubCandidates, fn (array $a, array $b) => $a[0] <=> $b[0]);
        foreach (array_slice($hubCandidates, 0, 2) as $hubCandidate) {
            $waypoints[] = [$hubCandidate[1], $hubCandidate[2]];
        }

        $unique = [];
        foreach ($waypoints as $waypoint) {
            $projection = $this->projectPointOnSegment($waypoint[0], $waypoint[1], $oLat, $oLng, $dLat, $dLng);
            if ($projection['t'] >= 0.20 && $projection['t'] <= 0.85) {
                $unique[round($waypoint[0], 5).':'.round($waypoint[1], 5)] = $waypoint;
            }
        }

        return array_values($unique);
    }

    private function projectPointOnSegment(float $lat, float $lng, float $oLat, float $oLng, float $dLat, float $dLng): array
    {
        $referenceLat = deg2rad(($oLat + $dLat + $lat) / 3);
        $east = ($dLng - $oLng) * cos($referenceLat);
        $north = $dLat - $oLat;
        $pointEast = ($lng - $oLng) * cos($referenceLat);
        $pointNorth = $lat - $oLat;
        $lengthSquared = ($east * $east) + ($north * $north);
        $t = $lengthSquared > 0 ? (($pointEast * $east) + ($pointNorth * $north)) / $lengthSquared : 0;
        $t = max(0, min(1, $t));

        return [
            't' => $t,
            'lat' => $oLat + (($dLat - $oLat) * $t),
            'lng' => $oLng + (($dLng - $oLng) * $t),
        ];
    }

    private function perpendicularAwayFromDanger(float $mLat, float $mLng, float $hLat, float $hLng, float $oLat, float $oLng, float $dLat, float $dLng): array
    {
        $referenceLat = deg2rad(($oLat + $dLat + $mLat + $hLat) / 4);
        $east = ($dLng - $oLng) * cos($referenceLat);
        $north = $dLat - $oLat;
        $length = max(0.000001, hypot($east, $north));
        $normalEast = -$north / $length;
        $normalNorth = $east / $length;
        $dangerEast = ($hLng - $mLng) * cos($referenceLat);
        $dangerNorth = $hLat - $mLat;
        $side = ($dangerEast * $normalEast) + ($dangerNorth * $normalNorth);
        if ($side > 0) {
            $normalEast *= -1;
            $normalNorth *= -1;
        }

        return ['east' => $normalEast, 'north' => $normalNorth];
    }

    private function isUsableAlternative(array $route, array $fastest, float $oLat, float $oLng, float $dLat, float $dLng): bool
    {
        $fastestDistance = max(1, (float) $fastest['distance']);
        if ((float) $route['distance'] > ($fastestDistance * 1.6)) {
            return false;
        }

        $coords = $route['geometry']['coordinates'] ?? [];
        if (count($coords) < 2) {
            return false;
        }

        $maxProgress = 0.0;
        $progresses = [];
        foreach ($coords as $coordinate) {
            if (! isset($coordinate[0], $coordinate[1])) {
                continue;
            }
            $progress = $this->projectPointOnSegment((float) $coordinate[1], (float) $coordinate[0], $oLat, $oLng, $dLat, $dLng)['t'];
            if ($progress < ($maxProgress - 0.22)) {
                return false;
            }
            foreach ($progresses as $previous) {
                if (abs($progress - $previous['progress']) > 0.12
                    && GeoService::distanceM((float) $coordinate[1], (float) $coordinate[0], $previous['lat'], $previous['lng']) < 18) {
                    return false;
                }
            }
            $progresses[] = [
                'progress' => $progress,
                'lat' => (float) $coordinate[1],
                'lng' => (float) $coordinate[0],
            ];
            $maxProgress = max($maxProgress, $progress);
        }

        return true;
    }

    private function geometryKey(array $route): string
    {
        return json_encode($route['geometry']['coordinates'] ?? []);
    }

    private function profileForMode(string $mode): string
    {
        return match ($mode) {
            'foot' => 'foot',
            'motorcycle' => 'bike',
            default => 'driving',
        };
    }

    private function openRouteServiceProfile(string $mode): string
    {
        return match ($mode) {
            'foot' => 'foot-walking',
            'motorcycle' => 'cycling-regular',
            default => 'driving-car',
        };
    }

    private function estimateDuration(float $distance, string $mode): int
    {
        $metersPerSecond = match ($mode) {
            'foot' => 4.8 / 3.6,
            'motorcycle' => 32 / 3.6,
            default => 28 / 3.6,
        };

        return (int) max(60, round($distance / $metersPerSecond));
    }

    public function normalizeMode(string $mode): string
    {
        return match ($mode) {
            'mobil', 'car' => 'car',
            'motor', 'motorcycle' => 'motorcycle',
            'jalan_kaki', 'foot' => 'foot',
            default => 'car',
        };
    }

    private function riskRadiusForMode(string $mode): int
    {
        return $mode === 'foot' ? 150 : 120;
    }

    private function routeNote(string $source, string $mode, string $profile): string
    {
        if ($source === 'stub') {
            return 'STUB: provider routing tidak terjangkau; rute dibuat dari garis lurus + offset (bukan jalan asli).';
        }
        if ($source === 'osrm-driving-fallback') {
            return "Profile {$profile} untuk mode {$mode} tidak tersedia di provider routing. Geometri OSRM driving digunakan sebagai fallback, tetapi duration dihitung ulang berdasarkan kecepatan mode.";
        }
        if ($source === 'openrouteservice-fallback') {
            return "Rute {$mode} menggunakan profile OpenRouteService {$this->openRouteServiceProfile($mode)}; duration dinormalisasi berdasarkan kecepatan rata-rata mode.";
        }

        return "Rute menggunakan profile OSRM {$profile}; duration dinormalisasi berdasarkan kecepatan rata-rata mode.";
    }

    private function osrmRoutes(float $oLat, float $oLng, float $dLat, float $dLng, string $profile): array
    {
        try {
            $base = rtrim((string) env('OSRM_URL', 'https://router.project-osrm.org'), '/');
            $url = "$base/route/v1/$profile/$oLng,$oLat;$dLng,$dLat";
            $res = Http::timeout(6)->get($url, ['overview' => 'full', 'geometries' => 'geojson', 'alternatives' => 'true', 'steps' => 'false']);
            if (! $res->successful()) {
                return [];
            }
            $routes = $res->json('routes', []);
            $out = [];
            foreach ($routes as $r) {
                $out[] = ['duration' => (int) round($r['duration'] ?? 0), 'distance' => (int) round($r['distance'] ?? 0), 'geometry' => $r['geometry'] ?? ['type' => 'LineString', 'coordinates' => []]];
            }

            return $out;
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function osrmRoutesWithWaypoint(float $oLat, float $oLng, float $wLat, float $wLng, float $dLat, float $dLng, string $profile): array
    {
        try {
            $base = rtrim((string) env('OSRM_URL', 'https://router.project-osrm.org'), '/');
            $url = "$base/route/v1/$profile/$oLng,$oLat;$wLng,$wLat;$dLng,$dLat";
            $res = Http::timeout(8)->get($url, ['overview' => 'full', 'geometries' => 'geojson', 'alternatives' => 'true', 'steps' => 'false']);
            if (! $res->successful()) {
                return [];
            }

            return array_map(fn (array $route) => [
                'duration' => (int) round($route['duration'] ?? 0),
                'distance' => (int) round($route['distance'] ?? 0),
                'geometry' => $route['geometry'] ?? ['type' => 'LineString', 'coordinates' => []],
            ], $res->json('routes', []));
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function openRouteServiceRoutes(float $oLat, float $oLng, float $dLat, float $dLng, string $profile): array
    {
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
            if (! $res->successful()) {
                return [];
            }

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

    private function stubRoutes(float $oLat, float $oLng, float $dLat, float $dLng, string $mode): array
    {
        // Dua polyline masuk akal: lurus, dan alternatif dengan offset 0.004 derajat.
        $mk = function ($offLat, $offLng) use ($oLat, $oLng, $dLat, $dLng) {
            $pts = [];
            for ($i = 0; $i <= 10; $i++) {
                $t = $i / 10;
                $pts[] = [round($oLng + ($dLng - $oLng) * $t + $offLng * sin($t * M_PI), 6), round($oLat + ($dLat - $oLat) * $t + $offLat * sin($t * M_PI), 6)];
            }

            return $pts;
        };
        $dist = (int) GeoService::distanceM($oLat, $oLng, $dLat, $dLng);
        $dur = $this->estimateDuration($dist, $mode);

        return [
            ['duration' => $dur, 'distance' => $dist, 'geometry' => ['type' => 'LineString', 'coordinates' => $mk(0, 0)]],
            ['duration' => (int) ($dur * 1.25), 'distance' => (int) ($dist * 1.15), 'geometry' => ['type' => 'LineString', 'coordinates' => $mk(0.004, 0.004)]],
        ];
    }
}
