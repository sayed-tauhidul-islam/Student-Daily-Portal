<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class LoginReview extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'login_reviews';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'role',
        'school',
        'phone',
        'area',
        'ip_address',
        'user_agent',
        'status',
        'blocked_at',
        'blocked_by',
    ];
}
