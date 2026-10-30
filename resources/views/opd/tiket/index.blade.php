@extends('layouts.opd')

@section('title', 'Tiket Saya — Layanan Permintaan Bagi Pakai DTSEN')
@section('page_title', 'Tiket Permohonan Saya')
@section('page_subtitle', 'Daftar Tiket Layanan Bagi Pakai DTSEN Instansi Anda')

@section('content')
<div class="ticket-stack-container">

    <!-- Breadcrumb Navigasi -->
    <nav class="app-breadcrumb">
        <a href="{{ route('opd.dashboard') }}"><i class="fi fi-rr-home"></i> Dashboard</a>
        <span class="separator">/</span>
        <span class="current">Tiket Saya</span>
    </nav>

    <!-- Support Tickets Card (DomaiNesia Style) -->
    <div class="ticket-portal-card">
        
        <!-- Header & Filter Bar -->
        <div class="ticket-portal-header">
            <div>
                <h2 class="ticket-portal-title">
                    <i class="fi fi-rr-ticket color-status-progress"></i>
                    <span>Daftar Tiket Dukungan & Permohonan</span>
                </h2>
                <div class="portal-subtitle">
                    Klik pada <strong>Nomor Tiket</strong> untuk melihat rincian progress tahapan secara lebih jelas.
                </div>
            </div>

            <div class="ticket-filter-bar">
                <form action="{{ route('opd.tiket.index') }}" method="GET" class="ticket-filter-form">
                    <div class="ticket-search-box">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Tiket / SE..." class="form-input ticket-search-input">
                        <i class="fi fi-rr-search ticket-search-icon"></i>
                    </div>

                    <select name="status" onchange="this.form.submit()" class="form-select ticket-filter-select">
                        <option value="">Semua Status</option>
                        <option value="PROSES" {{ request('status') === 'PROSES' ? 'selected' : '' }}>Sedang Berjalan</option>
                        <option value="SELESAI" {{ request('status') === 'SELESAI' ? 'selected' : '' }}>Selesai (100%)</option>
                        <option value="REVISI" {{ request('status') === 'REVISI' ? 'selected' : '' }}>Perlu Perbaikan</option>
                    </select>
                </form>

                <a href="{{ route('opd.tiket.create') }}" class="btn-pill-primary ticket-btn-back">
                    <i class="fi fi-rr-plus"></i> Buat Tiket Baru
                </a>
            </div>
        </div>

        <!-- Tabel Tiket DomaiNesia Support Style -->
        <div class="table-responsive">
            <table class="ticket-portal-table">
                <thead>
                    <tr>
                        <th class="th-w-28">Nama Instansi</th>
                        <th class="th-w-32">Subject (Nomor Tiket)</th>
                        <th class="th-w-20">Status</th>
                        <th class="th-w-20">Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tikets as $t)
                        <tr>
                            <!-- 1. Nama Instansi -->
                            <td>
                                <div class="ticket-table-instansi">
                                    {{ $t->nama_instansi }}
                                </div>
                                <div class="ticket-table-pic">
                                    PIC: {{ $t->nama_pemohon }}
                                </div>
                            </td>

                            <!-- 2. Subject (Nomor Tiket - CLICKABLE) -->
                            <td>
                                <a href="{{ route('opd.tiket.show', $t->id) }}" class="ticket-subject-link" title="Klik untuk melihat progress tiket ini">
                                    <span class="ticket-subject-num">#{{ $t->nomor_tiket }}</span>
                                </a>
                                <span class="ticket-subject-subtitle">
                                    SE: <strong>{{ $t->no_surat_edaran }}</strong> | {{ count($t->variabel_dtsen ?? []) }} Variabel
                                </span>
                            </td>

                            <!-- 3. Status Tiket -->
                            <td>
                                @if($t->status === 'PERLU_PERBAIKAN')
                                    <span class="ticket-badge ticket-badge-warning">
                                        <i class="fi fi-rr-exclamation"></i> Perlu Perbaikan OPD
                                    </span>
                                @elseif($t->step5_done)
                                    <span class="ticket-badge ticket-badge-success">
                                        <i class="fi fi-rr-check"></i> Selesai (100%)
                                    </span>
                                @else
                                    <span class="ticket-badge ticket-badge-progress">
                                        <i class="fi fi-rr-time-forward"></i> {{ $t->current_status_label }}
                                    </span>
                                @endif
                            </td>

                            <!-- 4. Last Updated -->
                            <td>
                                <div class="ticket-table-updated">
                                    {{ $t->updated_at ? $t->updated_at->format('d/m/Y H:i') . ' WIB' : '-' }}
                                </div>
                                <div class="ticket-table-ago">
                                    {{ $t->updated_at ? $t->updated_at->diffForHumans() : '-' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="ticket-empty-cell">
                                <div class="ticket-empty-icon">
                                    <i class="fi fi-rr-ticket"></i>
                                </div>
                                <div class="ticket-empty-title">Belum Ada Tiket Permohonan</div>
                                <div class="ticket-empty-desc form-group-mb-16">
                                    Anda belum memiliki tiket permohonan aktif atau sesuai filter yang dipilih.
                                </div>
                                <a href="{{ route('formulir.index') }}" class="btn-clean-cta ticket-btn-back">
                                    <i class="fi fi-rr-plus"></i> Buat Tiket Sekarang
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tikets->hasPages())
            <div class="ticket-pagination-wrap">
                {{ $tikets->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
