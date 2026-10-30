@extends('layouts.opd')

@section('title', 'Layanan Bagi Pakai DTSEN — Portal Perangkat Daerah')
@section('page_title', 'Layanan Bagi Pakai DTSEN')
@section('page_subtitle', 'Pantauan Progress Alur & Rekapitulasi Riwayat Data Resmi')

@section('content')
<div class="ticket-stack-container">

    <!-- 1. Header Profil Akun Perangkat Daerah & Nomor Laptop Login -->
    <div class="form-card ticket-section-card">
        <div class="ticket-top-actions">
            <div>
                <div class="form-badge-system form-group-mb-16">
                    <span><i class="fi fi-rr-building"></i> AKUN PERANGKAT DAERAH (OPD)</span>
                </div>
                <h1 class="ticket-heading-title">
                    {{ $user->nama_instansi ?: $user->name }}
                </h1>
                <div class="ticket-heading-meta">
                    <span><i class="fi fi-rr-user"></i> PIC: <strong>{{ $user->name }}</strong></span> &bull; 
                    <span><i class="fi fi-rr-envelope"></i> Email: <strong>{{ $user->email }}</strong></span> &bull; 
                    <span><i class="fi fi-rr-phone-call"></i> Kontak: <strong>{{ $user->nomor_kontak ?: '-' }}</strong></span>
                </div>
            </div>

            <!-- Badge Nomor Laptop / Perangkat Terdaftar Saat Login -->
            <div class="ticket-detail-item">
                <div class="ticket-status-summary-label">
                    Perangkat / Laptop Terdaftar:
                </div>
                <div class="ticket-status-summary-value color-status-progress">
                    <i class="fi fi-rr-laptop"></i> {{ $user->last_login_device ?: 'WIN-DEV-OPD01' }}
                </div>
                <div class="ticket-detail-sub">
                    IP Address: <strong class="color-status-progress">{{ $user->last_login_ip ?: '127.0.0.1' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Tabel Data Riwayat Bagi Data DTSEN Resmi (Read-Only untuk Akun OPD) -->
    <div class="form-card ticket-section-card-large">
        <div class="ticket-top-actions form-group-mb-20">
            <div>
                <h3 class="ticket-section-header">
                    <span><i class="fi fi-rr-document ticket-section-icon"></i> Tabel Data Riwayat Bagi Data DTSEN Resmi</span>
                    <span class="badge-verified">
                        TERVERIFIKASI
                    </span>
                </h3>
                <p class="portal-subtitle">
                    Rekapitulasi resmi berita acara serah terima dan proses bagi pakai data DTSEN instansi Anda.
                </p>
            </div>

            <div class="ticket-detail-sub">
                <i class="fi fi-rr-lock"></i> <em>Tabel ini diinput resmi oleh Admin dan bersifat Read-Only bagi Perangkat Daerah.</em>
            </div>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="table-th-instansi">1. Nama Instansi</th>
                        <th class="table-th-se">2. Surat Edaran</th>
                        <th class="table-th-created">3. Waktu Tiket Dibuat</th>
                        <th class="table-th-bast">4. Tanda Tangan BAST / Proses Selesai</th>
                        <th class="table-th-attendees">5. Orang OPD Hadir & Nomor Laptop</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($riwayatBagiDatas ?? []) as $item)
                        <tr>
                            <!-- 1. Nama Instansi -->
                            <td>
                                <div class="ticket-table-instansi">
                                    {{ $item->nama_instansi }}
                                </div>
                                @if($item->tiket)
                                    <div class="ticket-detail-sub">
                                        Ref Tiket: #{{ $item->tiket->nomor_tiket }}
                                    </div>
                                @endif
                            </td>

                            <!-- 2. Surat Edaran -->
                            <td>
                                <span class="badge-se-code">
                                    {{ $item->no_surat_edaran }}
                                </span>
                            </td>

                            <!-- 3. Tanggal dan Waktu Tiket Dibuat -->
                            <td class="nowrap">
                                <div class="ticket-table-updated">
                                    {{ $item->tanggal_tiket_dibuat ? $item->tanggal_tiket_dibuat->format('d/m/Y') : '-' }}
                                </div>
                                <div class="ticket-table-ago">
                                    {{ $item->tanggal_tiket_dibuat ? $item->tanggal_tiket_dibuat->format('H:i') . ' WIB' : '-' }}
                                </div>
                            </td>

                            <!-- 4. Tanda Tangan BAST / Proses Bagi Data Selesai -->
                            <td>
                                <span class="bast-badge-done">
                                    ✓ {{ $item->status_bast }}
                                </span>
                                <div class="ticket-detail-val">
                                    No. BAST: {{ $item->no_bast ?: '-' }}
                                </div>
                                <div class="ticket-detail-sub">
                                    Waktu BAST: {{ $item->tanggal_bast_selesai ? $item->tanggal_bast_selesai->format('d F Y, H:i') . ' WIB' : 'Selesai Sesuai Jadwal' }}
                                </div>
                            </td>

                            <!-- 5. Orang Perangkat Daerah yang Hadir beserta IP atau Nomor Laptop -->
                            <td>
                                <div class="attendee-box">
                                    <div class="attendee-box-header">
                                        Daftar Hadir Perangkat Daerah:
                                    </div>
                                    <div class="attendee-box-names">
                                        {{ $item->orang_hadir }}
                                    </div>
                                    <div class="attendee-box-footer">
                                        <span class="device-badge">
                                            <i class="fi fi-rr-laptop"></i> {{ $item->ip_atau_nomor_laptop }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="ticket-empty-cell">
                                <div class="ticket-empty-icon"><i class="fi fi-rr-folder"></i></div>
                                <div class="ticket-empty-title">Belum Ada Data Riwayat Bagi Data DTSEN Resmi</div>
                                <div class="ticket-empty-desc">
                                    Setelah tiket permohonan Anda diselesaikan dan BAST ditandatangani, data resmi akan diinput oleh Admin dan tercatat di sini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
