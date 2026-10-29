<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Mencegah akses ke halaman admin bagi yang belum login atau bukan Super Admin.
     * Mencegah perubahan halaman hanya dengan mengganti URL di browser.
     * Insiden hanya dicatat di audit log admin tanpa memunculkan notifikasi/banner di user.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Jika bukan Super Admin (misal akun OPD yang mencoba mengakses URL /admin/*)
        if (!$user->isSuperAdmin()) {
            // Dicatat di audit log admin saja
            AuditLogger::log(
                'UNAUTHORIZED_ADMIN_URL_ATTEMPT',
                "Pengguna OPD {$user->name} ({$user->email}) mencoba mengakses URL Administrator [{$request->path()}]. Akses dicegat & diblokir.",
                'WARNING',
                ['user_id' => $user->id, 'attempted_url' => $request->path(), 'ip' => $request->ip()]
            );

            // Pengalihan senyap ke dashboard OPD tanpa notifikasi popup/banner
            return redirect()->route('opd.dashboard');
        }

        return $next($request);
    }
}
