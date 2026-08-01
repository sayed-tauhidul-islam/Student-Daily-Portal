<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Notice;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentAssignment;
use App\Models\StudentExamResult;
use App\Models\StudentProgress;
use App\Models\Teacher;
use App\Models\TeacherPost;
use App\Models\PaymentConfirmation;
use App\Support\TeacherMatcher;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class StudentDashboardController extends Controller
{
    public function index(): View
    {
        $profile = Student::where('user_id', Auth::id())->first();
        $teachers = Teacher::query()->get();

        $profileFields = ['class', 'school', 'subject', 'subjects', 'area', 'bio', 'phone'];

        if ($this->classUsesGroup((string) ($profile?->class ?? ''))) {
            $profileFields[] = 'group';
        }
        $filledFields = 0;

        if ($profile) {
            foreach ($profileFields as $field) {
                if ($field === 'subjects' && ! empty($profile->subjects)) {
                    $filledFields++;
                    continue;
                }

                if (! empty($profile->{$field})) {
                    $filledFields++;
                }
            }
        }

        $profileCompleteness = $profile ? (int) round(($filledFields / count($profileFields)) * 100) : 0;
        $missingFields = $profile
            ? array_values(array_filter($profileFields, fn ($field) => $field === 'subjects' ? empty($profile->subjects ?? []) : empty($profile->{$field})))
            : $profileFields;

        $selectedSubjects = TeacherMatcher::studentSubjects($profile)->all();
        $schoolRecord = $profile && $profile->school
            ? School::query()->where('name', $profile->school)->first()
            : null;
        $schoolRating = $schoolRecord->rating ?? null;
        $schoolTeachers = TeacherMatcher::schoolTeachers($profile, $teachers);
        $teacherCount = $schoolTeachers->count();
        $allTeacherMatches = TeacherMatcher::forStudent($profile, $teachers);
        $teacherMatches = $allTeacherMatches->take(4)->values();
        $teacherMatchCount = $allTeacherMatches->count();

        $posts = TeacherPost::query()->orderBy('created_at', 'desc')->take(8)->get();
        $postAuthors = Teacher::query()
            ->whereIn('user_id', $posts->pluck('user_id')->filter()->unique()->values()->all())
            ->get()
            ->mapWithKeys(fn ($teacher) => [(string) $teacher->user_id => $teacher]);
        $currentMonth = now()->format('Y-m');
        $tuitionPayment = PaymentConfirmation::query()
            ->where('user_id', (string) Auth::id())
            ->where('type', 'tuition_fee')
            ->where('month', $currentMonth)
            ->first();
        $tuitionCleared = ! empty($tuitionPayment?->confirmed_at);

        $studentInstitute = trim((string) ($profile?->school ?? Auth::user()?->school ?? ''));
        $attendanceRecords = Attendance::query()
            ->where('student_user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->take(30)
            ->get()
            ->filter(fn ($record) => $studentInstitute === '' || $this->institutesMatch((string) ($record->institute ?? ''), $studentInstitute))
            ->values();
        $attendanceStats = [
            'present' => $attendanceRecords->where('status', 'present')->count(),
            'late' => $attendanceRecords->where('status', 'late')->count(),
            'absent' => $attendanceRecords->where('status', 'absent')->count(),
            'total' => $attendanceRecords->count(),
        ];

        $assignments = StudentAssignment::query()
            ->where('user_id', Auth::id())
            ->orderBy('deadline')
            ->take(5)
            ->get();
        $upcomingAssignments = $assignments
            ->filter(fn ($assignment) => ($assignment->status ?? 'pending') !== 'submitted')
            ->values();

        $examResults = StudentExamResult::query()
            ->where('student_user_id', Auth::id())
            ->orderBy('exam_date')
            ->get();
        $upcomingExams = $examResults
            ->filter(fn ($exam) => $exam->exam_date && Carbon::parse($exam->exam_date)->isFuture())
            ->take(5)
            ->values();
        $examAverage = $examResults->count() > 0
            ? round($examResults->avg(fn ($exam) => ((float) $exam->marks / max((float) $exam->out_of, 1)) * 100), 1)
            : null;

        $progress = StudentProgress::query()->firstWhere('student_user_id', Auth::id());
        $dashboardProgressBreakdown = collect([
            ['label' => 'Attendance', 'score' => $progress?->attendance_score, 'color' => '#14b8a6'],
            ['label' => 'Reading', 'score' => $progress?->reading_score, 'color' => '#3b82f6'],
            ['label' => 'Writing', 'score' => $progress?->writing_score, 'color' => '#8b5cf6'],
            ['label' => 'Assignments', 'score' => $progress?->assignment_score, 'color' => '#f59e0b'],
            ['label' => 'Behaviour', 'score' => $progress?->behavior_score, 'color' => '#10b981'],
            ['label' => 'Exams', 'score' => $examAverage, 'color' => '#ef4444'],
        ])->filter(fn ($item) => $item['score'] !== null && $item['score'] !== '')
            ->map(fn ($item) => $item + ['score' => round((float) $item['score'], 1)])
            ->values();
        $dashboardProgressScore = $dashboardProgressBreakdown->isNotEmpty()
            ? round($dashboardProgressBreakdown->avg('score'), 1)
            : (float) ($progress?->overall_score ?? 0);
        $dashboardProgressGradient = 'rgba(148,163,184,0.28) 0 100%';
        if ($dashboardProgressBreakdown->isNotEmpty()) {
            $totalScore = max((float) $dashboardProgressBreakdown->sum('score'), 1);
            $cursor = 0.0;
            $segments = [];

            foreach ($dashboardProgressBreakdown as $item) {
                $slice = ((float) $item['score'] / $totalScore) * 100;
                $end = $cursor + $slice;
                $segments[] = sprintf('%s %.2f%% %.2f%%', $item['color'], $cursor, $end);
                $cursor = $end;
            }

            $dashboardProgressGradient = implode(', ', $segments);
        }

        $schoolNotices = Notice::query()
            ->orderBy('published_at', 'desc')
            ->take(20)
            ->get()
            ->filter(fn ($notice) => $studentInstitute === '' || $this->institutesMatch((string) ($notice->institute ?? ''), $studentInstitute))
            ->values();
        $eventNotices = $schoolNotices
            ->filter(fn ($notice) => str_contains(strtolower((string) $notice->title), 'event') || str_contains(strtolower((string) $notice->title), 'program'))
            ->take(3)
            ->values();
        $holidayNotices = $schoolNotices
            ->filter(fn ($notice) => str_contains(strtolower((string) $notice->title), 'holiday') || str_contains(strtolower((string) $notice->title), 'holyday') || str_contains(strtolower((string) $notice->title), 'vacation'))
            ->take(3)
            ->values();

        return view('dashboard', compact(
            'profile',
            'profileCompleteness',
            'missingFields',
            'teacherCount',
            'teacherMatches',
            'teacherMatchCount',
            'selectedSubjects',
            'schoolRecord',
            'schoolRating',
            'posts',
            'postAuthors',
            'tuitionCleared',
            'currentMonth',
            'attendanceStats',
            'upcomingAssignments',
            'upcomingExams',
            'examAverage',
            'progress',
            'dashboardProgressBreakdown',
            'dashboardProgressScore',
            'dashboardProgressGradient',
            'schoolNotices',
            'eventNotices',
            'holidayNotices'
        ));
    }

    private function institutesMatch(string $left, string $right): bool
    {
        $a = $this->normalizeInstitute($left);
        $b = $this->normalizeInstitute($right);

        if ($a === '' || $b === '') {
            return false;
        }

        return $a === $b;
    }

    private function classUsesGroup(string $class): bool
    {
        preg_match('/\d+/', $class, $matches);
        $number = isset($matches[0]) ? (int) $matches[0] : null;

        return $number === null || $number < 1 || $number > 8;
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
