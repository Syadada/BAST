@echo off
setlocal enabledelayedexpansion

REM Memastikan system PATH standar Windows tersedia
set "PATH=%SystemRoot%\system32;%SystemRoot%;%SystemRoot%\System32\Wbem;%SystemRoot%\System32\WindowsPowerShell\v1.0\;%PATH%"

title BAST DTSEN - Server Launcher and Auto Installer
color 0A

echo =======================================================================
echo          LAYANAN BAGI PAKAI DTSEN DAN PENGESAHAN BAST 2026
echo             ALL-IN-ONE AUTOMATIC SETUP DAN SERVER LAUNCHER
echo =======================================================================
echo.

set "PROJECT_DIR=%~dp0"
cd /d "%PROJECT_DIR%"

set PHP_EXE=
set COMPOSER_PHAR=
set PYTHON_EXE=

rem =======================================================================
rem 1. OTOMASI DETEKSI DAN INSTALASI PYTHON BACKEND RUNTIME
rem =======================================================================
echo [1/8] Memeriksa Runtime Python Backend...

if exist "%PROJECT_DIR%python\python.exe" (
    set "PYTHON_EXE=%PROJECT_DIR%python\python.exe"
    echo [OK] Menggunakan Local Portable Python: %PROJECT_DIR%python\python.exe
    goto :check_python_pip
)

where python >nul 2>&1
if %errorlevel% equ 0 (
    set "PYTHON_EXE=python"
    echo [OK] Menggunakan Python dari System PATH.
    goto :check_python_pip
)

where py >nul 2>&1
if %errorlevel% equ 0 (
    set "PYTHON_EXE=py"
    echo [OK] Menggunakan Python Launcher.
    goto :check_python_pip
)

echo.
echo [PERINGATAN] Python belum terpasang di komputer ini!
echo [SETUP] Mengunduh dan memasang Python 3.11 otomatis via Windows Winget...
winget install Python.Python.3.11 --accept-package-agreements --accept-source-agreements --silent
where python >nul 2>&1
if %errorlevel% equ 0 (
    set "PYTHON_EXE=python"
    echo [OK] Instalasi Python berhasil!
) else (
    echo [WARNING] Python tidak ditemukan. Backend akan menggunakan kalkulator internal.
)

:check_python_pip
if not defined PYTHON_EXE goto :check_php_runtime

"%PYTHON_EXE%" -m pip --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [INFO] Menyiapkan modul pip untuk Python...
    "%PYTHON_EXE%" -m ensurepip --default-pip >nul 2>&1
)

if exist "%PROJECT_DIR%requirements.txt" (
    echo [SETUP] Memeriksa dan mengunduh paket dependensi Python requirements.txt...
    "%PYTHON_EXE%" -m pip install -r "%PROJECT_DIR%requirements.txt" --no-warn-script-location --quiet
    echo [OK] Seluruh pustaka Python Backend siap digunakan.
)

rem =======================================================================
rem 2. OTOMASI DETEKSI DAN INSTALASI PHP RUNTIME (FRONTEND LARAVEL)
rem =======================================================================
:check_php_runtime
echo.
echo [2/8] Memeriksa Runtime PHP (Frontend Presentation Layer)...

if exist "%PROJECT_DIR%php\php.exe" (
    set "PHP_EXE=%PROJECT_DIR%php\php.exe"
    set "COMPOSER_PHAR=%PROJECT_DIR%php\composer.phar"
    echo [OK] Menggunakan Local Portable PHP: %PROJECT_DIR%php\php.exe
    goto :php_ready
)

if exist "C:\xampp\php\php.exe" (
    set "PHP_EXE=C:\xampp\php\php.exe"
    set "COMPOSER_PHAR=C:\xampp\php\composer.phar"
    echo [OK] Menggunakan PHP dari XAMPP: C:\xampp\php\php.exe
    if exist "C:\xampp\php\php.ini" (
        powershell -NoProfile -Command "(Get-Content 'C:\xampp\php\php.ini') -replace ';extension=intl', 'extension=intl' -replace ';extension=zip', 'extension=zip' -replace ';extension=pdo_sqlite', 'extension=pdo_sqlite' -replace ';extension=sqlite3', 'extension=sqlite3' -replace ';extension=fileinfo', 'extension=fileinfo' -replace ';extension=curl', 'extension=curl' -replace ';extension=gd', 'extension=gd' | Set-Content -Encoding ASCII 'C:\xampp\php\php.ini'" >nul 2>&1
    )
    goto :php_ready
)

