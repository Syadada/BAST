@extends('layouts.admin')

@section('title', 'Manajemen Pengguna — Admin Panel')

@section('content')
<div class="page-top-bar">
    <div>
        <h1 class="page-top-title">
            <i class="fi fi-rr-users"></i>
            <span>Manajemen Pengguna & Riwayat</span>
        </h1>
        <p class="page-top-subtitle">
            Kelola akun pengguna, pembuatan akun Perangkat Daerah (OPD), dan input penerbitan riwayat bagi data resmi DTSEN.
        </p>
    </div>
</div>

<!-- SUB PAGE MENU / TAB NAVIGATION BAR -->
<div class="sub-page-nav-bar" id="users-tab-bar">
    <button type="button" class="sub-page-tab-item {{ $activeTab === 'users' ? 'active' : '' }}" onclick="switchUserTab('users')" id="btn-tab-users">
        <i class="fi fi-rr-users-alt sub-page-tab-icon"></i>
        <span>Daftar Akun Pengguna</span>
        <span class="sub-page-tab-count">{{ $totalUsers }}</span>
    </button>
    
    <button type="button" class="sub-page-tab-item {{ $activeTab === 'tambah-user' ? 'active' : '' }}" onclick="switchUserTab('tambah-user')" id="btn-tab-tambah-user">
        <i class="fi fi-rr-user-add sub-page-tab-icon"></i>
        <span>Tambah Akun Baru</span>
    </button>

    <button type="button" class="sub-page-tab-item {{ $activeTab === 'input-riwayat' ? 'active' : '' }}" onclick="switchUserTab('input-riwayat')" id="btn-tab-input-riwayat">
        <i class="fi fi-rr-edit sub-page-tab-icon"></i>
        <span>Input Riwayat Bagi Data</span>
    </button>

    <button type="button" class="sub-page-tab-item {{ $activeTab === 'riwayat-terbit' ? 'active' : '' }}" onclick="switchUserTab('riwayat-terbit')" id="btn-tab-riwayat-terbit">
        <i class="fi fi-rr-document-signed sub-page-tab-icon"></i>
        <span>Riwayat BAST Terbit (Profil OPD)</span>
        <span class="sub-page-tab-count">{{ $riwayatBagiDatas->total() }}</span>
    </button>
</div>

