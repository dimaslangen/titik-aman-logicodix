<?php
namespace App\Services;
class GeoService {
    public static function distanceM(float $lat1, float $lng1, float $lat2, float $lng2): float {
        $r = 6371000; $dLat = deg2rad($lat2-$lat1); $dLng = deg2rad($lng2-$lng1);
        $a = sin($dLat/2)**2 + cos(deg2rad($lat1))*cos(deg2rad($lat2))*sin($dLng/2)**2;
        return $r * 2 * atan2(sqrt($a), sqrt(1-$a));
    }
    public static function inBbox(float $lat, float $lng, array $box): bool {
        return $lat >= $box['min_lat'] && $lat <= $box['max_lat'] && $lng >= $box['min_lng'] && $lng <= $box['max_lng'];
    }
    public static function regionForPoint(float $lat, float $lng): ?string {
        foreach (config('titikaman.regions') as $slug => $r) {
            if (self::inBbox($lat, $lng, $r)) return $slug;
        }
        return null;
    }
    public static function minDistanceToPolyline(float $lat, float $lng, array $coords): float {
        $min = PHP_FLOAT_MAX;
        $previous = null;
        foreach ($coords as $coordinate) {
            if (!isset($coordinate[0], $coordinate[1])) {
                continue;
            }

            $current = [(float) $coordinate[0], (float) $coordinate[1]];
            if ($previous !== null) {
                $min = min($min, self::pointToSegmentDistanceM($lat, $lng, $previous, $current));
            } else {
                $min = min($min, self::distanceM($lat, $lng, $current[1], $current[0]));
            }
            $previous = $current;
        }
        return $min === PHP_FLOAT_MAX ? 999999 : $min;
    }

    private static function pointToSegmentDistanceM(float $lat, float $lng, array $start, array $end): float {
        $earthRadius = 6371000;
        $referenceLat = deg2rad(($lat + $start[1] + $end[1]) / 3);
        $scaleX = $earthRadius * cos($referenceLat) * pi() / 180;
        $scaleY = $earthRadius * pi() / 180;

        $px = ($lng - $start[0]) * $scaleX;
        $py = ($lat - $start[1]) * $scaleY;
        $sx = 0.0;
        $sy = 0.0;
        $ex = ($end[0] - $start[0]) * $scaleX;
        $ey = ($end[1] - $start[1]) * $scaleY;
        $dx = $ex - $sx;
        $dy = $ey - $sy;
        $lengthSquared = ($dx * $dx) + ($dy * $dy);

        if ($lengthSquared === 0.0) {
            return hypot($px, $py);
        }

        $projection = (($px - $sx) * $dx + ($py - $sy) * $dy) / $lengthSquared;
        $projection = max(0.0, min(1.0, $projection));
        $closestX = $sx + $projection * $dx;
        $closestY = $sy + $projection * $dy;

        return hypot($px - $closestX, $py - $closestY);
    }
}
