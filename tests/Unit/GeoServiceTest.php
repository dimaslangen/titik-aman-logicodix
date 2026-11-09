<?php

namespace Tests\Unit;

use App\Services\GeoService;
use PHPUnit\Framework\TestCase;

class GeoServiceTest extends TestCase
{
    public function test_polyline_distance_uses_segments_between_vertices(): void
    {
        $distance = GeoService::minDistanceToPolyline(
            0.0005,
            0.005,
            [[0.0, 0.0], [0.01, 0.0]],
        );

        $this->assertEqualsWithDelta(55.6, $distance, 1.0);
    }

    public function test_point_outside_segment_does_not_project_beyond_endpoint(): void
    {
        $distance = GeoService::minDistanceToPolyline(
            0.001,
            0.02,
            [[0.0, 0.0], [0.01, 0.0]],
        );

        $this->assertEqualsWithDelta(
            GeoService::distanceM(0.001, 0.02, 0.0, 0.01),
            $distance,
            0.5,
        );
    }
}
