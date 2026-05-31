<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TeacherAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Auth::shouldUse('teacher_admin');
        $request->session()->put('active_guard', 'teacher_admin');

        $user = Auth::guard('teacher_admin')->user();

        if (! $user || (($user->role ?? '') !== 'teacher_admin' && ($user->role ?? '') !== 'admin' && ($user->role ?? '') !== 'super_admin')) {
            abort(403, 'Teacher admin access required.');
        }

        return $next($request);
    }
}
