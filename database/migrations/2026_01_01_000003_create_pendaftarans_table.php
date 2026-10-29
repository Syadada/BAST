<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pendaftarans')) {
            Schema::create('pendaftarans', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_pendaftaran', 40)->unique();
                $table->string('nama', 150);
                $table->text('alamat')->nullable();
                $table->date('tanggal_lahir');
                $table->string('media_sumber', 100);
                $table->string('media_lain', 150)->nullable();
                $table->text('alasan_mendaftar');
                $table->string('ip_address', 50)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftarans');
    }
};
