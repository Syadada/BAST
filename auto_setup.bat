@echo off
setlocal enabledelayedexpansion
title Otomasi Environment & Dependencies - Layanan Bagi Pakai DTSEN (BAST)
color 0A

echo =======================================================================
echo     OTOMASI INSTALASI DEPENDENCIES & ENVIRONMENT SETUP (BAST DTSEN)
echo =======================================================================
echo.

set "PROJECT_DIR=%~dp0"
cd /d "%PROJECT_DIR%"

rem ---------------------------------------------------------
rem 1. DETEKSI PHP
rem ---------------------------------------------------------
echo [1/5] Memeriksa instalasi PHP...
if exist "%PROJECT_DIR%php\php.exe" (
    set "PHP_BIN=%PROJECT_DIR%php\php.exe"
    echo [OK] Menggunakan PHP bawaan folder: !PHP_BIN!
    goto CHECK_COMPOSER
)

where php >nul 2>nul
if %errorlevel% equ 0 (
    set "PHP_BIN=php"
    echo [OK] Menggunakan PHP dari System PATH.
    goto CHECK_COMPOSER
)

if exist "C:\xampp\php\php.exe" (
    set "PHP_BIN=C:\xampp\php\php.exe"
    echo [OK] Menggunakan PHP dari XAMPP: C:\xampp\php\php.exe
    goto CHECK_COMPOSER
)

echo [ERROR] PHP tidak ditemukan di sistem Anda!
echo Silakan install PHP terlebih dahulu atau sediakan portable PHP di folder 'php'.
pause
exit /b 1

rem ---------------------------------------------------------
rem 2. OTOMASI COMPOSER & DOWNLOAD DEPENDENCIES
rem ---------------------------------------------------------
:CHECK_COMPOSER
echo.
echo [2/5] Memeriksa Composer dan dependensi vendor...

rem Cek apakah vendor sudah ada
if exist "%PROJECT_DIR%vendor\autoload.php" (
    echo [OK] Folder vendor sudah terpasang.
    goto SETUP_ENV
)

echo [INFO] Folder vendor belum ada. Memulai instalasi dependensi otomatis...

rem Cek composer global
where composer >nul 2>nul
if %errorlevel% equ 0 (
    echo [INFO] Menjalankan 'composer install' via Composer sistem...
    composer install --no-interaction --prefer-dist --optimize-autoloader
    goto SETUP_ENV
)

rem Jika composer belum terpasang, download composer.phar secara otomatis
if not exist "%PROJECT_DIR%composer.phar" (
    echo [INFO] Mengunduh composer.phar resmi otomatis dari getcomposer.org...
    powershell -Command "[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; (New-Object System.Net.WebClient).DownloadFile('https://getcomposer.org/composer-stable.phar', '%PROJECT_DIR%composer.phar')"
    if not exist "%PROJECT_DIR%composer.phar" (
        echo [ERROR] Gagal mengunduh composer.phar otomatis. Periksa koneksi internet Anda.
        pause
        exit /b 1
    )
    echo [OK] composer.phar berhasil diunduh.
)

echo [INFO] Mengunduh semua dependensi PHP (composer install)...
"!PHP_BIN!" "%PROJECT_DIR%composer.phar" install --no-interaction --prefer-dist --optimize-autoloader

rem ---------------------------------------------------------
rem 3. OTOMASI ENVIRONMENT (.env)
rem ---------------------------------------------------------
:SETUP_ENV
echo.
echo [3/5] Menyiapkan environment configuration (.env)...

if not exist "%PROJECT_DIR%.env" (
    if exist "%PROJECT_DIR%.env.example" (
        copy "%PROJECT_DIR%.env.example" "%PROJECT_DIR%.env" >nul
        echo [OK] File .env berhasil dibuat dari .env.example.
    )
) else (
    echo [OK] File .env sudah ada.
)

rem Generasi APP_KEY jika belum ada
findstr /C:"APP_KEY=base64:" "%PROJECT_DIR%.env" >nul 2>&1
if %errorlevel% neq 0 (
    echo [INFO] Menghasilkan Application Key (APP_KEY)...
    "!PHP_BIN!" artisan key:generate --force
)

rem ---------------------------------------------------------
rem 4. OTOMASI DATABASE SQLITE & MIGRATIONS + SEEDER
rem ---------------------------------------------------------
:SETUP_DB
echo.
echo [4/5] Menyiapkan database SQLite dan migrasi tabel...

if not exist "%PROJECT_DIR%database\database.sqlite" (
    type nul > "%PROJECT_DIR%database\database.sqlite"
    echo [OK] Database SQLite kosong berhasil dibuat.
)

echo [INFO] Menjalankan migrasi database dan pengisian akun bawaan (seeder)...
"!PHP_BIN!" artisan migrate --force
"!PHP_BIN!" artisan db:seed --force

rem ---------------------------------------------------------
rem 5. SELESAI & MENJALANKAN APLIKASI
rem ---------------------------------------------------------
:LAUNCH
echo.
echo [5/5] Lingkungan server siap digunakan!
echo =======================================================================
echo [PORTAL LOGIN]
echo  - Portal OPD      : http://127.0.0.1:8080/login
echo  - Portal Admin    : http://127.0.0.1:8080/dkisp-secret/ (Rahasia)
echo.
echo [AKUN BAWAAN]
echo  - Super Admin     : admin / admin (atau admin@gmail.com / admin)
echo  - OPD Dinsos      : dinsos / dinsos123
echo  - OPD Bappeda     : bappeda / bappeda123
echo =======================================================================
echo.
echo Membuka browser otomatis ke http://127.0.0.1:8080/ ...
start http://127.0.0.1:8080/

echo Server aktif berjalan di port 8080. Tekan Ctrl+C untuk menghentikan.
echo.
"!PHP_BIN!" artisan serve --host=127.0.0.1 --port=8080
