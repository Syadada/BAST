@extends('layouts.app')

@section('title', 'Tambah Kegiatan Baru')
@section('header_title', 'Pendaftaran Program & Kegiatan Baru')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg style="width: 20px; height: 20px; color: var(--accent-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Formulir Entri Kegiatan Kerja</span>
            </div>
            <a href="{{ route('kegiatan.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali ke Daftar</a>
        </div>

        <form action="{{ route('kegiatan.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Kode Kegiatan <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="kode_kegiatan" value="{{ old('kode_kegiatan', 'KEG-' . date('Y') . '-' . rand(100, 999)) }}" class="form-control" required>
                    <small style="color: var(--text-muted); font-size: 11px;">Identifikasi unik kegiatan, contoh: KEG-2026-003</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Program Kerja Induk</label>
                    <select name="program_id" class="form-control">
                        <option value="">-- Tanpa Program Induk / Mandiri --</option>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}" {{ old('program_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->kode_program }} - {{ $p->nama_program }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Kegiatan Kerja <span style="color: #ef4444;">*</span></label>
                <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan') }}" class="form-control" placeholder="Contoh: Pengadaan Perangkat Penyimpanan Server dan Komputer" required>
            </div>

            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Bidang / Unit Kerja Pelaksana <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="unit_kerja" value="{{ old('unit_kerja') }}" class="form-control" placeholder="Contoh: Bidang Teknologi Informasi" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Pejabat Pembuat Teknis Kegiatan (PPTK) <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab') }}" class="form-control" placeholder="Contoh: Andi Wijaya, S.Kom, M.T." required>
                </div>
            </div>

            <div class="grid grid-cols-3">
                <div class="form-group">
                    <label class="form-label">Tahun Anggaran <span style="color: #ef4444;">*</span></label>
                    <input type="number" name="tahun_anggaran" value="{{ old('tahun_anggaran', 2026) }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Target Triwulan <span style="color: #ef4444;">*</span></label>
                    <select name="triwulan_target" class="form-control" required>
                        <option value="TW1">Triwulan I (TW1)</option>
                        <option value="TW2">Triwulan II (TW2)</option>
                        <option value="TW3">Triwulan III (TW3)</option>
                        <option value="TW4">Triwulan IV (TW4)</option>
                        <option value="Sepanjang Tahun" selected>Sepanjang Tahun</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori Belanja <span style="color: #ef4444;">*</span></label>
                    <select name="kategori_belanja" class="form-control" required>
                        <option value="Operasional">Belanja Operasional</option>
                        <option value="Modal / Aset">Belanja Modal / Aset</option>
                        <option value="Pelatihan & Sosialisasi">Belanja Pelatihan & Sosialisasi</option>
                        <option value="Jasa Konsultansi">Belanja Jasa Pihak Ketiga</option>
                        <option value="Pengadaan Barang">Pengadaan Barang & Logistik</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Pagu Anggaran Disetujui (Rupiah) <span style="color: #ef4444;">*</span></label>
                    <input type="number" step="any" name="pagu_anggaran" value="{{ old('pagu_anggaran') }}" class="form-control currency-input" placeholder="Contoh: 250000000" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Target Capaian Fisik (%) <span style="color: #ef4444;">*</span></label>
                    <input type="number" step="0.1" name="target_fisik" value="{{ old('target_fisik', 100) }}" class="form-control" required>
                </div>
            </div>

            <div class="grid grid-cols-3">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Selesai Target</label>
                    <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Status Awal Kegiatan <span style="color: #ef4444;">*</span></label>
                    <select name="status_kegiatan" class="form-control" required>
                        <option value="Sedang Berjalan" selected>Sedang Berjalan</option>
                        <option value="Belum Dimulai">Belum Dimulai</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Tertunda">Tertunda</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan & Keterangan Tambahan</label>
                <textarea name="catatan" rows="3" class="form-control" placeholder="Rincian latar belakang kegiatan atau catatan khusus...">{{ old('catatan') }}</textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                <a href="{{ route('kegiatan.index') }}" class="btn btn-secondary">Batalkan</a>
                <button type="submit" class="btn btn-primary">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Kegiatan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