where php >nul 2>&1
if %errorlevel% equ 0 (
    set "PHP_EXE=php"
    echo [OK] Menggunakan PHP dari System PATH.
    goto :php_ready
)

echo.
echo [PERINGATAN] PHP belum terpasang di komputer ini!
echo [SETUP] Mengunduh dan memasang PHP 8.2 otomatis via Windows Winget...
winget install PHP.PHP.8.2 --accept-package-agreements --accept-source-agreements --silent
where php >nul 2>&1
if %errorlevel% equ 0 (
    set "PHP_EXE=php"
    echo [OK] Instalasi PHP berhasil!
    goto :php_ready
) else (
    echo [ERROR] PHP gagal dipasang otomatis. Sediakan folder 'php' portable.
    pause
    exit /b 1
)

:php_ready

rem =======================================================================
rem 3. OTOMASI COMPOSER DAN DEPENDENSI VENDOR LARAVEL
rem =======================================================================
if not exist "%PROJECT_DIR%vendor\autoload.php" (
    echo.
    echo [3/8] Folder vendor belum ada. Mengunduh seluruh dependensi Composer otomatis...
    if exist "%COMPOSER_PHAR%" (
        echo [INFO] Menjalankan Composer phar lokal...
        "%PHP_EXE%" "%COMPOSER_PHAR%" install --no-interaction --prefer-dist --ignore-platform-reqs
    ) else if exist "%PROJECT_DIR%composer.phar" (
        echo [INFO] Menjalankan composer.phar direktori root...
        "%PHP_EXE%" "%PROJECT_DIR%composer.phar" install --no-interaction --prefer-dist --ignore-platform-reqs
    ) else (
        where composer >nul 2>&1
        if %errorlevel% equ 0 (
            echo [INFO] Menjalankan Composer dari System PATH...
            composer install --no-interaction --prefer-dist --ignore-platform-reqs
        ) else (
            echo [INFO] Mengunduh composer.phar resmi dari getcomposer.org...
            powershell -Command "[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; (New-Object System.Net.WebClient).DownloadFile('https://getcomposer.org/composer-stable.phar', '%PROJECT_DIR%composer.phar')"
            if exist "%PROJECT_DIR%composer.phar" (
                "%PHP_EXE%" "%PROJECT_DIR%composer.phar" install --no-interaction --prefer-dist --ignore-platform-reqs
            )
        )
    )
    echo [OK] Seluruh dependensi Composer vendor berhasil terpasang!
) else (
    echo [OK] Folder vendor dependensi sudah terpasang lengkap.
)

rem =======================================================================
rem 4. OTOMASI ENVIRONMENT DAN APPLICATION KEY (.env)
rem =======================================================================
echo.
echo [4/8] Memeriksa konfigurasi environment (.env)...
if not exist "%PROJECT_DIR%.env" (
    echo [INFO] Membuat file .env dari template...
    copy "%PROJECT_DIR%.env.example" "%PROJECT_DIR%.env" >nul
    "%PHP_EXE%" artisan key:generate --force >nul 2>&1
)

findstr /C:"APP_KEY=base64:" "%PROJECT_DIR%.env" >nul 2>&1
if %errorlevel% neq 0 (
    echo [INFO] Menghasilkan APP_KEY baru...
    "%PHP_EXE%" artisan key:generate --force >nul 2>&1
)
echo [OK] Konfigurasi environment siap.

rem =======================================================================
rem 5. MEMASTIKAN DIREKTORI STORAGE DAN STORAGE LINK
rem =======================================================================
if not exist "%PROJECT_DIR%storage\logs" mkdir "%PROJECT_DIR%storage\logs"
if not exist "%PROJECT_DIR%storage\framework\views" mkdir "%PROJECT_DIR%storage\framework\views"
if not exist "%PROJECT_DIR%storage\framework\sessions" mkdir "%PROJECT_DIR%storage\framework\sessions"
if not exist "%PROJECT_DIR%storage\framework\cache\data" mkdir "%PROJECT_DIR%storage\framework\cache\data"
if not exist "%PROJECT_DIR%storage\app\public" mkdir "%PROJECT_DIR%storage\app\public"
if not exist "%PROJECT_DIR%bootstrap\cache" mkdir "%PROJECT_DIR%bootstrap\cache"
if not exist "%PROJECT_DIR%public\storage" (
    "%PHP_EXE%" artisan storage:link >nul 2>&1
)

