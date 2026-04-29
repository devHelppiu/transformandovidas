<?php

namespace Tests\Feature\Tickets;

use App\Models\Comercial;
use App\Models\Sorteo;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompraConcurrenteTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_se_puede_comprar_numero_ya_reservado_o_pagado(): void
    {
        $sorteo = Sorteo::factory()->create([
            'estado' => 'activo',
            'fecha_sorteo' => now()->addDays(7),
            'fecha_cierre_ventas' => now()->addDays(5),
            'total_tickets' => 100,
            'precio_ticket' => 50000,
            'compra_minima' => 1,
        ]);

        // Pre-crear ticket en estado 'pagado' con número 0042
        Ticket::factory()->create([
            'sorteo_id' => $sorteo->id,
            'numero' => '0042',
            'estado' => 'pagado',
            'comprador_email' => 'previo@test.com',
        ]);

        // Intentar comprar el mismo número 0042
        $response = $this->post(route('tickets.comprar', $sorteo), [
            'nombre' => 'Test',
            'email' => 'test@test.com',
            'cantidad' => 1,
            'numeros' => ['0042'],
        ]);

        // Debe rechazar (validation o session error)
        $this->assertEquals(1, Ticket::where('sorteo_id', $sorteo->id)
            ->where('numero', '0042')->count(), 
            'Solo debe haber un ticket para el número 0042');
    }

    public function test_compra_aleatoria_asigna_cantidad_correcta(): void
    {
        $sorteo = Sorteo::factory()->create([
            'estado' => 'activo',
            'fecha_sorteo' => now()->addDays(7),
            'fecha_cierre_ventas' => now()->addDays(5),
            'total_tickets' => 100,
            'precio_ticket' => 50000,
            'compra_minima' => 1,
        ]);

        $response = $this->post(route('tickets.comprar', $sorteo), [
            'nombre' => 'Test User',
            'email' => 'test@test.com',
            'cantidad' => 5,
        ]);

        $this->assertEquals(5, Ticket::where('sorteo_id', $sorteo->id)
            ->where('comprador_email', 'test@test.com')->count());
    }

    public function test_unique_constraint_previene_duplicados_db(): void
    {
        $sorteo = Sorteo::factory()->create(['total_tickets' => 100]);

        Ticket::factory()->create([
            'sorteo_id' => $sorteo->id,
            'numero' => '0001',
        ]);

        // Insertar duplicado debe lanzar QueryException
        $this->expectException(\Illuminate\Database\QueryException::class);
        Ticket::factory()->create([
            'sorteo_id' => $sorteo->id,
            'numero' => '0001',
        ]);
    }
}
