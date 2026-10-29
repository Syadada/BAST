<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
if (! file_exists(__DIR__.'/../vendor/autoload.php')) {
    http_response_code(503);
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Instalasi Dependensi Diperlukan</title>';
    echo '<style>body{font-family:sans-serif;background:#0f172a;color:#f8fafc;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}';
    echo '.card{background:#1e293b;padding:36px;border-radius:14px;box-shadow:0 10px 30px rgba(0,0,0,0.4);max-width:540px;text-align:center;border-top:4px solid #3b82f6;}';
    echo 'h2{margin-top:0;color:#60a5fa;}code{background:#0f172a;color:#38bdf8;padding:4px 8px;border-radius:6px;font-family:monospace;display:inline-block;margin:6px 0;}';
    echo 'ol{text-align:left;line-height:1.8;color:#cbd5e1;}</style></head><body>';
    echo '<div class="card"><h2>⚙️ Dependensi Belum Terpasang</h2>';
    echo '<p>Folder <code>vendor/autoload.php</code> belum ditemukan di repositori ini.</p>';
    echo '<ol><li>Klik dua kali file <code>auto_setup.bat</code> di folder proyek (Otomatis), atau</li>';
    echo '<li>Jalankan perintah di terminal: <code>composer install</code></li></ol>';
    echo '<p style="font-size:12px;color:#94a3b8;margin-top:20px;">Setelah selesai, refresh halaman ini.</p></div></body></html>';
    exit;
}

require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
