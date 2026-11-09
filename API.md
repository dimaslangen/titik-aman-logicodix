# TitikAman — Dokumentasi API (untuk Tim)

> **Base URL (lokal):** `http://127.0.0.1:8000/api/v1`
> **Stack:** Laravel **13.40.0** (`laravel/framework ^13.17`), PHP 8.3, MySQL 8.0, Sanctum.
> **Bahasa:** dokumen ini Bahasa Indonesia, untuk Chika (PM), Amy (Front-End), Rafa (AI).
> **Format umum:** semua response JSON. Sukses: `{"success": true, "data": {...}}`.
> Error: `{"success": false, "message": "..."}` + HTTP status yang sesuai.
> Kalau tanpa token di endpoint yang butuh login, Laravel mengembalikan
> `401 {"message": "Unauthenticated."}`.

## 0. Konsep cepat (baca dulu, 2 menit)

- **Tanpa login boleh:** lihat region, Safety Hub, laporan, bandingkan rute.
- **Wajib login (token Sanctum):** buat laporan, upvote, share rute.
- **Admin saja:** dashboard, verifikasi/resolve/hide laporan, blokir user, SOS list,
  toggle Mode Simulasi.
- **Region hard-locked:** Rawamangun, UNESA Lidah Wetan, UNESA Ketintang.
  Semua titik (asal, tujuan, laporan) harus di dalam bounding box region-nya,
  kalau tidak → `422`.
- **Kategori laporan** (label objektif, sesuai PRD — jangan pakai label stigmatis):
  - `penerangan_padam` → "Penerangan Padam / Terbatas" (warna merah/oranye)
  - `area_sepi` → "Kondisi Area Sepi / Perlu Perhatian" (warna merah/oranye)
  - `hambatan_fisik` → "Hambatan Fisik Jalan" (warna kuning)
  - Safety Hub itu **bukan** laporan — warnanya biru, tipenya `polisi | satpam | faskes | halte`.
- **Status laporan:** `UNVERIFIED` → `VERIFIED` (≥3 upvote atau 1 verifikasi admin) ·
  `PENDING_MODERATION` (Gemini masih stub / gagal — ditinjau admin) ·
  `RESOLVED` (selesai ditangani) · `EXPIRED` (TTL 6 jam lewat) · `HIDDEN` (spam/hoax).
  Yang tampil di peta publik & memengaruhi rute hanya yang statusnya aktif
  (`UNVERIFIED`/`VERIFIED`/`PENDING_MODERATION`) dan belum kedaluwarsa.
  Yang memengaruhi bobot SafePass hanya yang `VERIFIED`.

## 1. Bounding Box Region

| Region (`region_slug`) | Pusat | Bounding box |
|---|---|---|
| `rawamangun` | -6.1928, 106.89763 | lat -6.2100 s.d. -6.1750, lng 106.8750 s.d. 106.9200 |
| `unesa-lidah-wetan` | -7.30071, 112.67289 | lat -7.3150 s.d. -7.2850, lng 112.6550 s.d. 112.6900 |
| `unesa-ketintang` | -7.31143, 112.72937 | lat -7.3250 s.d. -7.2950, lng 112.7100 s.d. 112.7500 |

Ubah di `config/titikaman.php` + seeder `RegionSeeder` (jangan hardcode di front-end).

## 2. Auth — Register / Login / Admin

### POST /register — daftar warga
```json
// Request
{"name":"Budi","email":"budi@mail.com","password":"Rahasia123","password_confirmation":"Rahasia123"}
// Response 201
{"success":true,"message":"Registrasi berhasil.","data":{"user":{"id":5,"name":"Budi","email":"budi@mail.com","role":"user"},"token":"1|..."}}
```
Password minimal 8 karakter, disimpan bcrypt (Laravel `hashed`). Simpan `token`
di front-end (misal `localStorage`), kirim di setiap request login sebagai header:
`Authorization: Bearer <token>`.

### POST /login — login warga
```json
// Request
{"email":"warga@titikaman.id","password":"Warga123!"}
// Response 200
{"success":true,"data":{"user":{"id":2,"name":"Warga Demo","email":"warga@titikaman.id","role":"user"},"token":"2|..."}}
```
Salah email/password → `422` dengan pesan "Email atau password salah."
Akun diblokir → `403` "Akun diblokir (hoax/spam)."

### POST /admin/login — login admin (khusus)
Sama seperti `/login`, tapi email harus ber-role `admin`.
Akun demo: **`admin@titikaman.id` / `Admin123!`** (seeder — ganti sebelum produksi).
Halaman web-nya: `/admin/login`.

