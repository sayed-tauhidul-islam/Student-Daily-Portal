<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Support\TeacherMatcher;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentInstituteTeacherController extends Controller
{
    public function index(): View
    {
        $student = Student::query()->firstWhere('user_id', Auth::id());
        $institute = trim((string) ($student?->school ?? ''));

        if ($institute === '') {
            return view('student.institute-teachers.index', [
                'student' => $student,
                'institute' => null,
                'teachers' => collect(),
                'matchSummary' => 'Complete your profile and select your school, area, and subjects to see matched teachers.',
            ]);
        }

        $allTeachers = Teacher::query()->get();
        $teachers = TeacherMatcher::forStudent($student, $allTeachers);

        if ($teachers->isEmpty()) {
            $teachers = TeacherMatcher::schoolTeachers($student, $allTeachers);
        }

        $userIds = $teachers->pluck('user_id')->filter()->unique()->values();
        $users = User::query()->whereIn('_id', $userIds->all())->get(['_id', 'email', 'phone']);
        $userMap = $users->mapWithKeys(fn ($user) => [(string) $user->getKey() => $user]);

        $teachers = $teachers->map(function ($teacher) use ($userMap) {
            $user = $userMap[(string) ($teacher->user_id ?? '')] ?? null;
            $teacher->contact_email = $user?->email;
            $teacher->contact_phone = $user?->phone;

            return $teacher;
        });

        return view('student.institute-teachers.index', [
            'student' => $student,
            'institute' => $institute,
            'teachers' => $teachers,
            'matchSummary' => $this->matchSummary($student),
        ]);
    }

    private function matchSummary(?Student $student): string
    {
        $parts = array_filter([
            $student?->school ? 'school: '.$student->school : null,
            $student?->area ? 'area: '.$student->area : null,
            TeacherMatcher::studentSubjects($student)->isNotEmpty()
                ? 'subjects: '.TeacherMatcher::studentSubjects($student)->implode(', ')
                : null,
        ]);

        return $parts
            ? 'Matched by '.implode(' | ', $parts).'.'
            : 'Add school, area, and subjects to improve teacher matches.';
    }
}
