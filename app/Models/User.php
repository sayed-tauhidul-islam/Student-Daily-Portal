<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Auth\MustVerifyEmail;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

use Illuminate\Notifications\Notifiable;

class User extends Model implements AuthenticatableContract, MustVerifyEmailContract, CanResetPasswordContract
{
    use HasFactory, Authenticatable, MustVerifyEmail, CanResetPassword, Notifiable;

    protected $connection = 'mongodb';

    protected $collection = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'school',
        'image',
        'phone',
        'area',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}