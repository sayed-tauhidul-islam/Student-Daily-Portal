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
use Illuminate\Validation\Rule;

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
        $schoolNames = SchoolList::names()->all();
        $subjectNames = SubjectList::allNames()->all();
        $teacherNames = Teacher::query()
            ->get(['name', 'institution'])
            ->map(function ($teacher) {
                $institution = trim((string) ($teacher->institution ?? ''));

                return $institution !== ''
                    ? ((string) $teacher->name) . ' (' . $institution . ')'
                    : (string) $teacher->name;
            })
            ->unique()
            ->values()
            ->all();

        $request->validate([
            'class' => ['required', 'string', 'max:50'],
            'group' => ['nullable', 'string', 'max:100'],
            'school' => ['required', 'string', 'max:255', Rule::in($schoolNames)],
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*' => ['required', 'string', 'max:255', Rule::in($subjectNames)],
            'preferred_teacher' => ['nullable', 'string', 'max:255', Rule::in($teacherNames)],
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
            if ($user) {
                $user->image = $imagePath;
                $user->save();
            }
        }

        Student::updateOrCreate([
            'user_id' => Auth::id(),
        ], [
            'class' => $request->class,
            'group' => $request->group,
            'school' => $request->school,
            'subject' => implode(', ', $request->subjects),
            'subjects' => array_values($request->subjects),
            'preferred_teacher' => $request->preferred_teacher,
            'area' => $request->area,
            'phone' => $request->phone,
            'bio' => $request->bio,
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Profile saved successfully!');
    }
}