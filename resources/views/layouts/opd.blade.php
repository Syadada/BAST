<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Perangkat Daerah') — Layanan Bagi Pakai DTSEN</title>
    
    <!-- Modern Typography (Roboto & JetBrains Mono) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Unified Master Stylesheet (Single CSS + Flaticon UIcons) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    @yield('styles')
</head>
<body>
    <div class="opd-layout">
        <!-- 1. SIDEBAR NAVIGASI OPD -->
        <aside class="opd-sidebar">
            <!-- Header Brand -->
            <div class="opd-sidebar-header">
                <a href="{{ route('opd.dashboard') }}" class="opd-brand-box">
                    <div class="opd-brand-icon">
                        DTSEN
                    </div>
                    <div>
                        <div class="opd-brand-title">Layanan DTSEN</div>
                        <span class="opd-brand-subtitle">Portal Perangkat Daerah</span>
                    </div>
                </a>
            </div>

            <!-- Menu Navigasi -->
            <div class="opd-sidebar-body">
                <div class="opd-nav-section-title">Menu Utama</div>

                <!-- 1. Dashboard Clean (Halaman Utama) -->
                <a href="{{ route('opd.dashboard') }}" class="opd-nav-link {{ request()->routeIs('opd.dashboard') ? 'active' : '' }}">
                    <i class="fi fi-rr-apps"></i>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Tiket Saya (Support Tickets ala DomaiNesia) -->
                <a href="{{ route('opd.tiket.index') }}" class="opd-nav-link {{ request()->routeIs('opd.tiket.*') ? 'active' : '' }}">
                    <i class="fi fi-rr-ticket"></i>
                    <span>Tiket Saya</span>
                </a>

                <!-- 3. Layanan Bagi Pakai DTSEN (Progress 5 Tahap & Riwayat BAST) -->
                <a href="{{ route('opd.layanan-bagi-pakai') }}" class="opd-nav-link {{ request()->routeIs('opd.layanan-bagi-pakai') || request()->routeIs('profile') ? 'active' : '' }}">
                    <i class="fi fi-rr-document"></i>
                    <span>Layanan Bagi Pakai DTSEN</span>
                </a>

                <!-- 4. Buat Tiket Baru (Formulir Pengajuan) -->
                <a href="{{ route('formulir.index') }}" class="opd-nav-link {{ request()->routeIs('formulir.*') ? 'active' : '' }}">
                    <i class="fi fi-rr-plus"></i>
                    <span>Buat Tiket Baru</span>
                    <span class="opd-nav-badge">+Form</span>
                </a>

                @if(Auth::check() && Auth::user()->isSuperAdmin())
                    <div class="opd-nav-section-title">Akses Khusus Admin</div>
                    <a href="{{ route('admin.riwayat') }}" class="opd-nav-link">
                        <i class="fi fi-rr-settings"></i>
                        <span>Panel Admin</span>
                    </a>
                @endif
            </div>

            <!-- Footer User Profile & Device ID -->
            <div class="opd-sidebar-footer">
                <div class="opd-user-box">
                    <div class="opd-user-name" title="{{ Auth::user()->name }}">
                        <i class="fi fi-rr-user"></i> {{ Auth::user()->name }}
                    </div>
                    <div class="opd-user-instansi" title="{{ Auth::user()->nama_instansi ?: Auth::user()->name }}">
                        {{ Auth::user()->nama_instansi ?: Auth::user()->name }}
                    </div>
                    <div>
                        <span class="opd-device-pill" title="ID Perangkat Laptop Login">
                            <i class="fi fi-rr-laptop"></i> {{ Auth::user()->last_login_device ?: 'WIN-DEV-OPD01' }}
                        </span>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="opd-logout-btn">
                        <i class="fi fi-rr-sign-out-alt"></i>
                        <span>Keluar / Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- 2. MAIN CONTENT WRAPPER -->
        <div class="opd-main">
            <!-- Topbar OPD -->
            <header class="opd-topbar">
                <div class="opd-topbar-left">
                    <div>
                        <span class="opd-page-title">@yield('page_title', 'Dashboard OPD')</span>
                        <span class="opd-page-subtitle d-none d-md-inline">@yield('page_subtitle', 'Portal Layanan Bagi Pakai DTSEN')</span>
                    </div>
                </div>

                <div class="opd-topbar-right">
                    <div style="font-size: 12px; color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 6px;">
                        <i class="fi fi-rr-calendar"></i>
                        <span>{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                    </div>

                    <a href="{{ route('formulir.index') }}" class="btn-pill-primary" style="font-size: 12px; padding: 7px 16px;">
                        <i class="fi fi-rr-plus"></i> Tiket Baru
                    </a>
                </div>
            </header>

            <!-- Alert Flash Messages -->
            <div style="padding: 18px 32px 0; max-width: 1200px; width: 100%; margin: 0 auto;">
                @if(session('success'))
                    <div class="form-alert-success" style="margin-bottom: 0;">
                        <i class="fi fi-rr-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="form-alert-error" style="margin-bottom: 0;">
                        <i class="fi fi-rr-cross-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="opd-content">
                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
