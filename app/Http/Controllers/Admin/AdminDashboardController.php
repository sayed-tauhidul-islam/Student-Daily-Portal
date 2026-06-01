<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Group;
use App\Models\Rating;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentRequest;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $schools = School::query()->orderBy('name')->get();
        $schoolNames = $schools->filter(fn ($school) => ! str_contains(strtolower((string) ($school->type ?? 'school')), 'college'));
        $collegeNames = $schools->filter(fn ($school) => str_contains(strtolower((string) ($school->type ?? '')), 'college'));

        $teacherIds = Teacher::query()->pluck('user_id')->filter()->unique()->values();
        $headTeacherIds = User::query()
            ->whereIn('_id', $teacherIds->all())
            ->where('role', 'teacher_admin')
            ->pluck('_id')
            ->map(fn ($id) => (string) $id)
            ->values();

        return view('admin.dashboard', [
            'users' => User::query()->count(),
            'students' => Student::query()->count(),
            'teachers' => Teacher::query()->count(),
            'schools' => $schools->count(),
            'schoolNames' => $schoolNames,
            'collegeNames' => $collegeNames,
            'headTeachersCount' => $headTeacherIds->count(),
            'subjects' => Subject::query()->count(),
            'groups' => Group::query()->count(),
            'requests' => StudentRequest::query()->count(),
            'ratings' => Rating::query()->count(),
            'messages' => Message::query()->count(),
            'verifiedTeachers' => Teacher::query()->where('verification_status', 'verified')->count(),
            'pendingRequests' => StudentRequest::query()->where('status', 'pending')->count(),
        ]);
    }
}