### GET /me — profil (auth)
Header: `Authorization: Bearer <token>`. Response: `{"success":true,"data":{"id":2,"name":"Warga Demo","email":"warga@titikaman.id","role":"user"}}`

### POST /logout — (auth)
Menghapus token saat ini. Response: `{"success":true,"message":"Logout berhasil."}`

## 3. Publik — Region, Safety Hub, Laporan

### GET /health
```json
{"success":true,"app":"TitikAman","laravel":"13","status":"ok","time":"2026-11-09T08:11:49.690320Z"}
```

### GET /regions
Response: daftar 3 region + `center` + `bbox` (lihat tabel §1).

### GET /regions/{slug}
Contoh: `GET /regions/rawamangun` → satu region lengkap.

### GET /safety-hubs?region_slug=rawamangun&type=polisi
Response: array hub `{id, region_slug, name, type, lat, lng, address, is_24h, phone}`.
`type` diisi `polisi | satpam | faskes | halte`. Filter `type` opsional.

### GET /reports?region_slug=rawamangun&status=VERIFIED&category=hambatan_fisik&per_page=20
Response: paginasi `{data:[...], meta:{current_page, total}}`.
Tiap laporan (identitas pelapor **disamarkan** — tidak ada nama/user_id di publik):
```json
{"id":1,"region_slug":"rawamangun","category":"penerangan_padam","color":"red_orange",
 "label":"Penerangan Padam / Terbatas","lat":-6.1935,"lng":106.8985,
 "description":"Penerangan padam total di ruas ini, gelap banget malam hari.",
 "status":"VERIFIED","upvotes_count":3,"expires_at":"2026-11-09T14:10:03.000000Z",
 "created_at":"2026-11-09T08:10:03.000000Z","is_simulation":false}
```

### GET /reports/{id}
Satu laporan, format sama seperti di atas.

## 4. Rute — Fastest vs SafePass (publik)

### POST /routes/compare
```json
// Request
{"origin_lat":-6.1955,"origin_lng":106.9005,"destination_lat":-6.19,"destination_lng":106.894,
 "mode":"motor","region_slug":"rawamangun"}
// mode: mobil | motor | jalan_kaki
// Response 200 (contoh nyata dari tes — OSRM asli)
{"success":true,"data":{
  "region":"rawamangun","mode":"motor","source":"osrm",
  "disclaimer":"SafePass adalah alat bantu informasi berbasis komunitas, bukan jaminan keselamatan absolut, dan tidak menggantikan layanan darurat resmi (hubungi 110).",
  "fastest":{"duration":202,"distance":1717,"geometry":{"type":"LineString","coordinates":[[106.9,-6.19],...]},"penalty_seconds":300,"bonus_seconds":120,"score_seconds":382,"avoided_reports":[],"hubs_passed":[...]},
  "safepass":{"duration":202,"distance":1717,"geometry":{"type":"LineString","coordinates":[[...]]},"penalty_seconds":300,"bonus_seconds":120,"score_seconds":382,"avoided_reports":[{"report_id":1,"category":"penerangan_padam","distance_m":21.2}],"hubs_passed":[{"hub_id":1,"name":"Pos Polisi Rawamangun","type":"polisi","distance_m":45.0}]},
  "note":"Rute kandidat dari OSRM publik (router.project-osrm.org), pembobotan SafePass dihitung di Laravel."
}}
```
Penjelasan field:
- `geometry.coordinates`: GeoJSON `[[lng, lat], ...]` — langsung bisa digambar di Leaflet
  (`L.polyline(coords.map(c => [c[1], c[0]]))`). SafePass = hijau tebal, Tercepat = kuning putus-putus.
- `duration`: detik (OSRM), `distance`: meter.
- `score_seconds = duration + penalty - bonus`. `fastest` = durasi terkecil,
  `safepass` = skor terkecil. Kalau tidak ada laporan `VERIFIED`, keduanya sama.
- Penalti: laporan `VERIFIED` ≤ 50 m dari rute → +300 detik (penerangan/area sepi),
  +180 detik (hambatan). Bonus: Safety Hub ≤ 100 m → −60 detik/hub, maks −300 detik.
- `source`: `"osrm"` (asli) atau `"stub"` (OSRM tidak terjangkau — rute garis lurus +
  offset, **bukan** jalan asli; tampilkan catatan ini di UI demo kalau muncul).
- Di luar bbox → `422` "Asal / tujuan di luar bounding box region …".

