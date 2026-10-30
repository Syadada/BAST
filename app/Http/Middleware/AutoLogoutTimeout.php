<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoLogoutTimeout
{
    /**
     * Waktu timeout sesi dalam detik (90 detik).
     */
    protected int $timeoutSeconds = 90;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = $request->session()->get('last_activity_time');
            $currentTime = time();

            if ($lastActivity && ($currentTime - $lastActivity) > $this->timeoutSeconds) {
                $user = Auth::user();
                $userName = $user ? $user->name : 'Pengguna';

                // Catat di Audit Logger
                AuditLogger::log(
                    'SESSION_TIMEOUT_90S',
                    "Sesi {$userName} berakhir otomatis karena tidak ada aktivitas selama 90 detik.",
                    'WARNING',
                    [
                        'user_id' => $user ? $user->id : null,
                        'idle_seconds' => $currentTime - $lastActivity,
                        'ip' => $request->ip()
                    ]
                );

                // Logout dan batalkan sesi
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Sesi Anda telah berakhir karena tidak ada aktivitas selama 90 detik.',
                        'redirect' => route('login')
                    ], 401);
                }

                // Sesuai permintaan: selalu kembalikan ke login page biasa
                return redirect()->route('login')
                    ->with('warning', 'Sesi Anda telah berakhir otomatis (tidak ada aktivitas selama 90 detik). Silakan login kembali.');
            }

            // Perbarui waktu aktivitas terakhir
            $request->session()->put('last_activity_time', $currentTime);
        }

        return $next($request);
    }
}
