<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
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
            ]);
        }

        $teachers = Teacher::query()->get()
            ->filter(function ($teacher) use ($institute) {
                return $this->normalizeInstitute((string) ($teacher->institution ?? '')) === $this->normalizeInstitute($institute);
            })
            ->sortBy(function ($teacher) {
                return strtolower((string) ($teacher->name ?? ''));
            })
            ->values();

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
        ]);
    }

    private function normalizeInstitute(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace(['&'], ' and ', $value);
        $value = preg_replace('/[^a-z0-9\s]/', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value);
    }
}
