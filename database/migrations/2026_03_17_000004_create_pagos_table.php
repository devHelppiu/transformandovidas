<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->string('metodo', 30)->default('helppiu'); // helppiu, nequi, daviplata, breb, visa, mastercard, pse, etc.
            $table->decimal('monto', 10, 2);
            $table->string('comprobante_url')->nullable();
            $table->string('referencia_pago')->nullable();
            $table->string('checkout_session_id')->nullable(); // Helppiu Pay session ID
            $table->string('transaction_id')->nullable(); // Helppiu Pay transaction ID
            $table->enum('estado', ['pendiente', 'verificado', 'rechazado'])->default('pendiente');
            $table->foreignId('verificado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('verificado_at')->nullable();
            $table->text('nota_rechazo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
