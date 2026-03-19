<?php

namespace Database\Seeders;

use App\Models\Comercial;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComercialSeeder extends Seeder
{
    public function run(): void
    {
        $comerciales = [
            [
                'name' => 'Carlos Mendoza',
                'email' => 'carlos@transformandovidas.com',
                'phone' => '3109876543',
                'codigo_ref' => 'TV-AAA111',
                'comision_tipo' => 'porcentaje',
                'comision_valor' => 10.00,
            ],
            [
                'name' => 'María López',
                'email' => 'maria@transformandovidas.com',
                'phone' => '3205551234',
                'codigo_ref' => 'TV-BBB222',
                'comision_tipo' => 'fijo',
                'comision_valor' => 5000.00,
            ],
        ];

        foreach ($comerciales as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => 'password',
                'role' => 'comercial',
                'email_verified_at' => now(),
            ]);

            Comercial::create([
                'user_id' => $user->id,
                'codigo_ref' => $data['codigo_ref'],
                'comision_tipo' => $data['comision_tipo'],
                'comision_valor' => $data['comision_valor'],
            ]);
        }
    }
}
