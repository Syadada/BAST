<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RopaDtsen extends Model
{
    use HasFactory;

    protected $fillable = [
        'tiket_dtsen_id',
        'tujuan_pemrosesan',
        'kategori_data_pribadi',
        'pihak_penerima',
        'dasar_hukum_se',
        'jangka_waktu_retensi',
        'metode_penyimpanan',
    ];

    public function tiket()
    {
        return $this->belongsTo(TiketDtsen::class, 'tiket_dtsen_id');
    }
}
