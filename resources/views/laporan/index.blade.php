@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi Realisasi')
@section('header_title', 'Matriks Rekapitulasi Monitoring & Evaluasi Anggaran')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- Filter & Export Toolbar -->
    <div class="card" style="padding: 18px 24px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 14px;">
            <form method="GET" action="{{ route('laporan.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; flex: 1;">
                <div style="min-width: 180px;">
                    <label class="form-label">Unit / Bidang Kerja</label>
                    <select name="unit_kerja" class="form-control">
                        <option value="">-- Semua Unit Kerja --</option>
                        @foreach($unitKerjas as $u)
                            <option value="{{ $u }}" {{ request('unit_kerja') == $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="min-width: 150px;">
                    <label class="form-label">Triwulan Target</label>
                    <select name="triwulan_target" class="form-control">
                        <option value="">-- Semua Triwulan --</option>
                        <option value="TW1" {{ request('triwulan_target') == 'TW1' ? 'selected' : '' }}>Triwulan I</option>
                        <option value="TW2" {{ request('triwulan_target') == 'TW2' ? 'selected' : '' }}>Triwulan II</option>
                        <option value="TW3" {{ request('triwulan_target') == 'TW3' ? 'selected' : '' }}>Triwulan III</option>
                        <option value="TW4" {{ request('triwulan_target') == 'TW4' ? 'selected' : '' }}>Triwulan IV</option>
                        <option value="Sepanjang Tahun" {{ request('triwulan_target') == 'Sepanjang Tahun' ? 'selected' : '' }}>Sepanjang Tahun</option>
                    </select>
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                    <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            <div style="display: flex; gap: 10px;">
                <a href="{{ route('laporan.cetak', request()->all()) }}" target="_blank" class="btn btn-secondary">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Lembar Resmi
                </a>
                <a href="{{ route('laporan.export-csv') }}" class="btn btn-success">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Ekspor CSV (Python Engine)
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Statistics Badge Row -->
    <div class="grid grid-cols-4">
        <div class="card" style="padding: 16px 20px;">
            <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Total Pagu Tersaring</div>
            <div style="font-size: 18px; font-weight: 800; color: #fff; margin-top: 4px; font-family: var(--font-mono);">
                Rp {{ number_format($totalPagu, 0, ',', '.') }}
            </div>
        </div>
        <div class="card" style="padding: 16px 20px;">
            <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Realisasi Keuangan</div>
            <div style="font-size: 18px; font-weight: 800; color: #34d399; margin-top: 4px; font-family: var(--font-mono);">
                Rp {{ number_format($totalRealisasi, 0, ',', '.') }} ({{ $persenKeuangan }}%)
            </div>
        </div>
        <div class="card" style="padding: 16px 20px;">
            <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Sisa Saldo</div>
            <div style="font-size: 18px; font-weight: 800; color: #fbbf24; margin-top: 4px; font-family: var(--font-mono);">
                Rp {{ number_format($totalSisa, 0, ',', '.') }}
            </div>
        </div>
        <div class="card" style="padding: 16px 20px;">
            <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Rata-rata Fisik</div>
            <div style="font-size: 18px; font-weight: 800; color: #a78bfa; margin-top: 4px; font-family: var(--font-mono);">
                {{ $rataFisik }}%
            </div>
        </div>
    </div>

    <!-- Matriks Data Table -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span>Matriks Laporan Monitoring & Evaluasi Kegiatan (TA {{ $pengaturan->tahun_anggaran_aktif ?? 2026 }})</span>
            </div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kode & Nama Kegiatan</th>
                        <th>Unit Pelaksana</th>
                        <th>PPTK</th>
                        <th style="text-align: right;">Pagu Anggaran</th>
                        <th style="text-align: right;">Realisasi (Rp)</th>
                        <th style="text-align: right;">Sisa (Rp)</th>
                        <th style="text-align: center;">% Keu</th>
                        <th style="text-align: center;">% Fisik</th>
                        <th>Evaluasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kegiatans as $index => $k)
                    <tr>
                        <td style="font-family: var(--font-mono); color: var(--text-muted);">{{ $index + 1 }}</td>
                        <td>
                            <div style="font-family: var(--font-mono); font-size: 11px; color: #93c5fd;">{{ $k->kode_kegiatan }}</div>
                            <div style="font-weight: 600; color: #fff;">{{ $k->nama_kegiatan }}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">{{ $k->program->nama_program ?? '-' }}</div>
                        </td>
                        <td>{{ $k->unit_kerja }}</td>
                        <td>{{ $k->penanggung_jawab }}</td>
                        <td style="text-align: right; font-family: var(--font-mono); font-weight: 600;">
                            Rp {{ number_format($k->pagu_anggaran, 0, ',', '.') }}
                        </td>
                        <td style="text-align: right; font-family: var(--font-mono); font-weight: 700; color: #34d399;">
                            Rp {{ number_format($k->total_realisasi, 0, ',', '.') }}
                        </td>
                        <td style="text-align: right; font-family: var(--font-mono); color: #fbbf24;">
                            Rp {{ number_format($k->sisa_anggaran, 0, ',', '.') }}
                        </td>
                        <td style="text-align: center; font-family: var(--font-mono); font-weight: 700; color: #34d399;">
                            {{ $k->persen_keuangan }}%
                        </td>
                        <td style="text-align: center; font-family: var(--font-mono); font-weight: 700; color: #a78bfa;">
                            {{ $k->progres_fisik_terakhir }}%
                        </td>
                        <td>
                            <span class="badge" style="background: rgba(255,255,255,0.06); color: {{ $k->indikator_kinerja['color'] }}; border: 1px solid {{ $k->indikator_kinerja['color'] }}44;">
                                {{ $k->indikator_kinerja['status'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="text-align: center; color: var(--text-muted); padding: 32px;">
                            Tidak ada data kegiatan untuk laporan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background: rgba(15, 23, 42, 0.9); font-weight: 700; border-top: 2px solid var(--border-color);">
                        <td colspan="4" style="text-align: right; color: #fff; font-size: 13px;">TOTAL KESELURUHAN:</td>
                        <td style="text-align: right; font-family: var(--font-mono); color: #fff;">
                            Rp {{ number_format($totalPagu, 0, ',', '.') }}
                        </td>
                        <td style="text-align: right; font-family: var(--font-mono); color: #34d399;">
                            Rp {{ number_format($totalRealisasi, 0, ',', '.') }}
                        </td>
                        <td style="text-align: right; font-family: var(--font-mono); color: #fbbf24;">
                            Rp {{ number_format($totalSisa, 0, ',', '.') }}
                        </td>
                        <td style="text-align: center; font-family: var(--font-mono); color: #34d399;">
                            {{ $persenKeuangan }}%
                        </td>
                        <td style="text-align: center; font-family: var(--font-mono); color: #a78bfa;">
                            {{ $rataFisik }}%
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
