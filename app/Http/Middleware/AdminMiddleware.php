<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Auth::shouldUse('admin');
        $request->session()->put('active_guard', 'admin');

        $user = Auth::guard('admin')->user();

        if (! $user || (($user->role ?? '') !== 'admin' && ($user->role ?? '') !== 'super_admin')) {
            abort(403, 'Admin access required.');
        }

        return $next($request);
    }
}
