<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $guard = $this->resolveActiveGuard($request);
        $user = $guard ? Auth::guard($guard)->user() : null;

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $guard = $this->resolveActiveGuard($request);
        $user = $guard ? Auth::guard($guard)->user() : null;

        if (! $user) {
            abort(401);
        }

        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'area' => $validated['area'] ?? null,
        ]);

        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            $user->image = $request->file('image')->store('profile-images', 'public');
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $portal = $this->portalForGuard($guard);

        return Redirect::route('profile.edit', $portal ? ['portal' => $portal] : [])
            ->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $guard = $this->resolveActiveGuard($request);

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password:'.($guard ?? 'web')],
        ]);

        $user = $guard ? Auth::guard($guard)->user() : null;

        if (! $user) {
            abort(401);
        }

        Auth::guard($guard)->logout();
        $request->session()->forget('active_guard');

        $user->delete();

        if (! $this->hasAnyAuthenticatedGuard()) {
            $request->session()->invalidate();
        }

        $request->session()->regenerateToken();

        return Redirect::to('/');
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
            Auth::shouldUse($portalGuard);

            return $portalGuard;
        }

        $sessionGuard = (string) $request->session()->get('active_guard', '');
        if ($sessionGuard !== '' && Auth::guard($sessionGuard)->check()) {
            Auth::shouldUse($sessionGuard);

            return $sessionGuard;
        }

        foreach (['admin', 'teacher_admin', 'teacher', 'student', 'web'] as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::shouldUse($guard);

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

    private function portalForGuard(?string $guard): ?string
    {
        return match ($guard) {
            'teacher' => 'teacher',
            'teacher_admin' => 'teacher-admin',
            'admin' => 'admin',
            'student' => 'student',
            default => null,
        };
    }
}
