<?php

namespace Database\Factories;

use App\Models\Comercial;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComercialFactory extends Factory
{
    protected $model = Comercial::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'codigo_ref' => 'TV-' . strtoupper(fake()->lexify('???')) . fake()->numerify('###'),
            'comision_tipo' => 'porcentaje',
            'comision_valor' => fake()->numberBetween(5, 15),
            'is_active' => true,
        ];
    }
}
