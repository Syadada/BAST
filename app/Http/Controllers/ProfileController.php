<?php

namespace App\Http\Controllers;

use App\Models\TiketDtsen;
use App\Models\RiwayatBagiDataDtsen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Halaman Profil Akun Perangkat Daerah & Progress Status Tiket
     */
    public function index()
    {
        $user = Auth::user();

        // Cari tiket instansi perangkat daerah ini
        $namaInstansi = $user->nama_instansi ?: $user->name;
        
        $tikets = TiketDtsen::where('nama_instansi', 'like', '%' . $namaInstansi . '%')
            ->orWhere('email_pemohon', $user->email)
            ->latest()
            ->get();

        // Tiket yang sedang disorot untuk progress bar
        $selectedTiket = $tikets->first();

        // Tabel Data Riwayat Bagi Data DTSEN Resmi (Diinput oleh Admin, Read-Only untuk OPD)
        $riwayatBagiDatas = RiwayatBagiDataDtsen::where(function ($q) use ($user, $namaInstansi) {
                $q->where('user_id', $user->id)
                  ->orWhere('nama_instansi', 'like', '%' . $namaInstansi . '%');
            })
            ->latest('tanggal_tiket_dibuat')
            ->get();

        return view('profile.index', compact('user', 'tikets', 'selectedTiket', 'riwayatBagiDatas'));
    }
}
