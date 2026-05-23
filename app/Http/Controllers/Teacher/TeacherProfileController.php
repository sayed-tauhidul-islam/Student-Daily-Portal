<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Subject;
use App\Models\Teacher;
use App\Support\Lists\ClassList;
use App\Support\Lists\GroupList;
use App\Support\Lists\SchoolList;
use App\Support\Lists\SubjectList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TeacherProfileController extends Controller
{
    public function create(): View
    {
        $profile = Teacher::where('user_id', Auth::id())->first();
        $subjects = SubjectList::allNames();
        $schools = SchoolList::names();
        $areas = SchoolList::areas();
        $classes = ClassList::all();
        $groups = GroupList::all();
        $teacher = Teacher::where('user_id', Auth::id())->first();

        return view('teacher.profile', compact('profile', 'teacher', 'subjects', 'schools', 'areas', 'classes', 'groups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'qualification' => ['required', 'string', 'max:255'],
            'experience' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*' => ['required', 'string', 'max:255'],
            'salary' => ['required', 'integer', 'min:0'],
            'area' => ['required', 'string', 'max:255'],
            'availability' => ['nullable', 'string', 'max:255'],
            'institution' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:50'],
            'online' => ['nullable', 'boolean'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'class_level' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $teacher = Teacher::where('user_id', Auth::id())->first();
        $currentImage = $teacher?->image;

        if ($request->hasFile('image')) {
            if ($currentImage) {
                Storage::disk('public')->delete($currentImage);
            }

            $currentImage = $request->file('image')->store('teacher-images', 'public');
        }

        Teacher::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'name' => $request->name,
                'qualification' => $request->qualification,
                'experience' => $request->experience,
                'subject' => $request->subject,
                'subjects' => array_values($request->subjects),
                'salary' => (int) $request->salary,
                'area' => $request->area,
                'availability' => $request->availability,
                'institution' => $request->institution,
                'gender' => $request->gender,
                'online' => $request->boolean('online'),
                'bio' => $request->bio,
                'class_level' => $request->class_level,
                'verification_status' => 'pending',
                'rating' => $request->input('rating', 0),
                'image' => $currentImage,
            ]
        );

        if ($currentImage && Auth::user()) {
            Auth::user()->image = $currentImage;
            Auth::user()->save();
        }

        return redirect()->route('teacher.profile.create')->with('success', 'Teacher profile saved successfully.');
    }

    // AJAX avatar upload
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'max:2048'],
        ]);

        $teacher = Teacher::where('user_id', Auth::id())->first();
        if (! $teacher) {
            $teacher = new Teacher();
            $teacher->user_id = Auth::id();
        }

        // delete previous
        if ($teacher->image) {
            Storage::disk('public')->delete($teacher->image);
        }

        $path = $request->file('image')->store('teacher-images', 'public');
        $teacher->image = $path;
        $teacher->save();

        if (Auth::user()) {
            Auth::user()->image = $path;
            Auth::user()->save();
        }

        return response()->json(['url' => Storage::disk('public')->url($path)]);
    }
}
