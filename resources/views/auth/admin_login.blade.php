<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Masuk Administrator — Layanan Bagi Pakai DTSEN</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Unified Master Stylesheet (Single CSS) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .admin-login-shell {
            max-width: 480px;
            margin: 0 auto;
        }
        .admin-header-chip {
            background: #0f172a;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body class="form-page" style="background: #f1f5f9;">

    <div class="form-shell admin-login-shell">
        
        <!-- Standard White Form Card with Dark Slate Accents -->
        <div class="form-card" style="box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); border-top: 4px solid #0f172a; padding: 32px 36px;">
            
            <!-- Top Utility Bar: Admin Badge & Language Selector -->
            <div class="form-top-bar" style="margin-bottom: 20px;">
                <div class="admin-header-chip">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <span>PORTAL KHUSUS ADMINISTRATOR</span>
                </div>

                <div class="lang-selector-box">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1 4-10z"></path>
                    </svg>
                    <select id="langSwitcher" class="lang-dropdown" aria-label="Pilih Bahasa">
                        <option value="id">🇮🇩 Bahasa Indonesia</option>
                        <option value="en">🇬🇧 English</option>
                    </select>
                </div>
            </div>

            <div class="form-header" style="margin-bottom: 24px;">
                <h1 class="form-title" style="color: #0f172a; font-size: 22px;">Masuk Administrator</h1>
                <p class="form-subtitle">Autentikasi verifikator Walidata untuk mengelola tiket permohonan dan verifikasi bagi pakai DTSEN.</p>
            </div>

            <!-- Laptop Device Identification Badge -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 12px; margin-bottom: 20px; font-size: 11px; color: #334155; display: flex; align-items: center; justify-content: space-between;">
                <span><i class="fi fi-rr-laptop" style="color: #0f172a;"></i> <strong>ID Laptop Admin:</strong></span>
                <span id="detectedLaptopId" style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: #0f172a;">
                    Memuat...
                </span>
            </div>

            @if(isset($errors) && $errors->any())
                <div class="form-alert-error" style="margin-bottom: 18px;">
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('admin.login.process') }}" method="POST">
                @csrf

                <div class="form-group" style="margin-bottom: 16px;">
                    <label for="input_login" class="form-label">
                        <span>Username atau Email Administrator</span> <span class="form-required">*</span>
                    </label>
                    <input type="text" 
                           name="login" 
                           id="input_login" 
                           class="form-control" 
                           value="{{ old('login') }}" 
                           placeholder="Masukkan akun admin..." 
                           required 
                           autofocus>
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="input_password" class="form-label">
                        <span>Kata Sandi Administrator</span> <span class="form-required">*</span>
                    </label>
                    <input type="password" 
                           name="password" 
                           id="input_password" 
                           class="form-control" 
                           placeholder="••••••••" 
                           required>
                </div>

                <button type="submit" class="form-btn-submit" style="width: 100%; justify-content: center; background: #0f172a; border-color: #0f172a;">
                    <span>Masuk ke Dashboard Administrator</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>
        </div>

        <!-- Footer Notice -->
        <footer class="form-footer">
            <p>Portal Pengendalian Data Terpadu Sosial Ekonomi Nasional (DTSEN) — Administrator</p>
            <p style="font-size: 11px; margin-top: 4px; color: var(--text-muted);">
                Akses Administrator terproteksi secara ketat. Semua percobaan akses ilegal diawasi dan dicatat.
            </p>
        </footer>

    </div>

    <!-- Unified Master JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        (function() {
            let devId = localStorage.getItem('admin_device_id');
            if (!devId) {
                const nav = navigator.userAgent;
                let pref = 'WIN';
                if (nav.indexOf('Mac') !== -1) pref = 'MAC';
                else if (nav.indexOf('Linux') !== -1) pref = 'LNX';
                devId = pref + '-ADM-' + Math.floor(1000 + Math.random() * 9000);
                localStorage.setItem('admin_device_id', devId);
            }
            document.cookie = "app_device_id=" + devId + "; path=/; max-age=31536000";
            const el = document.getElementById('detectedLaptopId');
            if (el) el.textContent = devId;
        })();
    </script>
</body>
</html>
