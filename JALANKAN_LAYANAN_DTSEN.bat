@echo off
title Layanan Permintaan Bagi Pakai DTSEN - Sistem Tiket & Tracking
color 0B

echo =======================================================================
echo          LAYANAN PERMINTAAN BAGI PAKAI DTSEN (BENTUK TIKET)
echo                     STAND-ALONE EDITION 2026
echo =======================================================================
echo.
echo [1/3] Memeriksa lingkungan server dan portable PHP...

set "PROJECT_DIR=%~dp0"
cd /d "%PROJECT_DIR%"

rem 1. Deteksi Portable PHP lokal
if exist "%PROJECT_DIR%php\php.exe" (
    set "PHP_BIN=%PROJECT_DIR%php\php.exe"
    echo [OK] Menggunakan Portable PHP bawaan direktori.
    goto CHECK_ENV
)

rem 2. Cek PHP di PATH
where php >nul 2>nul
if %errorlevel% equ 0 (
    set "PHP_BIN=php"
    echo [OK] Menggunakan PHP dari System PATH.
    goto CHECK_ENV
)

rem 3. Cek Lokasi XAMPP
if exist "C:\xampp\php\php.exe" (
    set "PHP_BIN=C:\xampp\php\php.exe"
    echo [OK] Menggunakan PHP dari XAMPP.
    goto CHECK_ENV
)

echo [ERROR] PHP tidak ditemukan. Pastikan folder 'php' berisi php.exe.
pause
exit /b 1

:CHECK_ENV
echo.
echo [2/3] Menyiapkan database SQLite dan konfigurasi offline...

if not exist "%PROJECT_DIR%.env" (
    if exist "%PROJECT_DIR%.env.example" (
        copy "%PROJECT_DIR%.env.example" "%PROJECT_DIR%.env" >nul
        echo [OK] File .env dibuat otomatis.
    )
)

if not exist "%PROJECT_DIR%database\database.sqlite" (
    type nul > "%PROJECT_DIR%database\database.sqlite"
    echo [OK] Database SQLite berhasil dibuat.
)

findstr /C:"APP_KEY=base64:" "%PROJECT_DIR%.env" >nul 2>&1
if %errorlevel% neq 0 (
    echo [CONFIG] Menggenerasi Application Key...
    "%PHP_BIN%" artisan key:generate --force >nul 2>&1
)

echo [MIGRATE] Memeriksa struktur tabel database...
"%PHP_BIN%" artisan migrate --force >nul 2>&1
"%PHP_BIN%" artisan db:seed --force >nul 2>&1

echo.
echo [3/3] Membuka Layanan Permintaan Bagi Pakai DTSEN di browser...
powershell -Command "Start-Process 'http://127.0.0.1:8080/'"

echo =======================================================================
echo [SERVER AKTIF] Buka peramban di: http://127.0.0.1:8080/
echo [FITUR] 
echo  - Layanan Pengguna: Buat Tiket Bagi Pakai DTSEN (+ Tambah Kolom Variabel)
echo  - Progress Tracker: Visual Progress Bar & Centang Hijau 5 Tahap Proses
echo  - Dashboard Admin: http://127.0.0.1:8080/admin/riwayat (Login admin@gmail.com / admin)
echo  - Manipulasi Proses: Admin klik OK -> Tahap Selesai & Centang Hijau di Sisi User
echo =======================================================================
echo.

"%PHP_BIN%" artisan serve --host=127.0.0.1 --port=8080
