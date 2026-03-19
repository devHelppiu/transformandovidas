<?php

namespace Database\Seeders;

use App\Models\Combo;
use App\Models\Comercial;
use App\Models\Pago;
use App\Models\Sorteo;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class SorteoSeeder extends Seeder
{
    public function run(): void
    {
        $sorteo = Sorteo::create([
            'nombre' => 'Sorteo Quincenal - Marzo 2026',
            'descripcion' => '¡Gran sorteo solidario! Compra tu bono y participa por el premio mayor.',
            'fecha_sorteo' => '2026-03-31',
            'fecha_cierre_ventas' => '2026-03-30 23:59:59',
            'total_tickets' => 10000,
            'precio_ticket' => 25000.00,
            'valor_premio' => 50000000.00,
            'estado' => 'activo',
        ]);

        $clientes = User::where('role', 'cliente')->get();
        $comerciales = Comercial::all();

        $numeros = ['0042', '1357', '2468', '5000', '7777', '9999', '0001', '3210'];

        foreach ($clientes as $index => $cliente) {
            // Each client gets 1-2 tickets
            $comercial = $comerciales->random();

            $ticket = Ticket::create([
                'sorteo_id' => $sorteo->id,
                'user_id' => $cliente->id,
                'comercial_id' => $comercial->id,
                'comprador_nombre' => $cliente->name,
                'comprador_email' => $cliente->email,
                'comprador_telefono' => $cliente->phone,
                'numero' => $numeros[$index],
                'tipo_asignacion' => 'aleatorio',
                'estado' => 'pagado',
            ]);

            Pago::create([
                'ticket_id' => $ticket->id,
                'metodo' => ['nequi', 'daviplata', 'visa'][$index % 3],
                'monto' => $sorteo->precio_ticket,
                'referencia_pago' => 'REF-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                'estado' => 'verificado',
                'verificado_por' => User::where('role', 'admin')->first()->id,
                'verificado_at' => now(),
            ]);

            // Give some clients a second ticket in "reservado" state
            if ($index < 3) {
                $ticket2 = Ticket::create([
                    'sorteo_id' => $sorteo->id,
                    'user_id' => $cliente->id,
                    'comercial_id' => $index === 0 ? null : $comercial->id,
                    'comprador_nombre' => $cliente->name,
                    'comprador_email' => $cliente->email,
                    'comprador_telefono' => $cliente->phone,
                    'numero' => $numeros[$index + 5],
                    'tipo_asignacion' => 'aleatorio',
                    'estado' => 'reservado',
                ]);

                Pago::create([
                    'ticket_id' => $ticket2->id,
                    'metodo' => 'nequi',
                    'monto' => $sorteo->precio_ticket,
                    'estado' => 'pendiente',
                ]);
            }
        }

        // Sample combos
        Combo::create(['sorteo_id' => $sorteo->id, 'nombre' => 'Combo 5 Tickets', 'cantidad' => 5, 'precio' => 100000, 'activo' => true]);
        Combo::create(['sorteo_id' => $sorteo->id, 'nombre' => 'Combo 10 Tickets', 'cantidad' => 10, 'precio' => 180000, 'activo' => true]);
        Combo::create(['sorteo_id' => $sorteo->id, 'nombre' => 'Combo 3 Tickets', 'cantidad' => 3, 'precio' => 65000, 'activo' => true]);
    }
}
