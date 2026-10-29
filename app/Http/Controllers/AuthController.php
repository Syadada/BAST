<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * 1. Halaman Login Khusus Perangkat Daerah (OPD) - URL: /login atau /opd/login
     */
    public function showOpdLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->isSuperAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('opd.dashboard');
        }

        // Ambil contoh akun OPD untuk kemudahan uji coba
        $demoOpd = User::where('role', '!=', 'admin')->where('role', '!=', 'super_admin')->where('role', '!=', 'superadmin')->first();

        return view('auth.login', compact('demoOpd'));
    }

    /**
     * Proses Login Khusus Perangkat Daerah (OPD)
     * Menolak akun Administrator agar tidak bisa masuk lewat portal user biasa
     */
    public function loginOpd(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Email atau Username OPD wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginInput = trim($request->input('login'));
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        $user = User::where('email', $loginInput)
            ->orWhere('username', $loginInput)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            // Mencegah Administrator masuk lewat portal login OPD
            if ($user->isSuperAdmin()) {
                AuditLogger::log(
                    'AUTH_REJECTED_ADMIN_ON_OPD_PORTAL',
                    "Akun Administrator [{$user->name}] mencoba masuk melalui portal login OPD. Akses ditolak.",
                    'WARNING',
                    ['user_id' => $user->id, 'attempted_portal' => 'OPD_PORTAL']
                );

                return back()->withErrors([
                    'login' => 'Akses Ditolak: Akun Administrator tidak dapat masuk melalui portal login OPD ini.',
                ])->withInput($request->only('login'));
            }

            Auth::login($user, $remember);
            $request->session()->regenerate();

            // Deteksi ID Perangkat / Nomor Laptop
            $deviceId = $this->resolveDeviceId($request);
            $user->last_login_at = now();
            $user->last_login_ip = $request->ip() ?? '127.0.0.1';
            $user->last_login_device = $deviceId;
            $user->save();

            AuditLogger::log(
                'AUTH_LOGIN_OPD',
                "Perangkat Daerah {$user->name} ({$user->nama_instansi}) berhasil masuk portal melalui perangkat [{$deviceId}].",
                'SUCCESS',
                ['user_id' => $user->id, 'device_id' => $deviceId]
            );

            return redirect()->route('opd.dashboard')
                ->with('success', "Selamat datang, {$user->name}! Silakan pantau tiket dan data riwayat bagi pakai DTSEN instansi Anda.");
        }

        AuditLogger::log(
            'AUTH_FAILED_OPD',
            "Percobaan login OPD gagal dengan identitas '{$loginInput}'",
            'FAILED',
            ['input' => $loginInput]
        );

        return back()->withErrors([
            'login' => 'Kredensial login tidak cocok. Pastikan email/username dan kata sandi benar.',
        ])->withInput($request->only('login'));
    }

    /**
     * 2. Halaman Login Khusus Administrator Walidata - URL Rahasia: /dkisp-secret
     */
    public function showAdminLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->isSuperAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('opd.dashboard');
        }

        // Ambil akun admin untuk referensi uji coba
        $demoAdmin = User::where(function ($q) {
            $q->where('role', 'admin')->orWhere('role', 'superadmin')->orWhere('role', 'super_admin');
        })->first();

        return view('auth.admin_login', compact('demoAdmin'));
    }

    /**
     * Proses Login Khusus Administrator Walidata
     * Menolak akun OPD agar tidak bisa masuk lewat portal admin
     */
    public function loginAdmin(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Email atau Username Administrator wajib diisi.',
            'password.required' => 'Kata sandi Administrator wajib diisi.',
        ]);

        $loginInput = trim($request->input('login'));
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        $user = User::where('email', $loginInput)
            ->orWhere('username', $loginInput)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            // Mencegah akun OPD masuk lewat portal login Admin
            if (!$user->isSuperAdmin()) {
                AuditLogger::log(
                    'AUTH_REJECTED_OPD_ON_ADMIN_PORTAL',
                    "Akun OPD [{$user->name}] mencoba masuk melalui portal login Administrator. Akses ditolak.",
                    'WARNING',
                    ['user_id' => $user->id, 'attempted_portal' => 'ADMIN_PORTAL']
                );

                return back()->withErrors([
                    'login' => 'Akses Ditolak: Akun Anda adalah Perangkat Daerah (OPD). Silakan masuk melalui Portal Masuk OPD di /login.',
                ])->withInput($request->only('login'));
            }

            Auth::login($user, $remember);
            $request->session()->regenerate();

            // Deteksi ID Perangkat / Nomor Laptop
            $deviceId = $this->resolveDeviceId($request);
            $user->last_login_at = now();
            $user->last_login_ip = $request->ip() ?? '127.0.0.1';
            $user->last_login_device = $deviceId;
            $user->save();

            AuditLogger::log(
                'AUTH_LOGIN_ADMIN',
                "Administrator {$user->name} berhasil masuk sistem administrasi melalui perangkat [{$deviceId}].",
                'SUCCESS',
                ['user_id' => $user->id, 'device_id' => $deviceId]
            );

            return redirect()->route('admin.dashboard')
                ->with('success', "Selamat datang kembali, {$user->name}! Anda memiliki kendali penuh di Dashboard Administrator.");
        }

        AuditLogger::log(
            'AUTH_FAILED_ADMIN',
            "Percobaan login Administrator gagal dengan identitas '{$loginInput}'",
            'FAILED',
            ['input' => $loginInput]
        );

        return back()->withErrors([
            'login' => 'Identitas Administrator atau kata sandi tidak cocok.',
        ])->withInput($request->only('login'));
    }

    /**
     * 3. Logout Sesi Pengguna & Arahkan ke Portal yang Sesuai
     */
    public function logout(Request $request)
    {
        $wasAdmin = Auth::check() && Auth::user()->isSuperAdmin();
        $userName = Auth::user() ? Auth::user()->name : 'Pengguna';

        AuditLogger::log(
            'AUTH_LOGOUT',
            "Pengguna {$userName} mengakhiri sesi dan keluar dari sistem.",
            'SUCCESS'
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($wasAdmin) {
            return redirect()->route('admin.login')
                ->with('info', 'Sesi Administrator telah diakhiri dengan aman.');
        }

        return redirect()->route('login')
            ->with('info', 'Anda telah berhasil logout. Sesi telah diakhiri.');
    }

    /**
     * Helper: Deteksi ID Perangkat / Laptop
     */
    private function resolveDeviceId(Request $request): string
    {
        $deviceId = $request->cookie('app_device_id');
        if (!$deviceId) {
            $ua = $request->userAgent() ?? '';
            $prefix = 'DEV';
            if (str_contains($ua, 'Windows')) $prefix = 'WIN';
            elseif (str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS X')) $prefix = 'MAC';
            elseif (str_contains($ua, 'Android')) $prefix = 'AND';
            elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) $prefix = 'IOS';
            elseif (str_contains($ua, 'Linux')) $prefix = 'LNX';
            $deviceId = $prefix . '-' . strtoupper(substr(md5(($request->ip() ?? '127.0.0.1') . $ua), 0, 4));
        }
        return $deviceId;
    }
}
