<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingCheckout extends Model
{
    use HasFactory;

    protected $table = 'pending_checkouts';

    protected $fillable = [
        'stripe_session_id',
        'payment_gateway',
        'gateway_session_id',
        'gateway_reference',
        'id_client',
        'payload',
        'gateway_payload',
        'expires_at',
        'completed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'gateway_payload' => 'array',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