## 5. Laporan — buat & upvote (login wajib)

### POST /reports (auth, multipart/form-data ATAU JSON tanpa foto)
```json
// Field (JSON):
{"region_slug":"rawamangun","category":"area_sepi","lat":-6.194,"lng":106.899,
 "user_lat":-6.194,"user_lng":106.899,"description":"Area sepi di sini"}
// Kalau pakai foto: kirim sebagai multipart, field `photo` (image, maks 5 MB).
//   Di front-end, foto WAJIB dari kamera live (capture="environment") — PRD FR-02.2.
// Response 201
{"success":true,"message":"Laporan dibuat. Status awal PENDING_MODERATION. Butuh >=3 upvote warga atau 1 verifikasi admin untuk jadi VERIFIED.",
 "data":{"id":5,"region_slug":"rawamangun","category":"area_sepi","color":"red_orange","label":"Kondisi Area Sepi / Perlu Perhatian","lat":-6.194,"lng":106.899,"description":"Tes area sepi dari curl","status":"PENDING_MODERATION","upvotes_count":0,"expires_at":"...","created_at":"...","is_simulation":false},
 "ai_check":{"is_valid":true,"category":"area_sepi","darkness":"unknown","has_face":false,"has_plate":false,"reason":"STUB: GEMINI_API_KEY belum diisi...","raw":null,"source":"stub"}}
```
Aturan & error:
- Tanpa token → `401`. `category` harus salah satu dari §0.
- Di luar bbox → `422` (hard-locked).
- Jarak `user_lat/user_lng` ke titik laporan > 20 m → `422`
  `{"distance_m": 709.5, "geofence_m": 20, ...}` — kecuali Mode Simulasi aktif.
- Duplikat (kategori sama, ≤ 20 m, masih aktif) → `409`
  `{"duplicate_report_id": 5, "duplicate_report": {...}}` — front-end arahkan user
  untuk Up-Check laporan itu, jangan buat laporan baru.
- Rate limit: maks 3 laporan/jam per user → `429`.
- Foto berwajah/plat jelas (kalau AI sudah aktif) → `422`.
- Status awal `PENDING_MODERATION` selama Gemini masih stub (FR-02.7). Setelah Rafa
  pasang Gemini asli, status awal jadi `UNVERIFIED` (lihat §8).

### POST /reports/{id}/upvote (auth)
```json
// Response 200
{"success":true,"message":"Up-Check tercatat.","data":{"id":5,"status":"VERIFIED","upvotes_count":3,...}}
```
Satu user hanya bisa 1x per laporan (upvote ke-2 → `409`). Begitu `upvotes_count`
mencapai 3, status otomatis jadi `VERIFIED` + `verified_at` terisi. Laporan yang
sudah `VERIFIED`/`RESOLVED`/`HIDDEN` tidak bisa di-upvote lagi.

### POST /reports/{id}/ai-check (auth)
Endpoint kontrak untuk Rafa — saat ini memanggil **STUB** (lihat §8).
Response: `{"success":true,"data":{"report_id":5,"ai_check":{...},"report_status":"VERIFIED"}}`
Hanya pemilik laporan atau admin yang boleh menjalankan endpoint ini; user lain mendapat
`403`. Endpoint ini tidak mengubah status verifikasi laporan secara otomatis.

## 6. SOS & Live Companion

### POST /sos (boleh tanpa login — darurat)
```json
// Request
{"region_slug":"rawamangun","lat":-6.193,"lng":106.898,"message":"SOS tes"}
// Response 201
{"success":true,"message":"SOS tercatat. Untuk darurat segera hubungi 110 (tel:110). Alert ini diteruskan ke dashboard admin.","data":{"id":1,"status":"open",...}}
```
**Penting untuk Amy:** tombol SOS di UI harus `tel:110` (nelpon beneran).
Endpoint ini hanya mencatat alert supaya admin bisa melihat + menindaklanjuti.
`lat` dan `lng` harus dikirim berpasangan. Jika koordinat dikirim, koordinat tersebut
harus berada di region yang valid dan konsisten dengan `region_slug`; jika tidak, response
`422`. Endpoint dibatasi maksimal 3 request per menit per user/IP (`429` setelahnya).

