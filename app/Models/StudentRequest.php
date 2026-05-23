<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class StudentRequest extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'student_requests';

    protected $casts = [
        'applications' => 'array',
        'is_featured' => 'boolean',
        'online' => 'boolean',
    ];

    protected $fillable = [
        'user_id',
        'teacher_id',
        'post_id',
        'student_name',
        'title',
        'class_level',
        'group',
        'school',
        'subject',
        'area',
        'budget',
        'gender',
        'online',
        'phone',
        'description',
        'status',
        'applications',
        'is_featured',
        'deadline',
    ];
}
