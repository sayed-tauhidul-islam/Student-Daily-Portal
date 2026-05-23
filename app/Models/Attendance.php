<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Attendance extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'attendances';

    protected $fillable = [
        'institute',
        'student_user_id',
        'teacher_user_id',
        'date',
        'status',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
