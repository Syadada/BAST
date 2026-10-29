<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama_instansi', 200)->nullable()->after('name');
            $table->string('nomor_kontak', 100)->nullable()->after('nama_instansi');
            $table->timestamp('last_login_at')->nullable()->after('role');
            $table->string('last_login_ip', 60)->nullable()->after('last_login_at');
            $table->string('last_login_device', 100)->nullable()->after('last_login_ip');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nama_instansi',
                'nomor_kontak',
                'last_login_at',
                'last_login_ip',
                'last_login_device',
            ]);
        });
    }
};
