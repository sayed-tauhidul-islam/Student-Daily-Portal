<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Complaint extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'complaints';

    protected $fillable = [
        'school',
        'created_by',
        'creator_role',
        'against_user_id',
        'against_name',
        'against_role',
        'title',
        'body',
        'status',
        'action_note',
        'action_by',
    ];
}
