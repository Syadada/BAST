<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pendaftaran',
        'nama',
        'alamat',
        'tanggal_lahir',
        'media_sumber',
        'media_lain',
        'alasan_mendaftar',
        'ip_address',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Hitung usia pendaftar
     */
    public function getUsiaAttribute()
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : null;
    }
}
