<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Rating extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'ratings';

    protected $casts = [
        'verified' => 'boolean',
    ];

    protected $fillable = [
        'user_id',
        'target_type',
        'target_id',
        'rating',
        'comment',
        'verified',
    ];
}
