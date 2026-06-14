<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class PaymentConfirmation extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'payment_confirmations';

    protected $casts = [
        'submitted_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'receiver_confirmed_at' => 'datetime',
    ];

    protected $fillable = [
        'school',
        'user_id',
        'role',
        'type',
        'month',
        'amount',
        'status',
        'submitted_by',
        'submitted_at',
        'confirmed_by',
        'confirmed_at',
        'receiver_confirmed_at',
        'note',
    ];
}
