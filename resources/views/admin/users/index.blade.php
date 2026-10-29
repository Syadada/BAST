<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna & Log Laptop — Hak Kendali Super Admin</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Unified Master Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        /* Modern, Neutral Tech Badge (No Jarring Blue) */
        .device-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 11px;
            border-radius: 6px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 600;
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        /* Clean Charcoal / Slate Super Admin Role Badge (No Red) */
        .role-badge-super {
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }

        /* OPD Role Badge (Subtle Soft Emerald) */
        .role-badge-opd {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }

        /* Generous Padding for Table Headers & Cells */
        .data-table th {
            padding: 16px 20px !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            color: #4b5563 !important;
            background: #f8fafc !important;
            border-bottom: 2px solid #e2e8f0 !important;
            letter-spacing: 0.3px;
            vertical-align: middle;
        }

        .data-table td {
            padding: 18px 20px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #edf2f7 !important;
            color: #1f2937 !important;
            line-height: 1.45;
        }

        .data-table tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Fixed Section Header Box */
        .section-header-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            background: #ffffff;
            border-bottom: 1px solid #edf2f7;
            flex-wrap: wrap;
            gap: 12px;
        }

        .section-header-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Safe SVG constraints inside tables and pagination */
        .data-table svg,
        .dash-card svg,
        .dash-pagination svg {
            flex-shrink: 0;
        }
    </style>