<!-- ============================================================== -->
<!-- SUB-PAGE 1: DAFTAR AKUN PENGGUNA TERDAFTAR (RINGKAS ALA TIKET) -->
<!-- ============================================================== -->
<div class="sub-page-panel {{ $activeTab === 'users' ? 'active' : '' }}" id="panel-users">
    <!-- Ringkasan Statistik Pengguna -->
    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total Akun Terdaftar</div>
            <div class="admin-stat-val admin-stat-val-blue">{{ number_format($totalUsers, 0, ',', '.') }}</div>
            <div class="admin-stat-sub">Seluruh pengguna aktif di sistem</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Akun Perangkat Daerah</div>
            <div class="admin-stat-val admin-stat-val-green">{{ number_format($totalOpd, 0, ',', '.') }}</div>
            <div class="admin-stat-sub">Dinas, Badan, dan Satker pemohon</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Super Administrator</div>
            <div class="admin-stat-val admin-stat-val-purple">{{ number_format($totalAdmin, 0, ',', '.') }}</div>
            <div class="admin-stat-sub">Pengelola sistem kendali penuh</div>
        </div>
    </div>

    <div class="admin-card-section">
        <div class="admin-card-header">
            <div class="admin-card-header-left">
                <h2 class="admin-card-header-title">
                    <i class="fi fi-rr-users-alt"></i>
                    <span>Daftar Akun Pengguna Terdaftar</span>
                </h2>
            </div>
            <div class="admin-card-header-sub">
                Klik pada <strong>Username</strong> untuk melihat informasi akun, kontak, dan jejak perangkat secara detail.
            </div>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Instansi / Perangkat Daerah</th>
                        <th>Akun Pengguna (Username)</th>
                        <th class="table-col-nowrap">Role / Hak Akses</th>
                        <th class="table-col-nowrap">Terakhir Login</th>
                        <th class="table-col-action">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <!-- 1. Nama Instansi & PIC -->
                            <td>
                                <div class="user-title-bold">
                                    {{ $u->nama_instansi ?: 'Pemerintah Daerah' }}
                                </div>
                                <div class="user-sub-muted">
                                    PIC: {{ $u->name }}
                                    @if($u->nomor_kontak)
                                        • {{ $u->nomor_kontak }}
                                    @endif
                                </div>
                            </td>

                            <!-- 2. Username (Clickable Link Biru ke Halaman Detail) -->
                            <td>
                                <a href="{{ route('admin.users.show', $u->id) }}" class="user-subject-link" title="Buka Detail Akun {{ $u->username }}">
                                    <span class="user-subject-uname">{{ $u->username }}</span>
                                </a>
                                <span class="user-subject-subtitle">{{ $u->email }}</span>
                            </td>

                            <!-- 3. Role / Hak Akses -->
                            <td class="table-col-nowrap">
                                @if($u->role === 'superadmin')
                                    <span class="role-badge-super">
                                        <i class="fi fi-rr-shield-check"></i>
                                        Super Admin
                                    </span>
                                @else
                                    <span class="role-badge-opd">
                                        <i class="fi fi-rr-building"></i>
                                        Perangkat Daerah
                                    </span>
                                @endif
                            </td>

                            <!-- 4. Terakhir Login -->
                            <td class="table-col-nowrap">
                                @if($u->last_login_at)
                                    <div class="user-title-name">{{ \Carbon\Carbon::parse($u->last_login_at)->format('d M Y') }}</div>
                                    <div class="user-sub-muted">{{ \Carbon\Carbon::parse($u->last_login_at)->format('H:i') }} WIB</div>
                                @else
                                    <span class="user-sub-muted">Belum Pernah Login</span>
                                @endif
                            </td>

                            <!-- 5. Aksi -->
                            <td class="table-col-action">
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->username }} ({{ $u->nama_instansi }})? Tindakan ini tidak dapat dibatalkan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="table-btn-delete" title="Hapus Akun Pengguna">
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="role-badge-super">Aktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="table-empty-box">
                                <div class="table-empty-icon">
                                    <i class="fi fi-rr-users-slash"></i>
                                </div>
                                <div class="table-empty-text">Belum ada akun pengguna terdaftar.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="admin-card-pagination">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- ============================================================== -->
<!-- SUB-PAGE 2: FORM TAMBAH AKUN BARU                              -->
<!-- ============================================================== -->
<div class="sub-page-panel {{ $activeTab === 'tambah-user' ? 'active' : '' }}" id="panel-tambah-user">
    <div class="admin-card-section">
        <div class="admin-card-header">
            <div class="admin-card-header-left">
                <h2 class="admin-card-header-title">
                    <i class="fi fi-rr-user-add"></i>
                    <span>Formulir Penerbitan Akun Pengguna Baru</span>
                </h2>
            </div>
            <span class="role-badge-opd">Hak Akses: OPD / Super Admin</span>
        </div>

        <div class="admin-card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="admin-form-grid-3">
                    <div class="admin-form-group">
                        <label class="admin-form-label">
                            Nama Instansi / Perangkat Daerah <span class="admin-form-req">*</span>
                        </label>
                        <input type="text" name="nama_instansi" class="form-input" placeholder="Contoh: Dinas Kesehatan Kota Surabaya" required value="{{ old('nama_instansi') }}">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">
                            Nama Lengkap PIC / Pemohon <span class="admin-form-req">*</span>
                        </label>
                        <input type="text" name="name" class="form-input" placeholder="Contoh: Budi Prasetyo, S.Kom" required value="{{ old('name') }}">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">
                            Username Login <span class="admin-form-req">*</span>
                        </label>
                        <input type="text" name="username" class="form-input" placeholder="Contoh: dinkes" required value="{{ old('username') }}">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">
                            Alamat Email <span class="admin-form-req">*</span>
                        </label>
                        <input type="email" name="email" class="form-input" placeholder="Contoh: dinkes@daerah.go.id" required value="{{ old('email') }}">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">
                            Kata Sandi (Password) <span class="admin-form-req">*</span>
                        </label>
                        <input type="password" name="password" class="form-input" placeholder="Minimal 4 karakter" required>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">
                            Peran / Role <span class="admin-form-req">*</span>
                        </label>
                        <select name="role" class="form-input">
                            <option value="perangkat_daerah" selected>Perangkat Daerah (Hanya Tiket & Profile)</option>
                            <option value="superadmin">Super Administrator (Hak Kendali Penuh)</option>
                        </select>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">
                            Nomor Telepon / WhatsApp
                        </label>
                        <input type="text" name="nomor_kontak" class="form-input" placeholder="Contoh: 0812-3456-7890" value="{{ old('nomor_kontak') }}">
                    </div>
                </div>

                <div class="admin-form-actions">
                    <button type="submit" class="btn-pill-primary">
                        <i class="fi fi-rr-disk"></i>
                        <span>Simpan & Terbitkan Akun</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- SUB-PAGE 3: FORM INPUT RIWAYAT BAGI DATA DTSEN RESMI           -->
