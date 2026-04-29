<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * FIX 62.B: Cambiar role de ENUM a string (compatible cross-DB y más flexible)
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite: recrear columna manteniendo datos
            Schema::table('users', function (Blueprint $table) {
                $table->string('role_new', 30)->default('cliente')->after('password');
            });
            DB::statement('UPDATE users SET role_new = role');
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('role_new', 'role');
            });
        } else {
            // MySQL: ALTER directo
            DB::statement("ALTER TABLE users MODIFY role VARCHAR(30) NOT NULL DEFAULT 'cliente'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hacer nada — volver a ENUM rompería datos con coordinador/lider
    }
};
