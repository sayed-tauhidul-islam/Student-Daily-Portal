<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Auth::shouldUse('student');
        $request->session()->put('active_guard', 'student');

        $user = Auth::guard('student')->user();

        if (! $user || ($user->role ?? 'student') !== 'student') {
            abort(403, 'Student access required.');
        }

        return $next($request);
    }
}
