# Product Requirement Document (PRD)

## TitikAman

> **Hyperlocal Community-Powered Safe Navigation & Civic Safety Grid**

| Atribut | Keterangan |
| :--- | :--- |
| **Nama Proyek** | TitikAman |
| **Versi MVP** | 1.0.0 (Multi-Region Hard-Scope) |
| **Target Region** | Rawamangun (Jakarta Timur) & Kawasan Kampus UNESA (Lidah Wetan & Ketintang, Surabaya) |
| **Kategori & Subtema** | Web Development — Civic Tech & Community Service (LOGICODIX 2026) |
| **Platform** | Aplikasi Web Responsif (Mobile-First untuk Warga, Desktop/Tablet untuk Admin) |

---

## Daftar Isi

1. [Document Overview & Objective](#1-document-overview--objective)
2. [User Roles & Permissions Matrix (2 Roles)](#2-user-roles--permissions-matrix-2-roles)
3. [Functional Requirements (Spesifikasi Fungsional)](#3-functional-requirements-spesifikasi-fungsional)
4. [Technical Architecture & Tech Stack](#4-technical-architecture--tech-stack)
5. [Non-Functional Requirements (NFR) & Etika Digital](#5-non-functional-requirements-nfr--etika-digital)

---

## 1. Document Overview & Objective

Dokumen ini mendefinisikan kebutuhan fungsional dan teknis untuk pembangunan aplikasi web **TitikAman** versi MVP 1.0.0. TitikAman membantu pejalan kaki serta pengendara malam hari (motor dan mobil) membandingkan **Rute Tercepat** dengan **Rute SafePass**—yaitu rute yang memprioritaskan jalan berpenerangan baik dan keberadaan fasilitas pendukung (*Safety Hub*) berdasarkan kontribusi warga dan petugas keamanan lokal yang terverifikasi.

Aplikasi menerapkan *Multi-Region Hard-Scope* pada dua kawasan percontohan: **Rawamangun (Jakarta Timur)** dan **Kawasan Kampus UNESA Lidah Wetan & Ketintang (Surabaya)** guna menjaga kepadatan data lapangan, akurasi verifikasi komunitas, dan kecepatan respons kueri geospasial. Aplikasi ini adalah alat bantu keputusan perjalanan bersama komunitas, tidak menjamin keselamatan absolut, dan tidak menggantikan layanan darurat resmi.

---

## 2. User Roles & Permissions Matrix (2 Roles)

Sistem membagi hak akses ke dalam 2 Peran Utama:

| Fitur / Modul | User (Warga / Pejalan Kaki / Pengendara) | Admin (Petugas Keamanan Lokal / Moderator) |
| :--- | :---: | :---: |
| Region Scope Selector (Rawamangun Jakarta / UNESA Surabaya) | ✅ | ✅ |
| Navigasi Multi-Moda SafePass (Mobil, Motor, Jalan Kaki) | ✅ | ✅ |
| Melihat Visual Marker, Safety Hub, & Kartu Kondisi Lapangan | ✅ | ✅ |
| Lapor Kondisi Jalan (Geofence <=20m + Live Camera + Cek AI) | ✅ | ✅ |
| Up-Check / Konfirmasi Laporan Warga Sekitar (+1 Upvote) | ✅ | ✅ |
| Fitur Live Companion (Share Route URL) & Panggilan Cepat SOS 110 | ✅ | ❌ |
| Akses Admin / Officer Dashboard (`/admin/login` & Patrol Heatmap) | ❌ | ✅ |
| Memberikan Instant / Official Verification Badge pada Laporan | ❌ | ✅ |
| Menerima Alert Sinyal SOS & Tindak Lanjut Fasilitas (WA RT/Kelurahan) | ❌ | ✅ |
| Moderasi Konten (Tandai Selesai / Sembunyikan) & Blokir Akun Hoax | ❌ | ✅ |

---

## 3. Functional Requirements (Spesifikasi Fungsional)

### FR-01: Modul Navigasi & Map Rendering

| ID | Requirement |
| :--- | :--- |
| **FR-01.1** | Web wajib memuat basis peta bertema *Dark Mode* menggunakan Mapbox GL JS (`mapbox://styles/mapbox/dark-v11`) yang nyaman dilihat pada malam hari. |
| **FR-01.2** | Web wajib menyediakan *Multi-Region Scope Viewport Selector* untuk memindahkan fokus peta secara instan antara kawasan **Rawamangun (Jakarta)** dan **UNESA (Surabaya)**. |
| **FR-01.3** | Web wajib menyediakan 3 tab pilihan moda transportasi: **Mobil**, **Motor**, dan **Jalan Kaki**. |
| **FR-01.4** | Web wajib merender 2 *polyline* rute sekaligus di peta: **Garis Hijau Tebal (Rute SafePass / Rekomendasi)** dan **Garis Kuning Putus-putus (Rute Tercepat)** beserta kartu perbandingan waktu tempuh, jumlah titik kondisi yang dihindari, dan jumlah *Safety Hub* yang dilewati. |
| **FR-01.5** | Web wajib menampilkan 3 kategori marker visual di peta: **Biru** (*Safety Hub*: Pos Polisi, Pos Satpam Kampus/Ronda 24 Jam, Faskes 24 Jam, Halte Aktif), **Merah/Oranye** (Kondisi Perlu Perhatian Tinggi: penerangan padam total / area sepi ekstrem), dan **Kuning** (Waspada: hambatan fisik jalan / jalan berlubang / genangan). |
| **FR-01.6** | Menyediakan fitur **Live Companion** (berbagi tautan rute perjalanan via URL unik) dan tombol cepat **SOS Emergency / Panggilan 110** di layar navigasi pengguna. |

### FR-02: Modul Pelaporan, AI Filter, & Anti-Hoax System

| ID | Requirement |
| :--- | :--- |
| **FR-02.1** | Sistem wajib memvalidasi koordinat GPS pengguna saat melapor (jarak ke titik laporan wajib **<=20 meter**). Jika di luar radius, form ditolak. Sistem wajib menyediakan *toggle* **Mode Simulasi Lokasi Uji Juri** agar Dewan Juri dapat menguji fitur pelaporan dan konfirmasi di Rawamangun maupun UNESA dari lokasi mana pun. |
| **FR-02.2** | Input foto bukti wajib menggunakan *Live Camera Stream* dari browser (`capture="environment"`). Opsi unggah gambar dari galeri perangkat dinonaktifkan untuk mencegah foto lama/rekayasa. |
| **FR-02.3** | Foto dikirim ke **Google Gemini API (Flash Multimodal)** untuk: (a) memvalidasi relevansi foto jalan/luar ruang, (b) mengklasifikasi tingkat kegelapan atau kerusakan fasilitas, dan (c) menolak foto yang menampilkan wajah individu atau plat nomor kendaraan secara jelas demi perlindungan privasi. AI hanya bertindak sebagai penyaring awal dan **tidak** menentukan status verifikasi akhir. |
| **FR-02.4** | Sistem wajib mendeteksi **duplikasi laporan** aktif dalam radius **<=20 meter** dengan kategori yang sama. Jika terdeteksi duplikat, sistem mengarahkan pengguna untuk memberikan *Up-Check* (+1 Upvote) pada laporan yang sudah ada. |
| **FR-02.5** | Laporan baru yang lolos validasi AI disimpan dengan status `UNVERIFIED` (belum memengaruhi rute publik) dan membutuhkan akumulasi **>=3 Upvote** dari warga sekitar (atau 1 klik *Official Verification* dari Admin/Petugas) untuk berubah menjadi `VERIFIED`. |
| **FR-02.6** | Sistem menerapkan **Time-To-Live (TTL) Decay**: laporan kondisi sementara otomatis kedaluwarsa (*expired*) dalam rentang **3–6 jam** jika tidak ada konfirmasi pembaruan dari warga, sehingga peta bebas dari peringatan usang. |
| **FR-02.7** | Apabila layanan API AI mengalami gangguan atau *timeout*, sistem menerapkan *fallback* otomatis dengan menyimpan laporan ke status `PENDING_MODERATION` untuk ditinjau manual di Dashboard Admin tanpa menggagalkan input pengguna. |

### FR-03: Modul SafePass Routing Engine

| ID | Requirement |
| :--- | :--- |
| **FR-03.1** | Sistem wajib menghitung pembobotan kandidat rute menggunakan kueri geospasial pada database **PostgreSQL + PostGIS** (atau MySQL 8.0+ Spatial). |
| **FR-03.2** | Titik laporan berstatus `VERIFIED` dalam radius **50 meter** dari segmen jalan memberikan *penalty weight* (penalti bobot) sesuai kategori dan kesegaran waktu laporan, sehingga algoritma *SafePass* memutar ke jalur alternatif (*bypass*). |
| **FR-03.3** | Titik biru (*Safety Hub*: Pos Polisi, Pos Keamanan Kampus UNESA/UNJ, Pos Ronda, Faskes 24 Jam) dalam radius **100 meter** dari segmen jalan memberikan *bonus weight* untuk memprioritaskan jalur yang melewati area terang dan ramai. |

### FR-04: Modul Admin Dashboard & Moderasi (`/admin/login`)

| ID | Requirement |
| :--- | :--- |
| **FR-04.1** | Menyediakan halaman autentikasi khusus Admin / Petugas Keamanan Lokal di `/admin/login`. |
| **FR-04.2** | Menyajikan **Heatmap Patroli Presisi** yang memvisualisasikan kerapatan titik laporan di kawasan Rawamangun dan UNESA sebagai panduan pemantauan lapangan berbasis data (*Data-Driven Patrol*). |
| **FR-04.3** | Menyediakan aksi **Official Verification Badge** bagi petugas/moderator untuk memverifikasi laporan warga secara instan atau menandai masalah fasilitas telah selesai ditangani (`RESOLVED`). |
| **FR-04.4** | Menyediakan panel pemantauan sinyal **SOS Emergency**, fitur pembuat pesan tindak lanjut siap kirim ke WhatsApp pengurus RT/Kelurahan/ Satpam Kampus, serta fitur moderasi untuk menyembunyikan laporan *spam* dan memblokir akun pembuat *hoax*. |

---

## 4. Technical Architecture & Tech Stack

| Komponen | Spesifikasi Teknis |
| :--- | :--- |
| **Framework Utama** | PHP 8.3+ (Laravel 13) + Tailwind CSS + Livewire / Alpine.js. |
| **Database Geospasial** | PostgreSQL 16 + Ekstensi PostGIS (Kueri spasial `ST_DWithin`, `ST_Distance`, *Bounding Box Polygon*) atau MySQL 8.0+ Spatial (`ST_Distance_Sphere`). |
| **API Map & Navigasi** | Mapbox GL JS + Mapbox Directions API / OSRM (Tema *Dark Style*: `mapbox://styles/mapbox/dark-v11`). |
| **API Artificial Intelligence** | Google Gemini API (`gemini-2.5-flash` Multimodal) dengan keluaran *Structured JSON* untuk klasifikasi foto kondisi jalan & penyaringan privasi. |
| **Browser Web API** | HTML5 Geolocation API, Media Capture API (`capture="environment"`), & Web Share API. |
| **Deployment & Local Testing** | Cloud Server berprotokol HTTPS (wajib untuk izin kamera & GPS browser), Laragon / Node Local + Cloudflare Tunnels (`cloudflared`). |

---

## 5. Non-Functional Requirements (NFR) & Etika Digital

| Kategori | Requirement |
| :--- | :--- |
| **Performance** | Perhitungan bobot dan *rendering* perbandingan dua rute (*Tercepat* vs *SafePass*) pada wilayah percontohan wajib tampil di layar dalam waktu **< 2.5 detik** pada jaringan normal. |
| **Security & Anti-Spam** | Endpoint pelaporan dilindungi *Rate Limiting* (maksimal 3 laporan per jam per akun/IP), proteksi CSRF, enkripsi password (`bcrypt`), serta seluruh kunci API wajib disimpan di `.env` (tidak di-*push* ke repositori GitHub). |
| **Responsiveness** | Antarmuka pengguna dirancang dengan pendekatan *Mobile-First Design* agar seluruh tombol utama mudah dijangkau satu tangan saat berada di luar ruang. |
| **Digital Ethics, Anti-Stigma, & Disclaimer** | 1. **Bahasa Objektif:** Menggunakan label kondisi fisik yang dapat ditindaklanjuti (*"Penerangan Padam / Terbatas"*, *"Kondisi Area Sepi / Perlu Perhatian"*, *"Hambatan Fisik Jalan"*) untuk mencegah stigmatisasi permanen pada wilayah tertentu.<br>2. **Privasi:** Identitas pelapor disamarkan di peta publik dan foto berwajah/plat nomor ditolak otomatis oleh AI.<br>3. **Penafian Wajib:** Setiap tampilan rute *SafePass* wajib menyertakan teks penafian bahwa rute tersebut adalah alat bantu informasi berbasis komunitas, bukan jaminan keselamatan absolut, dan tidak menggantikan layanan darurat. |