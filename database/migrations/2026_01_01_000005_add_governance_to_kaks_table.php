<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kaks', function (Blueprint $table) {
            $table->string('hash_sha256', 64)->nullable()->after('ekstensi_file');
            $table->string('klasifikasi_dokumen', 50)->default('TERBATAS (Level 3)')->after('hash_sha256');
            $table->string('status_verifikasi', 50)->default('TERVERIFIKASI SISTEM')->after('klasifikasi_dokumen');
        });
    }

    public function down(): void
    {
        Schema::table('kaks', function (Blueprint $table) {
            $table->dropColumn(['hash_sha256', 'klasifikasi_dokumen', 'status_verifikasi']);
        });
    }
};
