<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Formulir Pengajuan Dokumen KAK (Kerangka Acuan Kerja)
        Schema::create('kaks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_registrasi', 50)->unique();
            $table->string('judul_kak', 255);
            $table->string('penanggung_jawab', 150);
            $table->string('unit_kerja', 150)->nullable();
            $table->date('tanggal_pengajuan');
            $table->string('file_kak')->nullable();
            $table->string('nama_file_asli')->nullable();
            $table->string('ukuran_file')->nullable();
            $table->string('ekstensi_file', 20)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kaks');
    }
};
