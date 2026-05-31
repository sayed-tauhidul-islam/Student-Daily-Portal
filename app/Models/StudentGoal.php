<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class StudentGoal extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'student_goals';

    protected $fillable = [
        'user_id',
        'title',
        'target',
        'current',
        'unit',
    ];
}

