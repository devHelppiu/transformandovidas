<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar lider_id a comerciales
        Schema::table('comerciales', function (Blueprint $table) {
            $table->foreignId('lider_id')->nullable()->after('user_id')->constrained('lideres')->nullOnDelete();
        });

        // Agregar lider_id a tickets (para ventas directas del lider)
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('lider_id')->nullable()->after('comercial_id')->constrained('lideres')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('comerciales', function (Blueprint $table) {
            $table->dropForeign(['lider_id']);
            $table->dropColumn('lider_id');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['lider_id']);
            $table->dropColumn('lider_id');
        });
    }
};
