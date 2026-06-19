<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Notice extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'notices';

    protected $fillable = [
        'institute',
        'teacher_user_id',
        'target_user_id',
        'target_type',
        'title',
        'body',
        'attachments',
        'published_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'published_at' => 'datetime',
    ];
}
