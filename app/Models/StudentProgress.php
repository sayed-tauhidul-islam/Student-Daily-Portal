<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class StudentProgress extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'student_progress';

    protected $fillable = [
        'student_user_id',
        'school',
        'class',
        'overall_score',
        'attendance_score',
        'reading_score',
        'writing_score',
        'assignment_score',
        'behavior_score',
        'exam_goal',
        'motivation_note',
        'teacher_comment',
        'subjects',
        'updated_by',
    ];

    protected $casts = [
        'subjects' => 'array',
    ];
}
