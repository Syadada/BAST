<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use App\Models\TiketDtsen;
use App\Models\RiwayatBagiDataDtsen;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Halaman Manajemen Pengguna & Log Perangkat Laptop (Hak Kendali Super Admin)
     */
    public function index(Request $request)
    {
        $users = User::latest()->paginate(15, ['*'], 'users_page');
        
        // Log nomor laptop / perangkat yang digunakan login
        $loginLogs = AuditLog::where('action', 'AUTH_LOGIN')
            ->latest()
            ->paginate(15, ['*'], 'logs_page');

        // Data Riwayat Bagi Data DTSEN Resmi
        $riwayatBagiDatas = RiwayatBagiDataDtsen::latest()->paginate(15, ['*'], 'riwayat_page');

        // Daftar tiket untuk referensi cepat input riwayat
        $availableTikets = TiketDtsen::latest()->get();

        return view('admin.users.index', compact('users', 'loginLogs', 'riwayatBagiDatas', 'availableTikets'));
    }

    /**
     * Tambah Akun Perangkat Daerah Baru
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'nama_instansi' => 'required|string|max:200',
            'name' => 'required|string|max:150',
            'username' => 'required|string|max:80|unique:users,username',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:4',
            'role' => 'required|in:perangkat_daerah,superadmin',
            'nomor_kontak' => 'nullable|string|max:50',
        ], [
            'username.unique' => 'Username ini sudah digunakan.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.min' => 'Password minimal 4 karakter.',
        ]);

        $user = User::create([
            'nama_instansi' => $request->nama_instansi,
            'name' => $request->name,
            'username' => trim($request->username),
            'email' => trim($request->email),
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'nomor_kontak' => $request->nomor_kontak,
        ]);

        AuditLogger::log(
            'USER_CREATED',
            "Super Admin membuat akun [{$user->username}] untuk instansi {$user->nama_instansi} (Role: {$user->role}).",
            'SUCCESS'
        );

        return back()->with('success', "Akun [{$user->username}] ({$user->nama_instansi}) berhasil dibuat.");
    }

    /**
     * Hapus Akun Pengguna
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $username = $user->username;
        $instansi = $user->nama_instansi ?: $user->name;
        $user->delete();

        AuditLogger::log(
            'USER_DELETED',
            "Akun [{$username}] ({$instansi}) telah dihapus oleh Super Admin.",
            'WARNING'
        );

        return back()->with('success', "Akun [{$username}] ({$instansi}) berhasil dihapus.");
    }

    /**
     * Super Admin: Input Data Riwayat Bagi Data DTSEN Resmi
     */
    public function storeRiwayatBagiData(Request $request)
    {
        $request->validate([
            'nama_instansi' => 'required|string|max:200',
            'no_surat_edaran' => 'required|string|max:150',
            'tanggal_tiket_dibuat' => 'required|date',
            'tanggal_bast_selesai' => 'nullable|date',
            'no_bast' => 'nullable|string|max:150',
            'orang_hadir' => 'required|string',
            'ip_atau_nomor_laptop' => 'required|string|max:200',
            'tiket_dtsen_id' => 'nullable|exists:tiket_dtsens,id',
            'user_id' => 'nullable|exists:users,id',
            'status_bast' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
        ], [
            'nama_instansi.required' => 'Nama Instansi wajib diisi.',
            'no_surat_edaran.required' => 'Nomor Surat Edaran wajib diisi.',
            'orang_hadir.required' => 'Daftar nama orang perangkat daerah yang hadir wajib diisi.',
            'ip_atau_nomor_laptop.required' => 'IP atau nomor laptop yang digunakan wajib diisi.',
        ]);

        $riwayat = RiwayatBagiDataDtsen::create([
            'tiket_dtsen_id' => $request->tiket_dtsen_id,
            'user_id' => $request->user_id,
            'nama_instansi' => $request->nama_instansi,
            'no_surat_edaran' => $request->no_surat_edaran,
            'tanggal_tiket_dibuat' => $request->tanggal_tiket_dibuat,
            'tanggal_bast_selesai' => $request->tanggal_bast_selesai,
            'no_bast' => $request->no_bast ?: ('BAST/DTSEN/' . date('Y/m') . '/' . rand(100, 999)),
            'status_bast' => $request->status_bast ?: 'SELESAI DITANDATANGANI (BAST SAH)',
            'orang_hadir' => $request->orang_hadir,
            'ip_atau_nomor_laptop' => $request->ip_atau_nomor_laptop,
            'catatan' => $request->catatan,
            'created_by' => auth()->user()->name ?? 'Super Administrator',
        ]);

        AuditLogger::log(
            'RIWAYAT_BAGI_DATA_CREATED',
            "Admin menginput data riwayat bagi pakai DTSEN resmi untuk instansi: {$riwayat->nama_instansi}.",
            'SUCCESS'
        );

        return back()->with('success', "Data Riwayat Bagi Data DTSEN untuk [{$riwayat->nama_instansi}] berhasil disimpan dan tampil pada Profile Perangkat Daerah.");
    }

    /**
     * Hapus Data Riwayat Bagi Data DTSEN
     */
    public function destroyRiwayatBagiData($id)
    {
        $riwayat = RiwayatBagiDataDtsen::findOrFail($id);
        $instansi = $riwayat->nama_instansi;
        $riwayat->delete();

        AuditLogger::log(
            'RIWAYAT_BAGI_DATA_DELETED',
            "Data riwayat bagi data DTSEN untuk {$instansi} dihapus oleh Super Admin.",
            'WARNING'
        );

        return back()->with('success', "Data Riwayat Bagi Data DTSEN untuk [{$instansi}] berhasil dihapus.");
    }
}
