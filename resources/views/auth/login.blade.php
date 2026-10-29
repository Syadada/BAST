<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Masuk Perangkat Daerah (OPD) — Layanan Bagi Pakai DTSEN</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Unified Master Stylesheet (Single CSS) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="form-page">

    <div class="form-shell" style="max-width: 480px;">
        
        <!-- Standard White Form Card (24px Border Radius) -->
        <div class="form-card" style="padding: 32px 36px;">
            
            <!-- Top Utility Bar: Badge & Language Selector -->
            <div class="form-top-bar" style="margin-bottom: 20px;">
                <div class="form-badge-system" style="background: #e0f2fe; color: #0369a1; border-color: #bae6fd;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>PORTAL PERANGKAT DAERAH (OPD)</span>
                </div>

                <div class="lang-selector-box">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                    </svg>
                    <select id="langSwitcher" class="lang-dropdown" aria-label="Pilih Bahasa">
                        <option value="id">🇮🇩 Bahasa Indonesia</option>
                        <option value="en">🇬🇧 English</option>
                    </select>
                </div>
            </div>

            <div class="form-header" style="margin-bottom: 24px;">
                <h1 class="form-title" style="color: #0b57d0; font-size: 22px;">Masuk Portal OPD</h1>
                <p class="form-subtitle">Autentikasi akun resmi Perangkat Daerah untuk permohonan tiket bagi pakai DTSEN dan pemantauan progress alur data.</p>
            </div>

            <!-- Laptop Device Identification Badge -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 12px; margin-bottom: 20px; font-size: 11px; color: #334155; display: flex; align-items: center; justify-content: space-between;">
                <span><i class="fi fi-rr-laptop" style="color: #0b57d0;"></i> <strong>Nomor Laptop / ID Perangkat:</strong></span>
                <span id="detectedLaptopId" style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: #0b57d0;">
                    Memuat...
                </span>
            </div>

            @if(isset($errors) && $errors->any())
                <div class="form-alert-error" style="margin-bottom: 18px;">
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST">
                @csrf

                <div class="form-group" style="margin-bottom: 16px;">
                    <label for="input_login" class="form-label">
                        <span>Username atau Email Akun OPD</span> <span class="form-required">*</span>
                    </label>
                    <input type="text" 
                           name="login" 
                           id="input_login" 
                           class="form-control" 
                           value="{{ old('login') }}" 
                           placeholder="Contoh: dinsos atau bappeda" 
                           required 
                           autofocus>
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="input_password" class="form-label">
                        <span>Kata Sandi</span> <span class="form-required">*</span>
                    </label>
                    <input type="password" 
                           name="password" 
                           id="input_password" 
                           class="form-control" 
                           placeholder="••••••••" 
                           required>
                </div>

                <button type="submit" class="form-btn-submit" style="width: 100%; justify-content: center;">
                    <span>Masuk ke Dashboard OPD</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>
        </div>

        <!-- Footer Notice -->
        <footer class="form-footer">
            <p>Sistem Layanan Bagi Pakai Data Terpadu Sosial Ekonomi Nasional (DTSEN)</p>
            <p style="font-size: 11px; margin-top: 4px; color: var(--text-muted);">
                Setiap aktivitas login dan nomor laptop tercatat otomatis dalam Audit Log Keamanan.
            </p>
        </footer>

    </div>

    <!-- Unified Master JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        (function() {
            let devId = localStorage.getItem('opd_device_id');
            if (!devId) {
                const nav = navigator.userAgent;
                let pref = 'WIN';
                if (nav.indexOf('Mac') !== -1) pref = 'MAC';
                else if (nav.indexOf('Linux') !== -1) pref = 'LNX';
                devId = pref + '-OPD-' + Math.floor(1000 + Math.random() * 9000);
                localStorage.setItem('opd_device_id', devId);
            }
            document.cookie = "app_device_id=" + devId + "; path=/; max-age=31536000";
            const el = document.getElementById('detectedLaptopId');
            if (el) el.textContent = devId;
        })();
    </script>
</body>
</html>
