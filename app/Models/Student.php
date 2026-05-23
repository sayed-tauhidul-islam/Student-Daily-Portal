<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Student extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'students';

    protected $casts = [
        'subjects' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'class',
        'group',
        'school',
        'subject',
        'subjects',
        'preferred_teacher',
        'area',
        'bio',
        'phone',
    ];
}