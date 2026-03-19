<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            ['name' => 'Juan Pérez', 'email' => 'juan@example.com', 'phone' => '3001111111'],
            ['name' => 'Ana Gómez', 'email' => 'ana@example.com', 'phone' => '3002222222'],
            ['name' => 'Luis Torres', 'email' => 'luis@example.com', 'phone' => '3003333333'],
            ['name' => 'Sofía Ramírez', 'email' => 'sofia@example.com', 'phone' => '3004444444'],
            ['name' => 'Pedro Castro', 'email' => 'pedro@example.com', 'phone' => '3005555555'],
        ];

        foreach ($clientes as $data) {
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => 'password',
                'role' => 'cliente',
                'email_verified_at' => now(),
            ]);
        }
    }
}
