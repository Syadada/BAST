@extends('layouts.admin')

@section('title', 'Daftar Tiket Support — Admin Walidata')

@section('content')
<div class="ticket-stack-container">

    <!-- Breadcrumb Navigasi Admin -->
    <nav class="app-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fi fi-rr-apps"></i> Dashboard Admin</a>
        <span class="separator">/</span>
        <span class="current">Daftar Tiket Support</span>
    </nav>

    <!-- Support Tickets Card (DomaiNesia Style for Admin) -->
    <div class="ticket-portal-card">
        
        <!-- Header & Filter Bar -->
        <div class="ticket-portal-header">
            <div>
                <h2 class="ticket-portal-title">
                    <i class="fi fi-rr-ticket color-status-progress"></i>
                    <span>Semua Tiket Permohonan Perangkat Daerah</span>
                </h2>
                <div class="portal-subtitle">
                    Klik pada <strong>Nomor Tiket</strong> untuk melihat progress secara detail serta melakukan verifikasi & approval.
                </div>
            </div>

            <div class="ticket-filter-bar">
                <form action="{{ route('admin.tiket.index') }}" method="GET" class="ticket-filter-form">
                    <div class="ticket-search-box">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No Tiket / Instansi / SE..." class="form-input ticket-search-input">
                        <i class="fi fi-rr-search ticket-search-icon"></i>
                    </div>

                    <select name="status" onchange="this.form.submit()" class="form-select ticket-filter-select">
                        <option value="">Semua Status</option>
                        <option value="PROSES" {{ request('status') === 'PROSES' ? 'selected' : '' }}>Sedang Berjalan</option>
                        <option value="SELESAI" {{ request('status') === 'SELESAI' ? 'selected' : '' }}>Selesai (100%)</option>
                        <option value="REVISI" {{ request('status') === 'REVISI' ? 'selected' : '' }}>Perlu Perbaikan</option>
                    </select>
                </form>
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
                                    PIC: {{ $t->nama_pemohon }} ({{ $t->email_pemohon }})
                                </div>
                            </td>

                            <!-- 2. Subject (Nomor Tiket - CLICKABLE) -->
                            <td>
                                <a href="{{ route('admin.tiket.show', $t->id) }}" class="ticket-subject-link" title="Buka Detail Progress Tiket">
                                    <span class="ticket-subject-num">#{{ $t->nomor_tiket }}</span>
                                </a>
                                <span class="ticket-subject-subtitle">
                                    SE: <strong>{{ $t->no_surat_edaran }}</strong> | {{ count($t->variabel_dtsen ?? []) }} Variabel Dimohonkan
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
                                <div class="ticket-empty-title">Tidak Ditemukan Tiket</div>
                                <div class="ticket-empty-desc">
                                    Belum ada tiket yang terdaftar atau sesuai kriteria pencarian yang Anda masukkan.
                                </div>
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
