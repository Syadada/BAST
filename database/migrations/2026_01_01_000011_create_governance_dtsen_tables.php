<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pengolahan DTSEN (Cleansing & Filtering teknis)
        Schema::create('pengolahan_dtsens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_dtsen_id')->constrained('tiket_dtsens')->cascadeOnDelete();
            $table->foreignId('petugas_pengolah_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('tanggal_mulai')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();
            $table->unsignedBigInteger('jumlah_baris_data')->nullable()->default(0);
            $table->string('status_pengolahan', 50)->default('MENUNGGU'); // MENUNGGU, PROSES_FILTERING, SELESAI, GAGAL
            $table->text('catatan_teknis')->nullable();
            $table->timestamps();
        });

        // 2. Hak Akses DTSEN (Otorisasi Instansi & Masa Berlaku)
        Schema::create('hak_akses_dtsens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_dtsen_id')->constrained('tiket_dtsens')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('lingkup_data', 255);
            $table->string('status_akses', 40)->default('AKTIF'); // AKTIF, BERAKHIR, DIBEKUKAN, DICABUT
            $table->dateTime('masa_berlaku_mulai');
            $table->dateTime('masa_berlaku_selesai');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 3. Pengamanan DTSEN (Enkripsi & Checksum SHA-256)
        Schema::create('pengamanan_dtsens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_dtsen_id')->constrained('tiket_dtsens')->cascadeOnDelete();
            $table->string('metode_enkripsi', 100)->default('AES-256-GCM / Hardware Encrypted');
            $table->string('checksum_sha256', 64)->nullable();
            $table->string('status_integritas', 50)->default('TERVERIFIKASI'); // TERVERIFIKASI, DALAM_PENGECEKAN, INTEGRITAS_RUSAK
            $table->dateTime('tanggal_verifikasi')->nullable();
            $table->string('petugas_keamanan', 120)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 4. ROPA DTSEN (Record of Processing Activities - UU PDP)
        Schema::create('ropa_dtsens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_dtsen_id')->constrained('tiket_dtsens')->cascadeOnDelete();
            $table->text('tujuan_pemrosesan');
            $table->string('kategori_data_pribadi', 255);
            $table->string('pihak_penerima', 255);
            $table->string('dasar_hukum_se', 255);
            $table->string('jangka_waktu_retensi', 100)->default('1 Tahun / Sesuai Ketentuan BAST');
            $table->string('metode_penyimpanan', 150)->default('Perangkat Terenkripsi / Offline Safe Storage');
            $table->timestamps();
        });

        // 5. DPIA DTSEN (Data Protection Impact Assessment)
        Schema::create('dpia_dtsens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_dtsen_id')->constrained('tiket_dtsens')->cascadeOnDelete();
            $table->string('skor_risiko', 40)->default('SEDANG'); // RENDAH, SEDANG, TINGGI
            $table->text('identifikasi_dampak');
            $table->text('langkah_mitigasi');
            $table->string('status_rekomendasi', 50)->default('DISETUJUI'); // DISETUJUI, BUTUH_PENINJAUAN_LANJUTAN, DITOLAK
            $table->foreignId('pejabat_penilai_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('tanggal_penilaian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dpia_dtsens');
        Schema::dropIfExists('ropa_dtsens');
        Schema::dropIfExists('pengamanan_dtsens');
        Schema::dropIfExists('hak_akses_dtsens');
        Schema::dropIfExists('pengolahan_dtsens');
    }
};