### POST /shared-routes (auth) — Live Companion
```json
// Request
{"region_slug":"rawamangun","mode":"motor","origin_lat":-6.1955,"origin_lng":106.9005,"destination_lat":-6.19,"destination_lng":106.894,"route_data":{}}
// Response 201
{"success":true,"data":{"token":"3l2g0wL7iZxW","share_url":"http://127.0.0.1:8000/api/v1/shared-routes/3l2g0wL7iZxW","expires_at":"..."}}
```
Asal dan tujuan wajib berada di bounding box `region_slug`; jika tidak, response `422`.
### GET /shared-routes/{token} (publik)
Mengembalikan rute yang dibagikan (berlaku 6 jam). Setelah kedaluwarsa endpoint
mengembalikan `404`. Front-end bisa pakai `share_url` ini untuk Live Companion.

## 7. Mode Simulasi Lokasi (juri)

### GET /simulation (publik)
```json
{"success":true,"data":{"simulation_mode":false,"note":"Mode Simulasi Lokasi Uji Juri (FR-02.1): kalau aktif, geofence 20 m dilewati. Demo/juri only."}}
```

### POST /admin/simulation (admin)
```json
// Request {"enabled": true}  → Response {"success":true,"data":{"simulation_mode":true}}
```
Kalau aktif, `POST /reports` tidak menolak jarak > 20 m, dan laporan yang dibuat
ditandai `is_simulation: true`. **Matikan lagi setelah demo** (`{"enabled": false}`).
Bisa juga diaktifkan dari `.env` (`SIMULATION_MODE=true`), tapi toggle admin lebih
diutamakan.

## 8. Admin (token admin wajib)

### GET /admin/dashboard
```json
{"success":true,"data":{
  "totals":{"reports":5,"verified":3,"unverified":1,"pending_moderation":1,"sos_open":0},
  "heatmap":[{"cell":"-6.194,106.899","count":2,"verified":1},...],
  "by_category":{"penerangan_padam":1,"hambatan_fisik":2},
  "by_region":{"rawamangun":3,"unesa-lidah-wetan":1},
  "recent_reports":[...],"sos_alerts":[...]}}
```
`heatmap`: kerapatan laporan per grid ~0.001° — front-end admin bisa gambar sebagai
heatmap di Leaflet (FR-04.2). Tambah `?region_slug=rawamangun` untuk filter.

### POST /admin/reports/{id}/verify — Official Verification (1 klik)
Response: laporan jadi `VERIFIED`. Ini yang dimaksud "petugas 1 klik" di PRD.

### POST /admin/reports/{id}/resolve — tandai selesai
Status jadi `RESOLVED`.

### POST /admin/reports/{id}/hide — sembunyikan spam/hoax
Status jadi `HIDDEN` (tidak tampil di publik, tidak memengaruhi rute).

### POST /admin/reports/{id}/wa-followup
Response: `{"data":{"wa_text":"Halo, dari aplikasi TitikAman. Ada laporan ...","wa_url":"https://wa.me/?text=..."}}`
— pesan siap kirim ke WA RT/Kelurahan/Satpam (FR-04.4).

### GET /admin/sos — daftar SOS
Paginasi alert SOS. `POST /admin/sos/{id}/resolve` untuk menutup alert.

### POST /admin/users/{id}/block — blokir akun hoax
`POST /admin/users/{id}/unblock` untuk membuka blokir. Admin tidak bisa diblokir.
Akun yang diblokir tidak bisa login (`403`).

## 9. Perilaku keamanan dan lifecycle data

- `GET /reports/{id}` hanya mengembalikan laporan aktif dan belum kedaluwarsa. Laporan
  `HIDDEN`, `RESOLVED`, dan `EXPIRED` tidak tersedia di endpoint publik (`404`).
- Laporan kedaluwarsa ditandai menjadi `EXPIRED` oleh command `php artisan reports:expire`.
  Scheduler Laravel menjalankan command tersebut setiap jam (`php artisan schedule:work`
  pada server).
- Login, register, dan admin login dibatasi 5 request per menit per IP (`429`).
- Upvote diproses dalam database transaction dengan row lock agar jumlah upvote dan
  transisi `VERIFIED` konsisten ketika request bersamaan.
- Perhitungan jarak SafePass memakai jarak terpendek ke segmen polyline, bukan hanya
  ke titik vertex polyline.

## 10. Kontrak Gemini — UNTUK RAFA (AI Engineer)

**Status saat ini: STUB.** Tidak ada panggilan ke Google sama sekali.
File: `app/Services/AiPhotoCheckService.php`
(interface `AiPhotoCheckService` + `StubAiPhotoCheckService`),
di-binding di `app/Providers/AppServiceProvider.php`.

