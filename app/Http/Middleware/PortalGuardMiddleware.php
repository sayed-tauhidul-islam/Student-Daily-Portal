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

    private const PORTAL_ALIASES = [
        'student' => 'student',
        'teacher' => 'teacher',
        'teacheradmin' => 'teacher_admin',
        'teacher-admin' => 'teacher_admin',
        'superadmin' => 'admin',
        'super-admin' => 'admin',
        'admin' => 'admin',
    ];

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
        $portal = strtolower(trim((string) $portal));
        $portal = preg_replace('/[\s_]+/', '-', $portal) ?? $portal;
        $portal = preg_replace('/-+/', '-', $portal) ?? $portal;

        return self::PORTAL_ALIASES[$portal] ?? self::PORTAL_ALIASES[str_replace('-', '', $portal)] ?? null;
    }
}