<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::first() ?? new Pengaturan();
        return view('pengaturan.index', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'nama_dinas' => 'required|string|max:255',
            'nama_pimpinan' => 'required|string|max:150',
            'nip_pimpinan' => 'required|string|max:50',
            'nama_ppk' => 'required|string|max:150',
            'nip_ppk' => 'required|string|max:50',
            'tahun_anggaran_aktif' => 'required|integer',
        ]);

        $pengaturan = Pengaturan::first();
        if ($pengaturan) {
            $pengaturan->update($validated);
        } else {
            Pengaturan::create($validated);
        }

        return redirect()->route('pengaturan.index')
            ->with('success', 'Konfigurasi profil instansi & lembar pengesahan berhasil disimpan.');
    }
}
