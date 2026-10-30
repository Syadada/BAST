<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengamananDtsen extends Model
{
    use HasFactory;

    protected $fillable = [
        'tiket_dtsen_id',
        'metode_enkripsi',
        'checksum_sha256',
        'status_integritas',
        'tanggal_verifikasi',
        'petugas_keamanan',
        'catatan',
    ];

    protected $casts = [
        'tanggal_verifikasi' => 'datetime',
    ];

    public function tiket()
    {
        return $this->belongsTo(TiketDtsen::class, 'tiket_dtsen_id');
    }
}
