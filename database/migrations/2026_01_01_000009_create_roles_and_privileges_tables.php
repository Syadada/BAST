<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Master Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 80)->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Master Privileges / Hak Akses Fitur
        Schema::create('privileges', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 100)->unique();
            $table->string('module', 80)->default('SISTEM'); // TIKET, GOVERNANCE, SECURITY, USERS
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 3. Tabel Pivot Relasi Role - Privilege
        Schema::create('role_privilege', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('privilege_id')->constrained('privileges')->cascadeOnDelete();
            $table->primary(['role_id', 'privilege_id']);
        });

        // 4. Hubungkan role_id ke tabel users (nullable agar kompatibel dengan data yang ada)
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'role_id')) {
                    $table->foreignId('role_id')->nullable()->after('role')->constrained('roles')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            });
        }
        Schema::dropIfExists('role_privilege');
        Schema::dropIfExists('privileges');
        Schema::dropIfExists('roles');
    }
};