**Yang harus Rafa lakukan (tanpa mengubah controller):**
1. Isi `GEMINI_API_KEY` di `.env` (jangan commit key asli).
2. Buat class `App\Services\GeminiAiPhotoCheckService implements AiPhotoCheckService`
   yang memanggil Gemini 2.5 Flash Multimodal (`GEMINI_MODEL=gemini-2.5-flash`),
   kirim foto + prompt klasifikasi, minta output **structured JSON**.
3. Ganti binding di `AppServiceProvider::register()` dari `StubAiPhotoCheckService`
   ke `GeminiAiPhotoCheckService`.

**Kontrak output `check(?string $photoPath, array $context): array` — WAJIB dipertahankan:**
```json
{"is_valid": true, "category": "penerangan_padam",
 "darkness": "gelap_total | gelap_sebagian | terang | unknown",
 "has_face": false, "has_plate": false,
 "reason": "penjelasan singkat berbahasa Indonesia",
 "raw": { "…response mentah Gemini…": null },
 "source": "gemini"}
```
**Aturan dari PRD FR-02.3 (wajib):**
- (a) Validasi foto relevan: jalan / luar ruang. Kalau bukan (misal foto selfie di dalam
  ruangan) → `is_valid: false`.
- (b) Klasifikasi tingkat kegelapan / kerusakan fasilitas → isi `darkness` + `category`
  yang disarankan (`penerangan_padam | area_sepi | hambatan_fisik`).
- (c) Kalau foto menampilkan wajah individu atau plat nomor kendaraan secara jelas →
  `is_valid: false`, `has_face` / `has_plate: true` (privasi).
- AI **hanya penyaring awal**, tidak menentukan status verifikasi akhir — itu tetap
  dari upvote warga / admin.

**Fallback (FR-02.7, sudah diimplementasikan di `ReportController`):**
Kalau panggilan Gemini gagal/timeout, controller menyimpan laporan dengan status
`PENDING_MODERATION` + `ai_check.source = "stub"` dan tetap mengembalikan `201`
(tidak menggagalkan input user). Admin meninjau manual di dashboard.
Di implementasi Rafa: lempar exception / kembalikan `['source' => 'gemini_error', ...]`
kalau Gemini gagal, dan controller akan tetap menyimpannya sebagai
`PENDING_MODERATION` — jangan ubah perilaku ini.

**Endpoint terkait:** `POST /api/v1/reports/{id}/ai-check` (auth) — memanggil
service ini untuk laporan yang sudah ada, berguna untuk tes manual Rafa.

## 11. Error & status code

| Kode | Arti | Contoh |
|---|---|---|
| 200 | OK | GET, upvote, admin action |
| 201 | Dibuat | register, laporan baru, SOS, shared route |
| 401 | Belum login | tanpa token di endpoint auth |
| 403 | Terlarang | bukan admin, akun diblokir |
| 404 | Tidak ditemukan | report/region id tidak ada |
| 409 | Konflik | duplikat laporan, sudah upvote, sudah VERIFIED |
| 422 | Validasi gagal | di luar bbox, geofence > 20 m, foto wajah/plat, input tidak valid |
| 429 | Rate limit | > 3 laporan/jam, > 5 login/register per menit, atau > 3 SOS per menit |

## 12. Data dummy (seeder)

- **Region:** 3 (lihat §1).
- **Safety Hub:** 13 — Rawamangun 5 (Pos Polisi, Pos Satpam, RS Persahabatan,
  Halte TransJakarta, Pos Ronda), UNESA Lidah Wetan 4, UNESA Ketintang 4.
  Semua koordinat dummy realistis di sekitar titik tengah region.
- **User:** admin + 3 warga demo (lihat README).
- **Laporan:** 4 — Rawamangun: 1 `VERIFIED` (penerangan padam, 3 upvote) +
  1 `UNVERIFIED` (hambatan); UNESA Lidah Wetan: 1 `VERIFIED` (area sepi);
  UNESA Ketintang: 1 `PENDING_MODERATION` (hambatan/genangan).
- Reset kapan saja: `php artisan migrate:fresh --seed`.

## 13. Halaman tes (untuk Amy & tim)

Buka `http://127.0.0.1:8000/test` — peta Leaflet + OpenStreetMap:
pilih region & moda, bandingkan rute (hijau = SafePass, kuning putus-putus = Tercepat),
marker biru = Safety Hub, merah/oranye & kuning = laporan, form login + lapor,
tombol SOS, dan panel **Response JSON** mentah supaya kelihatan persis apa yang
dikembalikan API. Ini **bukan UI final** — UI final tugas Amy.
