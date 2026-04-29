<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * FIX 60: Índices para queries frecuentes en producción
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->index('estado');
            $table->index('user_id');
            $table->index('comercial_id');
            $table->index('lider_id');
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['comercial_id']);
            $table->dropIndex(['lider_id']);
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->dropIndex(['estado']);
        });
    }
};
