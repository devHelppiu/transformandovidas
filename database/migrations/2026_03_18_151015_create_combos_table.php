<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('combos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sorteo_id')->constrained()->cascadeOnDelete();
            $table->string('nombre'); // e.g. "Combo 5 tickets"
            $table->unsignedInteger('cantidad'); // number of tickets
            $table->decimal('precio', 12, 2); // total price for the combo
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combos');
    }
};
