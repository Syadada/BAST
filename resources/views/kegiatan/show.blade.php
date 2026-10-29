@extends('layouts.app')

@section('title', 'Rincian Kegiatan: ' . $kegiatan->kode_kegiatan)
@section('header_title', $kegiatan->nama_kegiatan)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Header Actions & Meta -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('kegiatan.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali ke Daftar</a>
            <span class="badge badge-primary" style="font-family: var(--font-mono); font-size: 13px;">{{ $kegiatan->kode_kegiatan }}</span>
            <span class="badge badge-secondary">{{ $kegiatan->unit_kerja }}</span>
            @if($kegiatan->status_kegiatan == 'Selesai')
                <span class="badge badge-success">Selesai</span>
            @elseif($kegiatan->status_kegiatan == 'Sedang Berjalan')
                <span class="badge badge-primary">Sedang Berjalan</span>
            @else
                <span class="badge badge-warning">{{ $kegiatan->status_kegiatan }}</span>
            @endif
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('realisasi.create', ['kegiatan_id' => $kegiatan->id]) }}" class="btn btn-primary btn-sm">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Entri Realisasi / SP2D Baru
            </a>
            <a href="{{ route('kegiatan.edit', $kegiatan->id) }}" class="btn btn-secondary btn-sm">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Ubah Kegiatan
            </a>
        </div>
    </div>

    <!-- 4 KPI Metrics per Kegiatan -->
    <div class="grid grid-cols-4">
        <div class="card" style="border-left: 4px solid #3b82f6;">
            <div style="font-size: 11px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600;">Pagu Anggaran</div>
            <div style="font-size: 20px; font-weight: 800; color: #fff; margin-top: 6px; font-family: var(--font-mono);">
                Rp {{ number_format($kegiatan->pagu_anggaran, 0, ',', '.') }}
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 8px;">Target Alokasi Disetujui</div>
        </div>

        <div class="card" style="border-left: 4px solid #10b981;">
            <div style="font-size: 11px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600;">Realisasi Keuangan</div>
            <div style="font-size: 20px; font-weight: 800; color: #34d399; margin-top: 6px; font-family: var(--font-mono);">
                Rp {{ number_format($kegiatan->total_realisasi, 0, ',', '.') }}
            </div>
            <div style="font-size: 11px; color: #34d399; margin-top: 8px; font-weight: 600;">
                Serapan: {{ $kegiatan->persen_keuangan }}%
            </div>
        </div>

        <div class="card" style="border-left: 4px solid #f59e0b;">
            <div style="font-size: 11px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600;">Sisa Anggaran</div>
            <div style="font-size: 20px; font-weight: 800; color: #fbbf24; margin-top: 6px; font-family: var(--font-mono);">
                Rp {{ number_format($kegiatan->sisa_anggaran, 0, ',', '.') }}
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 8px;">
                Sisa: {{ round(100 - $kegiatan->persen_keuangan, 2) }}%
            </div>
        </div>

        <div class="card" style="border-left: 4px solid #8b5cf6;">
            <div style="font-size: 11px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600;">Capaian Fisik Lapangan</div>
            <div style="font-size: 20px; font-weight: 800; color: #a78bfa; margin-top: 6px; font-family: var(--font-mono);">
                {{ $kegiatan->progres_fisik_terakhir }}%
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 8px;">
                Target: {{ $kegiatan->target_fisik }}% (Deviasi: {{ $kegiatan->deviasi_fisik }}%)
            </div>
        </div>
    </div>

    <!-- Informasi Detail Teknis -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg style="width: 18px; height: 18px; color: var(--accent-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Informasi Teknis & Jadwal Pelaksanaan</span>
            </div>
        </div>

        <div class="grid grid-cols-3" style="font-size: 13px;">
            <div>
                <span style="color: var(--text-muted);">Program Induk:</span>
                <div style="color: #fff; font-weight: 600; margin-top: 2px;">{{ $kegiatan->program->nama_program ?? 'Mandiri' }}</div>
            </div>
            <div>
                <span style="color: var(--text-muted);">Pejabat Pelaksana (PPTK):</span>
                <div style="color: #fff; font-weight: 600; margin-top: 2px;">{{ $kegiatan->penanggung_jawab }}</div>
            </div>
            <div>
                <span style="color: var(--text-muted);">Target Triwulan:</span>
                <div style="color: #fff; font-weight: 600; margin-top: 2px;">{{ $kegiatan->triwulan_target }}</div>
            </div>
            <div>
                <span style="color: var(--text-muted);">Kategori Belanja:</span>
                <div style="color: #fff; font-weight: 600; margin-top: 2px;">{{ $kegiatan->kategori_belanja }}</div>
            </div>
            <div>
                <span style="color: var(--text-muted);">Periode Waktu:</span>
                <div style="color: #fff; font-weight: 600; margin-top: 2px;">
                    {{ $kegiatan->tanggal_mulai ? date('d/m/Y', strtotime($kegiatan->tanggal_mulai)) : '-' }} s/d {{ $kegiatan->tanggal_selesai ? date('d/m/Y', strtotime($kegiatan->tanggal_selesai)) : '-' }}
                </div>
            </div>
            <div>
                <span style="color: var(--text-muted);">Evaluasi Status:</span>
                <div style="color: {{ $kegiatan->indikator_kinerja['color'] }}; font-weight: 700; margin-top: 2px;">
                    {{ $kegiatan->indikator_kinerja['status'] }}
                </div>
            </div>
        </div>

        @if($kegiatan->catatan)
            <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--border-color); font-size: 13px;">
                <span style="color: var(--text-muted);">Catatan / Keterangan:</span>
                <div style="color: #cbd5e1; margin-top: 4px; line-height: 1.5;">{{ $kegiatan->catatan }}</div>
            </div>
        @endif
    </div>

    <!-- Riwayat Transaksi Realisasi (SP2D / SPJ) -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg style="width: 18px; height: 18px; color: var(--accent-success);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Catatan Realisasi Belanja & SP2D ({{ $kegiatan->realisasis->count() }} Transaksi)</span>
            </div>
            <a href="{{ route('realisasi.create', ['kegiatan_id' => $kegiatan->id]) }}" class="btn btn-primary btn-sm">
                + Tambah Realisasi
            </a>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 110px;">Tanggal</th>
                        <th>No Bukti / SP2D</th>
                        <th>Tahapan / Termin</th>
                        <th style="text-align: right;">Nominal (Rp)</th>
                        <th style="text-align: center; width: 110px;">Fisik (%)</th>
                        <th>Rincian Belanja & Solusi</th>
                        <th style="text-align: center; width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kegiatan->realisasis as $r)
                    <tr>
                        <td style="font-family: var(--font-mono); font-size: 12px;">
                            {{ date('d/m/Y', strtotime($r->tanggal_realisasi)) }}
                        </td>
                        <td>
                            <span style="font-family: var(--font-mono); font-weight: 600; color: #93c5fd;">{{ $r->nomor_bukti ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ $r->tahap_periode }}</span>
                        </td>
                        <td style="text-align: right; font-family: var(--font-mono); font-weight: 700; color: #34d399;">
                            Rp {{ number_format($r->nominal_realisasi, 0, ',', '.') }}
                        </td>
                        <td style="text-align: center; font-family: var(--font-mono); font-weight: 700; color: #a78bfa;">
                            {{ $r->progres_fisik_persen }}%
                        </td>
                        <td style="font-size: 12px; line-height: 1.4;">
                            <div style="color: #fff; font-weight: 500;">{{ $r->keterangan_belanja }}</div>
                            @if($r->kendala_hambatan)
                                <div style="color: #fbbf24; margin-top: 4px; font-size: 11px;">
                                    <strong>Kendala:</strong> {{ $r->kendala_hambatan }}
                                </div>
                            @endif
                            @if($r->tindak_lanjut)
                                <div style="color: #34d399; font-size: 11px;">
                                    <strong>Solusi:</strong> {{ $r->tindak_lanjut }}
                                </div>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <a href="{{ route('realisasi.edit', $r->id) }}" class="btn btn-secondary btn-sm" style="padding: 4px 8px;" title="Edit">
                                    <svg style="width: 13px; height: 13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('realisasi.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi realisasi ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary btn-sm" style="padding: 4px 8px; color: #f87171;" title="Hapus">
                                        <svg style="width: 13px; height: 13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 32px;">
                            Belum ada entri realisasi belanja untuk kegiatan ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
