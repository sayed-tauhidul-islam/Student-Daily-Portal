<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BlockedIdentity;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    private const PORTAL_ALIASES = [
        'student' => 'student',
        'teacher' => 'teacher',
        'teacheradmin' => 'teacher-admin',
        'teacher-admin' => 'teacher-admin',
        'superadmin' => 'super-admin',
        'super-admin' => 'super-admin',
    ];

    /**
     * Display the registration view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $portal = $this->normalizePortal($request->query('portal'));

        if (in_array($portal, ['teacher-admin', 'super-admin'], true)) {
            return redirect()
                ->route('login', ['portal' => $portal])
                ->with('status', 'Registration is disabled for this portal.');
        }

        return view('auth.register');
    }

    private function normalizePortal(?string $portal): string
    {
        $portal = strtolower(trim((string) $portal));
        $portal = preg_replace('/[\s_]+/', '-', $portal) ?? $portal;
        $portal = preg_replace('/-+/', '-', $portal) ?? $portal;

        return self::PORTAL_ALIASES[$portal] ?? self::PORTAL_ALIASES[str_replace('-', '', $portal)] ?? 'student';
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:30'],
            'area' => ['nullable', 'string', 'max:255'],
            'school' => ['nullable', 'string', 'max:255'],
            'class' => ['nullable', 'string', 'max:50'],
            'group' => ['nullable', 'string', 'max:100'],
            'subjects_text' => ['nullable', 'string', 'max:1000'],
            'preferred_teacher' => ['nullable', 'string', 'max:255'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'experience' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'availability' => ['nullable', 'string', 'max:255'],
            'class_level' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ]);

        $role = (string) $request->input('role', 'student');
        if (! in_array($role, ['student', 'teacher'], true)) {
            throw ValidationException::withMessages([
                'role' => 'Only student and teacher registration is allowed from this page.',
            ]);
        }

        $blockedIdentity = BlockedIdentity::query()
            ->where('email', strtolower((string) $data['email']))
            ->where('school', (string) ($data['school'] ?? ''))
            ->first();

        if ($blockedIdentity) {
            throw ValidationException::withMessages([
                'email' => 'This email is blocked for the selected school/college.',
            ]);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $role,
            'school' => $data['school'] ?? null,
            'phone' => $data['phone'] ?? null,
            'area' => $data['area'] ?? null,
        ]);

        if ($role === 'student') {
            Student::create([
                'user_id' => $user->getKey(),
                'class' => $data['class'] ?? null,
                'group' => $data['group'] ?? null,
                'school' => $data['school'] ?? null,
                'subject' => $data['subjects_text'] ?? null,
                'subjects' => array_values(array_filter(array_map('trim', explode(',', (string) ($data['subjects_text'] ?? ''))))),
                'preferred_teacher' => $data['preferred_teacher'] ?? null,
                'area' => $data['area'] ?? null,
                'phone' => $data['phone'] ?? null,
                'bio' => $data['bio'] ?? null,
            ]);
        }

        if ($role === 'teacher') {
            Teacher::create([
                'user_id' => $user->getKey(),
                'name' => $data['name'],
                'qualification' => $data['qualification'],
                'experience' => $data['experience'] ?? null,
                'subject' => $data['subject'],
                'subjects' => array_values(array_filter(array_map('trim', explode(',', (string) ($data['subjects_text'] ?? $data['subject']))))),
                'salary' => 0,
                'area' => $data['area'],
                'bio' => $data['bio'] ?? null,
                'rating' => 0,
                'image' => null,
                'availability' => $data['availability'] ?? null,
                'institution' => $data['school'],
                'gender' => null,
                'online' => false,
                'class_level' => $data['class_level'] ?? null,
                'verification_status' => 'pending',
            ]);
        }

        event(new Registered($user));

        $guard = $role === 'teacher' ? 'teacher' : 'student';

        Auth::login($user);
        Auth::guard($guard)->login($user);
        Auth::shouldUse($guard);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
