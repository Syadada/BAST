<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'role_id',
        'nama_instansi',
        'nomor_kontak',
        'last_login_at',
        'last_login_ip',
        'last_login_device',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    public function isPerangkatDaerah(): bool
    {
        return $this->role === 'perangkat_daerah';
    }

    public function roleRelation()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function riwayatBagiDatas()
    {
        return $this->hasMany(RiwayatBagiDataDtsen::class, 'user_id');
    }

    public function loginLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id')->where('action', 'AUTH_LOGIN');
    }

    public function hakAkses()
    {
        return $this->hasMany(HakAksesDtsen::class, 'user_id');
    }
}
