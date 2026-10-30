<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengolahanDtsen extends Model
{
    use HasFactory;

    protected $fillable = [
        'tiket_dtsen_id',
        'petugas_pengolah_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_baris_data',
        'status_pengolahan',
        'catatan_teknis',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'jumlah_baris_data' => 'integer',
    ];

    public function tiket()
    {
        return $this->belongsTo(TiketDtsen::class, 'tiket_dtsen_id');
    }

    public function petugasPengolah()
    {
        return $this->belongsTo(User::class, 'petugas_pengolah_id');
    }
}
