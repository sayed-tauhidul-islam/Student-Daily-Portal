<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeacherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Auth::shouldUse('teacher');
        $request->session()->put('active_guard', 'teacher');

        $user = Auth::guard('teacher')->user();

        if (! $user || $this->normalizeRole((string) ($user->role ?? $user->getRawOriginal('role') ?? '')) !== 'teacher') {
            abort(403, 'Teacher access required.');
        }

        return $next($request);
    }

    private function normalizeRole(string $role): string
    {
        $role = strtolower(trim($role));
        $role = preg_replace('/[\s-]+/', '_', $role) ?? $role;

        return match ($role) {
            'teacherpanel', 'teacher_panel', 'tutor', 'instructor', 'faculty' => 'teacher',
            default => $role,
        };
    }
}
