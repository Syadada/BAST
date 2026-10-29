<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    /**
     * Audit log bersifat Append-Only (Immutable)
     */
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'status',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tanda Pengenal Unik Perangkat (Device Machine Identifier)
     */
    public function getDeviceIdentifierAttribute(): string
    {
        $meta = is_array($this->metadata) ? $this->metadata : json_decode($this->metadata ?? '[]', true);
        if (!empty($meta['device_id'])) {
            return $meta['device_id'];
        }
        
        $ua = $this->user_agent ?? '';
        $prefix = 'DEV';
        if (str_contains($ua, 'Windows')) $prefix = 'WIN';
        elseif (str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS X')) $prefix = 'MAC';
        elseif (str_contains($ua, 'Android')) $prefix = 'AND';
        elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) $prefix = 'IOS';
        elseif (str_contains($ua, 'Linux')) $prefix = 'LNX';

        $seed = ($this->ip_address ?? '127.0.0.1') . '-' . substr($ua, 0, 50);
        $code = strtoupper(substr(md5($seed), 0, 4));
        return "{$prefix}-{$code}";
    }

    /**
     * Sistem Operasi / Tipe Komputer
     */
    public function getDeviceOsAttribute(): string
    {
        $ua = $this->user_agent ?? '';
        if (str_contains($ua, 'Windows NT 10.0')) return 'Windows 10/11';
        if (str_contains($ua, 'Windows NT 6.3')) return 'Windows 8.1';
        if (str_contains($ua, 'Windows NT 6.1')) return 'Windows 7';
        if (str_contains($ua, 'Windows')) return 'Windows PC';
        if (str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS X')) return 'macOS / MacBook';
        if (str_contains($ua, 'Android')) return 'Android Mobile';
        if (str_contains($ua, 'iPhone')) return 'Apple iPhone';
        if (str_contains($ua, 'iPad')) return 'Apple iPad';
        if (str_contains($ua, 'Linux')) return 'Linux OS';
        return 'Laptop / PC';
    }

    /**
     * Nama Browser yang Digunakan
     */
    public function getDeviceBrowserAttribute(): string
    {
        $ua = $this->user_agent ?? '';
        if (str_contains($ua, 'Edg/')) return 'Edge';
        if (str_contains($ua, 'Chrome/')) return 'Chrome';
        if (str_contains($ua, 'Firefox/')) return 'Firefox';
        if (str_contains($ua, 'Safari/') && !str_contains($ua, 'Chrome/')) return 'Safari';
        if (str_contains($ua, 'Opera') || str_contains($ua, 'OPR/')) return 'Opera';
        return 'Browser';
    }

    /**
     * Ikon Representatif Perangkat
     */
    public function getDeviceIconAttribute(): string
    {
        $ua = $this->user_agent ?? '';
        if (str_contains($ua, 'Android') || str_contains($ua, 'iPhone')) return '📱';
        if (str_contains($ua, 'iPad') || str_contains($ua, 'Tablet')) return '📟';
        return '💻';
    }
}
