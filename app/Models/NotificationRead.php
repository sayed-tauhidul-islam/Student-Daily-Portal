<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class NotificationRead extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'notification_reads';

    protected $fillable = [
        'user_id',
        'item_type',
        'item_id',
        'seen_at',
    ];

    protected $casts = [
        'seen_at' => 'datetime',
    ];
}