rem =======================================================================
rem 6. BASIS DATA SQLITE (WAL MODE) DAN MIGRATIONS SERTA SEEDER
rem =======================================================================
echo.
echo [5/8] Menyiapkan database SQLite dan migrasi tabel...
if not exist "%PROJECT_DIR%database\database.sqlite" (
    type nul > "%PROJECT_DIR%database\database.sqlite"
    echo [OK] File database SQLite baru dibuat.
)
"%PHP_EXE%" artisan migrate --force >nul 2>&1
"%PHP_EXE%" artisan db:seed --force >nul 2>&1
echo [OK] Struktur tabel database dan akun bawaan siap.

rem =======================================================================
rem 7. OPTIMIZE DAN CLEAR CACHE
rem =======================================================================
"%PHP_EXE%" artisan optimize:clear >nul 2>&1

rem =======================================================================
rem 8. MENJALANKAN SERVER PYTHON BACKEND DAN LARAVEL FRONTEND
rem =======================================================================
echo.
echo [6/8] Menjalankan Python FastAPI Backend Engine pada port 8000...
if defined PYTHON_EXE (
    start "Python Backend Engine" /b "%PYTHON_EXE%" -m uvicorn python_backend.server:app --host 127.0.0.1 --port 8000 >nul 2>&1
    echo [OK] Python Backend aktif di http://127.0.0.1:8000/
)

rem Deteksi IP LAN Server untuk akses laptop lain
set "SERVER_IP="
for /f "delims=" %%i in ('powershell -Command "(Get-CimInstance Win32_NetworkAdapterConfiguration | Where-Object IPEnabled | Select-Object -ExpandProperty IPAddress)[0]" 2^>nul') do set "SERVER_IP=%%i"

echo.
echo =======================================================================
echo [SISTEM SUKSES AKTIF]
echo  - Backend Engine : Python FastAPI (Port 8000)
echo  - Frontend Client: Laravel 12 Blade (Port 8080)
echo  - Database       : SQLite WAL Mode (database/database.sqlite)
echo.
echo [AKSES DARI LAPTOP INI (SERVER)]
echo  - Portal OPD     : http://127.0.0.1:8080/login
echo  - Portal Admin   : http://127.0.0.1:8080/dkisp-secret/ (Rahasia)
echo.
if defined SERVER_IP (
echo [AKSES DARI LAPTOP LAIN DALAM JARINGAN WIFI ATAU LAN]
echo  - Buka di Browser : http://%SERVER_IP%:8080/
echo  - Portal OPD      : http://%SERVER_IP%:8080/login
echo  - Portal Admin    : http://%SERVER_IP%:8080/dkisp-secret/
echo.
)
echo [KREDENSIAL BAWAAN]
echo  - Super Admin    : admin / admin (atau admin@gmail.com / admin)
echo  - OPD Dinsos     : dinsos / dinsos123
echo  - OPD Bappeda    : bappeda / bappeda123
echo =======================================================================
echo.
rem Membebaskan port 8080 jika ada proses PHP lama yang masih menggantung
for /f "tokens=5" %%a in ('netstat -aon 2^>nul ^| findstr ":8080 " ^| findstr "LISTENING"') do (
    taskkill /f /pid %%a >nul 2>&1
)

echo [7/8] Menyiapkan pembukaan peramban browser otomatis...
rem Menjalankan peluncuran browser pada proses terpisah dengan jeda 2 detik (TIDAK menutup atau menyembunyikan jendela CMD utama)
start "" powershell -NoProfile -Command "Start-Sleep -Seconds 2; Start-Process 'http://127.0.0.1:8080/'"

echo [8/8] SERVER SEDANG AKTIF DI PORT 8080 (0.0.0.0)...
echo.
echo =======================================================================
echo  [PENTING] JANGAN TUTUP JENDELA CMD INI SELAMA SISTEM DIGUNAKAN!
echo  Jendela CMD ini adalah mesin web server lokal. Cukup MINIMIZE jendela
echo  ini ke taskbar. Jika jendela ini ditutup (X), maka website akan mati.
echo  Tekan CTRL+C jika memang ingin menghentikan server secara manual.
echo =======================================================================
echo.
"%PHP_EXE%" artisan serve --host=0.0.0.0 --port=8080

echo.
echo [INFO] Server Laravel telah dihentikan.
pause
