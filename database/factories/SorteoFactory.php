<?php

namespace Database\Factories;

use App\Models\Sorteo;
use Illuminate\Database\Eloquent\Factories\Factory;

class SorteoFactory extends Factory
{
    protected $model = Sorteo::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->words(3, true),
            'descripcion' => fake()->paragraph(),
            'fecha_sorteo' => fake()->dateTimeBetween('+1 week', '+1 month'),
            'fecha_cierre_ventas' => fake()->dateTimeBetween('+3 days', '+6 days'),
            'total_tickets' => 100,
            'precio_ticket' => fake()->randomElement([50000, 100000, 150000]),
            'valor_premio' => fake()->randomNumber(6),
            'compra_minima' => 1,
            'estado' => 'activo',
        ];
    }

    public function finalizado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'ejecutado',
        ]);
    }
}