<!-- ============================================================== -->
<div class="sub-page-panel {{ $activeTab === 'input-riwayat' ? 'active' : '' }}" id="panel-input-riwayat">
    <div class="admin-card-section">
        <div class="admin-card-header">
            <div class="admin-card-header-left">
                <h2 class="admin-card-header-title">
                    <i class="fi fi-rr-edit"></i>
                    <span>Form Input Riwayat Bagi Data DTSEN Resmi (5 Variabel)</span>
                </h2>
            </div>
            <div class="admin-card-header-sub">
                Data yang diinputkan akan langsung tampil pada riwayat profile OPD yang bersangkutan
            </div>
        </div>

        <div class="admin-card-body">
            <div class="admin-form-full">
                <label class="admin-form-label">
                    Pilih Dari Tiket Terdaftar (Otomatis Mengisi Form di Bawah)
                </label>
                <select id="select-tiket-autofill" class="form-input" onchange="autoFillFromTiket(this)">
                    <option value="">-- Ketik / Pilih Tiket yang Sudah Ada (Opsional) --</option>
                    @foreach($availableTikets as $t)
                        <option value="{{ $t->id }}" 
                            data-instansi="{{ $t->nama_opd ?: ($t->user ? $t->user->nama_instansi : '') }}"
                            data-tiket-id="{{ $t->id }}"
                            data-created="{{ \Carbon\Carbon::parse($t->created_at)->format('Y-m-d\TH:i') }}"
                            data-pemohon="{{ $t->nama_pemohon }}"
                            data-nip="{{ $t->nip_pemohon }}"
                            data-jabatan="{{ $t->jabatan_pemohon }}"
                            data-laptop="{{ $t->nomor_laptop_pemohon }}"
                            data-ip="{{ $t->ip_address_pemohon }}">
                            #{{ $t->id }} - {{ $t->nama_opd ?: ($t->user ? $t->user->nama_instansi : 'OPD') }} ({{ $t->nama_pemohon }}) - Status: {{ $t->status_badge }}
                        </option>
                    @endforeach
                </select>
            </div>

            <form action="{{ route('admin.riwayat-bagi-data.store') }}" method="POST">
                @csrf
                <div class="admin-form-grid-3">
                    <div class="admin-form-group">
                        <label class="admin-form-label">
                            1. Nama Perangkat Daerah / OPD <span class="admin-form-req">*</span>
                        </label>
                        <input type="text" name="nama_instansi" id="input_nama_opd" class="form-input" placeholder="Contoh: Dinas Sosial Kota Surabaya" required>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">
                            Nomor ID Tiket DTSEN Terkait
                        </label>
                        <input type="number" name="tiket_dtsen_id" id="input_tiket_id" class="form-input" placeholder="Contoh: 12 (Boleh dikosongkan)">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">
                            2. Nomor Surat Edaran / Dasar Permohonan <span class="admin-form-req">*</span>
                        </label>
                        <input type="text" name="no_surat_edaran" id="input_no_surat_edaran" class="form-input" placeholder="Contoh: SE/045/DTSEN/2026" required>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">
                            3. Tanggal & Waktu Tiket Dibuat <span class="admin-form-req">*</span>
                        </label>
                        <input type="datetime-local" name="tanggal_tiket_dibuat" id="input_tanggal_tiket" class="form-input" value="{{ date('Y-m-d\TH:i') }}" required>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">
                            4. Tanda Tangan BAST / Tanggal Penyelesaian
                        </label>
                        <input type="datetime-local" name="tanggal_bast_selesai" id="input_tanggal_bast" class="form-input" value="{{ date('Y-m-d\TH:i') }}">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">
                            Nomor BAST (Berita Acara Serah Terima)
                        </label>
                        <input type="text" name="no_bast" class="form-input" placeholder="Contoh: BAST/DTSEN/2026/089">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">
                            5. IP atau Nomor Laptop yang Digunakan <span class="admin-form-req">*</span>
                        </label>
                        <input type="text" name="ip_atau_nomor_laptop" id="input_laptop" class="form-input" placeholder="Contoh: LAPTOP-DINSOS-01 (IP: 192.168.1.105)" required>
                    </div>
                </div>

                <div class="admin-form-full">
                    <label class="admin-form-label">
                        Orang Perangkat Daerah yang Hadir dalam Proses Bagi Data <span class="admin-form-req">*</span>
                    </label>
                    <textarea name="orang_hadir" id="input_orang_hadir" class="form-input admin-form-textarea-sm" placeholder="Tuliskan nama-nama pejabat/petugas OPD yang hadir (contoh: 1. Ahmad Fauzi (Kabid Data), 2. Siti Rahma (Operator Simda))" required></textarea>
                </div>

                <div class="admin-form-full">
                    <label class="admin-form-label">
                        Status Pengesahan BAST
                    </label>
                    <select name="status_bast" class="form-input">
                        <option value="SELESAI DITANDATANGANI (BAST SAH)" selected>SELESAI DITANDATANGANI (BAST SAH)</option>
                        <option value="PROSES VERIFIKASI TANDA TANGAN">PROSES VERIFIKASI TANDA TANGAN</option>
                        <option value="BAGI DATA SELESAI">BAGI DATA SELESAI</option>
                    </select>
                </div>

                <div class="admin-form-actions">
                    <button type="submit" class="btn-pill-primary">
                        <i class="fi fi-rr-paper-plane"></i>
                        <span>Terbitkan Riwayat ke Profile OPD</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- SUB-PAGE 4: TABEL RIWAYAT BAGI DATA TERBIT                      -->
