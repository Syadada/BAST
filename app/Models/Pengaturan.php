<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $fillable = [
        'nama_instansi',
        'nama_dinas',
        'nama_pimpinan',
        'nip_pimpinan',
        'nama_ppk',
        'nip_ppk',
        'tahun_anggaran_aktif',
    ];
}
