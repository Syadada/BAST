@echo off
title Layanan Permintaan Bagi Pakai DTSEN (BAST) - Automatic Launcher
color 0B

echo =======================================================================
echo          LAYANAN PERMINTAAN BAGI PAKAI DTSEN & PENGESAHAN BAST        
echo                STAND-ALONE AUTOMATIC LAUNCHER 2026                    
echo =======================================================================
echo.

set "PROJECT_DIR=%~dp0"
cd /d "%PROJECT_DIR%"

set PHP_EXE=
set COMPOSER_PHAR=

rem =======================================================================
rem 1. DETEKSI PRIORITAS PHP (PORTABLE -> XAMPP -> SYSTEM PATH -> WINGET)
rem =======================================================================
rem A. Cek Local Portable PHP (./php/php.exe)
if exist "%PROJECT_DIR%php\php.exe" (
    set "PHP_EXE=%PROJECT_DIR%php\php.exe"
    set "COMPOSER_PHAR=%PROJECT_DIR%php\composer.phar"
    echo [OK] Menggunakan Local Portable PHP: %PROJECT_DIR%php\php.exe
    goto :php_found
)

rem B. Cek XAMPP PHP (C:\xampp\php\php.exe)
if exist "C:\xampp\php\php.exe" (
    set "PHP_EXE=C:\xampp\php\php.exe"
    set "COMPOSER_PHAR=C:\xampp\php\composer.phar"
    echo [OK] Menggunakan PHP dari XAMPP: C:\xampp\php\php.exe
    rem Auto-enable ekstensi PHP penting di php.ini XAMPP (sqlite, fileinfo, intl, zip, curl)
    if exist "C:\xampp\php\php.ini" (
        powershell -NoProfile -Command "(Get-Content 'C:\xampp\php\php.ini') -replace ';extension=intl', 'extension=intl' -replace ';extension=zip', 'extension=zip' -replace ';extension=pdo_sqlite', 'extension=pdo_sqlite' -replace ';extension=sqlite3', 'extension=sqlite3' -replace ';extension=fileinfo', 'extension=fileinfo' -replace ';extension=curl', 'extension=curl' | Set-Content -Encoding ASCII 'C:\xampp\php\php.ini'" >nul 2>&1
    )
    goto :php_found
)

rem C. Cek PHP di System PATH
where php >nul 2>&1
if %errorlevel% equ 0 (
    set "PHP_EXE=php"
    echo [OK] Menggunakan PHP dari System PATH.
    goto :php_found
)

rem D. Jika PHP belum terpasang di komputer/laptop ini:
echo.
echo =======================================================================
echo   [PERINGATAN] PHP belum terpasang di komputer / laptop ini!
echo =======================================================================
echo Komputer ini tidak memiliki folder 'php', XAMPP, maupun PHP di PATH.
echo.
echo Apakah Anda ingin menginstall PHP otomatis via Windows Package Manager (winget)?
echo (Ketik Y untuk install otomatis, atau N untuk keluar)
set /p AUTO_INSTALL="Pilihan [Y/N]: "
if /i "%AUTO_INSTALL%"=="Y" (
    echo.
    echo [SETUP] Menginstall PHP 8.2 via winget...
    winget install PHP.PHP.8.2 --accept-package-agreements --accept-source-agreements --silent
    set "PHP_EXE=php"
    echo [OK] Instalasi PHP selesai!
    goto :php_found
) else (
    echo.
    echo [TIPS] Anda bisa menyalin folder 'php' portable ke direktori ini:
    echo        %PROJECT_DIR%php\php.exe
    echo.
    pause
    exit /b 1
)

:php_found

