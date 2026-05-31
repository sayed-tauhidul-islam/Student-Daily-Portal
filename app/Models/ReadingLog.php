<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ReadingLog extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'reading_logs';

    protected $fillable = [
        'student_user_id',
        'school',
        'book_name',
        'subject',
        'read_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'note',
    ];
}
