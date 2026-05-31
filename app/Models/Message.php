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
        'edited_at' => 'datetime',
        'deleted_for_everyone_at' => 'datetime',
        'sender_deleted_at' => 'datetime',
        'receiver_deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'receiver_id',
        'body',
        'attachments',
        'voice_message',
        'read_at',
        'edited_at',
        'edited_by',
        'deleted_for_everyone_at',
        'deleted_for_everyone_by',
        'sender_deleted_at',
        'receiver_deleted_at',
    ];
}
