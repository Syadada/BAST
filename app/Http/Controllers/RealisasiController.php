<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Realisasi;
use Illuminate\Http\Request;

class RealisasiController extends Controller
{
    public function create(Request $request)
    {
        $kegiatanId = $request->kegiatan_id;
        $kegiatan = $kegiatanId ? Kegiatan::findOrFail($kegiatanId) : null;
        $kegiatans = Kegiatan::all();

        return view('realisasi.create', compact('kegiatan', 'kegiatans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'tanggal_realisasi' => 'required|date',
            'tahap_periode' => 'required|string',
            'nominal_realisasi' => 'required|numeric|min:0',
            'progres_fisik_persen' => 'required|numeric|min:0|max:100',
            'nomor_bukti' => 'nullable|string|max:100',
            'keterangan_belanja' => 'required|string',
            'kendala_hambatan' => 'nullable|string',
            'tindak_lanjut' => 'nullable|string',
        ]);

        $realisasi = Realisasi::create($validated);

        // Jika progres fisik mencapai 100%, otomatis update status kegiatan jika masih berjalan
        $kegiatan = Kegiatan::find($validated['kegiatan_id']);
        if ($kegiatan && $validated['progres_fisik_persen'] >= 100) {
            $kegiatan->update(['status_kegiatan' => 'Selesai']);
        }

        return redirect()->route('kegiatan.show', $realisasi->kegiatan_id)
            ->with('success', 'Entri realisasi belanja berhasil disimpan.');
    }

    public function edit($id)
    {
        $realisasi = Realisasi::with('kegiatan')->findOrFail($id);
        $kegiatans = Kegiatan::all();

        return view('realisasi.edit', compact('realisasi', 'kegiatans'));
    }

    public function update(Request $request, $id)
    {
        $realisasi = Realisasi::findOrFail($id);

        $validated = $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'tanggal_realisasi' => 'required|date',
            'tahap_periode' => 'required|string',
            'nominal_realisasi' => 'required|numeric|min:0',
            'progres_fisik_persen' => 'required|numeric|min:0|max:100',
            'nomor_bukti' => 'nullable|string|max:100',
            'keterangan_belanja' => 'required|string',
            'kendala_hambatan' => 'nullable|string',
            'tindak_lanjut' => 'nullable|string',
        ]);

        $realisasi->update($validated);

        return redirect()->route('kegiatan.show', $realisasi->kegiatan_id)
            ->with('success', 'Data realisasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $realisasi = Realisasi::findOrFail($id);
        $kegiatanId = $realisasi->kegiatan_id;
        $realisasi->delete();

        return redirect()->route('kegiatan.show', $kegiatanId)
            ->with('success', 'Catatan realisasi berhasil dihapus.');
    }
}
