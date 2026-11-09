<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([RegionSeeder::class, SafetyHubSeeder::class, UserSeeder::class, ReportSeeder::class]);
        \App\Models\Setting::updateOrCreate(['key'=>'simulation_mode'], ['value'=>'0']);
    }
}
