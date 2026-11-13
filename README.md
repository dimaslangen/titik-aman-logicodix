# TitikAman — Backend API (Laravel 13)

Navigasi komunitas malam hari: bandingkan **Rute Tercepat** vs **SafePass** (rute dengan
penerangan + Safety Hub lebih baik), berdasarkan laporan warga terverifikasi.
Hackathon **LOGICODIX 2026**, subtema Civic Tech & Community Service.

> **Versi ini: Laravel 13.40.0 (`laravel/framework: ^13.17`) + PHP 8.3.6 + MySQL 8.0.**
> Laravel 13 WAJIB (permintaan Dimas, 2026-11-09). Jangan downgrade ke Laravel 12 tanpa
> persetujuan tim. PRD patokan: `~/workspace/user/files/gemini-code-1794188570695.md`
> (versi Gemini full). File `prd.md` (versi pendek) hanya referensi.

## Tim

NAMA TIM: PATPUL HEKER

| Peran               |                       Orang   | Tugas |
|---------------------|-------------------------------|--------|
| Back-End, Ketua Tim | Dimas (Langen Dimas Pramudya) | API ini |
| PM & UI/UX          | Chika                         | Desain |
| Front-End           | Amy                           | UI (Leaflet) memakai API ini |
| AI & QA             | Rafa                          | Gemini 2.5 Flash (lihat `API.md` → Kontrak Gemini) |

## Yang dibangun di repo ini — dan yang TIDAK

**Dibangun (backend API + API Explorer demo):** Laravel 13, MySQL 8 (tanpa PostGIS,
pakai perhitungan Haversine/segmen polyline di PHP), Sanctum (token), semua endpoint
`/api/v1/...`, seeder dummy, API Explorer interaktif di `/test`, peta Leaflet untuk
memilih koordinat dan menampilkan rute SafePass/rute tercepat serta marker kondisi
jalan, halaman login admin di `/admin/login`.

**TIDAK dibangun:** UI final (tugas Amy), Mapbox (diganti Leaflet + OpenStreetMap,
tanpa token), Gemini asli (hanya stub — tugas Rafa, lihat `API.md`).

## Cara jalanin (lokal)

```bash
# 1. Prasyarat: PHP 8.3, Composer 2, MySQL 8
php -v            # 8.3.x
composer -V       # 2.x
mysql --version   # 8.0.x

# 2. Install dependency
composer install
cp .env.example .env
php artisan key:generate

# 3. Database (MySQL)
mysql -u root -e "CREATE DATABASE titikaman CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
# isi .env:
#   DB_CONNECTION=mysql, DB_HOST=127.0.0.1, DB_PORT=3306
#   DB_DATABASE=titikaman, DB_USERNAME=titikaman, DB_PASSWORD=titikaman123
#   (sesuaikan dengan MySQL di komputermu; user di atas dibuat saat setup VM)

# 4. Migrasi + seeder dummy
php artisan migrate:fresh --seed

# 5. Jalanin server
php artisan serve --host=127.0.0.1 --port=8000
# Buka: http://127.0.0.1:8000/test — API Explorer interaktif untuk mencoba endpoint

# Opsional: jalankan scheduler untuk menandai laporan kedaluwarsa sebagai EXPIRED
php artisan schedule:work
```

> `.env` **tidak** ikut di-zip / di-push. `GEMINI_API_KEY` dibiarkan kosong di
> `.env.example` — isi key asli hanya di server, jangan commit.

## Akun demo (seeder — GANTI sebelum produksi)

| Akun | Email | Password | Role |
|---|---|---|---|
| Admin | `admin@titikaman.id` | `Admin123!` | admin |
| Warga | `warga@titikaman.id` | `Warga123!` | user |
| Warga 2 | `warga2@titikaman.id` | `Warga123!` | user |
| Warga 3 | `warga3@titikaman.id` | `Warga123!` | user |

Login admin via `POST /api/v1/admin/login` atau halaman `/admin/login`.

## API Explorer untuk demo

Buka `http://127.0.0.1:8000/test` setelah server berjalan. Halaman ini menyediakan:

- katalog endpoint public, authentication, user, dan admin;
- form request dengan dropdown untuk region, moda, kategori, status, dan tipe hub;
- penyimpanan Bearer token Sanctum setelah login;
- peta Leaflet + OpenStreetMap untuk memilih asal, tujuan, titik laporan, dan SOS;
- visualisasi SafePass (hijau), rute tercepat (kuning putus-putus), laporan kondisi,
  Safety Hub, dan ringkasan status koridor;
- response JSON, status HTTP, dan latency request.

Urutan demo rute:

1. Pilih endpoint **Compare routes**.
2. Klik **Pilih asal** dan **Pilih tujuan** pada peta.
3. Klik **Jalankan request**.
4. Map akan menampilkan dua rute serta marker kondisi jalan.

Halaman ini adalah alat uji backend, bukan UI final aplikasi warga.

## Aturan kunci (dari PRD + klarifikasi Dimas 2026-11-09)

- **Tanpa login:** lihat peta, Safety Hub, laporan, bandingkan rute.
  **Wajib login:** buat laporan, upvote. Login diarahkan saat mau lapor.
- **Region hard-locked** (di luar bounding box → 422):
  - Rawamangun: lat -6.2100 s.d. -6.1750, lng 106.8750 s.d. 106.9200 (pusat -6.1928, 106.89763)
  - UNESA Lidah Wetan: lat -7.3150 s.d. -7.2850, lng 112.6550 s.d. 112.6900 (pusat -7.30071, 112.67289)
  - UNESA Ketintang: lat -7.3250 s.d. -7.2950, lng 112.7100 s.d. 112.7500 (pusat -7.31143, 112.72937)
