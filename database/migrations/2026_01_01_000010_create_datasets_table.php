<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datasets', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dataset', 200);
            $table->string('kode_dataset', 80)->unique();
            $table->string('kategori_desil', 100)->default('Desil 1 s/d Desil 10');
            $table->text('deskripsi')->nullable();
            $table->json('daftar_variabel_tersedia')->nullable(); // Master list variabel yang tersedia
            
            // Kolom SOP / Manual Book bagi pakai data
            $table->string('file_sop_manual')->nullable();
            $table->string('nama_file_sop')->nullable();
            
            $table->string('status', 40)->default('AKTIF'); // AKTIF, DRAF, ARSIP
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datasets');
    }
};
