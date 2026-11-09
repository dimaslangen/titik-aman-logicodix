<?php
return [
    'regions' => [
        'rawamangun' => [
            'slug' => 'rawamangun', 'name' => 'Rawamangun, Jakarta Timur', 'group' => 'rawamangun',
            'center_lat' => -6.1928, 'center_lng' => 106.89763,
            'min_lat' => -6.2100, 'max_lat' => -6.1750, 'min_lng' => 106.8750, 'max_lng' => 106.9200,
        ],
        'unesa-lidah-wetan' => [
            'slug' => 'unesa-lidah-wetan', 'name' => 'UNESA Lidah Wetan, Surabaya', 'group' => 'unesa',
            'center_lat' => -7.30071, 'center_lng' => 112.67289,
            'min_lat' => -7.3150, 'max_lat' => -7.2850, 'min_lng' => 112.6550, 'max_lng' => 112.6900,
        ],
        'unesa-ketintang' => [
            'slug' => 'unesa-ketintang', 'name' => 'UNESA Ketintang, Surabaya', 'group' => 'unesa',
            'center_lat' => -7.31143, 'center_lng' => 112.72937,
            'min_lat' => -7.3250, 'max_lat' => -7.2950, 'min_lng' => 112.7100, 'max_lng' => 112.7500,
        ],
    ],
    'geofence_m' => 20,
    'duplicate_radius_m' => 20,
    'verify_upvotes' => 3,
    'ttl_hours' => 6,
    'report_rate_limit_per_hour' => 3,
    'penalty_radius_m' => 50,
    'hub_bonus_radius_m' => 100,
    'disclaimer' => 'SafePass adalah alat bantu informasi berbasis komunitas, bukan jaminan keselamatan absolut, dan tidak menggantikan layanan darurat resmi (hubungi 110).',
];
