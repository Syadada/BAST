@echo off
title Layanan Permintaan Bagi Pakai DTSEN (BAST) - Update Sistem Otomatis
color 0B

echo ========================================================
echo       BAST DTSEN - AUTO UPDATE SYSTEM DARI GITHUB           
echo ========================================================
echo.

set "PROJECT_DIR=%~dp0"
cd /d "%PROJECT_DIR%"

echo [1/3] Mengamankan file database lokal & konfigurasi .env...
git update-index --assume-unchanged database/database.sqlite >nul 2>&1
git stash >nul 2>&1

echo [2/3] Mengambil update program terbaru dari server GitHub (syadada/BAST)...
git pull origin main

echo [3/3] Memastikan file database lokal tetap aman...
git update-index --assume-unchanged database/database.sqlite >nul 2>&1

echo.
echo ========================================================
echo   [SUKSES] Program BAST DTSEN berhasil diperbarui!
echo   Silakan jalankan JALANKAN_LAYANAN_DTSEN.bat untuk mulai.
echo ========================================================
echo.
pause
