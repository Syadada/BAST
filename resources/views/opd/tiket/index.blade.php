@extends('layouts.opd')

@section('title', 'Tiket Saya — Layanan Permintaan Bagi Pakai DTSEN')
@section('page_title', 'Tiket Permohonan Saya')
@section('page_subtitle', 'Daftar Tiket Layanan Bagi Pakai DTSEN Instansi Anda')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

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
                    <i class="fi fi-rr-ticket" style="color: #0b57d0;"></i>
                    <span>Daftar Tiket Dukungan & Permohonan</span>
                </h2>
                <div style="font-size: 13px; color: #64748b; margin-top: 4px;">
                    Klik pada <strong>Nomor Tiket</strong> untuk melihat rincian progress tahapan secara lebih jelas.
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <form action="{{ route('opd.tiket.index') }}" method="GET" style="display: flex; gap: 8px; align-items: center; margin: 0;">
                    <div style="position: relative;">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Tiket / SE..." class="form-input" style="height: 38px; font-size: 13px; padding-left: 32px; width: 220px;">
                        <i class="fi fi-rr-search" style="position: absolute; left: 10px; top: 11px; color: #94a3b8; font-size: 13px;"></i>
                    </div>

                    <select name="status" onchange="this.form.submit()" class="form-select" style="height: 38px; font-size: 13px; padding: 4px 10px; width: 150px;">
                        <option value="">Semua Status</option>
                        <option value="PROSES" {{ request('status') === 'PROSES' ? 'selected' : '' }}>Sedang Berjalan</option>
                        <option value="SELESAI" {{ request('status') === 'SELESAI' ? 'selected' : '' }}>Selesai (100%)</option>
                        <option value="REVISI" {{ request('status') === 'REVISI' ? 'selected' : '' }}>Perlu Perbaikan</option>
                    </select>
                </form>

                <a href="{{ route('formulir.index') }}" class="btn-pill-primary" style="font-size: 12.5px; padding: 8px 18px;">
                    <i class="fi fi-rr-plus"></i> Buat Tiket Baru
                </a>
            </div>
        </div>

        <!-- Tabel Tiket DomaiNesia Support Style -->
        <div class="table-responsive">
            <table class="ticket-portal-table">
                <thead>
                    <tr>
                        <th style="width: 28%;">Nama Instansi</th>
                        <th style="width: 32%;">Subject (Nomor Tiket)</th>
                        <th style="width: 20%;">Status</th>
                        <th style="width: 20%;">Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tikets as $t)
                        <tr>
                            <!-- 1. Nama Instansi -->
                            <td>
                                <div style="font-weight: 700; color: #0f172a; font-size: 14px;">
                                    {{ $t->nama_instansi }}
                                </div>
                                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                    PIC: {{ $t->nama_pemohon }}
                                </div>
                            </td>

                            <!-- 2. Subject (Nomor Tiket - CLICKABLE) -->
                            <td>
                                <a href="{{ route('opd.tiket.show', $t->id) }}" class="ticket-subject-link" title="Klik untuk melihat progress tiket ini">
                                    <span class="ticket-subject-num">#{{ $t->nomor_tiket }}</span>
                                    <i class="fi fi-rr-arrow-up-right" style="font-size: 11px;"></i>
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
                                <div style="font-size: 13px; font-weight: 600; color: #1e293b;">
                                    {{ $t->updated_at ? $t->updated_at->format('d/m/Y H:i') . ' WIB' : '-' }}
                                </div>
                                <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;">
                                    {{ $t->updated_at ? $t->updated_at->diffForHumans() : '-' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 48px 20px; color: #64748b;">
                                <div style="font-size: 32px; color: #94a3b8; margin-bottom: 10px;">
                                    <i class="fi fi-rr-ticket"></i>
                                </div>
                                <div style="font-weight: 700; font-size: 15px; color: #0f172a;">Belum Ada Tiket Permohonan</div>
                                <div style="font-size: 13px; color: #64748b; margin-top: 4px; margin-bottom: 16px;">
                                    Anda belum memiliki tiket permohonan aktif atau sesuai filter yang dipilih.
                                </div>
                                <a href="{{ route('formulir.index') }}" class="btn-clean-cta" style="font-size: 13px; padding: 10px 22px;">
                                    <i class="fi fi-rr-plus"></i> Buat Tiket Sekarang
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tikets->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #f1f5f9;">
                {{ $tikets->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
