<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@transformandovidas.co',
            'phone' => '3001234567',
            'password' => 'password',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }
}
