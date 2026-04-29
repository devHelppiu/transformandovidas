<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Coordinador extends Model
{
    protected $table = 'coordinadores';

    protected $fillable = [
        'user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // --- Relationships ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lideres(): HasMany
    {
        return $this->hasMany(Lider::class);
    }

    /**
     * Todos los comerciales bajo este coordinador (a través de sus lideres)
     */
    public function comerciales(): HasManyThrough
    {
        return $this->hasManyThrough(Comercial::class, Lider::class);
    }

    /**
     * Comisiones del coordinador
     */
    public function comisiones(): HasMany
    {
        return $this->hasMany(Comision::class, 'recipient_id')
            ->where('recipient_type', 'Coordinador');
    }

    // --- Helpers ---

    public function lideresActivos(): HasMany
    {
        return $this->lideres()->where('is_active', true);
    }

    public function totalComercialesActivos(): int
    {
        return Comercial::whereHas('lider', fn($q) => $q->where('coordinador_id', $this->id))
            ->where('is_active', true)
            ->count();
    }
}
