@extends('layouts.app')

@section('title', 'Daftar Kegiatan & Anggaran')
@section('header_title', 'Master Data Program & Kegiatan')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- Filter & Toolbar Bar -->
    <div class="card" style="padding: 18px 24px;">
        <form method="GET" action="{{ route('kegiatan.index') }}" style="display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 220px;">
                <label class="form-label">Cari Kegiatan / Kode / PPTK</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik kata kunci pencarian..." class="form-control">
            </div>

            <div style="flex: 1; min-width: 160px;">
                <label class="form-label">Unit / Bidang Kerja</label>
                <select name="unit_kerja" class="form-control">
                    <option value="">-- Semua Unit Kerja --</option>
                    @foreach($unitKerjas as $u)
                        <option value="{{ $u }}" {{ request('unit_kerja') == $u ? 'selected' : '' }}>{{ $u }}</option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 140px;">
                <label class="form-label">Target Triwulan</label>
                <select name="triwulan_target" class="form-control">
                    <option value="">-- Semua Triwulan --</option>
                    <option value="TW1" {{ request('triwulan_target') == 'TW1' ? 'selected' : '' }}>Triwulan I</option>
                    <option value="TW2" {{ request('triwulan_target') == 'TW2' ? 'selected' : '' }}>Triwulan II</option>
                    <option value="TW3" {{ request('triwulan_target') == 'TW3' ? 'selected' : '' }}>Triwulan III</option>
                    <option value="TW4" {{ request('triwulan_target') == 'TW4' ? 'selected' : '' }}>Triwulan IV</option>
                    <option value="Sepanjang Tahun" {{ request('triwulan_target') == 'Sepanjang Tahun' ? 'selected' : '' }}>Sepanjang Tahun</option>
                </select>
            </div>

            <div style="flex: 1; min-width: 140px;">
                <label class="form-label">Status Kinerja</label>
                <select name="status_kegiatan" class="form-control">
                    <option value="">-- Semua Status --</option>
                    <option value="Belum Dimulai" {{ request('status_kegiatan') == 'Belum Dimulai' ? 'selected' : '' }}>Belum Dimulai</option>
                    <option value="Sedang Berjalan" {{ request('status_kegiatan') == 'Sedang Berjalan' ? 'selected' : '' }}>Sedang Berjalan</option>
                    <option value="Selesai" {{ request('status_kegiatan') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Tertunda" {{ request('status_kegiatan') == 'Tertunda' ? 'selected' : '' }}>Tertunda</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Filter
                </button>
                <a href="{{ route('kegiatan.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span>Daftar Kegiatan Kerja & Serapan Anggaran</span>
                <span class="badge badge-primary">{{ $kegiatans->total() }} Data</span>
            </div>
            <a href="{{ route('kegiatan.create') }}" class="btn btn-primary btn-sm">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Kegiatan Baru
            </a>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 120px;">Kode & TW</th>
                        <th>Nama Kegiatan & Unit</th>
                        <th>Penanggung Jawab</th>
                        <th style="text-align: right;">Pagu Anggaran</th>
                        <th style="text-align: right;">Realisasi (Rp)</th>
                        <th style="width: 140px;">Progres Fisik</th>
                        <th>Status</th>
                        <th style="text-align: center; width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kegiatans as $k)
                    <tr>
                        <td>
                            <div style="font-family: var(--font-mono); font-weight: 700; color: #93c5fd; font-size: 12px;">{{ $k->kode_kegiatan }}</div>
                            <span class="badge badge-secondary" style="margin-top: 4px; font-size: 10px; background: rgba(255,255,255,0.06);">{{ $k->triwulan_target }}</span>
                        </td>
                        <td>
                            <a href="{{ route('kegiatan.show', $k->id) }}" style="font-weight: 600; color: #fff; text-decoration: none; display: block; font-size: 14px;">
                                {{ $k->nama_kegiatan }}
                            </a>
                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                                {{ $k->unit_kerja }} &bull; <span style="color: var(--accent-primary);">{{ $k->kategori_belanja }}</span>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 13px; color: #e2e8f0; font-weight: 500;">{{ $k->penanggung_jawab }}</div>
                        </td>
                        <td style="text-align: right; font-family: var(--font-mono); font-weight: 600; color: #fff;">
                            Rp {{ number_format($k->pagu_anggaran, 0, ',', '.') }}
                        </td>
                        <td style="text-align: right; font-family: var(--font-mono); font-weight: 700; color: #34d399;">
                            Rp {{ number_format($k->total_realisasi, 0, ',', '.') }}
                            <div style="font-size: 11px; color: var(--text-muted);">{{ $k->persen_keuangan }}% terserap</div>
                        </td>
                        <td>
                            <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 4px; font-family: var(--font-mono);">
                                <span style="color: #a78bfa; font-weight: 700;">{{ $k->progres_fisik_terakhir }}%</span>
                                <span style="color: var(--text-muted);">Target {{ $k->target_fisik }}%</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill progress-fill-primary" style="width: {{ min($k->progres_fisik_terakhir, 100) }}%;"></div>
                            </div>
                        </td>
                        <td>
                            @if($k->status_kegiatan == 'Selesai')
                                <span class="badge badge-success">Selesai</span>
                            @elseif($k->status_kegiatan == 'Sedang Berjalan')
                                <span class="badge badge-primary">Berjalan</span>
                            @elseif($k->status_kegiatan == 'Tertunda')
                                <span class="badge badge-danger">Tertunda</span>
                            @else
                                <span class="badge badge-warning">Belum Mulai</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <a href="{{ route('kegiatan.show', $k->id) }}" class="btn btn-secondary btn-sm" title="Rincian Kegiatan" style="padding: 5px 8px;">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.264 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('kegiatan.edit', $k->id) }}" class="btn btn-secondary btn-sm" title="Ubah Data" style="padding: 5px 8px;">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('kegiatan.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kegiatan {{ $k->nama_kegiatan }} beserta seluruh riwayat realisasinya?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary btn-sm" style="padding: 5px 8px; color: #f87171;" title="Hapus">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 32px;">
                            Tidak ada data kegiatan yang cocok dengan kriteria filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $kegiatans->links() }}
        </div>
    </div>

</div>
@endsection
