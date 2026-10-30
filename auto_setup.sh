#!/usr/bin/env bash
# Otomasi Instalasi Dependencies & Environment Setup (Linux / Mac / WSL)
set -e

echo "======================================================================="
echo "    OTOMASI INSTALASI DEPENDENCIES & ENVIRONMENT SETUP (BAST DTSEN)"
echo "======================================================================="
echo ""

# 1. Deteksi PHP
if ! command -v php &> /dev/null; then
    echo "[ERROR] PHP tidak ditemukan. Silakan pasang PHP 8.2+ terlebih dahulu."
    exit 1
fi
echo "[OK] PHP terdeteksi: $(php -v | head -n 1)"

# 2. Otomasi Composer & Dependencies
if [ ! -d "vendor" ]; then
    echo "[INFO] Folder vendor belum ada. Memulai instalasi dependensi..."
    if command -v composer &> /dev/null; then
        composer install --no-interaction --prefer-dist --optimize-autoloader
    else
        echo "[INFO] Mengunduh composer.phar otomatis..."
        curl -sS https://getcomposer.org/installer | php
        php composer.phar install --no-interaction --prefer-dist --optimize-autoloader
    fi
else
    echo "[OK] Folder vendor sudah terpasang."
fi

# 3. Otomasi Environment (.env)
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "[OK] File .env berhasil dibuat dari .env.example."
fi

php artisan key:generate --force

# 4. Otomasi Database SQLite & Migrations
touch database/database.sqlite
echo "[OK] Database SQLite siap."
php artisan migrate --force
php artisan db:seed --force

echo ""
echo "======================================================================="
echo " Lingkungan Server Siap Digunakan!"
echo " - Portal OPD   : http://127.0.0.1:8080/login"
echo "======================================================================="
echo ""
php artisan serve --host=127.0.0.1 --port=8080
