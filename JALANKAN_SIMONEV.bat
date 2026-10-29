@echo off
title SIMONEV - Monitoring Kegiatan & Anggaran (100%% OFFLINE)
color 0B

echo =======================================================================
echo          SIMONEV - SISTEM MONITORING KEGIATAN & ANGGARAN
echo                  STANDALONE OFFLINE EDITION 2026
echo =======================================================================
echo.
echo [1/4] Memeriksa direktori dan lingkungan aplikasi...

set "PROJECT_DIR=%~dp0"
cd /d "%PROJECT_DIR%"

rem 1. Deteksi Portable PHP lokal
if exist "%PROJECT_DIR%php\php.exe" (
    set "PHP_BIN=%PROJECT_DIR%php\php.exe"
    echo [OK] Menggunakan Portable PHP bawaan sistem.
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
echo [2/4] Menyiapkan konfigurasi database SQLite offline...

if not exist "%PROJECT_DIR%.env" (
    if exist "%PROJECT_DIR%.env.example" (
        copy "%PROJECT_DIR%.env.example" "%PROJECT_DIR%.env" >nul
        echo [OK] File .env berhasil dibuat dari template.
    )
)

if not exist "%PROJECT_DIR%database\database.sqlite" (
    type nul > "%PROJECT_DIR%database\database.sqlite"
    echo [OK] File database SQLite berhasil dibuat.
)

findstr /C:"APP_KEY=base64:" "%PROJECT_DIR%.env" >nul 2>&1
if %errorlevel% neq 0 (
    echo [CONFIG] Menggenerasi Application Key...
    "%PHP_BIN%" artisan key:generate --force >nul 2>&1
)

echo [MIGRATE] Memeriksa struktur tabel database...
"%PHP_BIN%" artisan migrate --force >nul 2>&1

echo.
echo [3/4] Memeriksa Python Analytics Engine...
where python >nul 2>&1
if %errorlevel% equ 0 (
    echo [OK] Python Engine terdeteksi di laptop ini.
) else (
    echo [INFO] Python tidak terdeteksi di PATH global. Aplikasi tetap berjalan via kalkulator internal.
)

echo.
echo [4/4] Membuka antarmuka peramban (browser) otomatis...
powershell -Command "Start-Process 'http://127.0.0.1:8080/'"

echo =======================================================================
echo [SERVER AKTIF] SIMONEV berjalan di: http://127.0.0.1:8080/
echo [INFO] Tekan tombol CTRL+C di jendela ini untuk menghentikan server.
echo =======================================================================
echo.

"%PHP_BIN%" artisan serve --host=127.0.0.1 --port=8080
