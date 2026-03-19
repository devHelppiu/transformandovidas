<?php

namespace App\Listeners;

use App\Events\SorteoEjecutado;
use App\Services\ComisionService;
use Illuminate\Contracts\Queue\ShouldQueue;

class LiquidarComisionesListener implements ShouldQueue
{
    public function __construct(private ComisionService $comisionService)
    {
    }

    public function handle(SorteoEjecutado $event): void
    {
        $this->comisionService->liquidar($event->sorteo);
    }
}
