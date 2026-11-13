<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" type="image/png" href="{{ asset('images/TitikAman(no-teks).png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<title>Masuk — TitikAman</title>
<style>
:root{--navy:#f8fafc;--navy2:#11653a;--green:#15803d;--blue:#15803d;--mute:#64748b;--line:#e2e8f0}
*{box-sizing:border-box}
body{margin:0;min-height:100vh;display:grid;grid-template-columns:1fr 1fr;font-family:"Plus Jakarta Sans","Instrument Sans",system-ui,-apple-system,"Segoe UI",sans-serif;color:#0f172a;background:#fff}
.side{background:#f8fafc;border-right:1px solid var(--line);padding:40px;display:flex;flex-direction:column;justify-content:space-between}
.brand{display:inline-flex;align-items:center;text-decoration:none;color:#0f172a}
.side h2{font-size:30px;line-height:1.2;margin:0 0 10px;color:#0f172a}.side p{color:#475569;margin:0;max-width:380px}
.side small{color:var(--mute);font-size:12px;max-width:380px}
.main{display:flex;align-items:center;justify-content:center;padding:32px}
.box{width:100%;max-width:380px}
.tabs{display:grid;grid-template-columns:1fr 1fr;background:#f1f5f9;border-radius:10px;padding:4px;margin-bottom:22px}
.tabs a{text-align:center;text-decoration:none;color:#475569;font-weight:700;padding:9px;border-radius:8px;font-size:14px}
.tabs a[aria-current=page]{background:#fff;color:#11653a;box-shadow:0 1px 3px #0002}
h1{margin:0 0 16px;font-size:22px}
label{display:block;font-size:13px;font-weight:600;margin:0 0 4px}
.field{display:block;width:100%;font:inherit;border:1px solid #cbd5e1;border-radius:8px;padding:11px 12px;margin-bottom:12px}
.btn{display:block;width:100%;font:inherit;font-weight:800;border:0;border-radius:8px;padding:13px;background:linear-gradient(135deg,#11653a,#2fa33e);color:#fff;cursor:pointer;margin-top:4px}
.btn:disabled{opacity:.6}
.err{color:#dc2626;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;font-size:13px;min-height:0;margin:0 0 8px;padding:0;display:none}
.err:not(:empty){display:block;padding:8px 12px}
.alt{margin:18px 0 0;font-size:13px;color:#64748b;text-align:center}.alt a{color:#2563eb}
a:focus-visible,input:focus-visible,button:focus-visible{outline:3px solid var(--blue);outline-offset:2px}
input:focus-visible{border-color:#15803d;box-shadow:0 0 0 3px rgba(21,128,61,.15)}
@media(max-width:820px){body{grid-template-columns:1fr}.side{padding:22px}.side h2,.side p,.side small{display:none}.side{flex-direction:row}}
@media(max-width:420px){.main{padding:20px 12px}.box{width:calc(100% - 24px);margin-inline:12px}.field{padding:10px 11px}}
</style>
</head>
<body>
<aside class="side">
  <a class="brand" href="/"><x-brand-logo id="auth-logo" theme="light" size="lg" /></a>
  <div><h2>Ikut jaga jalan di sekitarmu.</h2><p>Dengan akun, kamu bisa melapor kondisi jalan, mengonfirmasi laporan warga lain (Up-Check), dan membagikan rute perjalananmu.</p></div>
  <small>Alat bantu informasi berbasis komunitas, bukan jaminan keselamatan absolut. Darurat: hubungi 110.</small>
</aside>
<main class="main"><div class="box">
  <nav class="tabs" aria-label="Pilih"><a href="/masuk" id="t-in">Masuk</a><a href="/daftar" id="t-up">Daftar</a></nav>
  <h1 id="title">Masuk</h1>
  <form id="f" novalidate>
    <div id="g-name" hidden><label for="name">Nama</label><input class="field" id="name" autocomplete="name"></div>
    <label for="email">Email</label><input class="field" id="email" type="email" autocomplete="email" required>
    <label for="pass">Kata sandi</label><input class="field" id="pass" type="password" autocomplete="current-password" required>
    <div id="g-pass2" hidden><label for="pass2">Ulangi kata sandi</label><input class="field" id="pass2" type="password" autocomplete="new-password"></div>
    <p class="err" id="err" role="alert"></p>
    <button class="btn" id="go" type="submit">Masuk</button>
  </form>
  <p class="alt" id="alt"></p>
  <p class="alt"><a href="/app">Lanjut tanpa akun (Jelajahi Peta)</a></p>
</div></main>
<script>
const API='/api/v1',up=location.pathname.indexOf('/daftar')===0,$=id=>document.getElementById(id);
$(up?'t-up':'t-in').setAttribute('aria-current','page');
$('title').textContent=up?'Buat akun':'Masuk ke akunmu';$('go').textContent=up?'Daftar':'Masuk';
$('g-name').hidden=$('g-pass2').hidden=!up;$('pass').autocomplete=up?'new-password':'current-password';
$('alt').innerHTML=up?'Sudah punya akun? <a href="/masuk">Masuk</a>':'Belum punya akun? <a href="/daftar">Daftar</a>';
$('f').onsubmit=async e=>{
  e.preventDefault();$('err').textContent='';
  const b={email:$('email').value.trim(),password:$('pass').value};
  if(!b.email||!b.password){$('err').textContent='Isi email dan kata sandi.';return}
  if(up){
    b.name=$('name').value.trim();b.password_confirmation=$('pass2').value;
    if(!b.name){$('err').textContent='Isi nama.';return}
    if(b.password.length<8){$('err').textContent='Kata sandi minimal 8 karakter.';return}
    if(b.password!==b.password_confirmation){$('err').textContent='Kata sandi dan pengulangannya tidak sama.';return}
  }
  $('go').disabled=true;
  try{
    const r=await fetch(API+(up?'/register':'/login'),{method:'POST',headers:{'Content-Type':'application/json',Accept:'application/json'},body:JSON.stringify(b)});
    const j=await r.json().catch(()=>({}));
    if(r.status===429)throw new Error('Terlalu banyak percobaan. Tunggu 1 menit lalu coba lagi.');
    if(!r.ok){const first=j.errors&&Object.values(j.errors)[0];throw new Error((first&&first[0])||j.message||'Gagal. Coba lagi.')}
    const userRole = j.data?.user?.role;
    localStorage.setItem('ta_token', j.data.token);
    if (userRole === 'admin') {
      localStorage.setItem('ta_admin_token', j.data.token);
      location.href = '/admin';
    } else {
      location.href = '/app';
    }
  }catch(err){$('err').textContent=err.message}
  finally{$('go').disabled=false}
};
</script>
</body>
</html>