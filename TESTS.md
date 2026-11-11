# TitikAman — Hasil Tes (dijalankan di VM, 2026-11-09)

> Semua tes di bawah ini **benar-benar dijalankan** dengan `curl` ke server lokal
> `php artisan serve` (http://127.0.0.1:8000) setelah `php artisan migrate:fresh --seed`.
> Stack saat tes: Laravel 13.40.0, PHP 8.3.6, MySQL 8.0.46, Sanctum 4.
> Setelah tes, database di-reset lagi (`migrate:fresh --seed`) supaya yang diterima
> tim adalah data seeder bersih (4 laporan dummy, 13 Safety Hub).

## Ringkasan: 12/12 kelompok tes LULUS

| # | Tes | Hasil |
|---|---|---|
| 1 | `GET /api/v1/health` | LULUS — `{"success":true,"app":"TitikAman","laravel":"13","status":"ok"}` |
| 2 | `GET /api/v1/regions` | LULUS — 3 region + bbox |
| 3 | `GET /api/v1/safety-hubs?region_slug=rawamangun` | LULUS — 5 hub |
| 4 | `GET /api/v1/reports?region_slug=rawamangun` | LULUS — total 2, tanpa identitas pelapor |
| 5 | `POST /api/v1/routes/compare` (Rawamangun, motor) | LULUS — **OSRM asli**, `source:"osrm"`, 38 titik polyline, durasi 202 detik, jarak 1717 m, SafePass menghindari laporan VERIFIED (21,2 m) + 2 hub dilewati |
| 6 | Routes compare di luar bbox | LULUS — `422` "di luar bounding box region rawamangun" |
| 7 | Register + login warga | LULUS — token Sanctum diterbitkan |
| 8 | Buat laporan tanpa login | LULUS — `401 Unauthenticated` |
| 9 | Buat laporan di luar bbox / geofence > 20 m | LULUS — `422` (bbox) dan `422` "Jarak Anda … 709.5 m, melebihi geofence 20 m" |
| 10 | Buat laporan valid → duplikat → upvote ×3 → VERIFIED | LULUS — status awal `PENDING_MODERATION` (stub Gemini), duplikat `409` dengan `duplicate_report_id`, upvote ke-3 mengubah status jadi `VERIFIED` |
| 11 | `POST /reports/{id}/ai-check` (stub) | LULUS — `source:"stub"`, alasan "GEMINI_API_KEY belum diisi" |
| 12 | Admin login, dashboard, verify, SOS, shared route, simulasi, `/test` | LULUS — lihat detail di bawah |

## Detail output (disingkat, yang penting)

### 5. Routes compare — OSRM asli
```
source: osrm
fastest dur/dist: 202 detik / 1717 m, 38 titik polyline
safepass dur/score: 202 / 382, avoided: [{report_id: 1, category: penerangan_padam, distance_m: 21.2}], hubs: 2
```
Artinya SafePass benar-benar menghitung penalti laporan terverifikasi.

### 6. Di luar bbox → 422
```json
{"success":false,"message":"Asal / tujuan di luar bounding box region rawamangun. Region ini hard-locked (PRD Multi-Region Hard-Scope).","bbox":{"slug":"rawamangun","center_lat":-6.1928,"center_lng":106.89763,"min_lat":-6.21,"max_lat":-6.175,"min_lng":106.875,"max_lng":106.92}}
```

### 8–10. Alur laporan lengkap
- Tanpa login: HTTP `401`.
- Laporan valid (warga login, titik = posisi, ≤ 20 m):
  `201 {"status":"PENDING_MODERATION","upvotes_count":0,"ai_check":{"source":"stub",...}}`
- Laporan kedua, kategori sama, ~14 m dari yang pertama: `409`
  `{"message":"Laporan duplikat: sudah ada laporan kategori sama dalam radius 20 m...","duplicate_report_id":5}`
- Upvote oleh warga → 1, warga2 → 2, warga3 → 3 dan status berubah
  `PENDING_MODERATION → VERIFIED` tepat di upvote ke-3.

### 12. Admin & fitur lain
- Admin login (`admin@titikaman.id` / `Admin123!`): token diterbitkan.
- Dashboard: `totals {reports:5, verified:3, unverified:1, pending_moderation:1, sos_open:0}`, heatmap 4 cell.
- `POST /admin/reports/2/verify`: "Official Verification diberikan." → `VERIFIED`.
- Mode Simulasi: `GET /simulation` → `false`; admin toggle → `true`;
  laporan dari jarak jauh (posisi -6.1, 106.8 vs titik -6.196, 106.901) **berhasil**
  dibuat saat simulasi aktif; toggle kembali → `false`.
- SOS: `201` "SOS tercatat. Untuk darurat segera hubungi 110 (tel:110)."
  Admin SOS list: total 1.
- Shared route: `201 {"token":"3l2g0wL7iZxW","share_url":"http://127.0.0.1:8000/api/v1/shared-routes/3l2g0wL7iZxW"}`
- WA follow-up: menghasilkan `wa_text` + `wa_url` (https://wa.me/?text=...).
- Halaman tes: `GET /test` → HTTP `200`, memuat Leaflet.

## Bug yang ketemu saat tes (sudah diperbaiki)

1. **Route jadi `/api/health` bukan `/api/v1/health`** — `bootstrap/app.php`
   belum mendaftarkan `routes/api.php` dengan prefix. Diperbaiki: tambah
   `api: __DIR__.'/../routes/api.php'` + bungkus semua route di `Route::prefix('v1')`.
2. **Tanpa login: error `Route [login] not defined`** (bukan 401) — ditambah route
   bernama `login` di `routes/api.php` yang mengembalikan 401 JSON.
3. **Akun demo kena rate-limit 3 laporan/jam karena seeder** — laporan dummy
   seeder dibuat 2 jam lalu (`created_at` dimundurkan), `expires_at` tetap +4 jam
   ke depan, jadi akun demo bisa langsung dipakai untuk tes lapor.
4. **`upvotes_count: null` di response create laporan** — ditambah `$rep->refresh()`
   setelah create.

## Yang BELUM dites / masih stub

- **Gemini asli:** tidak dites (memang stub — belum ada key / implementasi).
- **Foto upload via multipart:** endpoint menerima `photo` (image, maks 5 MB),
  tapi tes curl di atas memakai JSON tanpa foto. Upload file perlu dites Amy
  dari front-end / Postman.
- **OSRM fallback stub:** tidak sempat dipaksa mati saat tes (OSRM-nya hidup dan
  dipakai). Kode fallback-nya ada di `RoutingService::stubRoutes()` dan ditandai
  `source:"stub"`.
- **Cloudflare Tunnel:** DICOBA 2026-11-09, GAGAL. `cloudflared tunnel --url http://127.0.0.1:8000`
  (binary resmi diunduh dari github.com/cloudflare/cloudflared) 2x gagal dengan
  `failed to request quick Tunnel: Post "https://api.trycloudflare.com/tunnel":
  tls: first record does not look like a TLS handshake`. Jaringan VM menolak/
  menghalangi TLS ke api.trycloudflare.com. TIDAK ada URL publik yang dibuat —
  jangan klaim ada. Dimas bisa expose sendiri dari laptopnya (seperti demo Kas
  dulu) dengan `cloudflared tunnel --url http://localhost:8000` setelah jalanin
  zip ini, atau tim deploy ke server HTTPS.
