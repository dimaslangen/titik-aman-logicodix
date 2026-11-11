<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — TitikAman Command Center</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #0f172a;
            color: #e2e8f0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        .form-control {
            background-color: #0f172a;
            border-color: #475569;
            color: #f8fafc;
        }
        .form-control:focus {
            background-color: #0f172a;
            border-color: #0d9488;
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(13, 148, 136, 0.25);
        }
        .btn-teal {
            background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
            color: #ffffff;
            font-weight: 600;
            border: none;
        }
        .btn-teal:hover {
            filter: brightness(1.1);
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="login-card p-4 mx-3">
        <div class="text-center mb-4">
            <div class="bg-success text-white d-inline-flex p-3 rounded-4 shadow-sm mb-2">
                <i class="bi bi-shield-lock-fill fs-3"></i>
            </div>
            <h4 class="fw-bold text-white mb-1">TitikAman Admin</h4>
            <small class="text-secondary">Civic Safety Command Center</small>
        </div>

        <div class="alert alert-info py-2 px-3 small border-0 bg-info-subtle text-info-emphasis mb-3 rounded-3">
            <i class="bi bi-info-circle-fill me-1"></i> Akun demo: <strong>admin@titikaman.id</strong> / <strong>Admin123!</strong>
        </div>

        <div id="errMsg" class="alert alert-danger py-2 small d-none" role="alert"></div>

        <form id="adminLoginForm" onsubmit="event.preventDefault(); loginAdmin();">
            <div class="mb-3">
                <label for="adminEmail" class="form-label small fw-semibold text-secondary">Email Petugas / Admin</label>
                <input type="email" class="form-control rounded-3 py-2" id="adminEmail" value="admin@titikaman.id" required>
            </div>
            <div class="mb-4">
                <label for="adminPassword" class="form-label small fw-semibold text-secondary">Kata Sandi</label>
                <input type="password" class="form-control rounded-3 py-2" id="adminPassword" value="Admin123!" required>
            </div>
            <button type="submit" id="btnLogin" class="btn btn-teal w-100 py-2 rounded-3 shadow-sm mb-3">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Command Center
            </button>
        </form>

        <div class="text-center pt-2 border-top border-secondary border-opacity-25">
            <a href="/app" class="text-secondary small text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Peta Warga
            </a>
        </div>
    </div>

    <script>
        async function loginAdmin() {
            const btn = document.getElementById('btnLogin');
            const errBox = document.getElementById('errMsg');
            const email = document.getElementById('adminEmail').value.trim();
            const password = document.getElementById('adminPassword').value;

            errBox.classList.add('d-none');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Memproses...';

            try {
                const response = await fetch('/api/v1/admin/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const payload = await response.json().catch(() => ({}));

                if (!response.ok) {
                    const firstErr = payload.errors ? Object.values(payload.errors)[0] : null;
                    throw new Error((firstErr && firstErr[0]) || payload.message || 'Email atau kata sandi admin tidak sesuai.');
                }

                if (payload.data?.token) {
                    localStorage.setItem('ta_admin_token', payload.data.token);
                    localStorage.setItem('ta_token', payload.data.token);
                    window.location.href = '/admin';
                } else {
                    throw new Error('Token tidak diterima dari server.');
                }
            } catch (err) {
                errBox.textContent = err.message;
                errBox.classList.remove('d-none');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-box-arrow-in-right me-1"></i> Masuk Command Center';
            }
        }
    </script>
</body>
</html>