rem =======================================================================
rem 2. OTOMASI INSTALASI DEPENDENCIES (JIKA VENDOR BELUM TERPASANG)
rem =======================================================================
if not exist "%PROJECT_DIR%vendor\autoload.php" (
    echo.
    echo [SETUP] Instalasi baru / clone GitHub terdeteksi!
    echo [SETUP] Mengunduh seluruh dependensi Composer secara otomatis...

    if exist "%COMPOSER_PHAR%" (
        "%PHP_EXE%" "%COMPOSER_PHAR%" install --no-interaction --prefer-dist --ignore-platform-reqs
    ) else (
        where composer >nul 2>&1
        if %errorlevel% equ 0 (
            composer install --no-interaction --prefer-dist --ignore-platform-reqs
        ) else (
            echo [INFO] Mengunduh composer.phar otomatis dari getcomposer.org...
            powershell -Command "[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; (New-Object System.Net.WebClient).DownloadFile('https://getcomposer.org/composer-stable.phar', '%PROJECT_DIR%composer.phar')"
            if exist "%PROJECT_DIR%composer.phar" (
                "%PHP_EXE%" "%PROJECT_DIR%composer.phar" install --no-interaction --prefer-dist --ignore-platform-reqs
            ) else (
                echo [ERROR] Gagal mengunduh composer.phar otomatis.
            )
        )
    )
    echo [SETUP] Dependensi vendor berhasil diinstall!
    echo.
)

rem =======================================================================
rem 3. OTOMASI ENVIRONMENT (.env) & APP_KEY
rem =======================================================================
if not exist "%PROJECT_DIR%.env" (
    echo [SETUP] Menyiapkan file .env dari .env.example...
    copy "%PROJECT_DIR%.env.example" "%PROJECT_DIR%.env" >nul
    "%PHP_EXE%" artisan key:generate --force >nul 2>&1
    echo [SETUP] Konfigurasi environment selesai!
)

findstr /C:"APP_KEY=base64:" "%PROJECT_DIR%.env" >nul 2>&1
if %errorlevel% neq 0 (
    "%PHP_EXE%" artisan key:generate --force >nul 2>&1
)

rem =======================================================================
rem 4. MEMASTIKAN DIREKTORI STORAGE & FRAMEWORK
rem =======================================================================
if not exist "%PROJECT_DIR%storage\logs" mkdir "%PROJECT_DIR%storage\logs"
if not exist "%PROJECT_DIR%storage\framework\views" mkdir "%PROJECT_DIR%storage\framework\views"
if not exist "%PROJECT_DIR%storage\framework\sessions" mkdir "%PROJECT_DIR%storage\framework\sessions"
if not exist "%PROJECT_DIR%storage\framework\cache\data" mkdir "%PROJECT_DIR%storage\framework\cache\data"
if not exist "%PROJECT_DIR%bootstrap\cache" mkdir "%PROJECT_DIR%bootstrap\cache"

rem =======================================================================
rem 5. OTOMASI DATABASE SQLITE & MIGRATIONS + SEEDER
rem =======================================================================
if not exist "%PROJECT_DIR%database\database.sqlite" (
    type nul > "%PROJECT_DIR%database\database.sqlite"
    echo [OK] Database SQLite berhasil dibuat.
)

echo [SETUP] Memeriksa struktur tabel database & seeder akun...
"%PHP_EXE%" artisan migrate --force >nul 2>&1
"%PHP_EXE%" artisan db:seed --force >nul 2>&1

rem =======================================================================
rem 6. OPTIMIZE & CLEAR CACHE
rem =======================================================================
"%PHP_EXE%" artisan optimize:clear >nul 2>&1

rem =======================================================================
rem 7. MEMBUKA BROWSER & MENJALANKAN SERVER
rem =======================================================================
echo.
echo =======================================================================
echo [SERVER AKTIF] Berjalan di port 8080
echo.
echo [PORTAL LOGIN]
echo  - Portal OPD   : http://127.0.0.1:8080/login
echo  - Portal Admin : http://127.0.0.1:8080/dkisp-secret/ (Rahasia)
echo.
echo [KREDENSIAL BAWAAN]
echo  - Super Admin  : admin / admin (atau admin@gmail.com / admin)
echo  - OPD Dinsos   : dinsos / dinsos123
echo  - OPD Bappeda  : bappeda / bappeda123
echo =======================================================================
echo.
echo Membuka peramban ke http://127.0.0.1:8080/ ...
powershell -Command "Start-Process 'http://127.0.0.1:8080/'"

echo Tekan CTRL+C di jendela ini untuk menghentikan server.
echo.
"%PHP_EXE%" artisan serve --host=127.0.0.1 --port=8080
pause
