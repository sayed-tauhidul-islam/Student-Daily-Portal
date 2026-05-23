<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\StudentRequest;
use App\Models\Teacher;
use App\Models\TeacherPost;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeacherDashboardController extends Controller
{
    public function index(): View
    {
        $profile = Teacher::where('user_id', Auth::id())->first();
        $matchedRequests = StudentRequest::query()->get()->filter(function ($request) use ($profile) {
            if (! $profile) {
                return false;
            }

            $areaMatch = empty($profile->area) || str_contains(strtolower((string) ($request->area ?? '')), strtolower((string) $profile->area));
            $subjectMatch = empty($profile->subject) || str_contains(strtolower((string) ($request->subject ?? '')), strtolower((string) $profile->subject));

            return $areaMatch && $subjectMatch;
        })->sortByDesc('created_at')->values();

        $teacherRatings = Rating::query()->where('target_id', (string) Auth::id());
        $averageRating = (float) $teacherRatings->avg('rating');
        $studentGroups = $matchedRequests->groupBy(function ($request) {
            return $request->user_id ?: $request->student_name ?: $request->title;
        })->values();

        $topStudents = $studentGroups->map(function ($group) {
            $first = $group->first();

            return (object) [
                'name' => $first->student_name ?: ($first->title ?: 'Student'),
                'area' => $first->area ?: 'Area not set',
                'subject' => $first->subject ?: 'Subject not set',
                'requests' => $group->count(),
                'budget' => (int) $group->sum(fn ($item) => (int) ($item->budget ?? 0)),
            ];
        })->sortByDesc('budget')->take(4)->values();

        $studentCount = $studentGroups->count();
        $estimatedEarnings = (int) $matchedRequests->sum(fn ($request) => (int) ($request->budget ?? 0));

        return view('teacher.dashboard', [
            'profile' => $profile,
            'requestCount' => StudentRequest::query()->where('status', 'pending')->count(),
            'featuredRequests' => $matchedRequests->take(5),
            'postCount' => TeacherPost::query()->where('user_id', Auth::id())->count(),
            'ratingCount' => Rating::query()->where('target_id', (string) Auth::id())->count(),
            'averageRating' => $averageRating,
            'matchCount' => $matchedRequests->count(),
            'studentCount' => $studentCount,
            'estimatedEarnings' => $estimatedEarnings,
            'topStudents' => $topStudents,
        ]);
    }
}
