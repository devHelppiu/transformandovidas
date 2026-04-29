<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Solo ejecutar MODIFY COLUMN en MySQL - SQLite usa string nativo
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pagos MODIFY COLUMN estado ENUM('pendiente', 'procesando', 'verificado', 'rechazado', 'reversado') DEFAULT 'pendiente'");
        }
        // En SQLite el campo ya es string y acepta cualquier valor
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pagos MODIFY COLUMN estado ENUM('pendiente', 'verificado', 'rechazado') DEFAULT 'pendiente'");
        }
    }
};
