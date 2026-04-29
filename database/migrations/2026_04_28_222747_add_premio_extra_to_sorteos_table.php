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
        Schema::table('sorteos', function (Blueprint $table) {
            $table->string('premio_extra', 80)->nullable()->after('valor_premio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sorteos', function (Blueprint $table) {
            $table->dropColumn('premio_extra');
        });
    }
};
