<?php

namespace App\Http\Controllers;

use App\Models\ParentPortalAccess;
use App\Models\Student;
use App\Models\StudentExamResult;
use App\Models\StudentProgress;
use App\Models\User;
use Illuminate\View\View;

class ParentPortalController extends Controller
{
    public function show(string $code): View
    {
        $access = ParentPortalAccess::query()->firstWhere('access_code', $code);
        abort_if(! $access || ! $access->is_active || ! $access->expires_at || $access->expires_at->isPast(), 404);

        $studentUser = User::query()->find($access->student_user_id);
        $student = Student::query()->firstWhere('user_id', $access->student_user_id);
        $progress = StudentProgress::query()->firstWhere('student_user_id', $access->student_user_id);
        $results = StudentExamResult::query()
            ->where('student_user_id', $access->student_user_id)
            ->orderBy('exam_date', 'desc')
            ->get();

        $overallAvg = $results->count() > 0
            ? round($results->avg(fn ($r) => ((float) $r->marks / max((float) $r->out_of, 1)) * 100), 1)
            : 0;

        return view('parent.portal', compact('access', 'studentUser', 'student', 'progress', 'results', 'overallAvg'));
    }
}
