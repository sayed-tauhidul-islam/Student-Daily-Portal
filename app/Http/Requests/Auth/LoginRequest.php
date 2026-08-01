<?php

namespace App\Http\Requests\Auth;

use App\Models\BlockedIdentity;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    private const PORTAL_ALIASES = [
        'student' => 'student',
        'teacher' => 'teacher',
        'teacheradmin' => 'teacher-admin',
        'teacher-admin' => 'teacher-admin',
        'superadmin' => 'super-admin',
        'super-admin' => 'super-admin',
    ];

    private const PORTAL_ROLES = [
        'student' => 'student',
        'teacher' => 'teacher',
        'teacher-admin' => 'teacher_admin',
        'super-admin' => 'super_admin',
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
            'portal' => ['nullable', 'string'],
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

        $role = self::PORTAL_ROLES[$portal] ?? 'student';
        $guard = $this->guardForRole($role);

        if ($guard && Auth::guard($guard)->attempt([
            'email' => $email,
            'password' => $password,
        ], $this->boolean('remember'))) {
            Auth::shouldUse($guard);
            $user = Auth::user();
            $userRole = $user
                ? $this->normalizeRole((string) ($user->getRawOriginal('role') ?? $user->role ?? 'student'))
                : null;

            // A successful password check is not enough: each login box only
            // accepts the role it represents.
            if ($user && $userRole === $role) {
                if ((string) ($user->status ?? '') === 'blocked') {
                    Auth::guard($guard)->logout();

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

                    throw ValidationException::withMessages([
                        'email' => 'You are blocked from this school. Please contact the head authority.',
                    ]);
                }

                $this->attributes->set('authenticated_guard', $guard);
                $this->attributes->set('authenticated_role', $userRole);
                RateLimiter::clear($this->throttleKey());

                return;
            }

            Auth::guard($guard)->logout();
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

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => (int) ceil($seconds / 60),
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

    protected function getRedirectUrl(): string
    {
        return route('login', ['portal' => $this->portal()]);
    }

    public function portal(): string
    {
        $portal = strtolower(trim((string) $this->string('portal')->toString()));
        $portal = preg_replace('/[\s_]+/', '-', $portal) ?? $portal;
        $portal = preg_replace('/-+/', '-', $portal) ?? $portal;

        return self::PORTAL_ALIASES[$portal] ?? self::PORTAL_ALIASES[str_replace('-', '', $portal)] ?? 'student';
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

    private function normalizeRole(string $role): string
    {
        $role = strtolower(trim($role));
        $role = preg_replace('/[\s-]+/', '_', $role) ?? $role;

        return match ($role) {
            'teacheradmin', 'teacher_admin', 'head_teacher', 'headteacher' => 'teacher_admin',
            'superadmin', 'super_admin' => 'super_admin',
            'teacherpanel', 'teacher_panel', 'tutor', 'instructor', 'faculty' => 'teacher',
            default => str_contains($role, 'teacher') ? 'teacher' : $role,
        };
    }
}
