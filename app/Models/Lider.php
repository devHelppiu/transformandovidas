<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lider extends Model
{
    protected $table = 'lideres';

    protected $fillable = [
        'user_id',
        'coordinador_id',
        'codigo_ref',
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

    public function coordinador(): BelongsTo
    {
        return $this->belongsTo(Coordinador::class);
    }

    public function comerciales(): HasMany
    {
        return $this->hasMany(Comercial::class);
    }

    /**
     * Tickets vendidos directamente con el código del lider
     */
    public function ticketsDirectos(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Comisiones del lider
     */
    public function comisiones(): HasMany
    {
        return $this->hasMany(Comision::class, 'recipient_id')
            ->where('recipient_type', 'Lider');
    }

    // --- Helpers ---

    public function comercialesActivos(): HasMany
    {
        return $this->comerciales()->where('is_active', true);
    }

    /**
     * Genera un código de referido único para el lider
     */
    public static function generarCodigoRef(): string
    {
        do {
            $codigo = 'TV-L' . strtoupper(substr(md5(uniqid()), 0, 5));
        } while (self::where('codigo_ref', $codigo)->exists());

        return $codigo;
    }

    /**
     * URL completa del enlace de referido
     */
    public function urlReferido(): string
    {
        return url('/ref/' . $this->codigo_ref);
    }
}
