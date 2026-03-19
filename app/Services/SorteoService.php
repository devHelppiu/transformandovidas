<?php

namespace App\Services;

use App\Events\SorteoEjecutado;
use App\Models\Sorteo;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SorteoService
{
    public function __construct(
        private ComisionService $comisionService,
    ) {}

    /**
     * Execute the raffle: pick a random winner from paid tickets.
     *
     * @throws InvalidArgumentException
     */
    public function ejecutar(Sorteo $sorteo): Ticket
    {
        if ($sorteo->estado !== 'cerrado') {
            throw new InvalidArgumentException('El sorteo debe estar en estado "cerrado" para ejecutarlo.');
        }

        $ticketsPagados = $sorteo->tickets()->where('estado', 'pagado')->get();

        if ($ticketsPagados->isEmpty()) {
            throw new InvalidArgumentException('No hay tickets pagados en este sorteo.');
        }

        $ganador = DB::transaction(function () use ($sorteo, $ticketsPagados) {
            $ganador = $ticketsPagados->random();

            $sorteo->update([
                'numero_ganador' => $ganador->numero,
                'estado' => 'ejecutado',
            ]);

            // Calculate commissions
            $this->comisionService->liquidar($sorteo);

            return $ganador;
        });

        SorteoEjecutado::dispatch($sorteo);

        return $ganador;
    }

    /**
     * Activate a sorteo (borrador → activo).
     */
    public function activar(Sorteo $sorteo): void
    {
        if ($sorteo->estado !== 'borrador') {
            throw new InvalidArgumentException('Solo se puede activar un sorteo en borrador.');
        }

        $sorteo->update(['estado' => 'activo']);
    }

    /**
     * Close sales for a sorteo (activo → cerrado).
     */
    public function cerrar(Sorteo $sorteo): void
    {
        if ($sorteo->estado !== 'activo') {
            throw new InvalidArgumentException('Solo se puede cerrar un sorteo activo.');
        }

        $sorteo->update(['estado' => 'cerrado']);
    }
}
