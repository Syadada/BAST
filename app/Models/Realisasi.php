<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Realisasi extends Model
{
    protected $fillable = [
        'kegiatan_id',
        'tanggal_realisasi',
        'tahap_periode',
        'nominal_realisasi',
        'progres_fisik_persen',
        'nomor_bukti',
        'keterangan_belanja',
        'kendala_hambatan',
        'tindak_lanjut',
    ];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }
}
