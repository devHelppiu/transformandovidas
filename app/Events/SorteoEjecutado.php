<?php

namespace App\Events;

use App\Models\Sorteo;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SorteoEjecutado
{
    use Dispatchable, SerializesModels;

    public function __construct(public Sorteo $sorteo)
    {
    }
}
