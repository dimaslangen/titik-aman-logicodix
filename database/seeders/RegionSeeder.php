<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder; use App\Models\Region;
class RegionSeeder extends Seeder {
    public function run(): void {
        foreach (config('titikaman.regions') as $r) Region::updateOrCreate(['slug'=>$r['slug']], $r);
    }
}
