<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ParentPortalAccess extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'parent_portal_accesses';

    protected $fillable = [
        'student_user_id',
        'access_code',
        'parent_name',
        'relation',
        'contact',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

