<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class StudentTask extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'student_tasks';

    protected $fillable = [
        'user_id',
        'title',
        'due_date',
        'is_completed',
        'priority',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'is_completed' => 'boolean',
    ];
}

