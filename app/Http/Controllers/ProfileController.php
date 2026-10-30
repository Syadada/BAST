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
     * 2. Halaman Daftar Tiket OPD (Ala DomaiNesia Support Tickets)
     * Menampilkan: Nama Instansi, Subject (No Tiket - Clickable), Status, Last Updated
     */
    public function daftarTiket(Request $request)
    {
        $user = Auth::user();
        $namaInstansi = $user->nama_instansi ?: $user->name;

        $query = TiketDtsen::where(function ($q) use ($user, $namaInstansi) {
                $q->where('nama_instansi', 'like', '%' . $namaInstansi . '%')
                  ->orWhere('email_pemohon', $user->email);
            })
            ->latest('updated_at');

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('nomor_tiket', 'like', $search)
                  ->orWhere('no_surat_edaran', 'like', $search)
                  ->orWhere('nama_pemohon', 'like', $search);
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            if ($request->status === 'SELESAI') {
                $query->where('step5_done', true);
            } elseif ($request->status === 'REVISI') {
                $query->where('status', 'PERLU_PERBAIKAN');
            } elseif ($request->status === 'PROSES') {
                $query->where('step5_done', false)
                      ->where('status', '!=', 'PERLU_PERBAIKAN');
            }
        }

        $tikets = $query->paginate(10)->withQueryString();

        return view('opd.tiket.index', compact('user', 'tikets'));
    }

    /**
     * 3. Halaman Detail Progress Tiket OPD
     * Menampilkan alur 5 tahapan progress secara lebih jelas dan interaktif
     */
    public function showTiket($id)
    {
        $user = Auth::user();
        $namaInstansi = $user->nama_instansi ?: $user->name;

        $tiket = TiketDtsen::where('id', $id)
            ->where(function ($q) use ($user, $namaInstansi) {
                // Keamanan data: hanya pemilik tiket yang dapat membuka (kecuali admin)
                if (!$user->isSuperAdmin()) {
                    $q->where('nama_instansi', 'like', '%' . $namaInstansi . '%')
                      ->orWhere('email_pemohon', $user->email);
                }
            })
            ->firstOrFail();

        return view('opd.tiket.show', compact('user', 'tiket'));
    }

    /**
     * 4. Halaman Layanan Bagi Pakai DTSEN
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
     * 5. Halaman Detail Riwayat Bagi Data DTSEN Resmi (Sisi OPD)
     */
    public function showRiwayatBagiData($id)
    {
        $user = Auth::user();
        $namaInstansi = $user->nama_instansi ?: $user->name;

        $riwayat = RiwayatBagiDataDtsen::with(['tiket', 'user'])
            ->where('id', $id)
            ->where(function ($q) use ($user, $namaInstansi) {
                if (!$user->isSuperAdmin()) {
                    $q->where('user_id', $user->id)
                      ->orWhere('nama_instansi', 'like', '%' . $namaInstansi . '%');
                }
            })
            ->firstOrFail();

        return view('opd.riwayat_bagi_data.show', compact('user', 'riwayat'));
    }

    /**
     * Alias untuk rute lama /profile agar kompatibel
     */
    public function index()
    {
        return $this->layananBagiPakai();
    }
}
