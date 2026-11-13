<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="TitikAman membantu warga memilih rute yang lebih aman berdasarkan laporan komunitas dan Safety Hub.">
    <meta property="og:title" content="TitikAman — Pilih Rute Paling Aman">
    <meta property="og:description" content="Navigasi komunitas untuk perjalanan yang lebih aman.">
    <meta property="og:image" content="{{ asset('images/TitikAman.png') }}">
    <meta property="og:type" content="website">
    <link rel="icon" type="image/png" href="{{ asset('images/TitikAman(no-teks).png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <title>TitikAman — Pilih Rute Paling Aman</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html { scroll-behavior: smooth; }
        body {
            background-color: #f8fafc;
            background-image: linear-gradient(rgba(15, 23, 42, .04) 1px, transparent 1px), linear-gradient(90deg, rgba(15, 23, 42, .04) 1px, transparent 1px);
            background-size: 64px 64px;
            color: #0f172a;
            font-family: "Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif;
        }
        .hero-glow { background: radial-gradient(circle at 70% 20%, rgba(128, 200, 38, .18), transparent 42%); }
        .route-map { background: linear-gradient(145deg, #f0fdf4, #eff6ff); }
        .route-line { stroke-dasharray: 8 8; }
        .gradient-brand { background: linear-gradient(135deg, #11653a, #2fa33e 55%, #80c826); }
        .mobile-menu[hidden] { display: none; }
    </style>
</head>
<body>
    <header class="sticky top-0 z-50 h-20 border-b border-slate-200/80 bg-white/90 backdrop-blur-md">
        <nav class="mx-auto flex h-full max-w-7xl items-center justify-between gap-6 px-5 lg:px-8" aria-label="Navigasi utama">
            <a href="{{ route('tentang') }}" class="shrink-0 no-underline"><x-brand-logo id="nav-logo" theme="light" /></a>
            <div class="hidden items-center gap-7 text-sm font-semibold text-slate-600 lg:flex">
                <a class="transition hover:text-emerald-700" href="#fitur">Fitur</a>
                <a class="transition hover:text-emerald-700" href="#cara-kerja">Cara Kerja</a>
                <a class="transition hover:text-emerald-700" href="#zona-keamanan">Zona Keamanan</a>
                <a class="transition hover:text-emerald-700" href="#wilayah">Wilayah</a>
                <a class="transition hover:text-emerald-700" href="{{ route('admin') }}">Admin</a>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('app') }}" class="gradient-brand rounded-full px-4 py-2.5 text-xs font-extrabold text-white shadow-lg shadow-emerald-900/10 no-underline transition hover:-translate-y-0.5 sm:px-5 sm:text-sm">Buka Peta <span aria-hidden="true">↗</span></a>
                <button id="mobile-menu-toggle" type="button" class="rounded-full border border-slate-200 p-2.5 text-slate-700 lg:hidden" aria-expanded="false" aria-controls="mobile-menu" aria-label="Buka menu"><span class="text-lg">☰</span></button>
            </div>
        </nav>
        <div id="mobile-menu" class="mobile-menu mx-5 border-t border-slate-200 bg-white py-4 lg:hidden" hidden>
            <div class="grid gap-3 text-sm font-semibold text-slate-600">
                <a class="py-1 hover:text-emerald-700" href="#fitur">Fitur</a>
                <a class="py-1 hover:text-emerald-700" href="#cara-kerja">Cara Kerja</a>
                <a class="py-1 hover:text-emerald-700" href="#zona-keamanan">Zona Keamanan</a>
                <a class="py-1 hover:text-emerald-700" href="#wilayah">Wilayah</a>
                <a class="py-1 hover:text-emerald-700" href="{{ route('admin') }}">Admin</a>
            </div>
        </div>
    </header>

    <main>
        <section class="hero-glow mx-auto grid max-w-7xl gap-12 px-5 pb-20 pt-16 md:grid-cols-[1.05fr_.95fr] md:items-center md:px-8 md:pb-28 md:pt-24">
            <div>
                <p class="mb-6 flex items-center gap-2 text-xs font-extrabold uppercase tracking-[.18em] text-emerald-700"><span class="h-px w-8 bg-emerald-600"></span>Civic Safety Grid</p>
                <h1 class="max-w-3xl text-4xl font-extrabold leading-tight tracking-[-.045em] text-slate-900 sm:text-5xl md:text-6xl">Jangan Sekadar Pilih Rute Tercepat.<br><span class="bg-gradient-to-r from-[#11653A] via-[#2FA33E] to-[#80C826] bg-clip-text text-transparent">Pilih Rute Paling Aman.</span></h1>
                <p class="mt-7 max-w-xl text-base leading-8 text-slate-600 md:text-lg">TitikAman merangkai laporan warga, konteks jalan, dan Safety Hub menjadi keputusan perjalanan yang lebih cerdas sebelum kamu berangkat.</p>
                <div class="mt-9 flex flex-wrap gap-3">
                    <a href="{{ route('app') }}" class="gradient-brand rounded-full px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-emerald-900/10 no-underline transition hover:-translate-y-0.5">Buka Peta TitikAman <span aria-hidden="true">↗</span></a>
                    <a href="{{ route('rute') }}" class="rounded-full border border-slate-300 bg-white px-6 py-3.5 text-sm font-bold text-slate-700 no-underline transition hover:border-emerald-600 hover:text-emerald-700">Cek Rute Saya <span aria-hidden="true">→</span></a>
                </div>
                <p class="mt-10 flex items-center gap-3 text-xs text-slate-500"><span class="h-2.5 w-2.5 rounded-full bg-lime-500 shadow-[0_0_0_7px_rgba(101,163,13,.12)]"></span>Monitoring komunitas aktif di 3 wilayah</p>
            </div>

            <div class="rounded-3xl border border-slate-200/90 bg-white p-4 shadow-xl shadow-slate-900/10 md:p-6" aria-label="Simulator perbandingan rute interaktif">
                <div class="route-map relative h-[24rem] overflow-hidden rounded-2xl border border-slate-200 p-4 sm:h-[29rem]">
                    <div class="absolute left-5 top-5 z-10 flex items-center gap-2 text-[10px] font-extrabold uppercase tracking-[.16em] text-slate-500"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Live route telemetry</div>
                    <svg class="absolute inset-0 h-full w-full" viewBox="0 0 500 520" fill="none" aria-hidden="true">
                        <path d="M25 120C125 80 125 205 220 165S320 80 480 125M-15 350C80 270 170 390 260 305S370 230 520 285M75 0C125 110 75 250 155 520M355-10C315 100 390 230 325 530" stroke="#cbd5e1" stroke-width="2"/>
                        <path d="M20 220C105 180 130 300 225 245S340 160 495 210M110 520C175 400 155 280 245 190S390 85 450 0" stroke="#bbf7d0" stroke-width="4"/>
                        <path id="fast-route-line" class="route-line transition-opacity duration-300" d="M95 185C190 220 260 240 390 345" stroke="#eab308" stroke-width="6"/>
                        <path id="safe-route-line" class="transition-opacity duration-300" d="M95 185C150 300 235 355 300 315S350 300 390 345" stroke="#15803d" stroke-width="7" opacity="0"/>
                        <circle cx="95" cy="185" r="10" fill="#15803d" stroke="white" stroke-width="4"/><circle cx="390" cy="345" r="10" fill="#dc2626" stroke="white" stroke-width="4"/><circle cx="300" cy="315" r="8" fill="#2563eb" stroke="white" stroke-width="3"/>
                    </svg>
                    <div class="absolute bottom-4 left-4 right-4 rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-lg backdrop-blur">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-3"><div><p class="m-0 text-[10px] font-extrabold uppercase tracking-[.15em] text-slate-400">Simulator</p><p id="route-mode-label" class="mt-1 text-sm font-bold text-slate-900">Rute Tercepat Biasa</p></div><div class="flex rounded-full bg-slate-100 p-1 text-[10px] font-bold"><button id="fast-route-toggle" type="button" class="rounded-full bg-slate-700 px-3 py-1.5 text-white">Tercepat</button><button id="safe-route-toggle" type="button" class="rounded-full px-3 py-1.5 text-slate-500">SafePass</button></div></div>
                        <div class="grid grid-cols-3 gap-3 text-xs"><div><span class="block text-slate-500">Durasi</span><strong id="route-duration" class="mt-1 block text-slate-900">18 menit</strong></div><div><span class="block text-slate-500">Risiko</span><strong id="route-risk" class="mt-1 block text-yellow-600">Sedang</strong></div><div><span class="block text-slate-500">Safety Hub</span><strong id="route-hubs" class="mt-1 block text-slate-900">0 dilewati</strong></div></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="fitur" class="mx-auto max-w-7xl scroll-mt-20 px-5 py-20 md:px-8 md:py-28">
            <div class="mb-12 flex flex-col justify-between gap-5 md:flex-row md:items-end md:mb-16"><div><p class="text-xs font-extrabold uppercase tracking-[.18em] text-emerald-700">One grid, many signals</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 md:text-5xl">Sinyal keamanan,<br>dirangkai jadi aksi.</h2></div><p class="max-w-sm text-sm leading-7 text-slate-600">Bukan sekadar peta. TitikAman menyatukan konteks lapangan agar keputusan terasa lebih terukur.</p></div>
            <div class="grid gap-6 md:grid-cols-12 md:gap-8">
                <article class="rounded-2xl border border-slate-200/90 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-md md:col-span-7 md:min-h-[20rem] md:p-9"><div class="mb-12 flex items-start justify-between"><span class="rounded-2xl bg-emerald-50 p-3 text-2xl text-emerald-700">⌁</span><span class="text-xs font-extrabold uppercase tracking-[.16em] text-emerald-700">01 / routing</span></div><h3 class="text-2xl font-extrabold text-slate-900">AI SafePass Routing</h3><p class="mt-3 max-w-lg text-sm leading-7 text-slate-600">Bandingkan rute tercepat dengan jalur yang mempertimbangkan area gelap, laporan warga, dan titik aman yang aktif.</p><a href="{{ route('rute') }}" class="mt-6 inline-block text-sm font-bold text-emerald-700 no-underline hover:text-emerald-900">Eksplorasi SafePass →</a></article>
                <article class="rounded-2xl border border-slate-200/90 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-md md:col-span-5 md:min-h-[20rem] md:p-9"><div class="mb-12 flex items-start justify-between"><span class="rounded-2xl bg-blue-50 p-3 text-2xl text-blue-700">◈</span><span class="text-xs font-extrabold uppercase tracking-[.16em] text-emerald-700">02 / vision</span></div><h3 class="text-2xl font-extrabold text-slate-900">Verifikasi Foto AI Gemini</h3><p class="mt-3 text-sm leading-7 text-slate-600">Foto kondisi jalan dianalisis sebelum masuk antrean moderasi agar sinyal komunitas tetap relevan.</p></article>
                <article class="rounded-2xl border border-slate-200/90 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-md md:col-span-5 md:p-9"><div class="mb-10 flex items-start justify-between"><span class="rounded-2xl bg-lime-50 p-3 text-2xl text-lime-700">⌖</span><span class="text-xs font-extrabold uppercase tracking-[.16em] text-emerald-700">03 / safe nodes</span></div><h3 class="text-xl font-extrabold text-slate-900">Direktori Safety Hub</h3><p class="mt-3 text-sm leading-7 text-slate-600">Temukan pos polisi, satpam, faskes, dan halte aktif di sekitar perjalananmu.</p><a href="/safety-hubs-ui" class="mt-5 inline-block text-sm font-bold text-emerald-700 no-underline hover:text-emerald-900">Lihat direktori →</a></article>
                <article class="rounded-2xl border border-slate-200/90 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-md md:col-span-7 md:p-9"><div class="mb-10 flex items-start justify-between"><span class="rounded-2xl bg-red-50 p-3 text-2xl text-red-700">!</span><span class="text-xs font-extrabold uppercase tracking-[.16em] text-emerald-700">04 / response</span></div><h3 class="text-xl font-extrabold text-slate-900">SOS 110 + Live Companion</h3><p class="mt-3 text-sm leading-7 text-slate-600">Akses sinyal darurat dan bagikan perjalanan Live Companion agar orang terdekat tahu jalurmu.</p></article>
            </div>
        </section>

        <section id="cara-kerja" class="mx-auto max-w-7xl scroll-mt-20 px-5 py-20 md:px-8 md:py-28">
            <div class="mb-12 grid gap-12 md:mb-16 md:grid-cols-[.8fr_1.2fr]"><div><p class="text-xs font-extrabold uppercase tracking-[.18em] text-emerald-700">Designed for the real world</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 md:text-5xl">Tiga langkah.<br>Satu perjalanan lebih siap.</h2></div><div class="grid gap-6 sm:grid-cols-3 md:gap-8"><div class="rounded-2xl border border-slate-200/90 bg-white p-7 shadow-sm md:p-9"><span class="text-4xl font-extrabold text-emerald-700">01</span><h3 class="mt-5 font-bold text-slate-900">Pilih tujuan</h3><p class="mt-2 text-sm leading-6 text-slate-600">Tentukan asal, tujuan, dan moda perjalananmu.</p></div><div class="rounded-2xl border border-slate-200/90 bg-white p-7 shadow-sm md:p-9"><span class="text-4xl font-extrabold text-emerald-700">02</span><h3 class="mt-5 font-bold text-slate-900">Bandingkan sinyal</h3><p class="mt-2 text-sm leading-6 text-slate-600">Lihat perbedaan rute tercepat dan SafePass.</p></div><div class="rounded-2xl border border-slate-200/90 bg-white p-7 shadow-sm md:p-9"><span class="text-4xl font-extrabold text-emerald-700">03</span><h3 class="mt-5 font-bold text-slate-900">Berangkat lebih siap</h3><p class="mt-2 text-sm leading-6 text-slate-600">Ikuti jalur dengan konteks keamanan yang jelas.</p></div></div></div>
        </section>

        <section id="zona-keamanan" class="mx-auto max-w-7xl scroll-mt-20 px-5 py-20 md:px-8 md:py-28">
            <div class="rounded-2xl border border-slate-200/90 bg-white p-7 shadow-sm md:p-9"><div class="grid gap-10 md:grid-cols-[.8fr_1.2fr] md:items-center"><div><p class="text-xs font-extrabold uppercase tracking-[.18em] text-emerald-700">Read the street</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl">Indikator zona keamanan.</h2><p class="mt-4 text-sm leading-7 text-slate-600">Setiap warna membantu membaca konteks secara cepat. Gunakan sebagai informasi pendukung, bukan pengganti kewaspadaan.</p></div><div><div class="h-2 rounded-full bg-gradient-to-r from-green-500 via-yellow-400 to-red-500"></div><div class="mt-6 grid grid-cols-3 gap-6 text-sm"><div><span class="mb-2 inline-block h-2 w-2 rounded-full bg-green-500"></span><h3 class="font-bold text-slate-900">Hijau</h3><p class="mt-1 text-xs leading-5 text-slate-600">Sinyal aman dan Safety Hub aktif.</p></div><div><span class="mb-2 inline-block h-2 w-2 rounded-full bg-yellow-500"></span><h3 class="font-bold text-slate-900">Kuning</h3><p class="mt-1 text-xs leading-5 text-slate-600">Perlu perhatian dan kewaspadaan.</p></div><div><span class="mb-2 inline-block h-2 w-2 rounded-full bg-red-500"></span><h3 class="font-bold text-slate-900">Merah</h3><p class="mt-1 text-xs leading-5 text-slate-600">Banyak sinyal risiko terdeteksi.</p></div></div></div></div></div>
        </section>

        <section id="wilayah" class="mx-auto max-w-7xl scroll-mt-20 px-5 py-20 md:px-8 md:py-28">
            <div class="mb-12 flex flex-col justify-between gap-6 md:mb-16 md:flex-row md:items-end"><div><p class="text-xs font-extrabold uppercase tracking-[.18em] text-emerald-700">Active coverage</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 md:text-5xl">Wilayah aktif.</h2></div><p class="max-w-sm text-sm leading-7 text-slate-600">Mulai dari lingkungan yang dekat. Perluas dampak dengan satu laporan yang bertanggung jawab.</p></div>
            <div class="grid gap-6 md:grid-cols-3 md:gap-8"><div class="rounded-2xl border border-slate-200/90 bg-white p-7 shadow-sm md:p-9"><span class="text-xs font-extrabold uppercase tracking-[.16em] text-emerald-700">Surabaya / 01</span><h3 class="mt-8 font-bold text-slate-900">UNESA Lidah Wetan</h3><p class="mt-2 text-xs text-slate-600">Grid komunitas aktif</p></div><div class="rounded-2xl border border-slate-200/90 bg-white p-7 shadow-sm md:p-9"><span class="text-xs font-extrabold uppercase tracking-[.16em] text-emerald-700">Surabaya / 02</span><h3 class="mt-8 font-bold text-slate-900">UNESA Ketintang</h3><p class="mt-2 text-xs text-slate-600">Grid komunitas aktif</p></div><div class="rounded-2xl border border-slate-200/90 bg-white p-7 shadow-sm md:p-9"><span class="text-xs font-extrabold uppercase tracking-[.16em] text-emerald-700">Jakarta / 03</span><h3 class="mt-8 font-bold text-slate-900">Rawamangun</h3><p class="mt-2 text-xs text-slate-600">Grid komunitas aktif</p></div></div>
            <div class="mt-16 rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-950 via-emerald-900 to-lime-800 p-8 text-white shadow-lg md:mt-24 md:p-14"><p class="text-xs font-extrabold uppercase tracking-[.18em] text-lime-200">Make your next move count</p><h2 class="mt-4 text-3xl font-extrabold tracking-tight md:text-5xl">Jalan lebih siap, bersama komunitas.</h2><p class="mt-5 max-w-lg leading-7 text-emerald-50/80">Buka peta untuk mulai menjelajah atau bantu warga lain dengan satu laporan kondisi yang akurat.</p><div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('app') }}" class="rounded-full bg-white px-6 py-3.5 text-sm font-extrabold text-emerald-900 no-underline transition hover:bg-emerald-50">Buka Peta TitikAman</a><a href="{{ route('lapor') }}" class="rounded-full border border-white/40 px-6 py-3.5 text-sm font-bold text-white no-underline transition hover:bg-white/10">Lapor Kondisi</a></div></div>
        </section>
    </main>

    <footer class="mt-20 border-t border-slate-200 bg-white">
        <div class="mx-auto grid max-w-7xl gap-10 px-5 py-12 md:grid-cols-[1fr_auto_auto] md:px-8"><div><a href="{{ route('tentang') }}" class="inline-block no-underline"><x-brand-logo id="footer-logo" theme="light" size="sm" /></a><p class="mt-5 max-w-xs text-xs leading-6 text-slate-600">Civic safety grid untuk membantu perjalanan warga menjadi lebih terinformasi.</p></div><div><p class="mb-4 text-xs font-extrabold uppercase tracking-[.18em] text-emerald-700">Explore</p><div class="grid gap-3 text-sm text-slate-600"><a class="hover:text-emerald-700" href="{{ route('app') }}">Peta Warga</a><a class="hover:text-emerald-700" href="{{ route('rute') }}">Rute Saya</a><a class="hover:text-emerald-700" href="/safety-hubs-ui">Safety Hub</a></div></div><div><p class="mb-4 text-xs font-extrabold uppercase tracking-[.18em] text-emerald-700">Connect</p><div class="grid gap-3 text-sm text-slate-600"><a class="hover:text-emerald-700" href="{{ route('lapor') }}">Lapor Kondisi</a><a class="hover:text-emerald-700" href="{{ route('admin') }}">Admin</a><a class="hover:text-emerald-700" href="tel:110">Darurat 110</a></div></div></div>
        <div class="mx-auto flex max-w-7xl flex-col justify-between gap-3 border-t border-slate-200 px-5 py-5 text-[11px] text-slate-500 sm:flex-row md:px-8"><span>© {{ date('Y') }} TitikAman. Dibangun untuk komunitas.</span><span>Data adalah sinyal. Kewaspadaan tetap utama.</span></div>
    </footer>

    <script>
        (() => {
            const fastToggle = document.getElementById('fast-route-toggle');
            const safeToggle = document.getElementById('safe-route-toggle');
            const label = document.getElementById('route-mode-label');
            const duration = document.getElementById('route-duration');
            const risk = document.getElementById('route-risk');
            const hubs = document.getElementById('route-hubs');
            const fastLine = document.getElementById('fast-route-line');
            const safeLine = document.getElementById('safe-route-line');
            const menuToggle = document.getElementById('mobile-menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');

            function setRoute(mode) {
                const safe = mode === 'safe';
                label.textContent = safe ? 'Rute SafePass TitikAman' : 'Rute Tercepat Biasa';
                duration.textContent = safe ? '22 menit' : '18 menit';
                risk.textContent = safe ? 'Rendah' : 'Sedang';
                risk.className = `mt-1 block ${safe ? 'text-emerald-700' : 'text-yellow-600'}`;
                hubs.textContent = safe ? '2 dilewati' : '0 dilewati';
                fastLine.classList.toggle('opacity-0', safe);
                safeLine.setAttribute('opacity', safe ? '1' : '0');
                fastToggle.className = safe ? 'rounded-full px-3 py-1.5 text-slate-500' : 'rounded-full bg-slate-700 px-3 py-1.5 text-white';
                safeToggle.className = safe ? 'rounded-full bg-emerald-100 px-3 py-1.5 text-emerald-800' : 'rounded-full px-3 py-1.5 text-slate-500';
            }

            fastToggle?.addEventListener('click', () => setRoute('fast'));
            safeToggle?.addEventListener('click', () => setRoute('safe'));
            menuToggle?.addEventListener('click', () => {
                const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
                menuToggle.setAttribute('aria-expanded', String(!isOpen));
                mobileMenu.hidden = isOpen;
            });
            mobileMenu?.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
                mobileMenu.hidden = true;
                menuToggle?.setAttribute('aria-expanded', 'false');
            }));
        })();
    </script>
</body>
</html>
