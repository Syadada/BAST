@extends('layouts.app')

@section('title', 'Dashboard Monitoring Eksekutif')
@section('header_title', 'Ringkasan Monitoring Realisasi Kegiatan & Anggaran TA ' . ($pengaturan->tahun_anggaran_aktif ?? 2026))

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- 1. KPI Metric Summary Cards -->
    <div class="grid grid-cols-4">
        <!-- Pagu Anggaran -->
        <div class="card" style="border-left: 4px solid #3b82f6;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Total Pagu Anggaran</div>
                    <div style="font-size: 22px; font-weight: 800; color: #fff; margin-top: 6px; font-family: var(--font-mono);">
                        Rp {{ number_format($totalPagu, 0, ',', '.') }}
                    </div>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(59, 130, 246, 0.15); display: flex; align-items: center; justify-content: center; color: #60a5fa;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div style="margin-top: 14px; font-size: 12px; color: var(--text-muted);">
                Dari total <strong style="color: #fff;">{{ $totalKegiatan }}</strong> kegiatan terdaftar
            </div>
        </div>

        <!-- Realisasi Keuangan -->
        <div class="card" style="border-left: 4px solid #10b981;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Realisasi Keuangan</div>
                    <div style="font-size: 22px; font-weight: 800; color: #34d399; margin-top: 6px; font-family: var(--font-mono);">
                        Rp {{ number_format($totalRealisasi, 0, ',', '.') }}
                    </div>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(16, 185, 129, 0.15); display: flex; align-items: center; justify-content: center; color: #34d399;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div style="margin-top: 14px;">
                <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                    <span style="color: var(--text-muted);">Persentase Serapan:</span>
                    <strong style="color: #34d399;">{{ $persenKeuangan }}%</strong>
                </div>
                <div class="progress-track">
                    <div class="progress-fill progress-fill-success" style="width: {{ min($persenKeuangan, 100) }}%;"></div>
                </div>
            </div>
        </div>

        <!-- Sisa Anggaran -->
        <div class="card" style="border-left: 4px solid #f59e0b;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Sisa Saldo Anggaran</div>
                    <div style="font-size: 22px; font-weight: 800; color: #fbbf24; margin-top: 6px; font-family: var(--font-mono);">
                        Rp {{ number_format($sisaAnggaran, 0, ',', '.') }}
                    </div>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center; color: #fbbf24;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div style="margin-top: 14px; font-size: 12px; color: var(--text-muted);">
                Tersisa <strong style="color: #fff;">{{ round(100 - $persenKeuangan, 2) }}%</strong> dari total alokasi
            </div>
        </div>

        <!-- Rata-rata Progres Fisik -->
        <div class="card" style="border-left: 4px solid #8b5cf6;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">Rata-rata Fisik</div>
                    <div style="font-size: 22px; font-weight: 800; color: #a78bfa; margin-top: 6px; font-family: var(--font-mono);">
                        {{ $rataFisik }}%
                    </div>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(139, 92, 246, 0.15); display: flex; align-items: center; justify-content: center; color: #a78bfa;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
            </div>
            <div style="margin-top: 14px;">
                <div class="progress-track">
                    <div class="progress-fill" style="background: var(--accent-purple-gradient); width: {{ min($rataFisik, 100) }}%;"></div>
                </div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px; text-align: right;">Target: 100%</div>
            </div>
        </div>
    </div>

    <!-- 2. Python High-Performance Analytics Box -->
    @if($pythonAnalytics)
    <div class="card" style="background: linear-gradient(135deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.95) 100%); border: 1px solid rgba(59, 130, 246, 0.3);">
        <div class="card-header" style="margin-bottom: 12px;">
            <div class="card-title">
                <span style="display: inline-flex; width: 10px; height: 10px; border-radius: 50%; background: #3b82f6; box-shadow: 0 0 10px #3b82f6;"></span>
                <span> Analytics Engine & Indeks Kinerja Anggaran (IKA)</span>
            </div>
            <div style="font-size: 11px; font-family: var(--font-mono); color: #93c5fd; background: rgba(59, 130, 246, 0.15); padding: 4px 10px; border-radius: 6px;">
                Diperbarui: {{ $pythonAnalytics['waktu_analisis'] }}
            </div>
        </div>

        <div class="grid grid-cols-3" style="margin-top: 10px;">
            <div style="background: rgba(15, 23, 42, 0.6); padding: 16px; border-radius: 12px; border: 1px solid var(--border-color);">
                <div style="font-size: 12px; color: var(--text-secondary);">Skor Indeks Kinerja (IKA)</div>
                <div style="font-size: 28px; font-weight: 800; color: #60a5fa; margin: 4px 0; font-family: var(--font-mono);">
                    {{ $pythonAnalytics['ringkasan']['skor_ika'] }} <span style="font-size: 14px; font-weight: 600;">/ 100</span>
                </div>
                <div class="badge badge-primary">{{ $pythonAnalytics['ringkasan']['predikat_ika'] }}</div>
            </div>

            <div style="background: rgba(15, 23, 42, 0.6); padding: 16px; border-radius: 12px; border: 1px solid var(--border-color);">
                <div style="font-size: 12px; color: var(--text-secondary);">Evaluasi Capaian Target</div>
                <div style="display: flex; gap: 16px; margin-top: 8px;">
                    <div>
                        <div style="font-size: 18px; font-weight: 700; color: #34d399;">{{ $pythonAnalytics['ringkasan']['kegiatan_sesuai'] }}</div>
                        <div style="font-size: 11px; color: var(--text-muted);">Sesuai Target</div>
                    </div>
                    <div style="border-left: 1px solid var(--border-color); padding-left: 16px;">
                        <div style="font-size: 18px; font-weight: 700; color: #f87171;">{{ $pythonAnalytics['ringkasan']['kegiatan_kritis'] }}</div>
                        <div style="font-size: 11px; color: var(--text-muted);">Perlu Evaluasi</div>
                    </div>
                </div>
            </div>

            <div style="background: rgba(15, 23, 42, 0.6); padding: 16px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="font-size: 12px; color: var(--text-secondary);">Aksi Ekspor Engine Cepat</div>
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Hasilkan rekapan CSV instan terstruktur</div>
                </div>
                <a href="{{ route('laporan.export-csv') }}" class="btn btn-success btn-sm" style="align-self: flex-start; margin-top: 10px;">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Matriks CSV Resmi
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- 3. Rincian Serapan per Unit Kerja & Distribusi Status -->
    <div class="grid grid-cols-2">
        <!-- Serapan per Unit Kerja -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg style="width: 18px; height: 18px; color: var(--accent-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Serapan Anggaran per Bidang / Unit Kerja</span>
                </div>
            </div>

            @if($pythonAnalytics && isset($pythonAnalytics['unit_kerja_analisis']))
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    @foreach($pythonAnalytics['unit_kerja_analisis'] as $u)
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                            <span style="font-weight: 600; color: #fff;">{{ $u['unit_kerja'] }}</span>
                            <span style="font-family: var(--font-mono); color: #34d399; font-weight: 600;">{{ $u['persen_serapan'] }}%</span>
                        </div>
                        <div class="progress-track" style="margin-bottom: 6px;">
                            <div class="progress-fill progress-fill-primary" style="width: {{ min($u['persen_serapan'], 100) }}%;"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--text-muted); font-family: var(--font-mono);">
                            <span>Pagu: Rp {{ number_format($u['pagu'], 0, ',', '.') }}</span>
                            <span>Realisasi: Rp {{ number_format($u['realisasi'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div style="font-size: 13px; color: var(--text-muted); text-align: center; padding: 20px;">
                    Belum ada data unit kerja yang cukup.
                </div>
            @endif
        </div>

        <!-- Status Kegiatan & Entri SP2D Terbaru -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg style="width: 18px; height: 18px; color: var(--accent-success);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Riwayat Pencairan & SP2D Terkini</span>
                </div>
                <a href="{{ route('realisasi.create') }}" class="btn btn-secondary btn-sm">+ Entri Baru</a>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No Bukti / SP2D</th>
                            <th>Kegiatan</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRealisasis as $r)
                        <tr>
                            <td style="font-family: var(--font-mono); font-size: 11px;">{{ date('d/m/Y', strtotime($r->tanggal_realisasi)) }}</td>
                            <td style="font-family: var(--font-mono); font-weight: 600; color: #93c5fd;">{{ $r->nomor_bukti ?? '-' }}</td>
                            <td style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <a href="{{ route('kegiatan.show', $r->kegiatan_id) }}" style="color: #fff; text-decoration: none;">
                                    {{ $r->kegiatan->nama_kegiatan ?? '-' }}
                                </a>
                            </td>
                            <td style="font-family: var(--font-mono); font-weight: 700; color: #34d399;">
                                Rp {{ number_format($r->nominal_realisasi, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 20px;">
                                Belum ada riwayat transaksi realisasi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
