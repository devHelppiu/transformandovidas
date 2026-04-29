<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comisiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comercial_id')->constrained('comerciales')->cascadeOnDelete();
            $table->foreignId('sorteo_id')->constrained('sorteos')->cascadeOnDelete();
            $table->integer('total_tickets_referidos')->default(0);
            $table->decimal('monto_recaudado', 12, 2)->default(0);
            $table->decimal('monto_comision', 10, 2)->default(0);
            $table->enum('estado', ['pendiente', 'pagada'])->default('pendiente');
            $table->dateTime('pagada_at')->nullable();
            $table->unique(['comercial_id', 'sorteo_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comisiones');
    }
};
