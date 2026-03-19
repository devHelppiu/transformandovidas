<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comision extends Model
{
    protected $table = 'comisiones';

    protected $fillable = [
        'comercial_id',
        'sorteo_id',
        'total_tickets_referidos',
        'monto_recaudado',
        'monto_comision',
        'estado',
        'pagada_at',
    ];

    protected $casts = [
        'monto_recaudado' => 'decimal:2',
        'monto_comision' => 'decimal:2',
        'pagada_at' => 'datetime',
    ];

    // --- Relationships ---

    public function comercial(): BelongsTo
    {
        return $this->belongsTo(Comercial::class);
    }

    public function sorteo(): BelongsTo
    {
        return $this->belongsTo(Sorteo::class);
    }
}
