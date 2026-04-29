<?php

namespace Database\Factories;

use App\Models\Comercial;
use App\Models\Sorteo;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'sorteo_id' => Sorteo::factory(),
            'user_id' => User::factory(),
            'comercial_id' => Comercial::factory(),
            'comprador_nombre' => fake()->name(),
            'comprador_email' => fake()->safeEmail(),
            'comprador_telefono' => fake()->phoneNumber(),
            'numero' => str_pad(fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'tipo_asignacion' => 'elegido',
            'estado' => 'reservado',
            'grupo_compra' => Str::uuid()->toString(),
        ];
    }

    public function pagado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'pagado',
        ]);
    }
}
