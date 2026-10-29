<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Realisasi;
use App\Models\Program;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class DashboardController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::first() ?? new Pengaturan();
        
        // 1. Eksekusi Python Analytics Engine jika tersedia
        $pythonPath = 'python';
        $scriptPath = base_path('python_engine/budget_analyzer.py');
        $pythonAnalytics = null;

        try {
            $cmd = escapeshellcmd("{$pythonPath} \"{$scriptPath}\"");
            $output = shell_exec($cmd);
            if ($output) {
                $decoded = json_decode($output, true);
                if (isset($decoded['status']) && $decoded['status'] === 'success') {
                    $pythonAnalytics = $decoded;
                }
            }
        } catch (\Throwable $e) {
            // Gunakan kalkulasi Eloquent bawaan jika Python bermasalah
        }

        // 2. Kalkulasi Eloquent Fallback & Data Tampilan
        $totalPagu = Kegiatan::sum('pagu_anggaran');
        $totalRealisasi = Realisasi::sum('nominal_realisasi');
        $sisaAnggaran = $totalPagu - $totalRealisasi;
        $persenKeuangan = $totalPagu > 0 ? round(($totalRealisasi / $totalPagu) * 100, 2) : 0;
        
        $kegiatans = Kegiatan::with(['realisasis', 'program'])->get();
        $totalKegiatan = $kegiatans->count();
        $rataFisik = $totalKegiatan > 0 ? round($kegiatans->avg('progres_fisik_terakhir'), 2) : 0;

        // Distribusi Status
        $statusCounts = [
            'Belum Dimulai' => $kegiatans->where('status_kegiatan', 'Belum Dimulai')->count(),
            'Sedang Berjalan' => $kegiatans->where('status_kegiatan', 'Sedang Berjalan')->count(),
            'Selesai' => $kegiatans->where('status_kegiatan', 'Selesai')->count(),
            'Tertunda' => $kegiatans->where('status_kegiatan', 'Tertunda')->count(),
        ];

        // Kegiatan Terbaru & Realisasi Terbaru
        $recentRealisasis = Realisasi::with('kegiatan')->latest('tanggal_realisasi')->take(6)->get();
        $recentKegiatans = Kegiatan::with('program')->latest()->take(5)->get();

        return view('dashboard.index', compact(
            'pengaturan',
            'totalPagu',
            'totalRealisasi',
            'sisaAnggaran',
            'persenKeuangan',
            'rataFisik',
            'totalKegiatan',
            'statusCounts',
            'recentRealisasis',
            'recentKegiatans',
            'pythonAnalytics'
        ));
    }
}
