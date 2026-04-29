<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Comercial extends Model
{
    use HasFactory;

    protected $table = 'comerciales';

    protected $fillable = [
        'user_id',
        'lider_id',
        'codigo_ref',
        'comision_tipo',
        'comision_valor',
        'is_active',
    ];

    protected $casts = [
        'comision_valor' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Comercial $comercial) {
            if (empty($comercial->codigo_ref)) {
                $comercial->codigo_ref = self::generarCodigo();
            }
        });
    }

    public static function generarCodigo(): string
    {
        do {
            $codigo = 'TV-' . Str::upper(Str::random(6));
        } while (self::where('codigo_ref', $codigo)->exists());

        return $codigo;
    }

    // --- Relationships ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lider(): BelongsTo
    {
        return $this->belongsTo(Lider::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function comisiones(): HasMany
    {
        return $this->hasMany(Comision::class, 'recipient_id')
            ->where('recipient_type', 'Comercial');
    }
}
