<?php

namespace App\Http\Controllers;

use App\Models\TiketDtsen;
use App\Models\AuditLog;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TiketDtsenController extends Controller
{
    /**
     * Halaman Utama: Layanan Permintaan Bagi Pakai DTSEN (Sisi Pengguna / Instansi)
     */
    public function index(Request $request)
    {
        // Admin tidak mengakses halaman tiket pemohon, arahkan langsung ke Dashboard Pengelolaan Tiket
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $query = TiketDtsen::query()->latest();

        // Pencarian tiket berdasarkan nomor tiket atau nama instansi
        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('nomor_tiket', 'like', $search)
                  ->orWhere('nama_instansi', 'like', $search)
                  ->orWhere('no_surat_edaran', 'like', $search)
                  ->orWhere('nama_pemohon', 'like', $search);
            });
        }

        $tikets = $query->paginate(10)->withQueryString();
        
        // Tiket yang sedang disorot (bisa dari session tiket baru, parameter URL, atau tiket teratas)
        $selectedTiket = null;
        if ($request->filled('track_id')) {
            $selectedTiket = TiketDtsen::find($request->track_id);
        } elseif (session('new_tiket_id')) {
            $selectedTiket = TiketDtsen::find(session('new_tiket_id'));
        } elseif ($tikets->count() > 0) {
            $selectedTiket = $tikets->first();
        }

        // Metrik Layanan
        $totalTiket = TiketDtsen::count();
        $totalSelesai = TiketDtsen::where('step5_done', true)->count();
        $totalProses = TiketDtsen::where('step5_done', false)->count();

        return view('formulir.dtsen_tiket', compact(
            'tikets',
            'selectedTiket',
            'totalTiket',
            'totalSelesai',
            'totalProses'
        ));
    }

    /**
     * Buat Tiket Permintaan Bagi Pakai DTSEN Baru
     */
    public function store(Request $request)
    {
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $request->validate([
            'nama_instansi' => 'required|string|max:200',
            'no_surat_edaran' => 'required|string|max:150',
            'nama_pemohon' => 'nullable|string|max:150',
            'kontak_pemohon' => 'nullable|string|max:100',
            'email_pemohon' => 'nullable|email|max:150',
            'tujuan_pemanfaatan' => 'nullable|string',
            'variabel_dtsen' => 'required|array|min:1',
            'file_surat' => 'nullable|file|max:51200', // Maksimal 50MB
        ], [
            'nama_instansi.required' => 'Nama Instansi / Lembaga pemohon wajib diisi.',
            'no_surat_edaran.required' => 'Nomor Surat Edaran / Permohonan wajib diisi.',
            'variabel_dtsen.required' => 'Minimal salah satu variabel DTSEN harus diisi.',
            'variabel_dtsen.min' => 'Silakan isi variabel DTSEN yang diminta.',
            'file_surat.max' => 'Ukuran berkas surat tidak boleh melebihi 50MB.',
        ]);

        // Bersihkan variabel kosong dan trim
        $variabelInput = array_filter(array_map('trim', (array) $request->input('variabel_dtsen')));
        
        if (empty($variabelInput)) {
            return back()->withInput()->withErrors([
                'variabel_dtsen' => 'Mohon isi setidaknya satu kolom variabel DTSEN yang Anda butuhkan.'
            ]);
        }

        // Handle file lampiran surat jika ada
        $filePath = null;
        $originalName = null;
        $formattedSize = null;
        $extension = null;
        $hashSha256 = null;

        if ($request->hasFile('file_surat')) {
            $file = $request->file('file_surat');
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension() ?: 'pdf');
            $sizeBytes = $file->getSize();

            if ($sizeBytes >= 1048576) {
                $formattedSize = round($sizeBytes / 1048576, 2) . ' MB';
            } else {
                $formattedSize = round($sizeBytes / 1024, 2) . ' KB';
            }

            $filePath = $file->store('surat_edaran', 'public');
            $hashSha256 = hash_file('sha256', $file->getRealPath());
        }

        // Generate Nomor Tiket Unik
        $nomorTiket = 'TIK-DTSEN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $tiket = TiketDtsen::create([
            'nomor_tiket' => $nomorTiket,
            'nama_instansi' => $request->input('nama_instansi'),
            'no_surat_edaran' => $request->input('no_surat_edaran'),
            'nama_pemohon' => $request->input('nama_pemohon') ?: 'PIC Instansi Pemohon',
            'kontak_pemohon' => $request->input('kontak_pemohon'),
            'email_pemohon' => $request->input('email_pemohon'),
            'tujuan_pemanfaatan' => $request->input('tujuan_pemanfaatan') ?: 'Permohonan bagi pakai data DTSEN untuk kebutuhan program instansi.',
            'variabel_dtsen' => array_values($variabelInput),
            'file_surat' => $filePath,
            'nama_file_asli' => $originalName,
            'ukuran_file' => $formattedSize,
            'ekstensi_file' => $extension,
            'hash_sha256' => $hashSha256,
            'current_step' => 1,
            'step1_done' => false,
            'step2_done' => false,
            'step3_done' => false,
            'step4_done' => false,
            'step5_done' => false,
            'status' => 'DIPROSES',
            'catatan_admin' => 'Tiket baru berhasil diajukan. Menunggu verifikasi berkas oleh admin.',
        ]);

        // Catat Audit Trail
        AuditLogger::log(
            'TIKET_DTSEN_CREATED',
            "Tiket Bagi Pakai DTSEN [{$nomorTiket}] diajukan oleh {$tiket->nama_instansi}. Jumlah variabel: " . count($variabelInput),
            'SUCCESS',
            ['nomor_tiket' => $nomorTiket, 'instansi' => $tiket->nama_instansi]
        );

        return redirect()->route('formulir.index', ['track_id' => $tiket->id])
            ->with('success', "Tiket Layanan DTSEN berhasil dibuat dengan Nomor: {$nomorTiket}! Status dapat dipantau pada progress bar di bawah.")
            ->with('new_tiket_id', $tiket->id);
    }

    /**
     * Download Berkas Surat Edaran Tiket
     */
    public function downloadSurat($id)
    {
        $tiket = TiketDtsen::findOrFail($id);

        if (!$tiket->file_surat || !Storage::disk('public')->exists($tiket->file_surat)) {
            return back()->with('error', 'Berkas surat edaran tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($tiket->file_surat, $tiket->nama_file_asli ?: "Surat_{$tiket->nomor_tiket}.{$tiket->ekstensi_file}");
    }

    /**
     * Perbarui / Revisi Variabel Tiket oleh Perangkat Daerah (Sesuai Catatan Admin)
     */
    public function updateVariabel(Request $request, $id)
    {
        $tiket = TiketDtsen::findOrFail($id);
        $user = auth()->user();

        // Validasi kepemilikan jika bukan admin
        if (!$user->isSuperAdmin() && !str_contains(strtolower($tiket->nama_instansi), strtolower($user->nama_instansi ?: '')) && $tiket->email_pemohon !== $user->email) {
            return back()->with('error', 'Anda tidak memiliki hak untuk merevisi tiket instansi lain.');
        }

        $request->validate([
            'variabel_dtsen' => 'required|array|min:1',
            'catatan_opd' => 'nullable|string',
        ], [
            'variabel_dtsen.required' => 'Minimal salah satu variabel DTSEN harus diisi.',
            'variabel_dtsen.min' => 'Silakan isi variabel DTSEN yang diminta.',
        ]);

        $variabelInput = array_filter(array_map('trim', (array) $request->input('variabel_dtsen')));

        if (empty($variabelInput)) {
            return back()->withInput()->withErrors([
                'variabel_dtsen' => 'Mohon isi setidaknya satu kolom variabel DTSEN yang Anda butuhkan.'
            ]);
        }

        $tiket->variabel_dtsen = array_values($variabelInput);
        $tiket->status = 'DIPROSES';
        $tiket->catatan_admin = 'Perbaikan variabel telah diajukan ulang oleh ' . $user->name . ' pada ' . now()->format('d/m/Y H:i') . ' WIB. Menunggu verifikasi kesesuaian dengan Surat Edaran oleh Admin.';
        $tiket->save();

        AuditLogger::log(
            'TIKET_VARIABEL_REVISED',
            "Instansi {$tiket->nama_instansi} memperbarui variabel tiket [{$tiket->nomor_tiket}]. Jumlah variabel: " . count($variabelInput),
            'SUCCESS',
            ['nomor_tiket' => $tiket->nomor_tiket]
        );

        return back()->with('success', "Variabel tiket [{$tiket->nomor_tiket}] berhasil diperbarui dan diajukan ulang untuk diverifikasi oleh Admin sesuai Surat Edaran.");
    }
}
