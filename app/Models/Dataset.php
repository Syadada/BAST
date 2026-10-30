<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_dataset',
        'kode_dataset',
        'kategori_desil',
        'deskripsi',
        'daftar_variabel_tersedia',
        'file_sop_manual',
        'nama_file_sop',
        'status',
    ];

    protected $casts = [
        'daftar_variabel_tersedia' => 'array',
    ];
}
