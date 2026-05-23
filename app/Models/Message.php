<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Message extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'messages';

    protected $casts = [
        'attachments' => 'array',
        'read_at' => 'datetime',
    ];

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'receiver_id',
        'body',
        'attachments',
        'voice_message',
        'read_at',
    ];
}
