<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kak extends Model
{
    protected $fillable = [
        'nomor_registrasi',
        'judul_kak',
        'penanggung_jawab',
        'unit_kerja',
        'tanggal_pengajuan',
        'file_kak',
        'nama_file_asli',
        'ukuran_file',
        'ekstensi_file',
        'hash_sha256',
        'klasifikasi_dokumen',
        'status_verifikasi',
        'keterangan',
    ];
}
