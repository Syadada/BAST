<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiket_dtsens', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket', 60)->unique();
            $table->string('nama_instansi', 200);
            $table->string('no_surat_edaran', 150);
            $table->string('nama_pemohon', 150)->nullable();
            $table->string('kontak_pemohon', 100)->nullable();
            $table->string('email_pemohon', 150)->nullable();
            $table->text('tujuan_pemanfaatan')->nullable();
            $table->json('variabel_dtsen'); // Array variabel DTSEN yang diminta
            $table->string('file_surat')->nullable();
            $table->string('nama_file_asli')->nullable();
            $table->string('ukuran_file')->nullable();
            $table->string('ekstensi_file', 20)->nullable();
            $table->string('hash_sha256', 64)->nullable();
            
            // Tahapan Proses Tiket (1 - 5)
            $table->unsignedTinyInteger('current_step')->default(1);
            $table->boolean('step1_done')->default(false);
            $table->boolean('step2_done')->default(false);
            $table->boolean('step3_done')->default(false);
            $table->boolean('step4_done')->default(false);
            $table->boolean('step5_done')->default(false);
            
            $table->string('status', 50)->default('DIPROSES'); // DIPROSES, SELESAI, REVISI
            $table->text('catatan_admin')->nullable();
            
            $table->timestamp('step1_at')->nullable();
            $table->timestamp('step2_at')->nullable();
            $table->timestamp('step3_at')->nullable();
            $table->timestamp('step4_at')->nullable();
            $table->timestamp('step5_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiket_dtsens');
    }
};
