<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = [
        'kode_program',
        'nama_program',
        'tahun_anggaran',
        'total_pagu',
        'keterangan',
    ];

    public function kegiatans(): HasMany
    {
        return $this->hasMany(Kegiatan::class);
    }

    public function getTotalRealisasiAttribute(): float
    {
        return (float) $this->kegiatans->sum(function ($kegiatan) {
            return $kegiatan->total_realisasi;
        });
    }

    public function getPersentaseRealisasiAttribute(): float
    {
        if ($this->total_pagu <= 0) return 0;
        return round(($this->total_realisasi / $this->total_pagu) * 100, 2);
    }
}
