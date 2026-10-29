@extends('layouts.app')

@section('title', 'Ubah Data Kegiatan')
@section('header_title', 'Perbarui Informasi Kegiatan: ' . $kegiatan->kode_kegiatan)

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg style="width: 20px; height: 20px; color: var(--accent-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit Rincian Kegiatan Kerja</span>
            </div>
            <a href="{{ route('kegiatan.show', $kegiatan->id) }}" class="btn btn-secondary btn-sm">&larr; Kembali ke Rincian</a>
        </div>

        <form action="{{ route('kegiatan.update', $kegiatan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Kode Kegiatan <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="kode_kegiatan" value="{{ old('kode_kegiatan', $kegiatan->kode_kegiatan) }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Program Kerja Induk</label>
                    <select name="program_id" class="form-control">
                        <option value="">-- Tanpa Program Induk / Mandiri --</option>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}" {{ old('program_id', $kegiatan->program_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->kode_program }} - {{ $p->nama_program }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Kegiatan Kerja <span style="color: #ef4444;">*</span></label>
                <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}" class="form-control" required>
            </div>

            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Bidang / Unit Kerja Pelaksana <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="unit_kerja" value="{{ old('unit_kerja', $kegiatan->unit_kerja) }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Pejabat Pembuat Teknis Kegiatan (PPTK) <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab', $kegiatan->penanggung_jawab) }}" class="form-control" required>
                </div>
            </div>

            <div class="grid grid-cols-3">
                <div class="form-group">
                    <label class="form-label">Tahun Anggaran <span style="color: #ef4444;">*</span></label>
                    <input type="number" name="tahun_anggaran" value="{{ old('tahun_anggaran', $kegiatan->tahun_anggaran) }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Target Triwulan <span style="color: #ef4444;">*</span></label>
                    <select name="triwulan_target" class="form-control" required>
                        <option value="TW1" {{ old('triwulan_target', $kegiatan->triwulan_target) == 'TW1' ? 'selected' : '' }}>Triwulan I (TW1)</option>
                        <option value="TW2" {{ old('triwulan_target', $kegiatan->triwulan_target) == 'TW2' ? 'selected' : '' }}>Triwulan II (TW2)</option>
                        <option value="TW3" {{ old('triwulan_target', $kegiatan->triwulan_target) == 'TW3' ? 'selected' : '' }}>Triwulan III (TW3)</option>
                        <option value="TW4" {{ old('triwulan_target', $kegiatan->triwulan_target) == 'TW4' ? 'selected' : '' }}>Triwulan IV (TW4)</option>
                        <option value="Sepanjang Tahun" {{ old('triwulan_target', $kegiatan->triwulan_target) == 'Sepanjang Tahun' ? 'selected' : '' }}>Sepanjang Tahun</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori Belanja <span style="color: #ef4444;">*</span></label>
                    <select name="kategori_belanja" class="form-control" required>
                        <option value="Operasional" {{ old('kategori_belanja', $kegiatan->kategori_belanja) == 'Operasional' ? 'selected' : '' }}>Belanja Operasional</option>
                        <option value="Modal / Aset" {{ old('kategori_belanja', $kegiatan->kategori_belanja) == 'Modal / Aset' ? 'selected' : '' }}>Belanja Modal / Aset</option>
                        <option value="Pelatihan & Sosialisasi" {{ old('kategori_belanja', $kegiatan->kategori_belanja) == 'Pelatihan & Sosialisasi' ? 'selected' : '' }}>Belanja Pelatihan & Sosialisasi</option>
                        <option value="Jasa Konsultansi" {{ old('kategori_belanja', $kegiatan->kategori_belanja) == 'Jasa Konsultansi' ? 'selected' : '' }}>Belanja Jasa Pihak Ketiga</option>
                        <option value="Pengadaan Barang" {{ old('kategori_belanja', $kegiatan->kategori_belanja) == 'Pengadaan Barang' ? 'selected' : '' }}>Pengadaan Barang & Logistik</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Pagu Anggaran Disetujui (Rupiah) <span style="color: #ef4444;">*</span></label>
                    <input type="number" step="any" name="pagu_anggaran" value="{{ old('pagu_anggaran', $kegiatan->pagu_anggaran) }}" class="form-control currency-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Target Capaian Fisik (%) <span style="color: #ef4444;">*</span></label>
                    <input type="number" step="0.1" name="target_fisik" value="{{ old('target_fisik', $kegiatan->target_fisik) }}" class="form-control" required>
                </div>
            </div>

            <div class="grid grid-cols-3">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $kegiatan->tanggal_mulai) }}" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Selesai Target</label>
                    <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $kegiatan->tanggal_selesai) }}" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Status Kinerja Kegiatan <span style="color: #ef4444;">*</span></label>
                    <select name="status_kegiatan" class="form-control" required>
                        <option value="Sedang Berjalan" {{ old('status_kegiatan', $kegiatan->status_kegiatan) == 'Sedang Berjalan' ? 'selected' : '' }}>Sedang Berjalan</option>
                        <option value="Belum Dimulai" {{ old('status_kegiatan', $kegiatan->status_kegiatan) == 'Belum Dimulai' ? 'selected' : '' }}>Belum Dimulai</option>
                        <option value="Selesai" {{ old('status_kegiatan', $kegiatan->status_kegiatan) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="Tertunda" {{ old('status_kegiatan', $kegiatan->status_kegiatan) == 'Tertunda' ? 'selected' : '' }}>Tertunda</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan & Keterangan Tambahan</label>
                <textarea name="catatan" rows="3" class="form-control">{{ old('catatan', $kegiatan->catatan) }}</textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                <a href="{{ route('kegiatan.show', $kegiatan->id) }}" class="btn btn-secondary">Batalkan</a>
                <button type="submit" class="btn btn-primary">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
