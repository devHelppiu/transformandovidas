<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sorteo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen',
        'fecha_sorteo',
        'fecha_cierre_ventas',
        'total_tickets',
        'precio_ticket',
        'valor_premio',
        'premio_extra',
        'compra_minima',
        'numero_ganador',
        'estado',
        'pago_simulado',
    ];

    protected $casts = [
        'fecha_sorteo' => 'date',
        'fecha_cierre_ventas' => 'datetime',
        'precio_ticket' => 'decimal:2',
        'valor_premio' => 'decimal:2',
        'pago_simulado' => 'boolean',
    ];

    // --- Relationships ---

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function combos(): HasMany
    {
        return $this->hasMany(Combo::class);
    }

    public function comisiones(): HasMany
    {
        return $this->hasMany(Comision::class);
    }

    // --- Scopes ---

    public function scopeActivo($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeCerrado($query)
    {
        return $query->where('estado', 'cerrado');
    }

    // --- Business methods ---

    public function numerosOcupados(): array
    {
        return $this->tickets()
            ->whereIn('estado', ['reservado', 'pagado'])
            ->pluck('numero')
            ->toArray();
    }

    public function numerosDisponibles(): array
    {
        $ocupados = $this->numerosOcupados();
        $todos = [];
        for ($i = 0; $i < $this->total_tickets && $i <= 9999; $i++) {
            $todos[] = str_pad($i, 4, '0', STR_PAD_LEFT);
        }

        return array_values(array_diff($todos, $ocupados));
    }

    public function ticketsVendidos(): int
    {
        return $this->tickets()->where('estado', 'pagado')->count();
    }

    public function ventasCerradas(): bool
    {
        return $this->fecha_cierre_ventas->isPast() || $this->estado !== 'activo';
    }

    /**
     * Obtiene el estado de números en un rango dado para el number picker.
     *
     * @param int $from Número inicial (0-9999)
     * @param int $to   Número final (0-9999)
     * @return array Array de objetos con 'numero' y 'estado' (disponible, reservado, vendido)
     */
    public function numerosEstadosEnRango(int $from, int $to): array
    {
        $ocupados = $this->tickets()
            ->whereIn('estado', ['reservado', 'pagado'])
            ->whereBetween('numero', [
                str_pad($from, 4, '0', STR_PAD_LEFT),
                str_pad($to, 4, '0', STR_PAD_LEFT),
            ])
            ->get(['numero', 'estado'])
            ->keyBy('numero');

        $resultado = [];
        for ($i = $from; $i <= $to && $i < $this->total_tickets; $i++) {
            $num = str_pad($i, 4, '0', STR_PAD_LEFT);
            $ticketEstado = $ocupados->get($num)?->estado ?? null;

            $resultado[] = [
                'numero' => $num,
                'estado' => match ($ticketEstado) {
                    'pagado' => 'vendido',
                    'reservado' => 'reservado',
                    default => 'disponible',
                },
            ];
        }

        return $resultado;
    }
}
