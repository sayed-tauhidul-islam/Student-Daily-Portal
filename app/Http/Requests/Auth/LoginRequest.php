<?php

namespace App\Http\Requests\Auth;

use App\Models\BlockedIdentity;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    private const PORTAL_ROLE_PRIORITY = [
        'student' => ['student', 'teacher', 'teacher_admin', 'admin', 'super_admin'],
        'teacher' => ['teacher', 'teacher_admin', 'admin', 'super_admin', 'student'],
        'teacher-admin' => ['teacher_admin', 'admin', 'super_admin', 'teacher', 'student'],
        'super-admin' => ['admin', 'super_admin', 'teacher_admin', 'teacher', 'student'],
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'portal' => ['nullable', 'in:student,teacher,teacher-admin,super-admin'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $portal = $this->portal();
        $email = (string) $this->string('email');
        $password = (string) $this->string('password');

        foreach ($this->rolePriorityForPortal($portal) as $role) {
            $guard = $this->guardForRole($role);

            if (! $guard) {
                continue;
            }

            if (! Auth::guard($guard)->attempt([
                'email' => $email,
                'password' => $password,
            ], $this->boolean('remember'))) {
                continue;
            }

            Auth::shouldUse($guard);

            $user = Auth::user();

            if (! $user) {
                Auth::guard($guard)->logout();
                continue;
            }

            // Check if user's role matches one of the priority roles
            $userRole = (string) ($user->role ?? 'student');
            if ($userRole !== $role && ! in_array($userRole, $this->rolePriorityForPortal($portal))) {
                Auth::guard($guard)->logout();
                continue;
            }

            if ((string) ($user->status ?? '') === 'blocked') {
                Auth::guard($guard)->logout();
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'email' => 'This account is blocked by your school authority.',
                ]);
            }

            $blockedIdentity = BlockedIdentity::query()
                ->where('email', Str::lower((string) ($user->email ?? '')))
                ->where('school', (string) ($user->school ?? ''))
                ->first();

            if ($blockedIdentity) {
                Auth::guard($guard)->logout();
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'email' => 'You are blocked from this school. Please contact the head authority.',
                ]);
            }

            $this->attributes->set('authenticated_guard', $guard);
            $this->attributes->set('authenticated_role', (string) ($user->role ?? $role));

            RateLimiter::clear($this->throttleKey());

            return;
        }

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }

    public function portal(): string
    {
        $portal = $this->string('portal')->toString();

        return array_key_exists($portal, self::PORTAL_ROLE_PRIORITY) ? $portal : 'student';
    }

    /**
     * @return array<int, string>
     */
    private function rolePriorityForPortal(string $portal): array
    {
        return self::PORTAL_ROLE_PRIORITY[$portal] ?? self::PORTAL_ROLE_PRIORITY['student'];
    }

    private function guardForRole(string $role): ?string
    {
        return match ($role) {
            'student' => 'student',
            'teacher' => 'teacher',
            'teacher_admin' => 'teacher_admin',
            'admin' => 'admin',
            'super_admin' => 'admin',
            default => null,
        };
    }
}
