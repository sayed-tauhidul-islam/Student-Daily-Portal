<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Teacher extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'teachers';

    protected $casts = [
        'subjects' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'qualification',
        'experience',
        'subject',
        'subjects',
        'salary',
        'area',
        'bio',
        'rating',
        'image',
        'availability',
        'institution',
        'gender',
        'online',
        'class_level',
        'verification_status',
    ];
}