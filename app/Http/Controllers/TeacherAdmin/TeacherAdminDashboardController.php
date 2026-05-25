<?php

namespace App\Http\Controllers\TeacherAdmin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeacherAdminDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $school = trim((string) ($user?->school ?? ''));

        $students = Student::query()->get()->filter(function (Student $student) use ($school) {
            return $this->belongsToSchool((string) ($student->school ?? ''), $school);
        })->values();

        $teachers = Teacher::query()->get()->filter(function (Teacher $teacher) use ($school) {
            return $this->belongsToSchool((string) ($teacher->institution ?? ''), $school);
        })->values();

        return view('teacher_admin.dashboard', [
            'school' => $school,
            'studentCount' => $students->count(),
            'teacherCount' => $teachers->count(),
            'pendingStudentEdits' => $students->count(),
            'pendingTeacherEdits' => $teachers->count(),
        ]);
    }

    public function database(): View
    {
        $school = trim((string) (Auth::user()?->school ?? ''));

        $students = Student::query()->get()->filter(function (Student $student) use ($school) {
            return $this->belongsToSchool((string) ($student->school ?? ''), $school);
        })->values();

        $teachers = Teacher::query()->get()->filter(function (Teacher $teacher) use ($school) {
            return $this->belongsToSchool((string) ($teacher->institution ?? ''), $school);
        })->values();

        $userIds = $students->pluck('user_id')
            ->merge($teachers->pluck('user_id'))
            ->filter()
            ->unique()
            ->values();
        $users = User::query()->whereIn('_id', $userIds->all())->get();
        $usersById = $users->mapWithKeys(fn ($user) => [(string) $user->getKey() => $user]);

        return view('teacher_admin.database.index', [
            'school' => $school,
            'students' => $students,
            'teachers' => $teachers,
            'usersById' => $usersById,
        ]);
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
}
