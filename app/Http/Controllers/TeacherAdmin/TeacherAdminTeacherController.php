<?php

namespace App\Http\Controllers\TeacherAdmin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TeacherAdminTeacherController extends Controller
{
    public function index(Request $request): View
    {
        $school = $this->schoolName();
        $search = trim((string) $request->query('q', ''));

        $teachers = Teacher::query()->get()->filter(function (Teacher $teacher) use ($school, $search) {
            if (! $this->belongsToSchool((string) ($teacher->institution ?? ''), $school)) {
                return false;
            }

            if ($search === '') {
                return true;
            }

            $user = User::find($teacher->user_id);
            $needle = strtolower($search);
            $teacherSubjects = collect($teacher->subjects ?? [$teacher->subject ?? '']);

            return str_contains(strtolower((string) ($user?->name ?? '')), $needle)
                || str_contains(strtolower((string) ($user?->email ?? '')), $needle)
                || str_contains(strtolower((string) ($teacher->name ?? '')), $needle)
                || str_contains(strtolower((string) ($teacher->qualification ?? '')), $needle)
                || str_contains(strtolower((string) ($teacher->experience ?? '')), $needle)
                || str_contains(strtolower((string) ($teacher->area ?? '')), $needle)
                || str_contains(strtolower((string) ($teacher->institution ?? '')), $needle)
                || $teacherSubjects->contains(fn ($subject) => str_contains(strtolower((string) $subject), $needle));
        })->sortBy(function (Teacher $teacher) {
            $user = User::find($teacher->user_id);

            return strtolower((string) ($user?->name ?? $teacher->name ?? ''));
        })->values();

        $teachers = $this->paginateCollection($teachers, $request, 8);

        return view('teacher_admin.teachers.index', compact('teachers', 'search', 'school'));
    }

    public function edit(Teacher $teacher): View
    {
        $this->ensureSchoolAccess($teacher->institution ?? null);

        return view('teacher_admin.teachers.form', [
            'teacher' => $teacher,
            'user' => User::find($teacher->user_id),
            'school' => $this->schoolName(),
            'action' => route('teacher-admin.teachers.update', $teacher),
            'method' => 'PUT',
        ]);
    }

    public function create(): View
    {
        return view('teacher_admin.teachers.form', [
            'teacher' => new Teacher(),
            'user' => null,
            'school' => $this->schoolName(),
            'action' => route('teacher-admin.teachers.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'qualification' => ['required', 'string', 'max:255'],
            'experience' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'subjects_text' => ['nullable', 'string', 'max:1000'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'area' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'availability' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:50'],
            'online' => ['nullable', 'boolean'],
            'class_level' => ['nullable', 'string', 'max:255'],
            'verification_status' => ['nullable', 'in:pending,verified,rejected'],
        ]);

        if (User::query()->where('email', $data['email'])->exists()) {
            return back()
                ->withErrors(['email' => 'This email is already registered.'])
                ->withInput();
        }

        $school = $this->schoolName();
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'teacher',
            'school' => $school,
            'area' => $data['area'],
        ]);

        Teacher::query()->create([
            'user_id' => $user->getKey(),
            'name' => $data['name'],
            'qualification' => $data['qualification'],
            'experience' => $data['experience'] ?? null,
            'subject' => $data['subject'],
            'subjects' => array_values(array_filter(array_map('trim', explode(',', (string) ($data['subjects_text'] ?? $data['subject']))))),
            'salary' => $data['salary'] ?? 0,
            'area' => $data['area'],
            'bio' => $data['bio'] ?? null,
            'rating' => $data['rating'] ?? 0,
            'availability' => $data['availability'] ?? null,
            'institution' => $school,
            'gender' => $data['gender'] ?? null,
            'online' => $request->boolean('online'),
            'class_level' => $data['class_level'] ?? null,
            'verification_status' => $data['verification_status'] ?? 'pending',
        ]);

        return redirect()->route('teacher-admin.teachers.index')->with('success', 'Teacher added successfully.');
    }

    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $this->ensureSchoolAccess($teacher->institution ?? null);

        $user = User::find($teacher->user_id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
            'image' => ['nullable', 'image', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'experience' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'subjects_text' => ['nullable', 'string', 'max:1000'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'area' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'availability' => ['nullable', 'string', 'max:255'],
            'institution' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:50'],
            'online' => ['nullable', 'boolean'],
            'class_level' => ['nullable', 'string', 'max:255'],
            'verification_status' => ['nullable', 'in:pending,verified,rejected'],
        ]);

        $imagePath = $user?->image;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('teacher-images', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        }

        if (! $user && ! empty($data['email'])) {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?: str()->random(16)),
                'role' => 'teacher',
                'school' => $teacher->institution ?? $this->schoolName(),
                'image' => $imagePath,
                'area' => $data['area'],
            ]);
            $teacher->user_id = $user->getKey();
        }

        if ($user) {
            $user->name = $data['name'];
            if (! empty($data['email'])) {
                $user->email = $data['email'];
            }
            $user->school = $data['institution'] ?? $teacher->institution ?? $user->school;
            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            $user->image = $imagePath;
            $user->area = $data['area'];
            $user->save();
        }

        $teacher->update([
            'name' => $data['name'],
            'qualification' => $data['qualification'] ?? null,
            'experience' => $data['experience'] ?? null,
            'subject' => $data['subject'] ?? null,
            'subjects' => array_values(array_filter(array_map('trim', explode(',', (string) ($data['subjects_text'] ?? $data['subject'] ?? ''))))),
            'salary' => $data['salary'] ?? 0,
            'area' => $data['area'],
            'bio' => $data['bio'] ?? null,
            'rating' => $data['rating'] ?? 0,
            'image' => $imagePath,
            'availability' => $data['availability'] ?? null,
            'institution' => $data['institution'] ?? $teacher->institution,
            'gender' => $data['gender'] ?? null,
            'online' => $request->boolean('online'),
            'class_level' => $data['class_level'] ?? null,
            'verification_status' => $data['verification_status'] ?? 'pending',
        ]);

        return redirect()->route('teacher-admin.teachers.index')->with('success', 'Teacher updated.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $this->ensureSchoolAccess($teacher->institution ?? null);

        $user = User::find($teacher->user_id);
        if ($user?->image) {
            Storage::disk('public')->delete($user->image);
        }

        $teacher->delete();
        $user?->delete();

        return back()->with('success', 'Teacher deleted.');
    }

    private function schoolName(): string
    {
        return trim((string) (Auth::user()?->school ?? ''));
    }

    private function ensureSchoolAccess(?string $school): void
    {
        if (! $this->belongsToSchool((string) $school, $this->schoolName())) {
            abort(403, 'This teacher does not belong to your school.');
        }
    }

    private function belongsToSchool(string $value, string $school): bool
    {
        $value = $this->normalize($value);
        $school = $this->normalize($school);

        if ($value === '' || $school === '') {
            return false;
        }

        return $value === $school || str_contains($value, $school) || str_contains($school, $value);
    }

    private function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace(['&'], ' and ', $value);
        $value = preg_replace('/[^a-z0-9\s]/', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value);
    }

    private function paginateCollection($items, Request $request, int $perPage = 8): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = $items->values();

        $paginator = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return $paginator->appends($request->query());
    }
}
