<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') — Layanan Bagi Pakai DTSEN</title>
    
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
    <div class="app-shell">
        
        <!-- 1. TOP APP BAR ADMIN -->
        <header class="app-header">
            <div class="app-header-left">
                <a href="{{ route('admin.dashboard') }}" class="app-brand">
                    <div class="app-brand-icon" style="background: #0b57d0; color: #fff; font-weight: 800; font-size: 11px;">
                        DTSEN
                    </div>
                    <div class="app-brand-title">
                        <span>Layanan Bagi Pakai DTSEN</span>
                        <span class="app-brand-badge">SUPER ADMIN</span>
                    </div>
                </a>
            </div>

            <!-- Right Controls -->
            <div class="app-header-right">
                <a href="{{ route('opd.dashboard') }}" class="btn-header-link" target="_blank" title="Lihat Portal OPD">
                    <i class="fi fi-rr-browser"></i> <span>Preview Portal OPD</span>
                </a>

                <div class="app-user-chip">
                    <div class="app-user-avatar">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <span>{{ Auth::user()->name ?? 'Administrator' }}</span>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="display: inline; margin: 0;">
                    @csrf
                    <button type="submit" class="btn-header-logout" title="Keluar dari akun">
                        <i class="fi fi-rr-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- 2. BODY LAYOUT (SIDEBAR + MAIN CONTENT) -->
        <div class="app-body">
            
            <!-- Left Sidebar Navigation Admin -->
            <aside class="app-sidebar">
                <ul class="app-nav-list">
                    <!-- 1. Dashboard Utama & Proses -->
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="app-nav-item {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.riwayat') ? 'active' : '' }}">
                            <div class="app-nav-item-left">
                                <i class="fi fi-rr-apps" style="font-size: 16px;"></i>
                                <span>Manajemen Tiket & Proses</span>
                            </div>
                        </a>
                    </li>

                    <!-- 2. Daftar Tiket Support (Ala DomaiNesia) -->
                    <li>
                        <a href="{{ route('admin.tiket.index') }}" class="app-nav-item {{ request()->routeIs('admin.tiket.*') ? 'active' : '' }}">
                            <div class="app-nav-item-left">
                                <i class="fi fi-rr-ticket" style="font-size: 16px;"></i>
                                <span>Daftar Tiket Support</span>
                            </div>
                            <span class="app-nav-badge" style="background: #eaf1fb; color: #0b57d0;">Tiket</span>
                        </a>
                    </li>

                    <!-- 3. User Management & Log Laptop -->
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="app-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <div class="app-nav-item-left">
                                <i class="fi fi-rr-users" style="font-size: 16px;"></i>
                                <span>User Management & Log</span>
                            </div>
                        </a>
                    </li>
                </ul>

                <div class="app-sidebar-divider"></div>
                
                <div class="app-sidebar-section">Ekspor Laporan</div>
                <ul class="app-nav-list">
                    <li>
                        <a href="{{ route('admin.riwayat.export') }}" class="app-nav-item">
                            <div class="app-nav-item-left">
                                <i class="fi fi-rr-download" style="font-size: 16px;"></i>
                                <span>Ekspor Data Tiket (CSV)</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.riwayat.export-logs', ['format' => 'csv']) }}" class="app-nav-item">
                            <div class="app-nav-item-left">
                                <i class="fi fi-rr-document" style="font-size: 16px;"></i>
                                <span>Ekspor Log Audit (CSV)</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </aside>

            <!-- Main Content Area Admin -->
            <main class="app-main" style="flex: 1; padding: 28px 32px; background: #f8fafc; min-width: 0;">
                
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="form-alert-success" style="margin-bottom: 20px;">
                        <i class="fi fi-rr-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="form-alert-error" style="margin-bottom: 20px;">
                        <i class="fi fi-rr-cross-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>

        </div>
    </div>

    @yield('scripts')
</body>
</html>
