<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Masuk — TitikAman</title>
<style>
:root{--navy:#1e293b;--navy2:#0f172a;--green:#10b981;--blue:#3b82f6;--mute:#94a3b8;--line:#334155}
*{box-sizing:border-box}
body{margin:0;min-height:100vh;display:grid;grid-template-columns:1fr 1fr;font-family:system-ui,-apple-system,"Segoe UI",sans-serif;color:#0f172a;background:#fff}
.side{background:var(--navy);color:#fff;padding:40px;display:flex;flex-direction:column;justify-content:space-between}
.brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:18px;text-decoration:none;color:#fff}
.mark{width:34px;height:34px;border-radius:10px;background:var(--green);color:var(--navy2);display:grid;place-items:center;font-weight:900;font-size:14px}
.side h2{font-size:30px;line-height:1.2;margin:0 0 10px}.side p{color:#cbd5e1;margin:0;max-width:380px}
.side small{color:var(--mute);font-size:12px;max-width:380px}
.main{display:flex;align-items:center;justify-content:center;padding:32px}
.box{width:100%;max-width:380px}
.tabs{display:grid;grid-template-columns:1fr 1fr;background:#f1f5f9;border-radius:10px;padding:4px;margin-bottom:22px}
.tabs a{text-align:center;text-decoration:none;color:#475569;font-weight:700;padding:9px;border-radius:8px;font-size:14px}
.tabs a[aria-current=page]{background:#fff;color:#0f172a;box-shadow:0 1px 3px #0002}
h1{margin:0 0 16px;font-size:22px}
label{display:block;font-size:13px;font-weight:600;margin:0 0 4px}
.field{display:block;width:100%;font:inherit;border:1px solid #cbd5e1;border-radius:8px;padding:11px 12px;margin-bottom:12px}
.btn{display:block;width:100%;font:inherit;font-weight:800;border:0;border-radius:8px;padding:13px;background:var(--green);color:var(--navy2);cursor:pointer;margin-top:4px}
.btn:disabled{opacity:.6}
.err{color:#dc2626;font-size:13px;min-height:18px;margin:0 0 8px}
.alt{margin:18px 0 0;font-size:13px;color:#64748b;text-align:center}.alt a{color:#2563eb}
a:focus-visible,input:focus-visible,button:focus-visible{outline:3px solid var(--blue);outline-offset:2px}
@media(max-width:820px){body{grid-template-columns:1fr}.side{padding:22px}.side h2,.side p,.side small{display:none}.side{flex-direction:row}}
</style>
</head>
<body>
<aside class="side">
  <a class="brand" href="/"><span class="mark">TA</span>TitikAman</a>
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
  <p class="alt"><a href="/admin/login">Masuk sebagai petugas</a> · <a href="/app">Lanjut tanpa akun</a></p>
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
    localStorage.setItem('ta_token',j.data.token);location.href='/app';
  }catch(err){$('err').textContent=err.message}
  finally{$('go').disabled=false}
};
</script>
</body>
</html>