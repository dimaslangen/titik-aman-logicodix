<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RawamangunDangerTestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'warga@titikaman.id'],
            [
                'name' => 'Warga Demo',
                'password' => Hash::make('Warga123!'),
                'role' => 'user',
            ]
        );

        $reports = [
            ['penerangan_padam', -6.1948, 106.8802, 'Lampu PJU Mati Total Depan Gerbang UNJ Pemuda', 'Ruas trotoar Jl. Pemuda arah Velodrome gelap gulita karena 4 tiang lampu padam.'],
            ['area_sepi', -6.1941, 106.8818, 'Area Sepi & Rawan Jambret Simpang Jl. Paus', 'Minim penerangan dan jarang kendaraan melintas saat malam di mulut Jl. Paus.'],
            ['hambatan_fisik', -6.1932, 106.8825, 'Trotoar Rusak & Galian Tanpa Pembatas Jl. Paus', 'Galian kabel terbuka memakan bahu jalan, berbahaya bagi pejalan kaki dan motor.'],
            ['penerangan_padam', -6.1924, 106.8834, 'Gang Gelap Tanpa Penerangan Tengah Jl. Paus', 'Lampu jalan mati sepanjang 150 meter di sekitar pertokoan tutup.'],
            ['area_sepi', -6.1918, 106.8846, 'Laporan Percobaan Begal Dekat Pertigaan Balai Pustaka', 'Warga melaporkan dua pengendara mencurigakan sering mangkal di tikungan gelap.'],
            ['penerangan_padam', -6.1945, 106.8838, 'Lampu Jalan Padam Depan Halte Pemuda Rawamangun', 'Area penyeberangan gelap total, visibilitas pengendara sangat rendah.'],
            ['hambatan_fisik', -6.1942, 106.8855, 'Pohon Tumbang Menutup Sebagian Jalur Lambat', 'Dahan besar menghalangi jalur kiri arah Velodrome dan kabel menjuntai.'],
            ['area_sepi', -6.1938, 106.8871, 'Titik Sepi Rawan Penodongan Dekat JPO Pemuda', 'Akses menuju JPO minim pengawasan dan lampu sorot mati.'],
            ['hambatan_fisik', -6.1930, 106.8884, 'Banjir Genangan Tinggi & Lubang Dalam Jl. Pemuda', 'Genangan air menutupi lubang sedalam 20 cm sebelum belokan Velodrome.'],
            ['penerangan_padam', -6.1921, 106.8891, 'Lampu Penerangan Sisi Barat Velodrome Mati', 'Koridor trotoar menuju pintu masuk Velodrome gelap dan sepi di malam hari.'],
        ];

        foreach ($reports as [$category, $lat, $lng, $title, $description]) {
            Report::updateOrCreate(
                [
                    'region_slug' => 'rawamangun',
                    'category' => $category,
                    'lat' => $lat,
                    'lng' => $lng,
                ],
                [
                    'user_id' => $user->id,
                    'user_lat' => $lat,
                    'user_lng' => $lng,
                    'description' => $title.': '.$description,
                    'photo_path' => null,
                    'status' => 'VERIFIED',
                    'upvotes_count' => 3,
                    'ai_check' => ['source' => 'seed', 'reason' => 'Data uji koridor UNJ menuju Velodrome.'],
                    'is_simulation' => true,
                    'expires_at' => now()->addDays(7),
                    'verified_at' => now(),
                ]
            );
        }
    }
}
