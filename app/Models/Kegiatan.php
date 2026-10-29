<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kegiatan extends Model
{
    protected $fillable = [
        'program_id',
        'kode_kegiatan',
        'nama_kegiatan',
        'unit_kerja',
        'penanggung_jawab',
        'tahun_anggaran',
        'triwulan_target',
        'kategori_belanja',
        'pagu_anggaran',
        'target_fisik',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_kegiatan',
        'catatan',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function realisasis(): HasMany
    {
        return $this->hasMany(Realisasi::class)->orderBy('tanggal_realisasi', 'desc');
    }

    public function getTotalRealisasiAttribute(): float
    {
        return (float) $this->realisasis->sum('nominal_realisasi');
    }

    public function getSisaAnggaranAttribute(): float
    {
        return (float) ($this->pagu_anggaran - $this->total_realisasi);
    }

    public function getPersenKeuanganAttribute(): float
    {
        if ($this->pagu_anggaran <= 0) return 0;
        return round(($this->total_realisasi / $this->pagu_anggaran) * 100, 2);
    }

    public function getProgresFisikTerakhirAttribute(): float
    {
        $latest = $this->realisasis()->latest('tanggal_realisasi')->first();
        return $latest ? (float) $latest->progres_fisik_persen : 0.0;
    }

    public function getDeviasiFisikAttribute(): float
    {
        return round($this->progres_fisik_terakhir - $this->target_fisik, 2);
    }

    public function getIndikatorKinerjaAttribute(): array
    {
        $persenKeuangan = $this->persen_keuangan;
        $progresFisik = $this->progres_fisik_terakhir;

        if ($persenKeuangan >= 80 && $progresFisik >= 80) {
            return ['status' => 'Sangat Baik', 'class' => 'success', 'color' => '#10b981'];
        } elseif ($persenKeuangan >= 50 && $progresFisik >= 50) {
            return ['status' => 'Sesuai Target', 'class' => 'primary', 'color' => '#3b82f6'];
        } elseif ($persenKeuangan >= 25 || $progresFisik >= 25) {
            return ['status' => 'Perhatian Khusus', 'class' => 'warning', 'color' => '#f59e0b'];
        } else {
            return ['status' => 'Kritis / Terlambat', 'class' => 'danger', 'color' => '#ef4444'];
        }
    }
}
