<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HakAksesDtsen extends Model
{
    use HasFactory;

    protected $fillable = [
        'tiket_dtsen_id',
        'user_id',
        'lingkup_data',
        'status_akses',
        'masa_berlaku_mulai',
        'masa_berlaku_selesai',
        'keterangan',
    ];

    protected $casts = [
        'masa_berlaku_mulai' => 'datetime',
        'masa_berlaku_selesai' => 'datetime',
    ];

    public function tiket()
    {
        return $this->belongsTo(TiketDtsen::class, 'tiket_dtsen_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
