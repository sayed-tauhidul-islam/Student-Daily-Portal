<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Support\Lists\ClassList;
use App\Support\Lists\GroupList;
use App\Support\Lists\SchoolList;
use App\Support\Lists\SubjectList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentProfileController extends Controller
{
    public function create()
    {
        $profile = Student::where('user_id', Auth::id())->first();
        $schools = SchoolList::sortedByRating();
        $subjects = SubjectList::allNames()->map(fn ($name) => (object) ['name' => $name]);
        $teachers = Teacher::query()
            ->orderBy('name')
            ->get(['name', 'institution', 'subject', 'area'])
            ->map(function ($teacher) {
                $institution = trim((string) ($teacher->institution ?? ''));

                return [
                    'name' => (string) $teacher->name,
                    'institution' => $institution,
                    'subject' => (string) ($teacher->subject ?? ''),
                    'area' => (string) ($teacher->area ?? ''),
                    'value' => $institution !== ''
                        ? ((string) $teacher->name) . ' (' . $institution . ')'
                        : (string) $teacher->name,
                ];
            })
            ->unique('value')
            ->values();
        $areas = SchoolList::areas();
        $groups = GroupList::all();
        $classes = ClassList::all();

        return view('student.profile-create', compact('profile', 'schools', 'subjects', 'teachers', 'areas', 'groups', 'classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class' => ['required', 'string', 'max:50'],
            'group' => ['nullable', 'string', 'max:100'],
            'school' => ['required', 'string', 'max:255'],
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*' => ['required', 'string', 'max:255'],
            'preferred_teacher' => ['nullable', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $user = Auth::user();
        $imagePath = $user?->image;

        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $request->file('image')->store('student-images', 'public');
        }

        if ($user) {
            $user->image = $imagePath;
            $user->school = $data['school'];
            $user->phone = $data['phone'] ?? null;
            $user->area = $data['area'];
            $user->save();
        }

        Student::updateOrCreate([
            'user_id' => Auth::id(),
        ], [
            'class' => $data['class'],
            'group' => $this->classUsesGroup((string) $data['class']) ? ($data['group'] ?? null) : null,
            'school' => $data['school'],
            'subject' => implode(', ', $data['subjects']),
            'subjects' => array_values($data['subjects']),
            'preferred_teacher' => $this->teacherBelongsToSchool((string) ($data['preferred_teacher'] ?? ''), (string) $data['school'])
                ? $data['preferred_teacher']
                : null,
            'area' => $data['area'],
            'phone' => $data['phone'] ?? null,
            'bio' => $data['bio'] ?? null,
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Profile saved successfully!');
    }

    private function teacherBelongsToSchool(string $teacherValue, string $school): bool
    {
        $teacherValue = trim($teacherValue);
        $school = trim($school);

        if ($teacherValue === '') {
            return true;
        }

        if ($school === '') {
            return false;
        }

        return Teacher::query()
            ->where('name', $this->teacherNameFromValue($teacherValue))
            ->get()
            ->contains(fn (Teacher $teacher) => $this->sameInstitute((string) ($teacher->institution ?? ''), $school));
    }

    private function classUsesGroup(string $class): bool
    {
        preg_match('/\d+/', $class, $matches);
        $number = isset($matches[0]) ? (int) $matches[0] : null;

        return $number === null || $number < 1 || $number > 8;
    }

    private function teacherNameFromValue(string $teacherValue): string
    {
        return trim((string) preg_replace('/\s+\([^)]*\)$/', '', $teacherValue));
    }

    private function sameInstitute(string $left, string $right): bool
    {
        $left = $this->normalize($left);
        $right = $this->normalize($right);

        return $left !== '' && $right !== '' && $left === $right;
    }

    private function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace('&', ' and ', $value);
        $value = preg_replace('/[^a-z0-9\s]/', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value);
    }
}
