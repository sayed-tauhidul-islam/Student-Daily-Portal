<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class BlockedIdentity extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'blocked_identities';

    protected $fillable = [
        'email',
        'school',
        'role',
        'user_id',
        'reason',
        'blocked_by',
        'blocked_at',
    ];
}
