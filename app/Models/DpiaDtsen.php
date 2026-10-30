<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DpiaDtsen extends Model
{
    use HasFactory;

    protected $fillable = [
        'tiket_dtsen_id',
        'skor_risiko',
        'identifikasi_dampak',
        'langkah_mitigasi',
        'status_rekomendasi',
        'pejabat_penilai_id',
        'tanggal_penilaian',
    ];

    protected $casts = [
        'tanggal_penilaian' => 'datetime',
    ];

    public function tiket()
    {
        return $this->belongsTo(TiketDtsen::class, 'tiket_dtsen_id');
    }

    public function pejabatPenilai()
    {
        return $this->belongsTo(User::class, 'pejabat_penilai_id');
    }
}
