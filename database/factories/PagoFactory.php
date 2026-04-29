<?php

namespace Database\Factories;

use App\Models\Pago;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class PagoFactory extends Factory
{
    protected $model = Pago::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'monto' => fake()->randomElement([50000, 100000, 150000]),
            'estado' => 'pendiente',
            'metodo' => 'helppiu',
        ];
    }

    public function verificado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'verificado',
        ]);
    }
}
