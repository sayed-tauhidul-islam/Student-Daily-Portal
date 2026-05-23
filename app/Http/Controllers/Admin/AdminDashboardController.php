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
        return view('admin.dashboard', [
            'users' => User::query()->count(),
            'students' => Student::query()->count(),
            'teachers' => Teacher::query()->count(),
            'schools' => School::query()->count(),
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
