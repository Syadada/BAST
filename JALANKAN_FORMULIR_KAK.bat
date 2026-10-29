@echo off
title Formulir KAK - Pengelolaan Berkas Kerangka Acuan Kerja (100%% OFFLINE)
color 0B

echo =======================================================================
echo         APLIKASI FORMULIR BERKAS KAK & LEMBAR PENGESAHAN
echo                  STAND-ALONE OFFLINE EDITION 2026
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
echo [3/3] Membuka formulir di browser (http://127.0.0.1:8080/)...
powershell -Command "Start-Process 'http://127.0.0.1:8080/'"

echo =======================================================================
echo [SERVER OFFLINE AKTIF] Buka peramban di: http://127.0.0.1:8080/
echo [FITUR] 
echo  - Layanan Permintaan Bagi Pakai DTSEN (Sistem Tiket)
echo  - Live Progress Bar & Centang Hijau Status Tahapan di Sisi User
echo  - Manipulasi Alur oleh Admin (Admin Klik OK -> Centang Hijau)
echo  - Login Admin Single Account: Email admin@gmail.com / Password admin
echo  - Dashboard Admin: http://127.0.0.1:8080/admin/riwayat
echo =======================================================================
echo.

"%PHP_BIN%" artisan serve --host=127.0.0.1 --port=8080
