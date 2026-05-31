<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class StudentExamResult extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'student_exam_results';

    protected $fillable = [
        'student_user_id',
        'exam_name',
        'term_name',
        'subject',
        'marks',
        'out_of',
        'exam_date',
        'comment',
        'entered_by',
    ];

    protected $casts = [
        'exam_date' => 'datetime',
    ];
}

