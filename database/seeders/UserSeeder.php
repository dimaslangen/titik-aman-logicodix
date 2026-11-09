<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder; use App\Models\User; use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder {
    public function run(): void {
        // Akun demo — GANTI password ini sebelum deploy produksi. Lihat README/API.md.
        User::updateOrCreate(['email'=>'admin@titikaman.id'], ['name'=>'Admin TitikAman','password'=>Hash::make('Admin123!'),'role'=>'admin']);
        User::updateOrCreate(['email'=>'warga@titikaman.id'], ['name'=>'Warga Demo','password'=>Hash::make('Warga123!'),'role'=>'user']);
        User::updateOrCreate(['email'=>'warga2@titikaman.id'], ['name'=>'Warga Demo 2','password'=>Hash::make('Warga123!'),'role'=>'user']);
        User::updateOrCreate(['email'=>'warga3@titikaman.id'], ['name'=>'Warga Demo 3','password'=>Hash::make('Warga123!'),'role'=>'user']);
    }
}
