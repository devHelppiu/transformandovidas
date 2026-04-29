<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comision extends Model
{
    protected $table = 'comisiones';

    protected $fillable = [
        'recipient_type',
        'recipient_id',
        'comercial_id', // deprecated, mantener para compatibilidad
        'sorteo_id',
        'total_tickets_referidos',
        'monto_recaudado',
        'monto_comision',
        'porcentaje_aplicado',
        'estado',
        'pagada_at',
    ];

    protected $casts = [
        'monto_recaudado' => 'decimal:2',
        'monto_comision' => 'decimal:2',
        'porcentaje_aplicado' => 'decimal:2',
        'pagada_at' => 'datetime',
    ];

    // --- Relationships ---

    /**
     * Relación polimórfica con el destinatario (Coordinador, Lider o Comercial)
     */
    public function recipient(): MorphTo
    {
        return $this->morphTo('recipient', 'recipient_type', 'recipient_id');
    }

    /**
     * @deprecated Usar recipient() en su lugar
     */
    public function comercial(): BelongsTo
    {
        return $this->belongsTo(Comercial::class);
    }

    public function sorteo(): BelongsTo
    {
        return $this->belongsTo(Sorteo::class);
    }

    // --- Scopes ---

    public function scopeDeComercial($query, int $comercialId)
    {
        return $query->where('recipient_type', 'Comercial')
            ->where('recipient_id', $comercialId);
    }

    public function scopeDeLider($query, int $liderId)
    {
        return $query->where('recipient_type', 'Lider')
            ->where('recipient_id', $liderId);
    }

    public function scopeDeCoordinador($query, int $coordinadorId)
    {
        return $query->where('recipient_type', 'Coordinador')
            ->where('recipient_id', $coordinadorId);
    }
}
