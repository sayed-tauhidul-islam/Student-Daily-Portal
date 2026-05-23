<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminTeacherController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $teachers = Teacher::query()->get()->filter(function ($teacher) use ($search) {
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
        })->sortBy(function ($teacher) use ($search) {
            $user = User::find($teacher->user_id);

            return strtolower((string) ($user?->name ?? $teacher->name ?? ''));
        })->values();

        return view('admin.teachers.index', compact('teachers', 'search'));
    }

    public function create(Request $request): View
    {
        return view('admin.teachers.form', [
            'teacher' => null,
            'user' => null,
            'subjects' => Subject::query()->orderBy('name')->get(),
            'institution' => $request->query('institution'),
            'action' => route('admin.teachers.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'image' => ['nullable', 'image', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'qualification' => ['required', 'string', 'max:255'],
            'experience' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
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

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('teacher-images', 'public');
        } elseif ($request->boolean('remove_image')) {
            $imagePath = null;
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'teacher',
            'image' => $imagePath,
            'phone' => null,
            'area' => $data['area'],
        ]);

        Teacher::create([
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
            'image' => $imagePath,
            'availability' => $data['availability'] ?? null,
            'institution' => $data['institution'] ?? null,
            'gender' => $data['gender'] ?? null,
            'online' => $request->boolean('online'),
            'class_level' => $data['class_level'] ?? null,
            'verification_status' => $data['verification_status'] ?? 'pending',
        ]);

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher created.');
    }

    public function edit(Teacher $teacher): View
    {
        $user = User::find($teacher->user_id);

        return view('admin.teachers.form', [
            'teacher' => $teacher,
            'user' => $user,
            'subjects' => Subject::query()->orderBy('name')->get(),
            'action' => route('admin.teachers.update', $teacher),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $user = User::find($teacher->user_id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
            'image' => ['nullable', 'image', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'qualification' => ['required', 'string', 'max:255'],
            'experience' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
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

        if ($user) {
            $user->name = $data['name'];
            $user->email = $data['email'];
            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            $user->image = $imagePath;
            $user->area = $data['area'];
            $user->save();
        }

        $teacher->update([
            'name' => $data['name'],
            'qualification' => $data['qualification'],
            'experience' => $data['experience'] ?? null,
            'subject' => $data['subject'],
            'subjects' => array_values(array_filter(array_map('trim', explode(',', (string) ($data['subjects_text'] ?? $data['subject']))))),
            'salary' => $data['salary'] ?? 0,
            'area' => $data['area'],
            'bio' => $data['bio'] ?? null,
            'rating' => $data['rating'] ?? 0,
            'image' => $imagePath,
            'availability' => $data['availability'] ?? null,
            'institution' => $data['institution'] ?? null,
            'gender' => $data['gender'] ?? null,
            'online' => $request->boolean('online'),
            'class_level' => $data['class_level'] ?? null,
            'verification_status' => $data['verification_status'] ?? 'pending',
        ]);

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher updated.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $user = User::find($teacher->user_id);
        if ($user?->image) {
            Storage::disk('public')->delete($user->image);
        }
        $teacher->delete();
        $user?->delete();

        return back()->with('success', 'Teacher deleted.');
    }
}
