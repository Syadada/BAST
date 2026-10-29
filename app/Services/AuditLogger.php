<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Catat aktivitas pengguna secara terpusat ke Audit Trail (Acuan Cepat Analytics)
     *
     * @param string $action Kode aksi: AUTH_LOGIN, AUTH_LOGOUT, FORM_SUBMIT, DATA_EXPORT, DATA_DELETE, dll.
     * @param string $description Keterangan aktivitas
     * @param string $status SUCCESS | FAILED | WARNING
     * @param array $metadata Informasi pelengkap
     * @param int|null $forcedUserId ID user khusus
     * @param string|null $forcedUserName Nama khusus
     * @param string|null $forcedRole Role khusus
     * @return AuditLog
     */
    public static function log(
        string $action,
        string $description,
        string $status = 'SUCCESS',
        array $metadata = [],
        ?int $forcedUserId = null,
        ?string $forcedUserName = null,
        ?string $forcedRole = null
    ): AuditLog {
        $user = Auth::user();
        
        $userId = $forcedUserId ?? ($user ? $user->id : null);
        $userName = $forcedUserName ?? ($user ? $user->name : 'Publik / Anonim');
        $userRole = $forcedRole ?? ($user ? $user->role : 'guest');

        $deviceId = Request::cookie('app_device_id');
        if (!$deviceId) {
            $ua = Request::userAgent() ?? '';
            $prefix = 'DEV';
            if (str_contains($ua, 'Windows')) $prefix = 'WIN';
            elseif (str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS X')) $prefix = 'MAC';
            elseif (str_contains($ua, 'Android')) $prefix = 'AND';
            elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) $prefix = 'IOS';
            elseif (str_contains($ua, 'Linux')) $prefix = 'LNX';
            $deviceId = $prefix . '-' . strtoupper(substr(md5((Request::ip() ?? '127.0.0.1') . $ua), 0, 4));
        }
        $metadata['device_id'] = $deviceId;

        return AuditLog::create([
            'user_id' => $userId,
            'user_name' => $userName,
            'user_role' => $userRole,
            'action' => strtoupper($action),
            'description' => $description,
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => substr(Request::userAgent() ?? 'Unknown Browser', 0, 500),
            'status' => strtoupper($status),
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}
