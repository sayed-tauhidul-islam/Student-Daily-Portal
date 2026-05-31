<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        $portal = in_array($request->string('portal')->toString(), ['student', 'teacher', 'teacher-admin', 'super-admin'], true)
            ? $request->string('portal')->toString()
            : 'student';

        return view('auth.login', [
            'portal' => $portal,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $guard = (string) $request->attributes->get('authenticated_guard', 'student');
        Auth::shouldUse($guard);

        $request->session()->regenerate();
        $request->session()->put('active_guard', $guard);

        $user = Auth::guard($guard)->user();
        if ($user) {
            LoginReview::create([
                'user_id' => (string) $user->getKey(),
                'name' => (string) ($user->name ?? ''),
                'email' => (string) ($user->email ?? ''),
                'role' => (string) ($user->role ?? 'student'),
                'school' => (string) ($user->school ?? ''),
                'phone' => (string) ($user->phone ?? ''),
                'area' => (string) ($user->area ?? ''),
                'ip_address' => (string) $request->ip(),
                'user_agent' => (string) ($request->userAgent() ?? ''),
                'status' => (string) ($user->status ?? 'allowed'),
            ]);
        }

        return redirect()->intended(route('dashboard', ['portal' => $this->portalForRole((string) ($user?->role ?? 'student'))], false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $guard = $this->resolveActiveGuard($request);

        if ($guard) {
            Auth::guard($guard)->logout();
        }

        $request->session()->forget('active_guard');

        if (! $this->hasAnyAuthenticatedGuard()) {
            $request->session()->invalidate();
        }

        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function resolveActiveGuard(Request $request): ?string
    {
        $portalGuard = match ($request->query('portal')) {
            'student' => 'student',
            'teacher' => 'teacher',
            'teacher-admin' => 'teacher_admin',
            'admin', 'super-admin' => 'admin',
            default => null,
        };

        if ($portalGuard && Auth::guard($portalGuard)->check()) {
            return $portalGuard;
        }

        $sessionGuard = (string) $request->session()->get('active_guard', '');
        if ($sessionGuard !== '' && Auth::guard($sessionGuard)->check()) {
            return $sessionGuard;
        }

        foreach (['admin', 'teacher_admin', 'teacher', 'student', 'web'] as $guard) {
            if (Auth::guard($guard)->check()) {
                return $guard;
            }
        }

        return null;
    }

    private function hasAnyAuthenticatedGuard(): bool
    {
        foreach (['admin', 'teacher_admin', 'teacher', 'student', 'web'] as $guard) {
            if (Auth::guard($guard)->check()) {
                return true;
            }
        }

        return false;
    }

    private function portalForRole(string $role): string
    {
        return match ($role) {
            'teacher' => 'teacher',
            'teacher_admin' => 'teacher-admin',
            'admin', 'super_admin' => 'admin',
            default => 'student',
        };
    }
}
