<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="TitikAman membantu warga memilih rute yang lebih aman berdasarkan laporan komunitas, Safety Hub, dan data lapangan.">
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
        :root {
            --ink: #070b14;
            --panel: #0c1322;
            --line: rgba(148, 163, 184, .14);
            --muted: #91a0b7;
            --green: #2fa33e;
            --lime: #80c826;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            background: var(--ink);
            color: #f8fafc;
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
        }
        body::before {
            background-image: linear-gradient(rgba(148, 163, 184, .045) 1px, transparent 1px), linear-gradient(90deg, rgba(148, 163, 184, .045) 1px, transparent 1px);
            background-size: 64px 64px;
            content: '';
            inset: 0;
            mask-image: linear-gradient(to bottom, black 0%, transparent 62%);
            pointer-events: none;
            position: fixed;
            z-index: -1;
        }
        .glass {
            background: rgba(7, 11, 20, .72);
            backdrop-filter: blur(18px);
            border: 1px solid var(--line);
        }
        .container-wide { margin: 0 auto; max-width: 1180px; padding: 0 1.25rem; }
        .eyebrow { color: #80c826; font-size: .68rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; }
        .muted { color: var(--muted); }
        .grid-glow { position: relative; }
        .grid-glow::after {
            background: radial-gradient(circle, rgba(47, 163, 62, .2), transparent 68%);
            content: '';
            height: 28rem;
            pointer-events: none;
            position: absolute;
            right: -9rem;
            top: -8rem;
            width: 28rem;
            z-index: -1;
        }
        .gradient-button {
            background: linear-gradient(135deg, #11653a 0%, #2fa33e 55%, #80c826 100%);
            box-shadow: 0 10px 26px rgba(47, 163, 62, .2);
        }
        .gradient-button:hover { filter: brightness(1.08); transform: translateY(-1px); }
        .ghost-button:hover { background: rgba(255,255,255,.07); }
        .map-panel {
            background: linear-gradient(145deg, rgba(17, 101, 58, .2), rgba(12, 19, 34, .96) 45%);
            border: 1px solid rgba(128, 200, 38, .22);
            box-shadow: 0 30px 80px rgba(0,0,0,.3);
        }
        .map-lines {
            background-image: linear-gradient(26deg, transparent 47%, rgba(128, 200, 38, .2) 48%, rgba(128, 200, 38, .2) 49%, transparent 50%), linear-gradient(105deg, transparent 43%, rgba(96, 165, 250, .18) 44%, rgba(96, 165, 250, .18) 45%, transparent 46%), radial-gradient(circle at 35% 35%, rgba(128, 200, 38, .13) 0 2px, transparent 3px), radial-gradient(circle at 68% 63%, rgba(248, 113, 113, .16) 0 2px, transparent 3px);
            background-size: 100% 100%, 100% 100%, 130px 110px, 170px 140px;
        }
        .route-line { border-top: 2px dashed #eab308; transform: rotate(12deg); }
        .route-line-safe { border-top: 3px solid #65c63e; transform: rotate(-17deg); }
        .pulse-dot { animation: pulse 2.4s infinite; }
        @keyframes pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(128, 200, 38, .4); } 50% { box-shadow: 0 0 0 10px rgba(128, 200, 38, 0); } }
        .feature-card { background: rgba(12, 19, 34, .78); border: 1px solid var(--line); transition: border-color .2s, transform .2s, background .2s; }
        .feature-card:hover { background: rgba(18, 30, 48, .9); border-color: rgba(128, 200, 38, .38); transform: translateY(-3px); }
        .section-rule { border-top: 1px solid var(--line); }
        .zone-bar { background: linear-gradient(90deg, #2fa33e 0 33%, #eab308 33% 66%, #ef4444 66%); }
        .footer-link:hover { color: #b6e76b; }
        @media (max-width: 767px) {
            .desktop-nav { display: none; }
            .hero-title { font-size: 2.45rem; line-height: 1.08; }
        }
    </style>
</head>
<body>
    <header class="glass sticky top-0 z-50 border-x-0 border-t-0">
        <nav class="container-wide flex h-20 items-center justify-between gap-6" aria-label="Navigasi utama">
            <a href="{{ route('tentang') }}" class="shrink-0 no-underline">
                <x-brand-logo id="nav-logo" />
            </a>
            <div class="desktop-nav hidden items-center gap-6 text-sm text-slate-400 md:flex">
                <a class="transition hover:text-white" href="#fitur">Fitur</a>
                <a class="transition hover:text-white" href="#cara-kerja">Cara Kerja</a>
                <a class="transition hover:text-white" href="#zona-keamanan">Zona Keamanan</a>
                <a class="transition hover:text-white" href="#wilayah">Wilayah</a>
                <a class="transition hover:text-white" href="{{ route('admin') }}">Admin</a>
            </div>
            <a href="{{ route('app') }}" class="gradient-button rounded-full px-4 py-2.5 text-xs font-extrabold text-white no-underline transition sm:px-5 sm:text-sm">Buka Peta <span aria-hidden="true">↗</span></a>
        </nav>
    </header>

    <main>
        <section class="grid-glow container-wide grid gap-12 pb-24 pt-20 md:grid-cols-[1.05fr_.95fr] md:items-center md:pb-32 md:pt-28">
            <div>
                <div class="eyebrow mb-5 flex items-center gap-2"><span class="h-px w-8 bg-lime-400"></span> Civic safety grid</div>
                <h1 class="hero-title max-w-3xl text-5xl font-extrabold leading-tight tracking-[-.045em] text-white md:text-7xl">Jangan Sekadar Pilih Rute Tercepat.<br><span class="bg-gradient-to-r from-lime-300 via-green-400 to-emerald-500 bg-clip-text text-transparent">Pilih Rute Paling Aman.</span></h1>
                <p class="muted mt-7 max-w-xl text-base leading-8 md:text-lg">TitikAman mengubah laporan warga, pencahayaan jalan, dan Safety Hub menjadi keputusan perjalanan yang lebih cerdas — sebelum kamu berangkat.</p>
                <div class="mt-9 flex flex-wrap gap-3">
                    <a href="{{ route('app') }}" class="gradient-button rounded-full px-6 py-3.5 text-sm font-extrabold text-white no-underline transition">Buka Peta TitikAman <span aria-hidden="true">↗</span></a>
                    <a href="{{ route('rute') }}" class="ghost-button rounded-full border border-slate-700 px-6 py-3.5 text-sm font-bold text-slate-200 no-underline transition">Cek Rute Saya <span aria-hidden="true">→</span></a>
                </div>
                <div class="mt-10 flex items-center gap-3 text-xs text-slate-500"><span class="pulse-dot h-2.5 w-2.5 rounded-full bg-lime-400"></span><span>Monitoring komunitas aktif di 3 wilayah</span></div>
            </div>

            <div class="map-panel relative overflow-hidden rounded-[2rem] p-5 md:p-7" aria-label="Simulator perbandingan rute interaktif">
                <div class="map-lines relative h-[24rem] overflow-hidden rounded-3xl border border-white/10 bg-[#0a1525] p-5 sm:h-[29rem]">
                    <div class="absolute left-5 top-5 flex items-center gap-2 text-[10px] font-bold uppercase tracking-[.16em] text-slate-500"><span class="h-2 w-2 rounded-full bg-lime-400"></span> Live route telemetry</div>
                    <div class="absolute left-[18%] top-[35%] h-3 w-3 rounded-full border-2 border-white bg-lime-400"></div>
                    <div class="absolute right-[20%] top-[58%] h-3 w-3 rounded-full border-2 border-white bg-red-400"></div>
                    <div class="absolute bottom-[19%] left-[45%] h-3 w-3 rounded-full border-2 border-white bg-blue-400"></div>
                    <div id="fast-route-line" class="route-line absolute left-[19%] top-[46%] w-[65%] transition-all"></div>
                    <div id="safe-route-line" class="route-line-safe absolute left-[19%] top-[45%] w-[55%] opacity-0 transition-all"></div>
                    <div class="absolute bottom-5 left-5 right-5 rounded-2xl border border-white/10 bg-[#070b14]/80 p-4 backdrop-blur">
                        <div class="mb-4 flex items-center justify-between gap-3"><div><p class="m-0 text-[10px] font-bold uppercase tracking-[.15em] text-slate-500">Simulator</p><p id="route-mode-label" class="mt-1 text-sm font-bold text-white">Rute Tercepat Biasa</p></div><div class="flex rounded-full bg-white/5 p-1 text-[10px] font-bold"><button id="fast-route-toggle" type="button" class="rounded-full bg-slate-700 px-3 py-1.5 text-white">Tercepat</button><button id="safe-route-toggle" type="button" class="rounded-full px-3 py-1.5 text-slate-400">SafePass</button></div></div>
                        <div class="grid grid-cols-3 gap-2 text-xs"><div><span class="block text-slate-500">Durasi</span><strong id="route-duration" class="mt-1 block text-white">18 menit</strong></div><div><span class="block text-slate-500">Risiko</span><strong id="route-risk" class="mt-1 block text-yellow-400">Sedang</strong></div><div><span class="block text-slate-500">Safety Hub</span><strong id="route-hubs" class="mt-1 block text-white">0 dilewati</strong></div></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="fitur" class="container-wide section-rule py-20 md:py-28">
            <div class="mb-10 flex flex-col justify-between gap-5 md:flex-row md:items-end"><div><p class="eyebrow">One grid, many signals</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight md:text-5xl">Sinyal keamanan,<br>dirangkai jadi aksi.</h2></div><p class="muted max-w-sm text-sm leading-7">Bukan sekadar peta. TitikAman menyatukan konteks lapangan agar setiap keputusan terasa lebih terukur.</p></div>
            <div class="grid gap-4 md:grid-cols-12">
                <article class="feature-card rounded-3xl p-7 md:col-span-7 md:min-h-[20rem]"><div class="mb-12 flex items-start justify-between"><span class="rounded-2xl bg-lime-400/10 p-3 text-2xl text-lime-300">⌁</span><span class="eyebrow">01 / routing</span></div><h3 class="text-2xl font-extrabold">AI SafePass Routing</h3><p class="muted mt-3 max-w-lg text-sm leading-7">Bandingkan rute tercepat dengan jalur yang mempertimbangkan area gelap, laporan warga, dan titik aman yang aktif.</p><a href="{{ route('rute') }}" class="mt-6 inline-block text-sm font-bold text-lime-300 no-underline hover:text-lime-200">Eksplorasi SafePass →</a></article>
                <article class="feature-card rounded-3xl p-7 md:col-span-5 md:min-h-[20rem]"><div class="mb-12 flex items-start justify-between"><span class="rounded-2xl bg-blue-400/10 p-3 text-2xl text-blue-300">◈</span><span class="eyebrow">02 / vision</span></div><h3 class="text-2xl font-extrabold">Verifikasi Foto AI Gemini</h3><p class="muted mt-3 text-sm leading-7">Foto kondisi jalan dianalisis sebelum masuk antrean moderasi, sehingga sinyal yang sampai ke komunitas lebih relevan.</p></article>
                <article class="feature-card rounded-3xl p-7 md:col-span-5"><div class="mb-10 flex items-start justify-between"><span class="rounded-2xl bg-emerald-400/10 p-3 text-2xl text-emerald-300">⌖</span><span class="eyebrow">03 / safe nodes</span></div><h3 class="text-xl font-extrabold">Direktori Safety Hub</h3><p class="muted mt-3 text-sm leading-7">Temukan pos polisi, satpam, faskes, dan halte aktif di sekitar perjalananmu.</p><a href="/safety-hubs-ui" class="mt-5 inline-block text-sm font-bold text-lime-300 no-underline hover:text-lime-200">Lihat direktori →</a></article>
                <article class="feature-card rounded-3xl p-7 md:col-span-7"><div class="mb-10 flex items-start justify-between"><span class="rounded-2xl bg-red-400/10 p-3 text-2xl text-red-300">!</span><span class="eyebrow">04 / response</span></div><h3 class="text-xl font-extrabold">SOS 110 + Live Companion</h3><p class="muted mt-3 text-sm leading-7">Akses sinyal darurat dan bagikan perjalanan Live Companion agar orang terdekat tahu jalurmu secara langsung.</p></article>
            </div>
        </section>

        <section id="cara-kerja" class="container-wide section-rule py-20 md:py-28">
            <div class="grid gap-12 md:grid-cols-[.8fr_1.2fr]"><div><p class="eyebrow">Designed for the real world</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight md:text-5xl">Tiga langkah.<br>Satu perjalanan lebih siap.</h2></div><div class="grid gap-8 sm:grid-cols-3"><div><span class="text-4xl font-extrabold text-lime-400">01</span><h3 class="mt-5 font-bold">Pilih tujuan</h3><p class="muted mt-2 text-sm leading-6">Tentukan asal, tujuan, dan moda perjalananmu.</p></div><div><span class="text-4xl font-extrabold text-lime-400">02</span><h3 class="mt-5 font-bold">Bandingkan sinyal</h3><p class="muted mt-2 text-sm leading-6">Lihat perbedaan rute tercepat dan SafePass.</p></div><div><span class="text-4xl font-extrabold text-lime-400">03</span><h3 class="mt-5 font-bold">Berangkat lebih siap</h3><p class="muted mt-2 text-sm leading-6">Ikuti jalur dengan konteks keamanan yang jelas.</p></div></div></div>
        </section>

        <section id="zona-keamanan" class="container-wide section-rule py-20 md:py-28">
            <div class="rounded-[2rem] border border-white/10 bg-[#0c1322] p-7 md:p-10"><div class="grid gap-10 md:grid-cols-[.8fr_1.2fr] md:items-center"><div><p class="eyebrow">Read the street</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight md:text-4xl">Indikator zona keamanan.</h2><p class="muted mt-4 text-sm leading-7">Setiap warna membantu membaca konteks secara cepat. Gunakan sebagai informasi pendukung, bukan pengganti kewaspadaan.</p></div><div><div class="zone-bar h-2 rounded-full"></div><div class="mt-5 grid grid-cols-3 gap-4 text-sm"><div><span class="mb-2 inline-block h-2 w-2 rounded-full bg-green-400"></span><h3 class="font-bold">Hijau</h3><p class="muted mt-1 text-xs leading-5">Sinyal aman dan Safety Hub aktif.</p></div><div><span class="mb-2 inline-block h-2 w-2 rounded-full bg-yellow-400"></span><h3 class="font-bold">Kuning</h3><p class="muted mt-1 text-xs leading-5">Perlu perhatian dan kewaspadaan.</p></div><div><span class="mb-2 inline-block h-2 w-2 rounded-full bg-red-400"></span><h3 class="font-bold">Merah</h3><p class="muted mt-1 text-xs leading-5">Banyak sinyal risiko terdeteksi.</p></div></div></div></div></div>
        </section>

        <section id="wilayah" class="container-wide section-rule py-20 md:py-28">
            <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end"><div><p class="eyebrow">Active coverage</p><h2 class="mt-3 text-3xl font-extrabold tracking-tight md:text-5xl">Wilayah aktif.</h2></div><p class="muted max-w-sm text-sm leading-7">Mulai dari lingkungan yang dekat. Perluas dampak dengan satu laporan yang bertanggung jawab.</p></div>
            <div class="mt-10 grid gap-4 md:grid-cols-3"><div class="feature-card rounded-2xl p-5"><span class="eyebrow">Surabaya / 01</span><h3 class="mt-8 font-bold">UNESA Lidah Wetan</h3><p class="muted mt-2 text-xs">Grid komunitas aktif</p></div><div class="feature-card rounded-2xl p-5"><span class="eyebrow">Surabaya / 02</span><h3 class="mt-8 font-bold">UNESA Ketintang</h3><p class="muted mt-2 text-xs">Grid komunitas aktif</p></div><div class="feature-card rounded-2xl p-5"><span class="eyebrow">Jakarta / 03</span><h3 class="mt-8 font-bold">Rawamangun</h3><p class="muted mt-2 text-xs">Grid komunitas aktif</p></div></div>
        </section>

        <section class="container-wide pb-24 pt-10 md:pb-32"><div class="relative overflow-hidden rounded-[2rem] border border-lime-400/20 bg-gradient-to-br from-[#123c2a] via-[#0c1d27] to-[#0c1322] p-8 md:p-14"><div class="relative z-10 max-w-2xl"><p class="eyebrow">Make your next move count</p><h2 class="mt-4 text-3xl font-extrabold tracking-tight md:text-5xl">Jalan lebih siap, bersama komunitas.</h2><p class="muted mt-5 max-w-lg leading-7">Buka peta untuk mulai menjelajah atau bantu warga lain dengan satu laporan kondisi yang akurat.</p><div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('app') }}" class="gradient-button rounded-full px-6 py-3.5 text-sm font-extrabold text-white no-underline transition">Buka Peta TitikAman</a><a href="{{ route('lapor') }}" class="ghost-button rounded-full border border-slate-600 px-6 py-3.5 text-sm font-bold text-slate-200 no-underline transition">Lapor Kondisi</a></div></div></div></section>
    </main>

    <footer class="border-t border-white/10">
        <div class="container-wide grid gap-10 py-12 md:grid-cols-[1fr_auto_auto] md:items-start"><div><a href="{{ route('tentang') }}" class="inline-block no-underline"><x-brand-logo id="footer-logo" size="sm" /></a><p class="muted mt-5 max-w-xs text-xs leading-6">Civic safety grid untuk membantu perjalanan warga menjadi lebih terinformasi.</p></div><div><p class="eyebrow mb-4">Explore</p><div class="grid gap-3 text-sm text-slate-400"><a class="footer-link transition" href="{{ route('app') }}">Peta Warga</a><a class="footer-link transition" href="{{ route('rute') }}">Rute Saya</a><a class="footer-link transition" href="/safety-hubs-ui">Safety Hub</a></div></div><div><p class="eyebrow mb-4">Connect</p><div class="grid gap-3 text-sm text-slate-400"><a class="footer-link transition" href="{{ route('lapor') }}">Lapor Kondisi</a><a class="footer-link transition" href="{{ route('admin') }}">Admin</a><a class="footer-link transition" href="tel:110">Darurat 110</a></div></div></div>
        <div class="container-wide flex flex-col justify-between gap-3 border-t border-white/10 py-5 text-[11px] text-slate-600 sm:flex-row"><span>© {{ date('Y') }} TitikAman. Dibangun untuk komunitas.</span><span>Data adalah sinyal. Kewaspadaan tetap utama.</span></div>
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

            function setRoute(mode) {
                const safe = mode === 'safe';
                label.textContent = safe ? 'Rute SafePass TitikAman' : 'Rute Tercepat Biasa';
                duration.textContent = safe ? '22 menit' : '18 menit';
                risk.textContent = safe ? 'Rendah' : 'Sedang';
                risk.className = `mt-1 block ${safe ? 'text-lime-400' : 'text-yellow-400'}`;
                hubs.textContent = safe ? '2 dilewati' : '0 dilewati';
                fastLine.classList.toggle('opacity-0', safe);
                safeLine.classList.toggle('opacity-0', !safe);
                fastToggle.className = safe ? 'rounded-full px-3 py-1.5 text-slate-400' : 'rounded-full bg-slate-700 px-3 py-1.5 text-white';
                safeToggle.className = safe ? 'rounded-full bg-lime-500/20 px-3 py-1.5 text-lime-300' : 'rounded-full px-3 py-1.5 text-slate-400';
            }

            fastToggle?.addEventListener('click', () => setRoute('fast'));
            safeToggle?.addEventListener('click', () => setRoute('safe'));
        })();
    </script>
</body>
</html>
