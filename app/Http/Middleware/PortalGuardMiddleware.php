<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PortalGuardMiddleware
{
    /**
     * @var array<int, string>
     */
    private const GUARDS = ['admin', 'teacher_admin', 'teacher', 'student', 'web'];

    public function handle(Request $request, Closure $next): Response
    {
        $guard = $this->resolveGuard($request);

        if (! $guard) {
            return redirect()->route('login');
        }

        Auth::shouldUse($guard);
        $request->session()->put('active_guard', $guard);

        return $next($request);
    }

    private function resolveGuard(Request $request): ?string
    {
        $portalGuard = $this->portalToGuard($request->query('portal'));
        if ($portalGuard && Auth::guard($portalGuard)->check()) {
            return $portalGuard;
        }

        $sessionGuard = (string) $request->session()->get('active_guard', '');
        if (in_array($sessionGuard, self::GUARDS, true) && Auth::guard($sessionGuard)->check()) {
            return $sessionGuard;
        }

        return collect(self::GUARDS)
            ->first(fn (string $guard) => Auth::guard($guard)->check());
    }

    private function portalToGuard(mixed $portal): ?string
    {
        return match ((string) $portal) {
            'student' => 'student',
            'teacher' => 'teacher',
            'teacher-admin' => 'teacher_admin',
            'admin', 'super-admin' => 'admin',
            default => null,
        };
    }
}