<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatBagiDataDtsen extends Model
{
    use HasFactory;

    protected $table = 'riwayat_bagi_data_dtsens';

    protected $fillable = [
        'tiket_dtsen_id',
        'user_id',
        'nama_instansi',
        'no_surat_edaran',
        'tanggal_tiket_dibuat',
        'tanggal_bast_selesai',
        'no_bast',
        'status_bast',
        'orang_hadir',
        'ip_atau_nomor_laptop',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_tiket_dibuat' => 'datetime',
        'tanggal_bast_selesai' => 'datetime',
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