</head>
<body>

    <div class="app-shell">
        
        <!-- Top App Bar -->
        <header class="app-header">
            <div class="app-header-left">
                <a href="{{ route('admin.riwayat') }}" class="app-brand">
                    <div class="app-brand-icon" style="background: #0b57d0; color: #fff; font-weight: 800; font-size: 11px;">
                        DTSEN
                    </div>
                    <div class="app-brand-title">
                        <span>Layanan Bagi Pakai DTSEN</span>
                        <!-- Refined Dark Slate Badge (No Red) -->
                        <span class="app-brand-badge" style="background: #0f172a; color: #f8fafc; font-weight: 700; font-size: 11px; padding: 3px 8px; border-radius: 4px;">
                            SUPER ADMIN
                        </span>
                    </div>
                </a>
            </div>

            <!-- Right Controls -->
            <div class="app-header-right">
                <a href="{{ route('profile') }}" class="btn-header-link">
                    <span><i class="fi fi-rr-user"></i> Profil Saya</span>
                </a>
                <a href="{{ route('admin.riwayat') }}" class="btn-header-link">
                    <span><i class="fi fi-rr-apps"></i> Manajemen Tiket</span>
                </a>
                <a href="{{ route('admin.tiket.index') }}" class="btn-header-link">
                    <span><i class="fi fi-rr-ticket"></i> Daftar Tiket Support</span>
                </a>

                <div class="app-user-chip">
                    <!-- Professional Dark Slate Avatar (No Red) -->
                    <div class="app-user-avatar" style="background: #0f172a; color: #ffffff;">
                        {{ strtoupper(substr(Auth::user()->name ?? 'S', 0, 1)) }}
                    </div>
                    <span style="color: #1f2937; font-weight: 600;">{{ Auth::user()->name ?? 'Super Administrator' }}</span>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="display: inline; margin: 0;">
                    @csrf
                    <button type="submit" class="btn-header-logout" title="Keluar dari akun">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Body Layout -->
        <div class="app-body">
            
            <!-- Left Sidebar Navigation -->
            <aside class="app-sidebar">
                <ul class="app-nav-list">


                    <li>
                        <a href="{{ route('admin.riwayat') }}" class="app-nav-item">
                            <div class="app-nav-item-left">
                                <svg width="18" height="18" style="width: 18px; height: 18px; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/>
                                </svg>
                                <span>Manajemen Tiket & Proses</span>
                            </div>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.users.index') }}" class="app-nav-item active">
                            <div class="app-nav-item-left">
                                <svg width="18" height="18" style="width: 18px; height: 18px; flex-shrink: 0;" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                </svg>
                                <span>User Management & Log</span>
                            </div>
                            <span class="app-nav-badge" style="background: #1e293b; color: #fff;">{{ $users->total() }}</span>
                        </a>
                    </li>
                </ul>

                <div class="app-sidebar-divider"></div>
                
                <div class="app-sidebar-section">Navigasi Langsung</div>
                <ul class="app-nav-list">
                    <li>
                        <a href="#section-users" class="app-nav-item">
                            <div class="app-nav-item-left">
                                <span><i class="fi fi-rr-users"></i> Daftar Akun</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#section-create-user" class="app-nav-item">
                            <div class="app-nav-item-left">
                                <span><i class="fi fi-rr-plus"></i> Tambah Akun OPD</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#section-laptop-logs" class="app-nav-item">
                            <div class="app-nav-item-left">
                                <span><i class="fi fi-rr-laptop"></i> Log Nomor Laptop</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#section-riwayat-input" class="app-nav-item">
                            <div class="app-nav-item-left">
                                <span><i class="fi fi-rr-edit"></i> Input Riwayat DTSEN</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </aside>

            <!-- Main Content Area -->
            <main class="app-main-content">

                @if(session('success'))
                    <div class="form-alert-success" style="margin-bottom: 24px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="form-alert-error" style="margin-bottom: 24px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div class="form-alert-error" style="margin-bottom: 24px;">
                        <ul style="margin: 0; padding-left: 18px;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Hero Header -->
                <div class="dash-hero">
                    <div>
                        <h1 class="dash-hero-title">
                            Manajemen Akun Pengguna & Log Laptop
                        </h1>
                        <p class="dash-hero-subtitle">
                            Hak kendali penuh Super Admin: kelola akses instansi, pantau nomor identitas laptop yang dipakai login, dan terbitkan tabel riwayat bagi pakai data DTSEN.
                        </p>
                    </div>
                    <div class="dash-hero-actions">
                        <a href="#section-create-user" class="btn-pill-primary">
                            + Tambah Akun Perangkat Daerah
                        </a>
                        <a href="#section-riwayat-input" class="btn-pill-outline">
                            📝 Input Riwayat Bagi Data
                        </a>
                    </div>
                </div>

                <!-- KPI Metric Cards Grid (Clean Neutral Numbers) -->
                <div class="dash-metric-grid">
                    <div class="dash-metric-card">
                        <div class="dash-metric-label">Total Akun Terdaftar</div>
                        <div class="dash-metric-value">{{ $users->total() }}</div>
                        <div class="dash-metric-sub">Super Admin & Perangkat Daerah</div>
                    </div>

                    <div class="dash-metric-card">
                        <div class="dash-metric-label">Akun Perangkat Daerah (OPD)</div>
                        <div class="dash-metric-value" style="color: #0f172a;">
                            {{ $users->where('role', 'perangkat_daerah')->count() }}
                        </div>
                        <div class="dash-metric-sub">Akses terbatas: Tiket & Profile</div>
                    </div>

                    <div class="dash-metric-card">
                        <div class="dash-metric-label">Log Login Perangkat Laptop</div>
                        <div class="dash-metric-value" style="color: #0f172a;">
                            {{ $loginLogs->total() }}
                        </div>
                        <div class="dash-metric-sub">Riwayat identifikasi laptop login</div>
                    </div>

                    <div class="dash-metric-card">
                        <div class="dash-metric-label">Data Riwayat Bagi DTSEN Resmi</div>
                        <div class="dash-metric-value" style="color: #166534;">
                            {{ $riwayatBagiDatas->total() }}
                        </div>
                        <div class="dash-metric-sub">Tampil di Profile Page OPD</div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- 1. DAFTAR AKUN PENGGUNA TERDAFTAR          -->
                <!-- ========================================== -->
                <div class="dash-card" id="section-users" style="margin-bottom: 32px;">
                    <div class="section-header-box">
                        <div class="section-header-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span>Daftar Akun Pengguna Terdaftar</span>
                        </div>
                        <span style="font-size: 12px; color: #64748b; font-weight: 500;">
                            Akun Perangkat Daerah hanya dapat mengakses Form Tiket dan Profile Page (Read-only data DTSEN)
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 70px; text-align: center;">ID</th>
                                    <th style="min-width: 240px;">Nama Instansi / OPD</th>
                                    <th style="min-width: 200px;">Nama PIC / User</th>
                                    <th style="min-width: 200px;">Username & Email</th>
                                    <th style="min-width: 170px; white-space: nowrap;">Role / Hak Akses</th>
                                    <th style="min-width: 210px; white-space: nowrap;">Nomor Laptop Terakhir Login</th>
                                    <th style="min-width: 140px; white-space: nowrap;">IP Terakhir</th>
                                    <th style="min-width: 190px; white-space: nowrap;">Waktu Terakhir Login</th>
                                    <th style="width: 80px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $u)
                                    <tr>
                                        <td style="text-align: center; font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #64748b; font-weight: 600;">
                                            #{{ $u->id }}
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: #0f172a; font-size: 14px;">
                                                {{ $u->nama_instansi ?: 'Pemerintah Daerah' }}
                                            </div>
                                            @if($u->nomor_kontak)
                                                <div style="font-size: 12px; color: #64748b; margin-top: 3px;">
                                                    Kontak: {{ $u->nomor_kontak }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #1e293b;">
                                                {{ $u->name }}
                                            </div>
                                        </td>
                                        <td>
                                            <!-- Clean dark text (No bright blue) -->
                                            <div style="font-weight: 700; color: #0f172a; font-size: 13px;">
                                                {{ $u->username }}
                                            </div>
                                            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                                {{ $u->email }}
                                            </div>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            @if($u->isSuperAdmin())
                                                <!-- Clean slate badge (No Red) -->
                                                <span class="role-badge-super">
                                                    <i class="fi fi-rr-crown"></i> Super Admin
                                                </span>
                                            @else
                                                <span class="role-badge-opd">
                                                    <i class="fi fi-rr-building"></i> Perangkat Daerah
                                                </span>
                                            @endif
                                        </td>
                                        <td style="white-space: nowrap;">
                                            @if($u->last_login_device)
                                                <span class="device-badge" title="Device ID Laptop">
                                                    <i class="fi fi-rr-laptop"></i> {{ $u->last_login_device }}
                                                </span>
                                            @else
                                                <span style="font-size: 12px; color: #94a3b8; font-style: italic;">Belum pernah login</span>
                                            @endif
                                        </td>
                                        <td style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #475569; white-space: nowrap;">
                                            {{ $u->last_login_ip ?: '-' }}
                                        </td>
                                        <td style="font-size: 12px; color: #334155; white-space: nowrap;">
                                            @if($u->last_login_at)
                                                {{ \Carbon\Carbon::parse($u->last_login_at)->format('d/m/Y H:i') }} WIB
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            @if($u->id !== auth()->id() && !$u->isSuperAdmin())
                                                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Hapus akun instansi {{ $u->username }}?');" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action-icon delete" title="Hapus Akun">
                                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <span style="font-size: 11px; color: #94a3b8; font-weight: 500;">Protected</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" style="text-align: center; padding: 36px 20px; color: #64748b;">
                                            Belum ada data pengguna terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($users->hasPages())
                        <div class="dash-pagination">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>

                <!-- ==================================================== -->
                <!-- 2. FORM TAMBAH AKUN PERANGKAT DAERAH (OPD)           -->
                <!-- ==================================================== -->
                <div class="dash-card" id="section-create-user" style="padding: 24px 28px; margin-bottom: 32px;">
                    <div style="margin-bottom: 20px; border-bottom: 1px solid #edf2f7; padding-bottom: 14px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <h2 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0;">
                                <i class="fi fi-rr-plus"></i> Tambah Akun Perangkat Daerah Baru
                            </h2>
                            <p style="font-size: 13px; color: #64748b; margin: 0;">
                                Buatkan akun untuk instansi dinas/badan/kantor agar dapat mengajukan tiket bagi pakai data dan memantau progress di halaman profile mereka.
                            </p>
                        </div>
                        <span class="role-badge-opd">Hak Akses: OPD (Terbatas)</span>
                    </div>

                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; margin-bottom: 20px;">
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                    Nama Instansi / Perangkat Daerah <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="text" name="nama_instansi" class="form-input" placeholder="Contoh: Dinas Kesehatan Kota Surabaya" required value="{{ old('nama_instansi') }}">
                            </div>

                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                    Nama Lengkap PIC / Pemohon <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="text" name="name" class="form-input" placeholder="Contoh: Budi Prasetyo, S.Kom" required value="{{ old('name') }}">
                            </div>

                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                    Username Login <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="text" name="username" class="form-input" placeholder="Contoh: dinkes" required value="{{ old('username') }}">
                            </div>

                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                    Alamat Email <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="email" name="email" class="form-input" placeholder="Contoh: dinkes@daerah.go.id" required value="{{ old('email') }}">
                            </div>

                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                    Kata Sandi (Password) <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="password" name="password" class="form-input" placeholder="Minimal 4 karakter" required>
                            </div>

                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                    Peran / Role <span style="color: #dc2626;">*</span>
                                </label>
                                <select name="role" class="form-input">
                                    <option value="perangkat_daerah" selected>Perangkat Daerah (Hanya Tiket & Profile)</option>
                                    <option value="superadmin">Super Administrator (Hak Kendali Penuh)</option>
                                </select>
                            </div>

                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                    Nomor Telepon / WhatsApp
                                </label>
                                <input type="text" name="nomor_kontak" class="form-input" placeholder="Contoh: 0812-3456-7890" value="{{ old('nomor_kontak') }}">
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 10px;">
                            <button type="submit" class="btn-pill-primary" style="height: 42px; padding: 0 24px;">
                                <i class="fi fi-rr-disk"></i> Simpan & Terbitkan Akun
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ==================================================== -->
                <!-- 3. LOG NOMOR LAPTOP / PERANGKAT YANG DIGUNAKAN LOGIN -->
                <!-- ==================================================== -->
                <div class="dash-card" id="section-laptop-logs" style="margin-bottom: 32px;">
                    <div class="section-header-box">
                        <div class="section-header-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                <line x1="8" y1="21" x2="16" y2="21"></line>
                                <line x1="12" y1="17" x2="12" y2="21"></line>
                            </svg>
                            <span>Log Nomor Laptop & Perangkat yang Digunakan Login</span>
                        </div>
                        <span style="font-size: 12px; color: #64748b; font-weight: 500;">
                            Audit jejak digital identitas laptop/device setiap kali ada akun yang melakukan login
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 80px; text-align: center;">ID Log</th>
                                    <th style="min-width: 200px; white-space: nowrap;">Waktu & Tanggal</th>
                                    <th style="min-width: 230px;">Akun Pengguna</th>
                                    <th style="min-width: 210px; white-space: nowrap;">Nomor Laptop / Device ID</th>
                                    <th style="min-width: 140px; white-space: nowrap;">Alamat IP</th>
                                    <th style="min-width: 120px; white-space: nowrap;">Status</th>
                                    <th style="min-width: 320px;">Aktivitas & Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loginLogs as $log)
                                    <tr>
                                        <td style="text-align: center; font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #64748b;">
                                            #{{ $log->id }}
                                        </td>
                                        <td style="font-size: 12px; white-space: nowrap; color: #334155;">
                                            {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i:s') }} WIB
                                        </td>
                                        <td>
                                            <!-- Clean dark text (No bright blue) -->
                                            <div style="font-weight: 700; color: #0f172a; font-size: 13px;">
                                                {{ $log->user ? $log->user->username : 'Sistem / Anonim' }}
                                            </div>
                                            @if($log->user && $log->user->nama_instansi)
                                                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                                    {{ $log->user->nama_instansi }}
                                                </div>
                                            @endif
                                        </td>
                                        <td style="white-space: nowrap;">
                                            @php
                                                $laptopId = '-';
                                                if (preg_match('/\[(LAPTOP-[^\]]+|WIN-[^\]]+|DEV-[^\]]+|MAC-[^\]]+)\]/', $log->description, $matches)) {
                                                    $laptopId = $matches[1];
                                                } elseif ($log->user && $log->user->last_login_device) {
                                                    $laptopId = $log->user->last_login_device;
                                                }
                                            @endphp
                                            <span class="device-badge">
                                                <i class="fi fi-rr-laptop"></i> {{ $laptopId }}
                                            </span>
                                        </td>
                                        <td style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #475569; white-space: nowrap;">
                                            {{ $log->ip_address }}
                                        </td>
                                        <td>
                                            @if($log->status === 'SUCCESS')
                                                <span style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;">
                                                    BERHASIL
                                                </span>
                                            @else
                                                <span style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca;">
                                                    {{ $log->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td style="font-size: 12px; color: #334155; line-height: 1.5;">
                                            {{ $log->description }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 36px 20px; color: #64748b;">
                                            Belum ada log login laptop yang tercatat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($loginLogs->hasPages())
                        <div class="dash-pagination">
                            {{ $loginLogs->links() }}
                        </div>
                    @endif
                </div>

                <!-- ============================================================== -->
                <!-- 4. INPUT & KELOLA DATA RIWAYAT BAGI DATA DTSEN RESMI (5 VAR)   -->
                <!-- ============================================================== -->
                <div class="dash-card" id="section-riwayat-input" style="padding: 24px 28px; margin-bottom: 32px;">
                    <div style="margin-bottom: 20px; border-bottom: 1px solid #edf2f7; padding-bottom: 14px;">
                        <h2 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0;">
                            <i class="fi fi-rr-edit"></i> Input & Terbitkan Riwayat Bagi Pakai Data DTSEN Resmi
                        </h2>
                        <p style="font-size: 13px; color: #64748b; margin: 0;">
                            Data tabel ini diinput oleh Admin berdasarkan tiket yang telah diverifikasi dan <strong>akan langsung tampil di Profile Page Akun Perangkat Daerah terkait (Read-Only)</strong>.
                        </p>
                    </div>

                    <form action="{{ route('admin.riwayat-bagi-data.store') }}" method="POST">
                        @csrf

                        <!-- Auto-fill Selector dari Tiket Masuk -->
                        @if($availableTikets->count() > 0)
                            <div style="background: #f8fafc; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
                                <label style="display: block; font-size: 12px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                                    <i class="fi fi-rr-bulb"></i> Pilih Tiket Masuk untuk Mengisi Otomatis (Opsional):
                                </label>
                                <select id="tiketSelector" class="form-input" style="background: #fff;" onchange="autoFillFromTiket(this)">
                                    <option value="">-- Pilih Tiket DTSEN Terdaftar --</option>
                                    @foreach($availableTikets as $t)
                                        <option value="{{ $t->id }}"
                                                data-instansi="{{ $t->nama_instansi }}"
                                                data-surat="{{ $t->no_surat_edaran }}"
                                                data-tanggal="{{ \Carbon\Carbon::parse($t->created_at)->format('Y-m-d\TH:i') }}"
                                                data-pemohon="{{ $t->nama_pemohon }}"
                                                data-userid="{{ $t->user_id }}">
                                            Tiket #{{ $t->nomor_tiket }} — {{ $t->nama_instansi }} ({{ $t->no_surat_edaran }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <input type="hidden" name="tiket_dtsen_id" id="input_tiket_id">
                        <input type="hidden" name="user_id" id="input_user_id">

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px; margin-bottom: 20px;">
                            <!-- 1. Nama Instansi -->
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                    1. Nama Instansi Perangkat Daerah <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="text" name="nama_instansi" id="input_nama_instansi" class="form-input" placeholder="Contoh: Dinas Sosial Kota..." required>
                            </div>

                            <!-- 2. Surat Edaran -->
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                    2. Nomor Surat Edaran / Dasar Permohonan <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="text" name="no_surat_edaran" id="input_no_surat_edaran" class="form-input" placeholder="Contoh: SE/045/DTSEN/2026" required>
                            </div>

                            <!-- 3. Tanggal dan Waktu Tiket Dibuat -->
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                    3. Tanggal & Waktu Tiket Dibuat <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="datetime-local" name="tanggal_tiket_dibuat" id="input_tanggal_tiket" class="form-input" value="{{ date('Y-m-d\TH:i') }}" required>
                            </div>

                            <!-- 4. Tanda Tangan BAST / Proses Bagi Data Selesai -->
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                    4. Tanda Tangan BAST / Tanggal Penyelesaian
                                </label>
                                <input type="datetime-local" name="tanggal_bast_selesai" id="input_tanggal_bast" class="form-input" value="{{ date('Y-m-d\TH:i') }}">
                            </div>

                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                    Nomor BAST (Berita Acara Serah Terima)
                                </label>
                                <input type="text" name="no_bast" class="form-input" placeholder="Contoh: BAST/DTSEN/2026/089">
                            </div>

                            <!-- 5. Orang Perangkat Daerah yang Hadir & IP/Laptop -->
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                    5. IP atau Nomor Laptop yang Digunakan <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="text" name="ip_atau_nomor_laptop" id="input_laptop" class="form-input" placeholder="Contoh: LAPTOP-DINSOS-01 (IP: 192.168.1.105)" required>
                            </div>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                Orang Perangkat Daerah yang Hadir dalam Proses Bagi Data <span style="color: #dc2626;">*</span>
                            </label>
                            <textarea name="orang_hadir" id="input_orang_hadir" class="form-input" style="height: 75px;" placeholder="Tuliskan nama-nama pejabat/petugas OPD yang hadir (contoh: 1. Ahmad Fauzi (Kabid Data), 2. Siti Rahma (Operator Simda))" required></textarea>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                Status Pengesahan BAST
                            </label>
                            <select name="status_bast" class="form-input">
                                <option value="SELESAI DITANDATANGANI (BAST SAH)" selected>✅ SELESAI DITANDATANGANI (BAST SAH)</option>
                                <option value="PROSES VERIFIKASI TANDA TANGAN">⏳ PROSES VERIFIKASI TANDA TANGAN</option>
                                <option value="BAGI DATA SELESAI">🎉 BAGI DATA SELESAI</option>
                            </select>
                        </div>

                        <div style="display: flex; justify-content: flex-end;">
                            <button type="submit" class="btn-pill-primary" style="height: 42px; padding: 0 24px;">
                                <i class="fi fi-rr-paper-plane"></i> Terbitkan Riwayat ke Profile OPD
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ============================================================== -->
                <!-- 5. TABEL DAFTAR RIWAYAT BAGI DATA DTSEN YANG SUDAH DITERBITKAN -->
                <!-- ============================================================== -->
                <div class="dash-card" style="margin-bottom: 40px;">
                    <div class="section-header-box">
                        <div class="section-header-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            <span>Daftar Riwayat Bagi Data DTSEN Resmi Terbit (5 Variabel)</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 60px; text-align: center;">No</th>
                                    <th style="min-width: 220px;">1. Nama Instansi</th>
                                    <th style="min-width: 200px;">2. Surat Edaran</th>
                                    <th style="min-width: 180px; white-space: nowrap;">3. Waktu Tiket Dibuat</th>
                                    <th style="min-width: 240px;">4. Tanda Tangan BAST / Selesai</th>
                                    <th style="min-width: 320px;">5. Orang Hadir & IP/Laptop</th>
                                    <th style="min-width: 140px; white-space: nowrap;">Penginput</th>
                                    <th style="width: 80px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatBagiDatas as $idx => $r)
                                    <tr>
                                        <td style="text-align: center; font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #64748b;">
                                            {{ $idx + 1 }}
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: #0f172a; font-size: 14px;">
                                                {{ $r->nama_instansi }}
                                            </div>
                                        </td>
                                        <td>
                                            <span style="font-family: 'JetBrains Mono', monospace; font-size: 11px; background: #f1f5f9; color: #334155; padding: 4px 8px; border-radius: 4px; border: 1px solid #e2e8f0;">
                                                {{ $r->no_surat_edaran }}
                                            </span>
                                        </td>
                                        <td style="font-size: 12px; white-space: nowrap; color: #334155;">
                                            <i class="fi fi-rr-calendar"></i> {{ \Carbon\Carbon::parse($r->tanggal_tiket_dibuat)->format('d/m/Y H:i') }} WIB
                                        </td>
                                        <td>
                                            <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; margin-bottom: 4px;">
                                                {{ $r->status_bast }}
                                            </span>
                                            @if($r->tanggal_bast_selesai)
                                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                                    Tgl Selesai: {{ \Carbon\Carbon::parse($r->tanggal_bast_selesai)->format('d/m/Y H:i') }}
                                                </div>
                                            @endif
                                            @if($r->no_bast)
                                                <!-- Clean dark text (No bright blue) -->
                                                <div style="font-size: 11px; font-family: 'JetBrains Mono', monospace; color: #334155; font-weight: 600; margin-top: 2px;">
                                                    {{ $r->no_bast }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="font-size: 12px; font-weight: 600; color: #1e293b; line-height: 1.45;">
                                                <i class="fi fi-rr-users"></i> {{ $r->orang_hadir }}
                                            </div>
                                            <div style="margin-top: 6px;">
                                                <span class="device-badge">
                                                    <i class="fi fi-rr-laptop"></i> {{ $r->ip_atau_nomor_laptop }}
                                                </span>
                                            </div>
                                        </td>
                                        <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                                            {{ $r->created_by }}
                                        </td>
                                        <td style="text-align: center;">
                                            <form action="{{ route('admin.riwayat-bagi-data.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus data riwayat bagi pakai ini?');" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action-icon delete" title="Hapus Riwayat">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" style="text-align: center; padding: 36px 20px; color: #64748b;">
                                            Belum ada data riwayat bagi pakai data DTSEN yang diinputkan. Gunakan form di atas untuk menerbitkan data baru.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($riwayatBagiDatas->hasPages())
                        <div class="dash-pagination">
                            {{ $riwayatBagiDatas->links() }}
                        </div>
                    @endif
                </div>

            </main>
        </div>
    </div>

    <script>
        function autoFillFromTiket(select) {
            const opt = select.options[select.selectedIndex];
            if (!opt.value) return;

            document.getElementById('input_tiket_id').value = opt.value;
            document.getElementById('input_user_id').value = opt.dataset.userid || '';
            document.getElementById('input_nama_instansi').value = opt.dataset.instansi || '';
            document.getElementById('input_no_surat_edaran').value = opt.dataset.surat || '';
            if (opt.dataset.tanggal) {
                document.getElementById('input_tanggal_tiket').value = opt.dataset.tanggal;
            }
            if (opt.dataset.pemohon) {
                document.getElementById('input_orang_hadir').value = opt.dataset.pemohon + ' (Perwakilan Instansi)';
            }
        }
    </script>
</body>
</html>
