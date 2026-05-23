<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class TeacherPost extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'teacher_posts';

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
        'online' => 'boolean',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'class_level',
        'experience',
        'budget',
        'tags',
        'category',
        'online',
        'is_featured',
        'published_at',
    ];
}
