@extends('layouts.app')

@section('title', 'Entri Realisasi Belanja')
@section('header_title', 'Formulir Pencatatan Realisasi Keuangan & Fisik')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg style="width: 20px; height: 20px; color: var(--accent-success);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Entri Transaksi Realisasi Belanja</span>
            </div>
            @if($kegiatan)
                <a href="{{ route('kegiatan.show', $kegiatan->id) }}" class="btn btn-secondary btn-sm">&larr; Kembali ke Kegiatan</a>
            @else
                <a href="{{ route('kegiatan.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali ke Daftar</a>
            @endif
        </div>

        <form action="{{ route('realisasi.store') }}" method="POST">
            @csrf

            <!-- Pilih Kegiatan -->
            <div class="form-group">
                <label class="form-label">Kegiatan Kerja <span style="color: #ef4444;">*</span></label>
                <select name="kegiatan_id" class="form-control" required>
                    <option value="">-- Pilih Kegiatan --</option>
                    @foreach($kegiatans as $kg)
                        <option value="{{ $kg->id }}" {{ (old('kegiatan_id', $kegiatan ? $kegiatan->id : null) == $kg->id) ? 'selected' : '' }}>
                            [{{ $kg->kode_kegiatan }}] {{ $kg->nama_kegiatan }} (Sisa Pagu: Rp {{ number_format($kg->sisa_anggaran, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Tanggal Realisasi / Pencairan <span style="color: #ef4444;">*</span></label>
                    <input type="date" name="tanggal_realisasi" value="{{ old('tanggal_realisasi', date('Y-m-d')) }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tahapan / Termin <span style="color: #ef4444;">*</span></label>
                    <select name="tahap_periode" class="form-control" required>
                        <option value="Triwulan I">Triwulan I</option>
                        <option value="Triwulan II">Triwulan II</option>
                        <option value="Triwulan III">Triwulan III</option>
                        <option value="Triwulan IV">Triwulan IV</option>
                        <option value="Uang Muka (Termin 1)">Uang Muka (Termin 1)</option>
                        <option value="Termin 2">Termin 2</option>
                        <option value="Termin 3">Termin 3</option>
                        <option value="Pelunasan 100%">Pelunasan 100%</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Nominal Realisasi (Rupiah) <span style="color: #ef4444;">*</span></label>
                    <input type="number" step="any" name="nominal_realisasi" value="{{ old('nominal_realisasi') }}" placeholder="Contoh: 45000000" class="form-control currency-input" required>
                    <small style="color: var(--text-muted); font-size: 11px;">Nilai tagihan / SP2D yang dicairkan</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Capaian Progres Fisik Saat Ini (%) <span style="color: #ef4444;">*</span></label>
                    <input type="number" step="0.1" name="progres_fisik_persen" value="{{ old('progres_fisik_persen', 25) }}" class="form-control" min="0" max="100" required>
                    <small style="color: var(--text-muted); font-size: 11px;">Persentase progres kumulatif pekerjaan di lapangan</small>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nomor Dokumen Bukti (SP2D / Kuitansi / BAST)</label>
                <input type="text" name="nomor_bukti" value="{{ old('nomor_bukti') }}" placeholder="Contoh: SP2D-0089/KEU/IV/2026 atau BAST-012/TI/2026" class="form-control">
            </div>

            <div class="form-group">
                <label class="form-label">Rincian Penggunaan Belanja <span style="color: #ef4444;">*</span></label>
                <textarea name="keterangan_belanja" rows="3" class="form-control" placeholder="Contoh: Pembayaran pengadaan perangkat router dan switch lantai 2 sesuai kontrak nomor..." required>{{ old('keterangan_belanja') }}</textarea>
            </div>

            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Kendala / Masalah Lapangan (Jika Ada)</label>
                    <textarea name="kendala_hambatan" rows="2" class="form-control" placeholder="Contoh: Keterlambatan pengiriman material dari rekanan...">{{ old('kendala_hambatan') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Rencana Tindak Lanjut / Solusi</label>
                    <textarea name="tindak_lanjut" rows="2" class="form-control" placeholder="Contoh: Melakukan koordinasi ulang dengan ekspedisi dan penambahan jam kerja...">{{ old('tindak_lanjut') }}</textarea>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                @if($kegiatan)
                    <a href="{{ route('kegiatan.show', $kegiatan->id) }}" class="btn btn-secondary">Batalkan</a>
                @else
                    <a href="{{ route('kegiatan.index') }}" class="btn btn-secondary">Batalkan</a>
                @endif
                <button type="submit" class="btn btn-success">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Entri Realisasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
