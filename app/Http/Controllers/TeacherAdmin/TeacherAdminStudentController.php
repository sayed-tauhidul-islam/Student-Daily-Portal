<?php

namespace App\Http\Controllers\TeacherAdmin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TeacherAdminStudentController extends Controller
{
    public function index(Request $request): View
    {
        $school = $this->schoolName();
        $search = trim((string) $request->query('q', ''));

        $students = Student::query()->get()->filter(function (Student $student) use ($school, $search) {
            if (! $this->belongsToSchool((string) ($student->school ?? ''), $school)) {
                return false;
            }

            if ($search === '') {
                return true;
            }

            $user = User::find($student->user_id);
            $needle = strtolower($search);

            return str_contains(strtolower((string) ($user?->name ?? '')), $needle)
                || str_contains(strtolower((string) ($user?->email ?? '')), $needle)
                || str_contains(strtolower((string) ($student->class ?? '')), $needle)
                || str_contains(strtolower((string) ($student->group ?? '')), $needle)
                || str_contains(strtolower((string) ($student->area ?? '')), $needle)
                || str_contains(strtolower((string) ($student->subject ?? '')), $needle);
        })->sortByDesc('created_at')->values();

        $students = $this->paginateCollection($students, $request, 8);

        return view('teacher_admin.students.index', compact('students', 'search', 'school'));
    }

    public function edit(Student $student): View
    {
        $this->ensureSchoolAccess($student->school ?? null);

        return view('teacher_admin.students.form', [
            'student' => $student,
            'user' => User::find($student->user_id),
            'school' => $this->schoolName(),
            'action' => route('teacher-admin.students.update', $student),
            'method' => 'PUT',
        ]);
    }

    public function create(): View
    {
        return view('teacher_admin.students.form', [
            'student' => new Student(),
            'user' => null,
            'school' => $this->schoolName(),
            'action' => route('teacher-admin.students.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'class' => ['required', 'string', 'max:50'],
            'group' => ['nullable', 'string', 'max:100'],
            'subject' => ['nullable', 'string', 'max:1000'],
            'preferred_teacher' => ['nullable', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string', 'max:2000'],
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
            'role' => 'student',
            'school' => $school,
            'phone' => $data['phone'] ?? null,
            'area' => $data['area'],
        ]);

        Student::query()->create([
            'user_id' => $user->getKey(),
            'class' => $data['class'],
            'group' => $data['group'] ?? null,
            'school' => $school,
            'subject' => $data['subject'] ?? null,
            'subjects' => array_values(array_filter(array_map('trim', explode(',', (string) ($data['subject'] ?? ''))))),
            'preferred_teacher' => $data['preferred_teacher'] ?? null,
            'area' => $data['area'],
            'phone' => $data['phone'] ?? null,
            'bio' => $data['bio'] ?? null,
        ]);

        return redirect()->route('teacher-admin.students.index')->with('success', 'Student added successfully.');
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $this->ensureSchoolAccess($student->school ?? null);

        $user = User::find($student->user_id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
            'image' => ['nullable', 'image', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'class' => ['required', 'string', 'max:50'],
            'group' => ['nullable', 'string', 'max:100'],
            'school' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:1000'],
            'preferred_teacher' => ['nullable', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ]);

        $imagePath = $user?->image;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('student-images', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        }

        if ($user) {
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->school = $data['school'] ?? $student->school ?? $user->school;
            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            $user->image = $imagePath;
            $user->phone = $data['phone'] ?? null;
            $user->area = $data['area'];
            $user->save();
        }

        $student->update([
            'class' => $data['class'],
            'group' => $data['group'] ?? null,
            'school' => $data['school'] ?? $student->school,
            'subject' => $data['subject'] ?? null,
            'subjects' => array_values(array_filter(array_map('trim', explode(',', (string) ($data['subject'] ?? ''))))),
            'preferred_teacher' => $data['preferred_teacher'] ?? null,
            'area' => $data['area'],
            'phone' => $data['phone'] ?? null,
            'bio' => $data['bio'] ?? null,
        ]);

        return redirect()->route('teacher-admin.students.index')->with('success', 'Student updated.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $this->ensureSchoolAccess($student->school ?? null);

        $user = User::find($student->user_id);
        if ($user?->image) {
            Storage::disk('public')->delete($user->image);
        }

        $student->delete();
        $user?->delete();

        return back()->with('success', 'Student deleted.');
    }

    private function schoolName(): string
    {
        return trim((string) (Auth::user()?->school ?? ''));
    }

    private function ensureSchoolAccess(?string $school): void
    {
        if (! $this->belongsToSchool((string) $school, $this->schoolName())) {
            abort(403, 'This student does not belong to your school.');
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
