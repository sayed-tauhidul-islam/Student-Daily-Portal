<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Models\Teacher;
use App\Support\Lists\GroupList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminStudentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $students = Student::query()->get()->filter(function ($student) use ($search) {
            $user = User::find($student->user_id);

            if (! $user || ($user->role ?? '') !== 'student') {
                return false;
            }

            if ($search === '') {
                return true;
            }

            $needle = strtolower($search);

            return str_contains(strtolower((string) ($user?->name ?? '')), $needle)
                || str_contains(strtolower((string) ($user?->email ?? '')), $needle)
                || str_contains(strtolower((string) ($student->class ?? '')), $needle)
                || str_contains(strtolower((string) ($student->group ?? '')), $needle)
                || str_contains(strtolower((string) ($student->school ?? '')), $needle)
                || str_contains(strtolower((string) ($student->area ?? '')), $needle)
                || str_contains(strtolower((string) ($student->subject ?? '')), $needle);
        })->sortByDesc('created_at')->values();

        $students = $this->paginateCollection($students, $request, 8);

        return view('admin.students.index', compact('students', 'search'));
    }

    public function create(): View
    {
        return view('admin.students.form', [
            'student' => null,
            'user' => null,
            'schools' => School::query()->orderBy('name')->get(),
            'subjects' => Subject::query()->orderBy('name')->get(),
            'teachers' => Teacher::query()->orderBy('name')->get(),
            'groups' => GroupList::all(),
            'action' => route('admin.students.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', 'min:6'],
            'image' => ['nullable', 'image', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'class' => ['required', 'string', 'max:50'],
            'group' => ['nullable', 'string', 'max:100'],
            'school' => ['nullable', 'string', 'max:255'],
            'subjects_text' => ['nullable', 'string', 'max:1000'],
            'preferred_teacher' => ['nullable', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('student-images', 'public');
        } elseif ($request->boolean('remove_image')) {
            $imagePath = null;
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'student',
            'image' => $imagePath,
            'phone' => $data['phone'] ?? null,
            'area' => $data['area'],
        ]);

        Student::create([
            'user_id' => $user->getKey(),
            'class' => $data['class'],
            'group' => $data['group'] ?? null,
            'school' => $data['school'] ?? null,
            'subject' => $data['subjects_text'] ?? null,
            'subjects' => array_values(array_filter(array_map('trim', explode(',', (string) ($data['subjects_text'] ?? ''))))),
            'preferred_teacher' => $data['preferred_teacher'] ?? null,
            'area' => $data['area'],
            'phone' => $data['phone'] ?? null,
            'bio' => $data['bio'] ?? null,
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Student created.');
    }

    public function edit(Student $student): View
    {
        $user = User::find($student->user_id);

        return view('admin.students.form', [
            'student' => $student,
            'user' => $user,
            'schools' => School::query()->orderBy('name')->get(),
            'subjects' => Subject::query()->orderBy('name')->get(),
            'teachers' => Teacher::query()->orderBy('name')->get(),
            'groups' => GroupList::all(),
            'action' => route('admin.students.update', $student),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $user = User::find($student->user_id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class)->ignore($user?->getKey())],
            'password' => ['nullable', 'string', 'min:6'],
            'image' => ['nullable', 'image', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'class' => ['required', 'string', 'max:50'],
            'group' => ['nullable', 'string', 'max:100'],
            'school' => ['nullable', 'string', 'max:255'],
            'subjects_text' => ['nullable', 'string', 'max:1000'],
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
            'school' => $data['school'] ?? null,
            'subject' => $data['subjects_text'] ?? null,
            'subjects' => array_values(array_filter(array_map('trim', explode(',', (string) ($data['subjects_text'] ?? ''))))),
            'preferred_teacher' => $data['preferred_teacher'] ?? null,
            'area' => $data['area'],
            'phone' => $data['phone'] ?? null,
            'bio' => $data['bio'] ?? null,
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Student updated.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $user = User::find($student->user_id);
        if ($user?->image) {
            Storage::disk('public')->delete($user->image);
        }
        $student->delete();
        $user?->delete();

        return back()->with('success', 'Student deleted.');
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
