<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comisiones', function (Blueprint $table) {
            // Agregar columnas polimórficas
            $table->string('recipient_type', 50)->after('id')->default('Comercial');
            $table->unsignedBigInteger('recipient_id')->after('recipient_type')->nullable();
            $table->decimal('porcentaje_aplicado', 5, 2)->nullable()->after('monto_comision');
        });

        // Migrar datos existentes: comercial_id -> recipient_type/recipient_id
        DB::statement("UPDATE comisiones SET recipient_type = 'Comercial', recipient_id = comercial_id WHERE comercial_id IS NOT NULL");

        Schema::table('comisiones', function (Blueprint $table) {
            // Hacer nullable comercial_id y agregar índice
            $table->unsignedBigInteger('comercial_id')->nullable()->change();
            $table->index(['recipient_type', 'recipient_id']);
        });
    }

    public function down(): void
    {
        Schema::table('comisiones', function (Blueprint $table) {
            $table->dropIndex(['recipient_type', 'recipient_id']);
            $table->dropColumn(['recipient_type', 'recipient_id', 'porcentaje_aplicado']);
        });
    }
};
