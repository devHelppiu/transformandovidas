<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sorteos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->date('fecha_sorteo');
            $table->dateTime('fecha_cierre_ventas');
            $table->integer('total_tickets');
            $table->decimal('precio_ticket', 10, 2);
            $table->decimal('valor_premio', 12, 2)->nullable();
            $table->unsignedSmallInteger('compra_minima')->default(1);
            $table->string('numero_ganador', 4)->nullable();
            $table->enum('estado', ['borrador', 'activo', 'cerrado', 'ejecutado'])->default('borrador');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sorteos');
    }
};
