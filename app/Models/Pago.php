<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $fillable = [
        'ticket_id',
        'metodo',
        'monto',
        'comprobante_url',
        'referencia_pago',
        'checkout_session_id',
        'transaction_id',
        'estado',
        'verificado_por',
        'verificado_at',
        'nota_rechazo',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'verificado_at' => 'datetime',
    ];

    // --- Relationships ---

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function verificador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verificado_por');
    }

    // --- Scopes ---

    public function scopePendiente($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeVerificado($query)
    {
        return $query->where('estado', 'verificado');
    }
}
