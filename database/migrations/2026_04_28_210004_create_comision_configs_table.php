<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comision_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sorteo_id')->nullable()->constrained()->cascadeOnDelete(); // null = default global
            $table->enum('rol', ['comercial', 'lider', 'coordinador']);
            $table->enum('canal', ['directo', 'override']); // directo = venta propia, override = venta de nivel inferior
            $table->enum('tipo', ['porcentaje', 'fijo', 'meta'])->default('porcentaje');
            $table->decimal('valor', 8, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['sorteo_id', 'rol', 'canal'], 'comision_config_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comision_configs');
    }
};
