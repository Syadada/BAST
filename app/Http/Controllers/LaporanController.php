<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Program;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $pengaturan = Pengaturan::first() ?? new Pengaturan();
        $query = Kegiatan::with(['program', 'realisasis']);

        if ($request->filled('unit_kerja')) {
            $query->where('unit_kerja', $request->unit_kerja);
        }

        if ($request->filled('triwulan_target')) {
            $query->where('triwulan_target', $request->triwulan_target);
        }

        if ($request->filled('status_kegiatan')) {
            $query->where('status_kegiatan', $request->status_kegiatan);
        }

        $kegiatans = $query->orderBy('unit_kerja')->get();
        $unitKerjas = Kegiatan::select('unit_kerja')->distinct()->pluck('unit_kerja');

        // Total Rekapitulasi
        $totalPagu = $kegiatans->sum('pagu_anggaran');
        $totalRealisasi = $kegiatans->sum('total_realisasi');
        $totalSisa = $totalPagu - $totalRealisasi;
        $persenKeuangan = $totalPagu > 0 ? round(($totalRealisasi / $totalPagu) * 100, 2) : 0;
        $rataFisik = $kegiatans->count() > 0 ? round($kegiatans->avg('progres_fisik_terakhir'), 2) : 0;

        return view('laporan.index', compact(
            'kegiatans',
            'unitKerjas',
            'pengaturan',
            'totalPagu',
            'totalRealisasi',
            'totalSisa',
            'persenKeuangan',
            'rataFisik'
        ));
    }

    public function cetak(Request $request)
    {
        $pengaturan = Pengaturan::first() ?? new Pengaturan();
        $query = Kegiatan::with(['program', 'realisasis']);

        if ($request->filled('unit_kerja')) {
            $query->where('unit_kerja', $request->unit_kerja);
        }

        if ($request->filled('triwulan_target')) {
            $query->where('triwulan_target', $request->triwulan_target);
        }

        $kegiatans = $query->orderBy('unit_kerja')->get();
        $totalPagu = $kegiatans->sum('pagu_anggaran');
        $totalRealisasi = $kegiatans->sum('total_realisasi');
        $totalSisa = $totalPagu - $totalRealisasi;
        $persenKeuangan = $totalPagu > 0 ? round(($totalRealisasi / $totalPagu) * 100, 2) : 0;
        $rataFisik = $kegiatans->count() > 0 ? round($kegiatans->avg('progres_fisik_terakhir'), 2) : 0;

        return view('laporan.cetak', compact(
            'kegiatans',
            'pengaturan',
            'totalPagu',
            'totalRealisasi',
            'totalSisa',
            'persenKeuangan',
            'rataFisik'
        ));
    }

    public function exportCsvPython()
    {
        $pythonPath = 'python';
        $scriptPath = base_path('python_engine/export_processor.py');

        try {
            $cmd = escapeshellcmd("{$pythonPath} \"{$scriptPath}\"");
            $output = shell_exec($cmd);
            $res = json_decode($output, true);

            if ($res && isset($res['status']) && $res['status'] === 'success') {
                $filePath = $res['file_path'];
                if (file_exists($filePath)) {
                    return response()->download($filePath, $res['file_name'], [
                        'Content-Type' => 'text/csv; charset=UTF-8',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return redirect()->back()->with('error', 'Gagal memproses ekspor via engine Python.');
    }
}
