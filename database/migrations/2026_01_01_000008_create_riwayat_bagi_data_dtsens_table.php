<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_bagi_data_dtsens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_dtsen_id')->nullable()->constrained('tiket_dtsens')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // 5 Variabel Utama Sesuai Request User:
            // 1. Nama Instansi
            $table->string('nama_instansi', 200);
            
            // 2. Surat Edaran
            $table->string('no_surat_edaran', 150);
            
            // 3. Tanggal dan Waktu Tiket Dibuat
            $table->dateTime('tanggal_tiket_dibuat');
            
            // 4. Tanda Tangan BAST / Proses Bagi Data Selesai
            $table->dateTime('tanggal_bast_selesai')->nullable();
            $table->string('no_bast', 150)->nullable();
            $table->string('status_bast', 80)->default('SELESAI DITANDATANGANI');
            
            // 5. Orang Perangkat Daerah yang Hadir beserta IP atau Nomor Laptop yang Digunakan
            $table->text('orang_hadir'); // Daftar nama orang OPD yang hadir
            $table->string('ip_atau_nomor_laptop', 200); // IP atau nomor laptop/perangkat
            
            $table->text('catatan')->nullable();
            $table->string('created_by', 100)->nullable(); // Petugas / Admin yang menginput
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_bagi_data_dtsens');
    }
};
