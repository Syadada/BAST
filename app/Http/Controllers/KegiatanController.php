<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Program;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kegiatan::with(['program', 'realisasis']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($b) use ($q) {
                $b->where('nama_kegiatan', 'like', "%{$q}%")
                  ->orWhere('kode_kegiatan', 'like', "%{$q}%")
                  ->orWhere('penanggung_jawab', 'like', "%{$q}%");
            });
        }

        if ($request->filled('unit_kerja')) {
            $query->where('unit_kerja', $request->unit_kerja);
        }

        if ($request->filled('status_kegiatan')) {
            $query->where('status_kegiatan', $request->status_kegiatan);
        }

        if ($request->filled('triwulan_target')) {
            $query->where('triwulan_target', $request->triwulan_target);
        }

        $kegiatans = $query->latest()->paginate(10)->withQueryString();
        $unitKerjas = Kegiatan::select('unit_kerja')->distinct()->pluck('unit_kerja');
        $programs = Program::all();

        return view('kegiatan.index', compact('kegiatans', 'unitKerjas', 'programs'));
    }

    public function create()
    {
        $programs = Program::all();
        $unitKerjas = Kegiatan::select('unit_kerja')->distinct()->pluck('unit_kerja');
        return view('kegiatan.create', compact('programs', 'unitKerjas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'nullable|exists:programs,id',
            'kode_kegiatan' => 'required|string|max:50|unique:kegiatans,kode_kegiatan',
            'nama_kegiatan' => 'required|string|max:255',
            'unit_kerja' => 'required|string|max:100',
            'penanggung_jawab' => 'required|string|max:100',
            'tahun_anggaran' => 'required|integer',
            'triwulan_target' => 'required|string',
            'kategori_belanja' => 'required|string',
            'pagu_anggaran' => 'required|numeric|min:0',
            'target_fisik' => 'required|numeric|min:0|max:100',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'status_kegiatan' => 'required|in:Belum Dimulai,Sedang Berjalan,Selesai,Tertunda',
            'catatan' => 'nullable|string',
        ]);

        $kegiatan = Kegiatan::create($validated);

        return redirect()->route('kegiatan.show', $kegiatan->id)
            ->with('success', 'Kegiatan baru berhasil didaftarkan ke sistem.');
    }

    public function show($id)
    {
        $kegiatan = Kegiatan::with(['program', 'realisasis'])->findOrFail($id);
        return view('kegiatan.show', compact('kegiatan'));
    }

    public function edit($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $programs = Program::all();
        $unitKerjas = Kegiatan::select('unit_kerja')->distinct()->pluck('unit_kerja');
        return view('kegiatan.edit', compact('kegiatan', 'programs', 'unitKerjas'));
    }

    public function update(Request $request, $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);

        $validated = $request->validate([
            'program_id' => 'nullable|exists:programs,id',
            'kode_kegiatan' => 'required|string|max:50|unique:kegiatans,kode_kegiatan,' . $kegiatan->id,
            'nama_kegiatan' => 'required|string|max:255',
            'unit_kerja' => 'required|string|max:100',
            'penanggung_jawab' => 'required|string|max:100',
            'tahun_anggaran' => 'required|integer',
            'triwulan_target' => 'required|string',
            'kategori_belanja' => 'required|string',
            'pagu_anggaran' => 'required|numeric|min:0',
            'target_fisik' => 'required|numeric|min:0|max:100',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'status_kegiatan' => 'required|in:Belum Dimulai,Sedang Berjalan,Selesai,Tertunda',
            'catatan' => 'nullable|string',
        ]);

        $kegiatan->update($validated);

        return redirect()->route('kegiatan.show', $kegiatan->id)
            ->with('success', 'Data kegiatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->delete();

        return redirect()->route('kegiatan.index')
            ->with('success', 'Kegiatan beserta riwayat realisasinya berhasil dihapus.');
    }
}
