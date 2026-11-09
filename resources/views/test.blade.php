<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TitikAman API Explorer</title>
    <style>
        :root{--bg:#08111f;--panel:#101d31;--panel-2:#14253d;--line:#263b57;--text:#e7eef8;--muted:#91a4bd;--brand:#39d0b1;--brand-dark:#0c423b;--blue:#77a9ff;--danger:#ff8f9b;--shadow:0 18px 50px #00000030}
        *{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--text);font:14px/1.55 Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}
        a{color:var(--brand);text-decoration:none}.shell{display:grid;grid-template-columns:280px minmax(0,1fr);min-height:100vh}
        .sidebar{border-right:1px solid var(--line);background:#0b1729;padding:24px 16px;position:sticky;top:0;height:100vh;overflow:auto}
        .brand{display:flex;align-items:center;gap:11px;padding:4px 10px 24px}.brand-mark{width:34px;height:34px;display:grid;place-items:center;border-radius:10px;background:var(--brand-dark);color:var(--brand);font-weight:800}.brand strong{font-size:16px}.brand small{display:block;color:var(--muted);font-size:11px}
        .side-label{color:#5f7692;font-size:10px;font-weight:800;letter-spacing:.13em;text-transform:uppercase;padding:14px 10px 7px}.endpoint-list{display:grid;gap:4px}.endpoint-link{display:flex;align-items:center;gap:8px;width:100%;border:0;border-radius:8px;background:transparent;color:#aabbd0;cursor:pointer;padding:9px 10px;text-align:left}.endpoint-link:hover,.endpoint-link.active{background:var(--panel-2);color:#fff}.endpoint-link.active{box-shadow:inset 3px 0 var(--brand)}
        .method{font-size:10px;font-weight:800;min-width:40px}.get{color:#72b7ff}.post{color:#58d4ad}.admin{color:#ffbd6d}
        .main{min-width:0}.topbar{border-bottom:1px solid var(--line);padding:24px clamp(20px,4vw,54px);display:flex;justify-content:space-between;align-items:flex-start;gap:20px}.eyebrow{color:var(--brand);font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}.topbar h1{font-size:28px;line-height:1.15;margin:7px 0}.topbar p{color:var(--muted);margin:0;max-width:680px}.health{display:flex;align-items:center;gap:8px;white-space:nowrap;color:var(--muted);font-size:12px}.dot{width:8px;height:8px;border-radius:50%;background:#687b93}.dot.ok{background:var(--brand);box-shadow:0 0 0 4px #39d0b118}
        .content{padding:28px clamp(20px,4vw,54px);max-width:1440px}.toolbar{display:flex;gap:10px;align-items:center;margin-bottom:22px}.token{flex:1;min-width:180px}.input,.select,.textarea{width:100%;border:1px solid var(--line);border-radius:8px;background:#0a1627;color:var(--text);padding:10px 12px;outline:none}.input:focus,.select:focus,.textarea:focus{border-color:var(--brand)}.token-wrap{position:relative;flex:1}.token-wrap .input{padding-right:72px}.token-toggle{position:absolute;right:7px;top:6px;border:0;background:transparent;color:var(--muted);padding:5px;cursor:pointer}
        .layout{display:grid;grid-template-columns:minmax(0,1fr) 390px;gap:22px;align-items:start}.card{background:var(--panel);border:1px solid var(--line);border-radius:14px;box-shadow:var(--shadow)}.intro{padding:22px}.intro h2{margin:0 0 5px;font-size:20px}.muted{color:var(--muted)}.badge{display:inline-flex;align-items:center;border-radius:999px;padding:4px 9px;font-size:11px;font-weight:800;background:#18334d;color:var(--blue)}.badge.auth{background:#3e3020;color:#ffca77}.badge.public{background:#143932;color:var(--brand)}
        .request-card{margin-top:16px;padding:22px}.request-head{display:flex;justify-content:space-between;gap:15px;align-items:flex-start;border-bottom:1px solid var(--line);padding-bottom:18px}.request-title{display:flex;align-items:center;gap:10px}.request-title h3{margin:0;font-size:16px}.path{font:13px ui-monospace,SFMono-Regular,Consolas,monospace;color:#c5d5e8;margin-top:5px}.method-pill{font:11px;font-weight:900;padding:6px 9px;border-radius:6px;background:#143b51;color:#75c5ff}.method-pill.post{background:#153e37;color:var(--brand)}.method-pill.admin{background:#49351e;color:#ffd18b}
        .field-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px;margin-top:20px}.field.full{grid-column:1/-1}.field label{display:flex;justify-content:space-between;gap:10px;font-size:12px;color:#b9c9dc;margin-bottom:6px}.field label span{color:#647b96;font-size:11px}.textarea{min-height:220px;resize:vertical;font:12px/1.5 ui-monospace,SFMono-Regular,Consolas,monospace}.actions{display:flex;gap:9px;align-items:center;margin-top:18px;flex-wrap:wrap}.button{border:0;border-radius:8px;padding:10px 15px;background:var(--brand);color:#06281f;font-weight:800;cursor:pointer}.button:hover{filter:brightness(1.08)}.button.secondary{background:#1b304a;color:#c8d7e8;border:1px solid var(--line)}.button.danger{background:#49232e;color:#ffb1ba}.hint{font-size:12px;color:var(--muted)}
        .response{position:sticky;top:22px;padding:18px}.response-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}.response h3{margin:0;font-size:15px}.status{border-radius:999px;padding:4px 8px;font-size:11px;font-weight:800;background:#26344a;color:var(--muted)}.status.success{background:#143d34;color:var(--brand)}.status.error{background:#49232e;color:#ffadb6}.meta{display:flex;gap:12px;color:var(--muted);font-size:11px;margin-bottom:12px}.response pre{margin:0;min-height:300px;max-height:620px;overflow:auto;background:#07101d;border:1px solid #1b2c43;border-radius:9px;padding:14px;color:#cfe0f3;font:12px/1.6 ui-monospace,SFMono-Regular,Consolas,monospace;white-space:pre-wrap;word-break:break-word}
        .quick{margin-top:16px;padding:18px}.quick h3{font-size:14px;margin:0 0 10px}.quick-row{display:flex;gap:8px;align-items:center;margin:8px 0;color:var(--muted);font:12px ui-monospace,SFMono-Regular,Consolas,monospace}.quick-row button{border:0;background:transparent;color:var(--brand);cursor:pointer;padding:0;text-align:left}.notice{padding:12px 14px;border:1px solid #5b4926;background:#2a2416;border-radius:9px;color:#e9c987;font-size:12px;margin-top:18px}
        .map-card{margin-bottom:22px;padding:18px}.map-head{display:flex;justify-content:space-between;gap:14px;align-items:flex-start}.map-head h3{margin:0 0 4px;font-size:15px}.map-tools{display:flex;gap:8px;flex-wrap:wrap;margin:14px 0 10px}.map-tools .button{padding:8px 11px;font-size:12px}.map-tools .button.active{background:#f5b84b;color:#30220b}.map-tools .select{width:auto;min-width:190px;padding:8px 10px}.map-hint{color:var(--muted);font-size:12px}.map{height:420px;border-radius:10px;overflow:hidden;border:1px solid var(--line);background:#17263a}.map-coordinates{display:flex;gap:12px;margin-top:10px;color:var(--muted);font:11px ui-monospace,SFMono-Regular,Consolas,monospace;flex-wrap:wrap}.map-legend{display:flex;gap:14px;flex-wrap:wrap;margin-top:11px;color:#b9c9dc;font-size:12px}.legend-item{display:flex;align-items:center;gap:6px}.legend-line{width:24px;border-top:4px solid}.legend-line.safe{border-color:#16a34a}.legend-line.fast{border-color:#eab308;border-top-style:dashed}.legend-dot{width:10px;height:10px;border-radius:50%;background:#2563eb;border:2px solid #fff}.legend-dot.danger{background:#dc2626}.legend-dot.warn{background:#f59e0b}.road-status{margin-top:12px;padding:10px 12px;border-radius:8px;background:#0a1627;color:#b9c9dc;font-size:12px}.road-status strong{color:#fff}
        @media(max-width:1050px){.layout{grid-template-columns:1fr}.response{position:static}}@media(max-width:760px){.shell{display:block}.sidebar{position:static;height:auto;border-right:0;border-bottom:1px solid var(--line)}.endpoint-list{grid-template-columns:repeat(2,minmax(0,1fr))}.topbar{display:block}.health{margin-top:16px}.field-grid{grid-template-columns:1fr}.field.full{grid-column:auto}.content{padding:20px 14px}}
    </style>
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <div class="brand"><div class="brand-mark">TA</div><div><strong>TitikAman</strong><small>Backend API Explorer</small></div></div>
        <div class="side-label">Overview</div>
        <div class="endpoint-list">
            <button class="endpoint-link active" data-endpoint="health"><span class="method get">GET</span> Health check</button>
        </div>
        <div class="side-label">Public endpoints</div>
        <div class="endpoint-list" id="public-endpoints"></div>
        <div class="side-label">Authentication</div>
        <div class="endpoint-list" id="auth-endpoints"></div>
        <div class="side-label">Authenticated</div>
        <div class="endpoint-list" id="user-endpoints"></div>
        <div class="side-label">Admin</div>
        <div class="endpoint-list" id="admin-endpoints"></div>
    </aside>
    <main class="main">
        <header class="topbar">
            <div><div class="eyebrow">Internal developer portal</div><h1>TitikAman API Explorer</h1><p>Uji endpoint Laravel secara langsung dengan kontrak request/response yang sama seperti dokumentasi API.</p></div>
            <div class="health"><span class="dot" id="health-dot"></span><span id="health-text">API belum dicek</span></div>
        </header>
        <section class="content">
            <div class="toolbar">
                <div class="token-wrap"><input class="input" id="token" type="password" placeholder="Bearer token Sanctum (opsional untuk endpoint publik)"><button class="token-toggle" id="token-toggle" type="button">Tampilkan</button></div>
                <button class="button secondary" id="clear-token" type="button">Hapus token</button>
            </div>
            <div class="card map-card">
                <div class="map-head"><div><h3>Map input demo</h3><div class="map-hint">Pilih mode, lalu klik peta. Koordinat akan otomatis mengisi field endpoint yang sesuai.</div></div><span class="badge public">Leaflet + OpenStreetMap</span></div>
                <div class="map-tools">
                    <select class="select" id="map-region" aria-label="Region peta"><option value="rawamangun">Rawamangun</option><option value="unesa-lidah-wetan">UNESA Lidah Wetan</option><option value="unesa-ketintang">UNESA Ketintang</option></select>
                    <button class="button secondary map-mode" data-map-mode="origin" type="button">Pilih asal</button>
                    <button class="button secondary map-mode" data-map-mode="destination" type="button">Pilih tujuan</button>
                    <button class="button secondary map-mode" data-map-mode="report" type="button">Pilih laporan</button>
                    <button class="button secondary map-mode" data-map-mode="sos" type="button">Pilih SOS</button>
                    <button class="button secondary" id="map-locate" type="button">Lokasi saya</button>
                </div>
                <div class="map" id="demo-map"></div>
                <div class="map-legend"><span class="legend-item"><i class="legend-line safe"></i> SafePass</span><span class="legend-item"><i class="legend-line fast"></i> Rute tercepat</span><span class="legend-item"><i class="legend-dot"></i> Safety Hub</span><span class="legend-item"><i class="legend-dot danger"></i> Perlu perhatian</span><span class="legend-item"><i class="legend-dot warn"></i> Waspada</span></div>
                <div class="road-status" id="road-status">Jalankan Compare routes untuk memuat status kondisi jalan dan marker laporan.</div>
                <div class="map-coordinates"><span id="map-mode-label">Mode: belum dipilih</span><span id="map-coordinates">Koordinat: —</span></div>
            </div>
            <div class="layout">
                <div>
                    <div class="card intro"><span class="badge public">API v1</span><h2 id="endpoint-name">Health check</h2><p class="muted" id="endpoint-description">Memastikan service API aktif dan mengembalikan informasi versi aplikasi.</p></div>
                    <div class="card request-card">
                        <div class="request-head"><div><div class="request-title"><span class="method-pill get" id="method-pill">GET</span><h3>Request</h3></div><div class="path" id="endpoint-path">/api/v1/health</div></div><span class="badge public" id="auth-badge">Public</span></div>
                        <div class="field-grid" id="fields"></div>
                        <div class="actions"><button class="button" id="execute" type="button">Jalankan request</button><button class="button secondary" id="example" type="button">Muat contoh</button><span class="hint" id="request-hint"></span></div>
                    </div>
                    <div class="notice">Catatan: halaman ini adalah alat uji backend internal, bukan UI final aplikasi warga. Jangan memakai token produksi pada komputer publik.</div>
                </div>
                <aside>
                    <div class="card response"><div class="response-head"><h3>Response</h3><span class="status" id="response-status">Belum ada request</span></div><div class="meta"><span id="response-time">—</span><span id="response-type">application/json</span></div><pre id="response-body">Pilih endpoint lalu jalankan request.</pre></div>
                    <div class="card quick"><h3>Quick start</h3><div class="quick-row"><span>1.</span><button data-endpoint="login">Login warga demo</button></div><div class="quick-row"><span>2.</span><button data-endpoint="reports">Lihat laporan publik</button></div><div class="quick-row"><span>3.</span><button data-endpoint="routes">Bandingkan SafePass</button></div><div class="quick-row"><span>4.</span><button data-endpoint="admin-dashboard">Buka dashboard admin</button></div></div>
                </aside>
            </div>
        </section>
    </main>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const examples = {
    region: {region_slug:'rawamangun'}, hubs: {region_slug:'rawamangun'}, reports: {region_slug:'rawamangun', per_page:20},
    'report-show': {id:1}, routes: {region_slug:'rawamangun', mode:'motor', origin_lat:-6.1955, origin_lng:106.9005, destination_lat:-6.1900, destination_lng:106.8940},
    register: {name:'Budi Demo', email:'budi@example.com', password:'Rahasia123!', password_confirmation:'Rahasia123!'},
    login: {email:'warga@titikaman.id', password:'Warga123!'}, 'admin-login': {email:'admin@titikaman.id', password:'Admin123!'},
    report: {region_slug:'rawamangun', category:'area_sepi', lat:-6.194, lng:106.899, user_lat:-6.194, user_lng:106.899, description:'Area sepi di sekitar titik ini.'},
    upvote: {id:1}, 'ai-check': {id:1}, sos: {region_slug:'rawamangun', lat:-6.193, lng:106.898, message:'SOS dari API Explorer'},
    'shared-create': {region_slug:'rawamangun', mode:'motor', origin_lat:-6.1955, origin_lng:106.9005, destination_lat:-6.1900, destination_lng:106.8940, route_data:{}},
    'shared-show': {token:'GANTI_TOKEN'}, simulation: {enabled:true}, 'admin-report': {id:1}, 'admin-user': {id:2}, 'admin-sos': {id:1}
};
const endpoints = {
    health:{name:'Health check',description:'Memastikan service API aktif dan mengembalikan informasi versi aplikasi.',method:'GET',path:'/health',group:'public',auth:'Public',fields:[]},
    regions:{name:'List regions',description:'Mengambil seluruh region yang tersedia beserta bounding box.',method:'GET',path:'/regions',group:'public',auth:'Public',fields:[]},
    region:{name:'Get region',description:'Mengambil detail satu region berdasarkan slug.',method:'GET',path:'/regions/{slug}',group:'public',auth:'Public',fields:[['slug','Region slug','rawamangun','path']]},
    hubs:{name:'List safety hubs',description:'Mengambil Safety Hub dan filter berdasarkan region atau tipe.',method:'GET',path:'/safety-hubs',group:'public',auth:'Public',fields:[['region_slug','Region slug','rawamangun'],['type','Tipe hub','polisi | satpam | faskes | halte']]},
    reports:{name:'List reports',description:'Mengambil laporan aktif yang identitas pelapornya sudah disamarkan.',method:'GET',path:'/reports',group:'public',auth:'Public',fields:[['region_slug','Region slug','rawamangun'],['status','Status','VERIFIED'],['category','Kategori','hambatan_fisik'],['per_page','Per halaman','20']]},
    'report-show':{name:'Get report',description:'Mengambil satu laporan publik yang masih aktif dan belum expired.',method:'GET',path:'/reports/{id}',group:'public',auth:'Public',fields:[['id','Report ID','1','path']]},
    routes:{name:'Compare routes',description:'Membandingkan rute tercepat dengan SafePass berbobot laporan dan Safety Hub.',method:'POST',path:'/routes/compare',group:'public',auth:'Public',fields:[['region_slug','Region slug','rawamangun'],['mode','Moda','motor'],['origin_lat','Asal latitude','-6.1955'],['origin_lng','Asal longitude','106.9005'],['destination_lat','Tujuan latitude','-6.1900'],['destination_lng','Tujuan longitude','106.8940']]},
    register:{name:'Register user',description:'Membuat akun warga baru dan menerbitkan token Sanctum.',method:'POST',path:'/register',group:'auth',auth:'Public',fields:[['name','Nama','Budi Demo'],['email','Email','budi@example.com'],['password','Password','Rahasia123!','password'],['password_confirmation','Konfirmasi password','Rahasia123!','password']]},
    login:{name:'Login user',description:'Login warga dan menerbitkan bearer token Sanctum.',method:'POST',path:'/login',group:'auth',auth:'Public',fields:[['email','Email','warga@titikaman.id'],['password','Password','Warga123!','password']]},
    'admin-login':{name:'Admin login',description:'Login khusus akun dengan role admin.',method:'POST',path:'/admin/login',group:'auth',auth:'Public',fields:[['email','Email','admin@titikaman.id'],['password','Password','Admin123!','password']]},
    me:{name:'Current user',description:'Mengambil profil dari token yang sedang digunakan.',method:'GET',path:'/me',group:'user',auth:'Bearer token',fields:[]},
    logout:{name:'Logout',description:'Menghapus token Sanctum yang sedang digunakan.',method:'POST',path:'/logout',group:'user',auth:'Bearer token',fields:[]},
    report:{name:'Create report',description:'Membuat laporan kondisi jalan. Membutuhkan login dan geofence maksimal 20 meter.',method:'POST',path:'/reports',group:'user',auth:'Bearer token',fields:[['region_slug','Region slug','rawamangun'],['category','Kategori','area_sepi'],['lat','Latitude laporan','-6.194'],['lng','Longitude laporan','106.899'],['user_lat','Latitude pengguna','-6.194'],['user_lng','Longitude pengguna','106.899'],['description','Deskripsi','Area sepi di sekitar titik ini.']]},
    upvote:{name:'Upvote report',description:'Memberikan satu Up-Check. Upvote ketiga otomatis memverifikasi laporan.',method:'POST',path:'/reports/{id}/upvote',group:'user',auth:'Bearer token',fields:[['id','Report ID','1','path']]},
    'ai-check':{name:'AI check report',description:'Menjalankan pemeriksaan AI pada laporan milik sendiri atau laporan oleh admin.',method:'POST',path:'/reports/{id}/ai-check',group:'user',auth:'Bearer token',fields:[['id','Report ID','1','path']]},
    sos:{name:'Create SOS alert',description:'Mencatat alert untuk dashboard admin. Untuk panggilan darurat gunakan tel:110.',method:'POST',path:'/sos',group:'user',auth:'Optional',fields:[['region_slug','Region slug','rawamangun'],['lat','Latitude','-6.193'],['lng','Longitude','106.898'],['message','Pesan','SOS dari API Explorer']]},
    'shared-create':{name:'Create shared route',description:'Membuat URL Live Companion yang berlaku selama 6 jam.',method:'POST',path:'/shared-routes',group:'user',auth:'Bearer token',fields:[['region_slug','Region slug','rawamangun'],['mode','Moda','motor'],['origin_lat','Asal latitude','-6.1955'],['origin_lng','Asal longitude','106.9005'],['destination_lat','Tujuan latitude','-6.1900'],['destination_lng','Tujuan longitude','106.8940'],['route_data','Route data JSON','{}','json']]},
    'shared-show':{name:'Get shared route',description:'Mengambil shared route publik yang belum expired.',method:'GET',path:'/shared-routes/{token}',group:'public',auth:'Public',fields:[['token','Share token','GANTI_TOKEN','path']]},
    simulation:{name:'Simulation status / toggle',description:'Mengaktifkan atau mematikan mode simulasi lokasi untuk demo juri.',method:'POST',path:'/admin/simulation',group:'admin',auth:'Admin token',fields:[['enabled','Enabled','true','boolean']]},
    'admin-dashboard':{name:'Admin dashboard',description:'Mengambil statistik laporan, heatmap, dan SOS terbaru.',method:'GET',path:'/admin/dashboard',group:'admin',auth:'Admin token',fields:[['region_slug','Filter region','rawamangun']]},
    'admin-report':{name:'Moderate report',description:'Pilih aksi admin: verify, resolve, hide, atau buat pesan follow-up WhatsApp.',method:'POST',path:'/admin/reports/{id}/verify',group:'admin',auth:'Admin token',fields:[['id','Report ID','1','path']]},
    'admin-sos':{name:'Resolve SOS',description:'Menutup alert SOS dari dashboard admin.',method:'POST',path:'/admin/sos/{id}/resolve',group:'admin',auth:'Admin token',fields:[['id','SOS ID','1','path']]},
    'admin-user':{name:'Block user',description:'Memblokir akun warga yang terbukti spam atau hoax.',method:'POST',path:'/admin/users/{id}/block',group:'admin',auth:'Admin token',fields:[['id','User ID','2','path']]}
};
const enumOptions = {
    region_slug: [
        ['rawamangun', 'Rawamangun, Jakarta Timur'],
        ['unesa-lidah-wetan', 'UNESA Lidah Wetan, Surabaya'],
        ['unesa-ketintang', 'UNESA Ketintang, Surabaya']
    ],
    mode: [
        ['mobil', 'Mobil'],
        ['motor', 'Motor'],
        ['jalan_kaki', 'Jalan kaki']
    ],
    category: [
        ['penerangan_padam', 'Penerangan padam / terbatas'],
        ['area_sepi', 'Kondisi area sepi / perlu perhatian'],
        ['hambatan_fisik', 'Hambatan fisik jalan']
    ],
    status: [
        ['UNVERIFIED', 'Belum terverifikasi'],
        ['VERIFIED', 'Terverifikasi'],
        ['PENDING_MODERATION', 'Menunggu moderasi'],
        ['RESOLVED', 'Selesai'],
        ['EXPIRED', 'Kedaluwarsa'],
        ['HIDDEN', 'Disembunyikan']
    ],
    type: [
        ['polisi', 'Polisi'],
        ['satpam', 'Satpam'],
        ['faskes', 'Faskes'],
        ['halte', 'Halte']
    ],
    enabled: [
        ['true', 'Aktif'],
        ['false', 'Nonaktif']
    ]
};
let current='health', token=localStorage.getItem('ta_token')||'', mapMode=null, mapMarker=null;
const $=id=>document.getElementById(id);
const centers={rawamangun:[-6.1928,106.89763],'unesa-lidah-wetan':[-7.30071,112.67289],'unesa-ketintang':[-7.31143,112.72937]};
const demoMap=L.map('demo-map',{zoomControl:true}).setView(centers.rawamangun,15);
const routeLayer=L.layerGroup().addTo(demoMap);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap contributors'}).addTo(demoMap);
function formatCoordinate(value){return Number(value).toFixed(6)}
function setField(name,value){
    const input=$('field-'+name);
    if(input){input.value=value}
}
function setMapMode(mode){
    mapMode=mapMode===mode?null:mode;
    document.querySelectorAll('.map-mode').forEach(button=>button.classList.toggle('active',button.dataset.mapMode===mapMode));
    $('map-mode-label').textContent='Mode: '+(mapMode?({origin:'pilih asal',destination:'pilih tujuan',report:'pilih laporan',sos:'pilih SOS'}[mapMode]):'belum dipilih');
}
function fillCoordinates(lat,lng){
    const latitude=formatCoordinate(lat), longitude=formatCoordinate(lng);
    if(mapMode==='origin'){setField('origin_lat',latitude);setField('origin_lng',longitude);setField('lat',latitude);setField('lng',longitude);setField('user_lat',latitude);setField('user_lng',longitude)}
    if(mapMode==='destination'){setField('destination_lat',latitude);setField('destination_lng',longitude)}
    if(mapMode==='report'){setField('lat',latitude);setField('lng',longitude);setField('user_lat',latitude);setField('user_lng',longitude)}
    if(mapMode==='sos'){setField('lat',latitude);setField('lng',longitude)}
    $('map-coordinates').textContent='Koordinat: '+latitude+', '+longitude;
}
function clearRoute(){
    routeLayer.clearLayers();
}
function reportColor(category){
    return category==='hambatan_fisik'?'#f59e0b':'#dc2626';
}
function reportLabel(report){
    return report.label||report.category||'Laporan kondisi jalan';
}
function pointDistanceToPolyline(point, route){
    let minimum=Infinity;
    for(let index=0;index<route.length;index++){
        const current=route[index], next=route[index+1];
        if(!next){minimum=Math.min(minimum,Math.hypot(point[0]-current[0],point[1]-current[1]));continue}
        const dx=next[0]-current[0], dy=next[1]-current[1], length=dx*dx+dy*dy;
        const projection=length?Math.max(0,Math.min(1,((point[0]-current[0])*dx+(point[1]-current[1])*dy)/length)):0;
        const closest=[current[0]+projection*dx,current[1]+projection*dy];
        minimum=Math.min(minimum,Math.hypot(point[0]-closest[0],point[1]-closest[1]));
    }
    return minimum*111320;
}
async function loadMapConditions(regionSlug, routeData){
    const [reportsResponse,hubsResponse]=await Promise.all([
        fetch('/api/v1/reports?region_slug='+encodeURIComponent(regionSlug)+'&per_page=100',{headers:{Accept:'application/json'}}),
        fetch('/api/v1/safety-hubs?region_slug='+encodeURIComponent(regionSlug),{headers:{Accept:'application/json'}})
    ]);
    const reportsPayload=await reportsResponse.json().catch(()=>({data:[]}));
    const hubsPayload=await hubsResponse.json().catch(()=>({data:[]}));
    const reports=Array.isArray(reportsPayload.data)?reportsPayload.data:[];
    const hubs=Array.isArray(hubsPayload.data)?hubsPayload.data:[];
    const safeRoute=routeCoordinates(routeData&&routeData.safepass);
    reports.forEach(report=>{
        const marker=L.circleMarker([Number(report.lat),Number(report.lng)],{
            radius:8,color:'#fff',weight:2,fillColor:reportColor(report.category),fillOpacity:.95
        }).bindPopup('<b>'+reportLabel(report)+'</b><br>Status: '+report.status+'<br>Kontribusi: '+(report.upvotes_count||0)+' Up-Check');
        marker.addTo(routeLayer);
    });
    hubs.forEach(hub=>{
        L.circleMarker([Number(hub.lat),Number(hub.lng)],{
            radius:7,color:'#fff',weight:2,fillColor:'#2563eb',fillOpacity:.95
        }).bindPopup('<b>'+hub.name+'</b><br>Safety Hub · '+hub.type).addTo(routeLayer);
    });
    const nearReports=reports.filter(report=>safeRoute.length&&pointDistanceToPolyline([Number(report.lat),Number(report.lng)],safeRoute)<=50);
    const nearVerified=nearReports.filter(report=>report.status==='VERIFIED');
    $('road-status').innerHTML=nearVerified.length
        ? '<strong>Perlu perhatian:</strong> SafePass melewati '+nearVerified.length+' laporan terverifikasi dalam radius 50 m.'
        : '<strong>Terpantau aman:</strong> tidak ada laporan terverifikasi dalam radius 50 m dari garis SafePass. Tetap perhatikan kondisi langsung di lapangan.';
}
function routeCoordinates(route){
    return route&&route.geometry&&Array.isArray(route.geometry.coordinates)
        ? route.geometry.coordinates.filter(coordinate=>Array.isArray(coordinate)&&coordinate.length>=2).map(coordinate=>[coordinate[1],coordinate[0]])
        : [];
}
function drawRouteResponse(data){
    const fastest=data&&data.fastest, safe=data&&data.safepass;
    const fastestPoints=routeCoordinates(fastest), safePoints=routeCoordinates(safe);
    if(!fastestPoints.length&&!safePoints.length)return;
    clearRoute();
    const bounds=[];
    if(fastestPoints.length){
        L.polyline(fastestPoints,{color:'#eab308',weight:5,opacity:.9,dashArray:'10 8',lineCap:'round'}).bindPopup('<b>Rute tercepat</b><br>'+Math.round(fastest.duration||0)+' detik').addTo(routeLayer);
        bounds.push(...fastestPoints);
    }
    if(safePoints.length){
        L.polyline(safePoints,{color:'#16a34a',weight:8,opacity:.95,lineCap:'round'}).bindPopup('<b>SafePass</b><br>Skor '+Math.round(safe.score_seconds||0)+' detik').addTo(routeLayer);
        bounds.push(...safePoints);
    }
    if(bounds.length)demoMap.fitBounds(bounds,{padding:[28,28],maxZoom:17});
}
function drawPointMarkers(data){
    const fastest=data&&data.fastest, safe=data&&data.safepass;
    const points=[];
    const first=routeCoordinates(fastest)[0], last=routeCoordinates(fastest).slice(-1)[0];
    if(first)points.push({point:first,label:'Asal',color:'#16a34a'});
    if(last)points.push({point:last,label:'Tujuan',color:'#dc2626'});
    (safe&&safe.hubs_passed||[]).forEach(hub=>{
        if(hub.lat&&hub.lng)points.push({point:[hub.lat,hub.lng],label:hub.name,color:'#2563eb'});
    });
    points.forEach(item=>L.circleMarker(item.point,{radius:7,color:'#fff',weight:2,fillColor:item.color,fillOpacity:1}).bindPopup('<b>'+item.label+'</b>').addTo(routeLayer));
}
demoMap.on('click',event=>{
    if(!mapMode){$('map-mode-label').textContent='Mode: pilih salah satu tombol dahulu';return}
    if(mapMarker)mapMarker.setLatLng(event.latlng);else mapMarker=L.marker(event.latlng).addTo(demoMap);
    mapMarker.bindPopup('Koordinat dipilih<br>'+formatCoordinate(event.latlng.lat)+', '+formatCoordinate(event.latlng.lng)).openPopup();
    fillCoordinates(event.latlng.lat,event.latlng.lng);
    setMapMode(null);
});
document.querySelectorAll('.map-mode').forEach(button=>button.onclick=()=>setMapMode(button.dataset.mapMode));
$('map-region').onchange=event=>demoMap.setView(centers[event.target.value],15);
$('map-locate').onclick=()=>{
    if(!navigator.geolocation){$('map-mode-label').textContent='Browser tidak mendukung geolocation';return}
    navigator.geolocation.getCurrentPosition(position=>{
        const point=[position.coords.latitude,position.coords.longitude];demoMap.setView(point,17);
        if(mapMarker)mapMarker.setLatLng(point);else mapMarker=L.marker(point).addTo(demoMap);
        mapMarker.bindPopup('Lokasi perangkat').openPopup();fillCoordinates(point[0],point[1]);
    },()=>{$('map-mode-label').textContent='Lokasi perangkat tidak tersedia'});
};
function methodClass(method, group){return group==='admin'?'admin':method.toLowerCase()}
function renderNav(){
    ['public','auth','user','admin'].forEach(group=>{
        const target=$(group==='public'?'public-endpoints':group==='auth'?'auth-endpoints':group==='user'?'user-endpoints':'admin-endpoints');
        target.innerHTML='';
        Object.entries(endpoints).filter(([,item])=>item.group===group).forEach(([key,item])=>{
            const button=document.createElement('button'); button.className='endpoint-link'; button.dataset.endpoint=key;
            button.innerHTML='<span class="method '+methodClass(item.method,item.group)+'">'+item.method+'</span> '+item.name;
            button.onclick=()=>selectEndpoint(key); target.appendChild(button);
        });
    });
}
function selectEndpoint(key){
    current=key; const item=endpoints[key]; document.querySelectorAll('.endpoint-link').forEach(button=>button.classList.toggle('active',button.dataset.endpoint===key));
    $('endpoint-name').textContent=item.name; $('endpoint-description').textContent=item.description; $('endpoint-path').textContent='/api/v1'+item.path;
    $('method-pill').textContent=item.method; $('method-pill').className='method-pill '+methodClass(item.method,item.group); $('auth-badge').textContent=item.auth; $('auth-badge').className='badge '+(item.auth==='Public'?'public':'auth');
    $('fields').innerHTML=''; item.fields.forEach(([name,label,value,type])=>{
        const field=document.createElement('div'); field.className='field '+(type==='json'?'full':'');
        field.innerHTML='<label for="field-'+name+'">'+label+' <span>'+name+'</span></label>';
        const input=type==='json'?document.createElement('textarea'):enumOptions[name]?document.createElement('select'):document.createElement('input'); input.id='field-'+name; input.className=type==='json'?'textarea':enumOptions[name]?'select':'input';
        const exampleValue=examples[key]&&examples[key][name]!==undefined?examples[key][name]:value||'';
        if(enumOptions[name]){
            enumOptions[name].forEach(([optionValue,optionLabel])=>{
                const option=document.createElement('option');
                option.value=optionValue; option.textContent=optionLabel; input.appendChild(option);
            });
            input.value=exampleValue;
        }else{
            input.value=type==='json'||(typeof exampleValue==='object'&&exampleValue!==null)?JSON.stringify(exampleValue,null,2):exampleValue;
        }
        input.type=type==='password'?'password':type==='boolean'?'text':'text'; field.appendChild(input); $('fields').appendChild(field);
    });
    $('request-hint').textContent=item.method==='GET'?'Query parameters akan ditambahkan ke URL.':'Body dikirim sebagai application/json.';
}
function loadExample(){selectEndpoint(current)}
function parseValue(value,name,type){
    if(type==='json') return JSON.parse(value||'{}');
    if(type==='boolean') return value==='true';
    if(['id','per_page','origin_lat','origin_lng','destination_lat','destination_lng','lat','lng','user_lat','user_lng'].includes(name)) return Number(value);
    return value;
}
async function execute(){
    const item=endpoints[current]; let path=item.path, body={}, query=[]; let error;
    item.fields.forEach(([name,, ,type])=>{
        const input=$('field-'+name); if(!input)return; const value=parseValue(input.value,name,type); if(name==='id'||type==='path'||name==='slug'||name==='token') path=path.replace('{'+name+'}',encodeURIComponent(input.value)); else if(item.method==='GET'&&input.value!=='') query.push(encodeURIComponent(name)+'='+encodeURIComponent(input.value)); else if(input.value!=='') body[name]=value;
    });
    if(path.includes('{')){showResponse(0,{success:false,message:'Isi semua path parameter terlebih dahulu.'},0);return}
    if(query.length)path+='?'+query.join('&');
    const started=performance.now(), headers={'Accept':'application/json','Content-Type':'application/json'}; if(token)headers.Authorization='Bearer '+token;
    try{
        const response=await fetch('/api/v1'+path,{method:item.method,headers,body:item.method==='GET'?undefined:JSON.stringify(body)});
        const data=await response.json().catch(()=>({message:'Response bukan JSON'})); showResponse(response.status,data,performance.now()-started);
        if(current==='routes'&&response.ok&&data.data){
            drawRouteResponse(data.data);
            drawPointMarkers(data.data);
            await loadMapConditions(body.region_slug,data.data);
        }
        if(data.data&&data.data.token){token=data.data.token;localStorage.setItem('ta_token',token);$('token').value=token}
        if(current==='health'&&response.ok){$('health-dot').className='dot ok';$('health-text').textContent='API online'}
    }catch(e){showResponse(0,{success:false,message:e.message},performance.now()-started)}
}
function showResponse(status,data,time){$('response-status').textContent=status?status+' '+(status>=200&&status<300?'OK':'Error'):'Request failed';$('response-status').className='status '+(status>=200&&status<300?'success':'error');$('response-time').textContent=Math.round(time)+' ms';$('response-body').textContent=JSON.stringify(data,null,2)}
$('execute').onclick=execute;$('example').onclick=loadExample;$('token').value=token;$('token').oninput=e=>{token=e.target.value;localStorage.setItem('ta_token',token)};$('clear-token').onclick=()=>{token='';localStorage.removeItem('ta_token');$('token').value=''};$('token-toggle').onclick=()=>{const input=$('token');input.type=input.type==='password'?'text':'password';$('token-toggle').textContent=input.type==='password'?'Tampilkan':'Sembunyikan'};
document.querySelectorAll('[data-endpoint]').forEach(button=>button.onclick=()=>selectEndpoint(button.dataset.endpoint));renderNav();selectEndpoint('health');execute();
</script>
</body>
</html>
