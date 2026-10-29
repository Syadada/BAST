<?php

namespace App\Http\Controllers;

use App\Models\Kak;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormulirController extends Controller
{
    /**
     * Halaman Utama: Daftar Berkas KAK ala Email Google (Gmail Style) & Formulir Tulis
     */
    public function index(Request $request)
    {
        $query = Kak::query()->latest();

        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('judul_kak', 'like', $search)
                  ->orWhere('nomor_registrasi', 'like', $search)
                  ->orWhere('nama_file_asli', 'like', $search)
                  ->orWhere('penanggung_jawab', 'like', $search)
                  ->orWhere('unit_kerja', 'like', $search);
            });
        }

        $kaks = $query->paginate(25)->withQueryString();
        $totalKak = Kak::count();
        $totalHariIni = Kak::whereDate('created_at', \Carbon\Carbon::today())->count();
        $auditLogs = \App\Models\AuditLog::latest()->take(30)->get();

        return view('formulir.google_form', compact('kaks', 'totalKak', 'totalHariIni', 'auditLogs'));
    }

    /**
     * Proses Unggah & Simpan Berkas KAK
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul_kak' => 'nullable|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:150',
            'unit_kerja' => 'nullable|string|max:150',
            'keterangan' => 'nullable|string',
            'file_kak' => 'required|file|max:51200', // Maksimal 50MB (PDF, DOCX, XLSX, ZIP)
        ], [
            'file_kak.required' => 'Pertanyaan ini wajib diisi. Silakan pilih berkas KAK Anda.',
            'file_kak.max' => 'Ukuran berkas tidak boleh melebihi 50MB.',
        ]);

        $file = $request->file('file_kak');
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension() ?: 'file');
        $sizeBytes = $file->getSize();

        // Format ukuran file (KB / MB)
        if ($sizeBytes >= 1048576) {
            $formattedSize = round($sizeBytes / 1048576, 2) . ' MB';
        } else {
            $formattedSize = round($sizeBytes / 1024, 2) . ' KB';
        }

        // Simpan file ke direktori storage/app/public/kak
        $savedPath = $file->store('kak', 'public');

        // Hitung Integritas Kriptografis SHA-256 (Acuan Dokumen BAST & SOP V2 DTSEN)
        $hashSha256 = hash_file('sha256', $file->getRealPath());

        // Nomor registrasi unik KAK
        $nomorRegistrasi = 'REG-KAK-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $judulKak = $request->filled('judul_kak') ? $request->input('judul_kak') : pathinfo($originalName, PATHINFO_FILENAME);
        $penanggungJawab = $request->filled('penanggung_jawab') ? $request->input('penanggung_jawab') : 'Pengusul Berkas KAK';
        $unitKerja = $request->filled('unit_kerja') ? $request->input('unit_kerja') : 'Unit Kerja Pelaksana';
        $klasifikasi = $request->input('klasifikasi_dokumen', 'TERBATAS (Level 3)');
        $keterangan = $request->filled('keterangan') ? $request->input('keterangan') : 'Dokumen KAK diunggah melalui formulir web standar offline.';

        $kak = Kak::create([
            'nomor_registrasi' => $nomorRegistrasi,
            'judul_kak' => $judulKak,
            'penanggung_jawab' => $penanggungJawab,
            'unit_kerja' => $unitKerja,
            'tanggal_pengajuan' => date('Y-m-d'),
            'file_kak' => $savedPath,
            'nama_file_asli' => $originalName,
            'ukuran_file' => $formattedSize,
            'ekstensi_file' => $extension,
            'hash_sha256' => $hashSha256,
            'klasifikasi_dokumen' => $klasifikasi,
            'status_verifikasi' => 'TERVERIFIKASI INTEGRITAS HASH',
            'keterangan' => $keterangan,
        ]);

        // Catat ke Audit Trail Forensik (Acuan Cepat Analytics)
        AuditLogger::log(
            'KAK_UPLOADED',
            "Berkas KAK [{$originalName}] ({$formattedSize}) diunggah resmi. SHA-256: {$hashSha256}.",
            'SUCCESS',
            [
                'kak_id' => $kak->id,
                'nomor_registrasi' => $nomorRegistrasi,
                'nama_file' => $originalName,
                'ukuran' => $formattedSize,
                'hash_sha256' => $hashSha256,
                'klasifikasi' => $klasifikasi,
            ]
        );

        session(['last_submitted_kak_id' => $kak->id]);

        return redirect()->route('formulir.konfirmasi')
            ->with('success_submitted', true)
            ->with('nomor_registrasi', $nomorRegistrasi)
            ->with('nama_file', $originalName);
    }

    /**
     * Halaman Konfirmasi Pengiriman ala Google Formulir
     */
    public function konfirmasi()
    {
        $lastId = session('last_submitted_kak_id');
        $kak = $lastId ? Kak::find($lastId) : Kak::latest()->first();

        return view('formulir.google_form_konfirmasi', compact('kak'));
    }

    /**
     * Cetak Lembar Pengesahan / Tanda Terima KAK (Tombol Print)
     */
    public function cetak($id)
    {
        $kak = Kak::findOrFail($id);
        return view('kak.cetak', compact('kak'));
    }

    /**
     * Cetak Blanko Kosong Formulir KAK (Tombol Print Langsung di Form)
     */
    public function cetakBlanko()
    {
        $kak = new Kak([
            'nomor_registrasi' => 'REG-KAK-' . date('Ymd') . '-BLANKO',
            'judul_kak' => '.......................................................................................',
            'penanggung_jawab' => '..........................................................',
            'unit_kerja' => '..........................................................',
            'tanggal_pengajuan' => date('Y-m-d'),
            'nama_file_asli' => 'Dokumen Fisik Lampiran KAK',
            'ukuran_file' => '- MB',
            'ekstensi_file' => 'PDF',
            'hash_sha256' => '................................................................',
            'klasifikasi_dokumen' => 'TERBATAS (Level 3)',
            'status_verifikasi' => 'BLANKO CETAK MANUAL',
            'keterangan' => 'Lembar pengesahan blanko cetak untuk pengisian tanda tangan manual.',
        ]);

        return view('kak.cetak', compact('kak'));
    }
}
