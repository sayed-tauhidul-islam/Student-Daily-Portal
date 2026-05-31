<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class StudentAssignment extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'student_assignments';

    protected $fillable = [
        'user_id',
        'title',
        'subject',
        'deadline',
        'status',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];
}