<!-- ============================================================== -->
<div class="sub-page-panel {{ $activeTab === 'riwayat-terbit' ? 'active' : '' }}" id="panel-riwayat-terbit">
    <div class="admin-card-section">
        <div class="admin-card-header">
            <div class="admin-card-header-left">
                <h2 class="admin-card-header-title">
                    <i class="fi fi-rr-document-signed"></i>
                    <span>Daftar Riwayat Bagi Data DTSEN Resmi Terbit (5 Variabel)</span>
                </h2>
            </div>
            <div class="admin-card-header-sub">
                Semua data di tabel ini langsung tampil di Halaman Profil / Layanan Bagi Pakai masing-masing OPD
            </div>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="table-col-id">No</th>
                        <th>1. Nama Perangkat Daerah</th>
                        <th>2. No Surat Edaran / Dasar</th>
                        <th class="table-col-nowrap">3. Tanggal Tiket Dibuat</th>
                        <th class="table-col-nowrap">4. TTD BAST / Selesai</th>
                        <th>5. Orang Hadir & Laptop / IP</th>
                        <th class="table-col-nowrap">Status BAST</th>
                        <th class="table-col-action">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatBagiDatas as $idx => $r)
                        <tr>
                            <td class="table-col-id">
                                {{ $riwayatBagiDatas->firstItem() + $idx }}
                            </td>
                            <td>
                                <div class="user-title-bold">
                                    {{ $r->nama_instansi }}
                                </div>
                                @if($r->tiket_dtsen_id)
                                    <div class="user-sub-muted">
                                        Tiket Terkait: #{{ $r->tiket_dtsen_id }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.riwayat-bagi-data.show', $r->id) }}" class="badge-se-clickable" title="Buka Detail Berita Acara, Petugas Hadir & Laptop">
                                    <i class="fi fi-rr-document"></i>
                                    <span>{{ $r->no_surat_edaran ?: '-' }}</span>
                                </a>
                                @if($r->no_bast)
                                    <div class="user-sub-muted" style="margin-top: 4px;">
                                        BAST: {{ $r->no_bast }}
                                    </div>
                                @endif
                            </td>
                            <td class="table-col-nowrap">
                                @if($r->tanggal_tiket_dibuat)
                                    <div class="user-title-name">{{ \Carbon\Carbon::parse($r->tanggal_tiket_dibuat)->format('d M Y') }}</div>
                                    <div class="user-sub-muted">{{ \Carbon\Carbon::parse($r->tanggal_tiket_dibuat)->format('H:i') }} WIB</div>
                                @else
                                    <span class="user-sub-muted">-</span>
                                @endif
                            </td>
                            <td class="table-col-nowrap">
                                @if($r->tanggal_bast_selesai)
                                    <div class="user-title-name">{{ \Carbon\Carbon::parse($r->tanggal_bast_selesai)->format('d M Y') }}</div>
                                    <div class="user-sub-muted">{{ \Carbon\Carbon::parse($r->tanggal_bast_selesai)->format('H:i') }} WIB</div>
                                @else
                                    <span class="user-sub-muted">Dalam Proses</span>
                                @endif
                            </td>
                            <td>
                                <div class="user-title-name">
                                    {{ $r->orang_hadir ?: '-' }}
                                </div>
                                <div class="user-sub-muted">
                                    Perangkat: <span class="device-badge">{{ $r->ip_atau_nomor_laptop ?: '-' }}</span>
                                </div>
                            </td>
                            <td class="table-col-nowrap">
                                <span class="role-badge-opd">
                                    <i class="fi fi-rr-check-circle"></i>
                                    {{ $r->status_bast }}
                                </span>
                            </td>
                            <td class="table-col-action">
                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                    <a href="{{ route('admin.riwayat-bagi-data.show', $r->id) }}" class="table-btn-view" title="Lihat Detail Riwayat & Perangkat">
                                        <i class="fi fi-rr-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.riwayat-bagi-data.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data riwayat bagi pakai ini? Data di profil OPD juga akan terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="table-btn-delete" title="Hapus Riwayat">
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="table-empty-box">
                                <div class="table-empty-icon">
                                    <i class="fi fi-rr-document-signed"></i>
                                </div>
                                <div class="table-empty-text">Belum ada riwayat bagi data resmi yang diterbitkan. Gunakan sub-menu "Input Riwayat Bagi Data" untuk menerbitkan riwayat.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($riwayatBagiDatas->hasPages())
            <div class="admin-card-pagination">
                {{ $riwayatBagiDatas->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    function switchUserTab(tabName) {
        // Update URL query string without reloading page
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.replaceState({}, '', url);

        // Update tab buttons
        const tabButtons = document.querySelectorAll('#users-tab-bar .sub-page-tab-item');
        tabButtons.forEach(btn => btn.classList.remove('active'));
        const activeBtn = document.getElementById('btn-tab-' + tabName);
        if (activeBtn) activeBtn.classList.add('active');

        // Update panels
        const panels = document.querySelectorAll('.sub-page-panel');
        panels.forEach(panel => panel.classList.remove('active'));
        const targetPanel = document.getElementById('panel-' + tabName);
        if (targetPanel) targetPanel.classList.add('active');
    }

    function autoFillFromTiket(select) {
        const option = select.options[select.selectedIndex];
        if (!option.value) return;

        const instansi = option.getAttribute('data-instansi') || '';
        const tiketId = option.getAttribute('data-tiket-id') || '';
        const created = option.getAttribute('data-created') || '';
        const pemohon = option.getAttribute('data-pemohon') || '';
        const nip = option.getAttribute('data-nip') || '';
        const jabatan = option.getAttribute('data-jabatan') || '';
        const laptop = option.getAttribute('data-laptop') || '';
        const ip = option.getAttribute('data-ip') || '';

        document.getElementById('input_nama_opd').value = instansi;
        document.getElementById('input_tiket_id').value = tiketId;
        if (created) {
            document.getElementById('input_tanggal_tiket').value = created;
        }

        let deviceString = '';
        if (laptop && laptop !== '-') {
            deviceString = laptop;
        }
        if (ip && ip !== '-') {
            deviceString += (deviceString ? ' (IP: ' + ip + ')' : ip);
        }
        document.getElementById('input_laptop').value = deviceString || 'LAPTOP-OPD-RESMI';

        let orang = '';
        if (pemohon) {
            orang = pemohon;
            if (jabatan) orang += ' (' + jabatan + ')';
            if (nip) orang += ' - NIP: ' + nip;
        }
        document.getElementById('input_orang_hadir').value = orang || 'Pejabat/Staf Teknis ' + instansi;
    }
</script>
@endsection
