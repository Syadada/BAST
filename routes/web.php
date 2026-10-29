<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TiketDtsenController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes - Layanan Permintaan Bagi Pakai DTSEN (Sistem Tiket & Tracking)
|--------------------------------------------------------------------------
*/

// --- 1. AUTENTIKASI: URL LOGIN BERBEDA UNTUK OPD VS ADMINISTRATOR ---
Route::middleware('guest')->group(function () {
    // A. Portal Login Khusus Perangkat Daerah (OPD)
    Route::get('/login', [AuthController::class, 'showOpdLogin'])->name('login');
    Route::get('/opd/login', [AuthController::class, 'showOpdLogin'])->name('opd.login');
    Route::post('/login', [AuthController::class, 'loginOpd'])->name('login.process');
    Route::post('/opd/login', [AuthController::class, 'loginOpd'])->name('opd.login.process');

    // B. Portal Login Khusus Administrator Walidata (Secret URL: dkisp-secret)
    Route::get('/dkisp-secret', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/dkisp-secret', [AuthController::class, 'loginAdmin'])->name('admin.login.process');

    // Decoy: Mengalihkan /admin/login biasa ke portal umum agar URL rahasia tidak bocor
    Route::get('/admin/login', function () {
        return redirect()->route('login');
    });
});

// Logout Sesi Pengguna
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- 2. SISTEM TERPROTEKSI (WAJIB LOGIN) & PENCEGAHAN PERUBAHAN URL ---
Route::middleware('auth')->group(function () {
    
    // Root URL & Dashboard generic: Arahkan ke dashboard masing-masing sesuai peran
    Route::get('/', function () {
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('opd.dashboard');
    });

    Route::get('/dashboard', function () {
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('opd.dashboard');
    });

    // --- A. PORTAL & DASHBOARD PERANGKAT DAERAH (OPD) ---
    // Diproteksi middleware 'opd' sehingga Admin TIDAK BISA mengakses dengan hanya ganti URL
    Route::middleware('opd')->group(function () {
        // Dashboard OPD (Profil, Log Laptop, Progress Bar 1..5, Tabel Riwayat Bagi Data)
        Route::get('/opd/dashboard', [ProfileController::class, 'index'])->name('opd.dashboard');
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile'); // Dukungan alias

        // Layanan Tiket Permohonan DTSEN & Revisi Variabel
        Route::get('/formulir', [TiketDtsenController::class, 'index'])->name('formulir.index');
        Route::post('/formulir', [TiketDtsenController::class, 'store'])->name('formulir.store');
        Route::post('/tiket', [TiketDtsenController::class, 'store'])->name('tiket.store');
        Route::get('/tiket/{id}/surat', [TiketDtsenController::class, 'downloadSurat'])->name('tiket.download-surat');
        Route::post('/tiket/{id}/update-variabel', [TiketDtsenController::class, 'updateVariabel'])->name('tiket.update-variabel');
    });

    // --- B. PORTAL & DASHBOARD SUPER ADMINISTRATOR ---
    // Diproteksi middleware 'admin' sehingga OPD TIDAK BISA mengakses dengan hanya ganti URL
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        // Root admin URL (/admin) diarahkan ke dashboard admin
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });

        // Dashboard Admin (Pengelolaan Seluruh Tiket, Verifikasi SE, Manipulasi Proses)
        Route::get('/dashboard', [HistoryController::class, 'index'])->name('dashboard');
        Route::get('/riwayat', [HistoryController::class, 'index'])->name('riwayat'); // Dukungan alias

        Route::post('/tiket/{id}/step-ok', [HistoryController::class, 'stepOk'])->name('tiket.step-ok');
        Route::post('/tiket/{id}/update-steps', [HistoryController::class, 'updateSteps'])->name('tiket.update-steps');
        Route::post('/tiket/{id}/verifikasi-variabel', [HistoryController::class, 'verifikasiVariabel'])->name('tiket.verifikasi-variabel');
        Route::get('/tiket/{id}/download', [HistoryController::class, 'downloadSurat'])->name('riwayat.download');
        Route::delete('/riwayat/{id}', [HistoryController::class, 'destroy'])->name('riwayat.destroy');
        Route::get('/riwayat/export', [HistoryController::class, 'exportCsv'])->name('riwayat.export');
        Route::get('/riwayat/export-logs', [HistoryController::class, 'exportLogs'])->name('riwayat.export-logs');
        
        // User Management & Log Laptop/Perangkat Login
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'storeUser'])->name('users.store');
        Route::delete('/users/{id}', [UserController::class, 'destroyUser'])->name('users.destroy');
        
        // Input & Kelola Tabel Riwayat Bagi Pakai Data DTSEN Resmi
        Route::post('/riwayat-bagi-data', [UserController::class, 'storeRiwayatBagiData'])->name('riwayat-bagi-data.store');
        Route::delete('/riwayat-bagi-data/{id}', [UserController::class, 'destroyRiwayatBagiData'])->name('riwayat-bagi-data.destroy');
    });
});
