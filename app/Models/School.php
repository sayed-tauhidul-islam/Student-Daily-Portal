<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class School extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'schools';

    protected $fillable = [
        'name',
        'area',
        'type',
        'rating',
        'students',
    ];
}
