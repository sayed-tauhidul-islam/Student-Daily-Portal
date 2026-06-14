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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        'status',
        'blocked_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getRoleAttribute(mixed $value): string
    {
        $role = Str::of((string) ($value ?: 'student'))
            ->lower()
            ->trim()
            ->replaceMatches('/[\s-]+/', '_')
            ->replaceMatches('/_+/', '_')
            ->toString();

        return match ($role) {
            'teacheradmin', 'teacher_admin', 'head_teacher', 'headteacher' => 'teacher_admin',
            'superadmin', 'super_admin' => 'super_admin',
            'teacherpanel', 'teacher_panel', 'tutor', 'instructor', 'faculty' => 'teacher',
            'admin', 'teacher', 'student' => $role,
            default => 'student',
        };
    }

    public function getImageUrlAttribute(): ?string
    {
        $image = trim((string) ($this->attributes['image'] ?? ''));

        if ($image === '') {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $image)) {
            return $image;
        }

        $normalized = ltrim($image, '/');

        if (str_starts_with($normalized, 'storage/')) {
            return asset($normalized);
        }

        return Storage::disk('public')->exists($normalized)
            ? Storage::disk('public')->url($normalized)
            : null;
    }
}
