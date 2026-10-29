<?php

namespace App\Http\Controllers;

use App\Models\Kak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KakController extends Controller
{
    /**
     * Halaman Utama: Menampilkan Formulir Input KAK & Riwayat Berkas Terunggah
     */
    public function index()
    {
        $kaks = Kak::latest()->get();
        return view('kak.index', compact('kaks'));
    }

    /**
     * Proses Simpan Formulir KAK (Upload File & Metadata)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul_kak' => 'nullable|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:150',
            'unit_kerja' => 'nullable|string|max:150',
            'tanggal_pengajuan' => 'nullable|date',
            'file_kak' => 'required|file|max:51200', // max 50MB (PDF, DOCX, XLSX, ZIP, Gambar)
            'keterangan' => 'nullable|string',
        ]);

        $file = $request->file('file_kak');
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $sizeBytes = $file->getSize();

        // Format ukuran file (KB / MB)
        if ($sizeBytes >= 1048576) {
            $formattedSize = round($sizeBytes / 1048576, 2) . ' MB';
        } else {
            $formattedSize = round($sizeBytes / 1024, 2) . ' KB';
        }

        // Simpan file ke direktori storage/app/public/kak
        $savedPath = $file->store('kak', 'public');

        // Hitung Integritas Kriptografis SHA-256
        $hashSha256 = hash_file('sha256', $file->getRealPath());

        // Nomor registrasi otomatis
        $nomorRegistrasi = 'REG-KAK-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $kak = Kak::create([
            'nomor_registrasi' => $nomorRegistrasi,
            'judul_kak' => $request->judul_kak ?: pathinfo($originalName, PATHINFO_FILENAME),
            'penanggung_jawab' => $request->penanggung_jawab ?: 'Pengusul Berkas KAK',
            'unit_kerja' => $request->unit_kerja ?: 'Satuan Kerja Pelaksana',
            'tanggal_pengajuan' => $request->tanggal_pengajuan ?: date('Y-m-d'),
            'file_kak' => $savedPath,
            'nama_file_asli' => $originalName,
            'ukuran_file' => $formattedSize,
            'ekstensi_file' => $extension,
            'hash_sha256' => $hashSha256,
            'klasifikasi_dokumen' => 'TERBATAS (Level 3)',
            'status_verifikasi' => 'TERVERIFIKASI INTEGRITAS HASH',
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('kak.index')
            ->with('success', "Berkas KAK [{$originalName}] berhasil diunggah dengan nomor {$nomorRegistrasi}. Anda dapat langsung mencetak lembar pengesahannya.");
    }

    /**
     * Halaman Cetak Lembar Pengesahan / Tanda Terima KAK (Tombol Print)
     */
    public function cetak($id)
    {
        $kak = Kak::findOrFail($id);
        return view('kak.cetak', compact('kak'));
    }

    /**
     * Download Berkas KAK yang Terunggah
     */
    public function download($id)
    {
        $kak = Kak::findOrFail($id);
        if ($kak->file_kak && Storage::disk('public')->exists($kak->file_kak)) {
            return Storage::disk('public')->download($kak->file_kak, $kak->nama_file_asli);
        }
        return redirect()->back()->with('error', 'Berkas fisik tidak ditemukan di penyimpanan server.');
    }

    /**
     * Hapus Data KAK
     */
    public function destroy($id)
    {
        $kak = Kak::findOrFail($id);
        if ($kak->file_kak && Storage::disk('public')->exists($kak->file_kak)) {
            Storage::disk('public')->delete($kak->file_kak);
        }
        $kak->delete();

        return redirect()->route('kak.index')
            ->with('success', 'Dokumen KAK berhasil dihapus.');
    }
}