- **Laporan:** GPS pengguna ≤ 20 m dari titik laporan (kalau tidak, 422), duplikat
  ≤ 20 m kategori sama → 409 + arahkan upvote laporan lama, status awal
  `PENDING_MODERATION` (karena Gemini masih stub) atau `UNVERIFIED`,
  jadi `VERIFIED` kalau ≥ 3 upvote atau 1 verifikasi admin, TTL 6 jam,
  rate limit maks 3 laporan/jam per user.
- **SafePass:** penalti laporan `VERIFIED` ≤ 50 m dari rute (300 detik untuk
  penerangan/area sepi, 180 detik hambatan), bonus Safety Hub ≤ 100 m
  (60 detik/hub, maks 300 detik). Kalau tidak ada laporan terverifikasi,
  SafePass = Tercepat.
- **Rute:** kandidat dari OSRM publik (`router.project-osrm.org`, tanpa key).
  Kalau OSRM tidak terjangkau, response memakai fallback stub dan
  `source: "stub"` — ditulis jelas di response, bukan dipalsukan sebagai OSRM.
- **Gemini:** STUB. Lihat `API.md` → Kontrak Gemini untuk Rafa.
- **SOS:** frontend pakai `tel:110`. Backend hanya mencatat alert untuk admin.
- **Mode Simulasi Lokasi (juri):** admin bisa aktifkan via
  `POST /api/v1/admin/simulation {"enabled": true}` — geofence 20 m dilewati.
  Demo only, matikan setelah demo.

## Struktur penting

```
app/Http/Controllers/Api/   Auth, Region, SafetyHub, Report, Route, Sos,
                            SharedRoute, Simulation, Admin
app/Services/               GeoService, RoutingService (OSRM + SafePass),
                            AiPhotoCheckService (interface + STUB)
app/Models/                 User, Region, SafetyHub, Report, ReportUpvote,
                            SosAlert, SharedRoute, Setting
config/titikaman.php        bounding box, geofence, TTL, penalti/bonus
database/migrations/        users (role/is_blocked), regions, safety_hubs,
                            reports, report_upvotes, sos_alerts, shared_routes, settings
database/seeders/           Region, SafetyHub (13 hub dummy), User, Report (4 laporan dummy)
resources/views/test.blade.php        Halaman tes Leaflet (bukan UI final)
resources/views/admin-login.blade.php Halaman login admin sederhana
routes/api.php              semua route di bawah /api/v1
API.md                      dokumentasi endpoint lengkap (untuk tim)
TESTS.md                    hasil tes curl yang benar-benar dijalankan
```

## Endpoint ringkas

Lihat `API.md` untuk detail + contoh JSON. Ringkasnya:

```
GET  /api/v1/health, /regions, /safety-hubs, /reports, /reports/{id}
POST /api/v1/routes/compare
POST /api/v1/register, /login, /admin/login
POST /api/v1/reports, /reports/{id}/upvote, /reports/{id}/ai-check (auth)
POST /api/v1/sos, /shared-routes
GET  /api/v1/simulation
GET  /api/v1/admin/dashboard, /admin/sos
POST /api/v1/admin/reports/{id}/verify|resolve|hide|wa-followup
POST /api/v1/admin/users/{id}/block|unblock, /admin/simulation
```

## Yang masih stub / dummy (jujur)

1. **OSRM:** asli (terverifikasi saat tes), tapi kalau server OSRM mati → fallback
   stub garis lurus. Ditandai `source: "stub"`.
2. **Gemini:** 100% stub. Belum ada panggilan ke Google sama sekali.
3. **Data:** semua Safety Hub + laporan adalah dummy realistis di sekitar titik
   tengah region. Ganti dengan data lapangan asli sebelum produksi.
4. **Bounding box:** kotak persegi per region, bukan poligon batas kelurahan/kampus
   yang persis. Bisa dipersempit di `config/titikaman.php`.
5. **Foto laporan:** diterima sebagai upload (`photo`, maks 5 MB, disimpan di
   `storage/app/public/reports`). Input kamera final dengan `capture="environment"`
   tetap menjadi tugas front-end Amy (PRD FR-02.2).

## Push project ke GitHub (Windows PowerShell)

Pastikan GitHub repository kosong sudah dibuat terlebih dahulu, lalu jalankan dari
folder project:

```powershell
Set-Location "C:\Users\ADVAN\Desktop\Projek\titik-aman"

# Cek Git dan buat repository lokal
git --version
git init
git branch -M main

# Pastikan .env, vendor, dan node_modules tidak ikut
git status --short

# Simpan perubahan pertama
git add .
git commit -m "Initial TitikAman backend API"

# Ganti URL ini dengan repository GitHub milikmu
git remote add origin https://github.com/USERNAME/NAMA-REPOSITORY.git
git push -u origin main
```

Jika GitHub meminta autentikasi HTTPS, gunakan akun GitHub dan Personal Access
Token (PAT), bukan password GitHub. Jangan pernah memasukkan token atau isi `.env`
ke dalam commit.

Untuk perubahan berikutnya:

```powershell
git status
git add .
git commit -m "Describe your change"
git push
```

Jika remote `origin` sudah pernah dibuat:

```powershell
git remote -v
git remote set-url origin https://github.com/USERNAME/NAMA-REPOSITORY.git
```
