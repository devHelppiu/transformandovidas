<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * FIX 54: Modelo para idempotencia de webhooks
 */
class WebhookEvent extends Model
{
    protected $fillable = [
        'source',
        'event_type',
        'transaction_id',
        'payload',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];
}
