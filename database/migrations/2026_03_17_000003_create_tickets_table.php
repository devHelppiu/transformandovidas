<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sorteo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('comercial_id')->nullable()->constrained('comerciales')->nullOnDelete();
            $table->string('comprador_nombre');
            $table->string('comprador_email');
            $table->string('comprador_telefono')->nullable();
            $table->string('grupo_compra', 36)->nullable()->index(); // UUID to group combo tickets
            $table->string('numero', 4); // 0000–9999
            $table->enum('tipo_asignacion', ['elegido', 'aleatorio'])->default('aleatorio');
            $table->enum('estado', ['reservado', 'pagado', 'anulado'])->default('reservado');
            $table->unique(['sorteo_id', 'numero']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
