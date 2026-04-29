<?php

namespace Tests\Feature\Comisiones;

use App\Models\Comercial;
use App\Models\Comision;
use App\Models\ComisionConfig;
use App\Models\Coordinador;
use App\Models\Lider;
use App\Models\Sorteo;
use App\Models\Ticket;
use App\Models\User;
use App\Services\ComisionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CascadaTest extends TestCase
{
    use RefreshDatabase;

    private function setUpJerarquia(): array
    {
        // Coordinador
        $userCoord = User::factory()->create(['role' => 'coordinador']);
        $coordinador = Coordinador::create([
            'user_id' => $userCoord->id,
            'is_active' => true,
        ]);

        // Lider asignado al coordinador
        $userLider = User::factory()->create(['role' => 'lider']);
        $lider = Lider::create([
            'user_id' => $userLider->id,
            'coordinador_id' => $coordinador->id,
            'codigo_ref' => 'TV-L' . strtoupper(uniqid('', false)),
            'is_active' => true,
        ]);

        // Comercial asignado al lider
        $userComercial = User::factory()->create(['role' => 'comercial']);
        $comercial = Comercial::create([
            'user_id' => $userComercial->id,
            'lider_id' => $lider->id,
            'codigo_ref' => 'TV-' . strtoupper(uniqid('', false)),
            'is_active' => true,
        ]);

        return compact('coordinador', 'lider', 'comercial');
    }

    private function configurarComisiones(): void
    {
        ComisionConfig::create([
            'sorteo_id' => null, 'rol' => 'comercial', 'canal' => 'directo',
            'tipo' => 'porcentaje', 'valor' => 5, 'activo' => true,
        ]);
        ComisionConfig::create([
            'sorteo_id' => null, 'rol' => 'lider', 'canal' => 'override',
            'tipo' => 'porcentaje', 'valor' => 2, 'activo' => true,
        ]);
        ComisionConfig::create([
            'sorteo_id' => null, 'rol' => 'coordinador', 'canal' => 'override',
            'tipo' => 'porcentaje', 'valor' => 1, 'activo' => true,
        ]);
    }

    public function test_venta_via_comercial_genera_comisiones_en_cascada(): void
    {
        $this->configurarComisiones();
        ['coordinador' => $coord, 'lider' => $lider, 'comercial' => $comercial] = $this->setUpJerarquia();

        $sorteo = Sorteo::factory()->create([
            'precio_ticket' => 100000,
            'total_tickets' => 100,
        ]);

        // 1 ticket pagado vía comercial
        Ticket::factory()->create([
            'sorteo_id' => $sorteo->id,
            'comercial_id' => $comercial->id,
            'estado' => 'pagado',
        ]);

        // Liquidar
        app(ComisionService::class)->liquidar($sorteo);

        // Verificar las 3 comisiones (polymorphic recipient_type/recipient_id)
        $this->assertDatabaseHas('comisiones', [
            'recipient_type' => 'Comercial',
            'recipient_id' => $comercial->id,
            'monto_comision' => 5000, // 5% de 100000
        ]);
        $this->assertDatabaseHas('comisiones', [
            'recipient_type' => 'Lider',
            'recipient_id' => $lider->id,
            'monto_comision' => 2000, // 2% override
        ]);
        $this->assertDatabaseHas('comisiones', [
            'recipient_type' => 'Coordinador',
            'recipient_id' => $coord->id,
            'monto_comision' => 1000, // 1% override
        ]);
    }

    public function test_ticket_no_pagado_no_genera_comision(): void
    {
        $this->configurarComisiones();
        ['comercial' => $comercial] = $this->setUpJerarquia();

        $sorteo = Sorteo::factory()->create(['precio_ticket' => 100000]);

        Ticket::factory()->create([
            'sorteo_id' => $sorteo->id,
            'comercial_id' => $comercial->id,
            'estado' => 'reservado', // no pagado
        ]);

        app(ComisionService::class)->liquidar($sorteo);

        $this->assertDatabaseMissing('comisiones', [
            'recipient_type' => 'Comercial',
            'recipient_id' => $comercial->id,
        ]);
    }
}
