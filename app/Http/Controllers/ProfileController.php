<?php

namespace App\Http\Controllers;

use App\Models\TiketDtsen;
use App\Models\RiwayatBagiDataDtsen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * 1. Halaman Clean Dashboard Utama Perangkat Daerah (OPD)
     * Hanya menampilkan sambutan, status instansi, dan Tombol Utama "Buat Tiket Baru"
     */
    public function dashboard()
    {
        $user = Auth::user();
        $namaInstansi = $user->nama_instansi ?: $user->name;

        // Ambil tiket permohonan terkini jika ada (untuk quick banner)
        $latestTiket = TiketDtsen::where(function ($q) use ($user, $namaInstansi) {
                $q->where('nama_instansi', 'like', '%' . $namaInstansi . '%')
                  ->orWhere('email_pemohon', $user->email);
            })
            ->latest()
            ->first();

        return view('opd.dashboard', compact('user', 'latestTiket'));
    }

    /**
     * 2. Halaman Layanan Bagi Pakai DTSEN
     * Memuat Progress 5 Tahapan Layanan DTSEN & Tabel Data Riwayat Bagi Data DTSEN Resmi
     */
    public function layananBagiPakai()
    {
        $user = Auth::user();
        $namaInstansi = $user->nama_instansi ?: $user->name;
        
        $tikets = TiketDtsen::where(function ($q) use ($user, $namaInstansi) {
                $q->where('nama_instansi', 'like', '%' . $namaInstansi . '%')
                  ->orWhere('email_pemohon', $user->email);
            })
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

        return view('opd.layanan_bagi_pakai', compact('user', 'tikets', 'selectedTiket', 'riwayatBagiDatas'));
    }

    /**
     * Alias untuk rute lama /profile agar kompatibel
     */
    public function index()
    {
        return $this->layananBagiPakai();
    }
}
