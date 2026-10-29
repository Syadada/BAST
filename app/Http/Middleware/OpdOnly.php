<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OpdOnly
{
    /**
     * Mencegah akses ke halaman Perangkat Daerah bagi tamu atau Administrator.
     * Mencegah perubahan halaman hanya dengan mengganti URL di browser.
     * Pengalihan dilakukan secara senyap tanpa notifikasi popup.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Jika user adalah Super Admin (mencoba masuk ke portal tiket OPD)
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
