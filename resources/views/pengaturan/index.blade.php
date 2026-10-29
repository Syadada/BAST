@extends('layouts.app')

@section('title', 'Profil Instansi & Pengesahan')
@section('header_title', 'Pengaturan Profil Satuan Kerja & Lembar Pengesahan Laporan')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg style="width: 20px; height: 20px; color: var(--accent-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Konfigurasi Kop Surat & Pejabat Pengesah</span>
            </div>
        </div>

        <form action="{{ route('pengaturan.update') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Nama Lembaga / Pemerintah Daerah <span style="color: #ef4444;">*</span></label>
                <input type="text" name="nama_instansi" value="{{ old('nama_instansi', $pengaturan->nama_instansi) }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Dinas / Badan / Satuan Kerja <span style="color: #ef4444;">*</span></label>
                <input type="text" name="nama_dinas" value="{{ old('nama_dinas', $pengaturan->nama_dinas) }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Tahun Anggaran Aktif <span style="color: #ef4444;">*</span></label>
                <input type="number" name="tahun_anggaran_aktif" value="{{ old('tahun_anggaran_aktif', $pengaturan->tahun_anggaran_aktif ?? 2026) }}" class="form-control" required style="max-width: 200px;">
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 24px 0;">

            <div style="font-weight: 700; color: #fff; margin-bottom: 16px; font-size: 14px;">
                Pejabat Pembuat Komitmen (PPK):
            </div>

            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap PPK & Gelar <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="nama_ppk" value="{{ old('nama_ppk', $pengaturan->nama_ppk) }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">NIP PPK <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="nip_ppk" value="{{ old('nip_ppk', $pengaturan->nip_ppk) }}" class="form-control" required>
                </div>
            </div>

            <div style="font-weight: 700; color: #fff; margin: 16px 0 16px; font-size: 14px;">
                Kepala Dinas / Pengguna Anggaran (PA):
            </div>

            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap Kepala Dinas & Gelar <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="nama_pimpinan" value="{{ old('nama_pimpinan', $pengaturan->nama_pimpinan) }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">NIP Kepala Dinas <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="nip_pimpinan" value="{{ old('nip_pimpinan', $pengaturan->nip_pimpinan) }}" class="form-control" required>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 24px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                <button type="submit" class="btn btn-primary">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Profil & Pengesahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
