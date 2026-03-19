<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ticket extends Model
{
    protected $fillable = [
        'sorteo_id',
        'user_id',
        'comercial_id',
        'comprador_nombre',
        'comprador_email',
        'comprador_telefono',
        'grupo_compra',
        'numero',
        'tipo_asignacion',
        'estado',
    ];

    // --- Relationships ---

    public function sorteo(): BelongsTo
    {
        return $this->belongsTo(Sorteo::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comercial(): BelongsTo
    {
        return $this->belongsTo(Comercial::class);
    }

    public function pago(): HasOne
    {
        return $this->hasOne(Pago::class);
    }

    // --- Scopes ---

    public function scopeReservado($query)
    {
        return $query->where('estado', 'reservado');
    }

    public function scopePagado($query)
    {
        return $query->where('estado', 'pagado');
    }

    public function scopeAnulado($query)
    {
        return $query->where('estado', 'anulado');
    }
}
