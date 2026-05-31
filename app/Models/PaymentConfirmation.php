<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class PaymentConfirmation extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'payment_confirmations';

    protected $fillable = [
        'school',
        'user_id',
        'role',
        'type',
        'month',
        'amount',
        'confirmed_by',
        'confirmed_at',
        'receiver_confirmed_at',
        'note',
    ];
}
