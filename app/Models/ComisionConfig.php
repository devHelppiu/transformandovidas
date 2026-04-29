<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComisionConfig extends Model
{
    protected $table = 'comision_configs';

    protected $fillable = [
        'sorteo_id',
        'rol',
        'canal',
        'tipo',
        'valor',
        'activo',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // --- Relationships ---

    public function sorteo(): BelongsTo
    {
        return $this->belongsTo(Sorteo::class);
    }

    // --- Static helpers ---

    /**
     * Obtiene la configuración de comisión para un rol y canal específicos.
     * Busca primero la configuración específica del sorteo, luego la global.
     */
    public static function obtenerConfig(string $rol, string $canal, ?int $sorteoId = null): ?self
    {
        // Primero buscar config específica del sorteo
        if ($sorteoId) {
            $config = self::where('sorteo_id', $sorteoId)
                ->where('rol', $rol)
                ->where('canal', $canal)
                ->where('activo', true)
                ->first();

            if ($config) {
                return $config;
            }
        }

        // Si no hay específica, buscar la global (sorteo_id = null)
        return self::whereNull('sorteo_id')
            ->where('rol', $rol)
            ->where('canal', $canal)
            ->where('activo', true)
            ->first();
    }

    /**
     * Calcula el monto de comisión según el tipo configurado
     */
    public function calcularComision(float $montoBase): float
    {
        return match ($this->tipo) {
            'porcentaje' => $montoBase * ($this->valor / 100),
            'fijo' => (float) $this->valor,
            'meta' => 0, // TODO: implementar lógica de metas
            default => 0,
        };
    }
}
