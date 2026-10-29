@extends('layouts.app')

@section('title', 'Ubah Entri Realisasi')
@section('header_title', 'Perbarui Data Transaksi Realisasi Belanja')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg style="width: 20px; height: 20px; color: var(--accent-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit Transaksi Realisasi SP2D</span>
            </div>
            <a href="{{ route('kegiatan.show', $realisasi->kegiatan_id) }}" class="btn btn-secondary btn-sm">&larr; Kembali ke Rincian</a>
        </div>

        <form action="{{ route('realisasi.update', $realisasi->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Kegiatan Kerja <span style="color: #ef4444;">*</span></label>
                <select name="kegiatan_id" class="form-control" required>
                    @foreach($kegiatans as $kg)
                        <option value="{{ $kg->id }}" {{ old('kegiatan_id', $realisasi->kegiatan_id) == $kg->id ? 'selected' : '' }}>
                            [{{ $kg->kode_kegiatan }}] {{ $kg->nama_kegiatan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Tanggal Realisasi <span style="color: #ef4444;">*</span></label>
                    <input type="date" name="tanggal_realisasi" value="{{ old('tanggal_realisasi', $realisasi->tanggal_realisasi) }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tahapan / Termin <span style="color: #ef4444;">*</span></label>
                    <select name="tahap_periode" class="form-control" required>
                        <option value="Triwulan I" {{ old('tahap_periode', $realisasi->tahap_periode) == 'Triwulan I' ? 'selected' : '' }}>Triwulan I</option>
                        <option value="Triwulan II" {{ old('tahap_periode', $realisasi->tahap_periode) == 'Triwulan II' ? 'selected' : '' }}>Triwulan II</option>
                        <option value="Triwulan III" {{ old('tahap_periode', $realisasi->tahap_periode) == 'Triwulan III' ? 'selected' : '' }}>Triwulan III</option>
                        <option value="Triwulan IV" {{ old('tahap_periode', $realisasi->tahap_periode) == 'Triwulan IV' ? 'selected' : '' }}>Triwulan IV</option>
                        <option value="Uang Muka (Termin 1)" {{ old('tahap_periode', $realisasi->tahap_periode) == 'Uang Muka (Termin 1)' ? 'selected' : '' }}>Uang Muka (Termin 1)</option>
                        <option value="Termin 2" {{ old('tahap_periode', $realisasi->tahap_periode) == 'Termin 2' ? 'selected' : '' }}>Termin 2</option>
                        <option value="Termin 3" {{ old('tahap_periode', $realisasi->tahap_periode) == 'Termin 3' ? 'selected' : '' }}>Termin 3</option>
                        <option value="Pelunasan 100%" {{ old('tahap_periode', $realisasi->tahap_periode) == 'Pelunasan 100%' ? 'selected' : '' }}>Pelunasan 100%</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Nominal Realisasi (Rupiah) <span style="color: #ef4444;">*</span></label>
                    <input type="number" step="any" name="nominal_realisasi" value="{{ old('nominal_realisasi', $realisasi->nominal_realisasi) }}" class="form-control currency-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Capaian Progres Fisik Saat Ini (%) <span style="color: #ef4444;">*</span></label>
                    <input type="number" step="0.1" name="progres_fisik_persen" value="{{ old('progres_fisik_persen', $realisasi->progres_fisik_persen) }}" class="form-control" min="0" max="100" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nomor Dokumen Bukti (SP2D / Kuitansi / BAST)</label>
                <input type="text" name="nomor_bukti" value="{{ old('nomor_bukti', $realisasi->nomor_bukti) }}" class="form-control">
            </div>

            <div class="form-group">
                <label class="form-label">Rincian Penggunaan Belanja <span style="color: #ef4444;">*</span></label>
                <textarea name="keterangan_belanja" rows="3" class="form-control" required>{{ old('keterangan_belanja', $realisasi->keterangan_belanja) }}</textarea>
            </div>

            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Kendala / Hambatan</label>
                    <textarea name="kendala_hambatan" rows="2" class="form-control">{{ old('kendala_hambatan', $realisasi->kendala_hambatan) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Rencana Tindak Lanjut / Solusi</label>
                    <textarea name="tindak_lanjut" rows="2" class="form-control">{{ old('tindak_lanjut', $realisasi->tindak_lanjut) }}</textarea>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                <a href="{{ route('kegiatan.show', $realisasi->kegiatan_id) }}" class="btn btn-secondary">Batalkan</a>
                <button type="submit" class="btn btn-primary">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
