<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class LeaveApplication extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'leave_applications';

    protected $fillable = [
        'school',
        'user_id',
        'role',
        'leave_type',
        'from_date',
        'to_date',
        'reason',
        'document_path',
        'status',
        'reviewed_by',
        'review_note',
    ];
}
