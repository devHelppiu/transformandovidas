<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketComprado
{
    use Dispatchable, SerializesModels;

    public function __construct(public Ticket $ticket)
    {
    }
}
