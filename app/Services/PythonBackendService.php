<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PythonBackendService
{
    protected static string $apiUrl = 'http://127.0.0.1:8000/api';

    /**
     * Memanggil API Python Backend (FastAPI) dengan fallback otomatis ke Python CLI Gateway
     */
    public static function call(string $action, array $params = [], string $httpMethod = 'GET', string $apiEndpoint = ''): array
    {
        // 1. Coba koneksi cepat ke FastAPI HTTP Server
        if (!empty($apiEndpoint)) {
            try {
                $url = rtrim(self::$apiUrl, '/') . '/' . ltrim($apiEndpoint, '/');
                $response = match (strtoupper($httpMethod)) {
                    'POST' => Http::timeout(3)->post($url, $params),
                    'DELETE' => Http::timeout(3)->delete($url, $params),
                    default => Http::timeout(3)->get($url, $params),
                };

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Throwable $e) {
                // Server background belum aktif atau timeout, gunakan fallback CLI
            }
        }

        // 2. Fallback: Eksekusi Python CLI Gateway secara lokal
        return self::executeCli($action, $params);
    }

    /**
     * Eksekusi langsung ke CLI Gateway Python
     */
    public static function executeCli(string $action, array $args = []): array
    {
        $pythonBin = self::detectPythonBinary();
        $scriptPath = base_path('python_backend/cli_gateway.py');
        $jsonPayload = escapeshellarg(json_encode($args, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        $cmd = "{$pythonBin} \"{$scriptPath}\" " . escapeshellarg($action) . " {$jsonPayload}";
        
        $output = @shell_exec($cmd);
        if ($output) {
            $decoded = json_decode($output, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return ['status' => 'error', 'message' => 'Gagal mengeksekusi Python Backend Engine.'];
    }

    /**
     * Deteksi biner Python (System PATH atau Local Portable)
     */
    public static function detectPythonBinary(): string
    {
        if (file_exists(base_path('python/python.exe'))) {
            return base_path('python/python.exe');
        }
        return 'python';
    }

    // --- Helper Methods untuk Memudahkan Controller ---

    public static function health(): array
    {
        return self::call('health', [], 'GET', 'health');
    }

    public static function listTikets(?string $search = null, int $page = 1): array
    {
        $params = array_filter(['search' => $search, 'page' => $page]);
        return self::call('list_tikets', $params, 'GET', 'tikets?' . http_build_query($params));
    }

    public static function getTiket(int $id): array
    {
        return self::call('get_tiket', ['id' => $id], 'GET', "tikets/{$id}");
    }

    public static function createTiket(array $data): array
    {
        return self::call('create_tiket', $data, 'POST', 'tikets');
    }

    public static function stepOk(int $id, array $data = []): array
    {
        $payload = array_merge(['id' => $id], $data);
        return self::call('step_ok', $payload, 'POST', "tikets/{$id}/step-ok");
    }

    public static function updateVariabel(int $id, array $variabel): array
    {
        $payload = ['id' => $id, 'variabel_dtsen' => $variabel];
        return self::call('update_variabel', $payload, 'POST', "tikets/{$id}/update-variabel");
    }

    public static function deleteTiket(int $id): array
    {
        return self::call('delete_tiket', ['id' => $id], 'DELETE', "tikets/{$id}");
    }

    public static function listUsers(): array
    {
        return self::call('list_users', [], 'GET', 'users');
    }

    public static function storeUser(array $data): array
    {
        return self::call('store_user', $data, 'POST', 'users');
    }

    public static function deleteUser(int $id): array
    {
        return self::call('delete_user', ['id' => $id], 'DELETE', "users/{$id}");
    }

    public static function listRiwayatBagiData(): array
    {
        return self::call('list_riwayat_bagi_data', [], 'GET', 'riwayat-bagi-data');
    }

    public static function storeRiwayatBagiData(array $data): array
    {
        return self::call('store_riwayat_bagi_data', $data, 'POST', 'riwayat-bagi-data');
    }

    public static function deleteRiwayatBagiData(int $id): array
    {
        return self::call('delete_riwayat_bagi_data', ['id' => $id], 'DELETE', "riwayat-bagi-data/{$id}");
    }

    public static function listAuditLogs(int $limit = 50): array
    {
        return self::call('list_audit_logs', ['limit' => $limit], 'GET', "audit-logs?limit={$limit}");
    }
}
