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

        if (! $user || ($user->role ?? '') !== 'teacher') {
            abort(403, 'Teacher access required.');
        }

        return $next($request);
    }
}
