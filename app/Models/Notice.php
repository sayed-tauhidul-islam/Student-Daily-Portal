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
        'title',
        'body',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
