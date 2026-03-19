<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Combo extends Model
{
    protected $fillable = [
        'sorteo_id',
        'nombre',
        'cantidad',
        'precio',
        'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function sorteo(): BelongsTo
    {
        return $this->belongsTo(Sorteo::class);
    }

    public function precioUnitario(): float
    {
        return $this->cantidad > 0 ? (float) $this->precio / $this->cantidad : 0;
    }

    public function descuento(float $precioTicketIndividual): float
    {
        $precioSinDescuento = $precioTicketIndividual * $this->cantidad;
        if ($precioSinDescuento <= 0) return 0;
        return round((1 - (float) $this->precio / $precioSinDescuento) * 100);
    }
}
