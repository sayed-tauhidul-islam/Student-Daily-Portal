<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\ParentPortalAccess;
use App\Models\Student;
use App\Models\StudentAssignment;
use App\Models\StudentExamResult;
use App\Models\StudentGoal;
use App\Models\StudentProgress;
use App\Models\StudentTask;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentProgressHubController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $student = Student::query()->firstWhere('user_id', Auth::id());
        $school = (string) ($student?->school ?? $user?->school ?? '');
        $class = (string) ($student?->class ?? '');

        $tasks = StudentTask::query()
            ->where('user_id', Auth::id())
            ->orderBy('due_date')
            ->get();
        $assignments = StudentAssignment::query()
            ->where('user_id', Auth::id())
            ->orderBy('deadline')
            ->get();
        $goals = StudentGoal::query()
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        $progress = StudentProgress::query()->firstWhere('student_user_id', Auth::id());
        $examResults = StudentExamResult::query()
            ->where('student_user_id', Auth::id())
            ->orderBy('exam_date')
            ->get();
        $parentAccess = ParentPortalAccess::query()->firstWhere('student_user_id', Auth::id());

        $notices = Notice::query()
            ->orderBy('published_at', 'desc')
            ->take(8)
            ->get()
            ->filter(function (Notice $notice) use ($school) {
                if ($school === '') {
                    return true;
                }

                return str_contains(strtolower((string) ($notice->institute ?? '')), strtolower($school));
            })
            ->values();

        $teachers = Teacher::query()->get()->filter(function (Teacher $teacher) use ($school) {
            if ($school === '') {
                return false;
            }

            return str_contains(strtolower((string) ($teacher->institution ?? '')), strtolower($school));
        })->take(6)->values();

        $completedTasks = $tasks->where('is_completed', true)->count();
        $totalTasks = $tasks->count();
        $taskCompletionRate = $totalTasks > 0 ? (int) round(($completedTasks / $totalTasks) * 100) : 0;
        $overdueAssignments = $assignments->filter(fn ($item) => $item->status !== 'submitted' && $item->deadline && Carbon::parse($item->deadline)->isPast())->count();
        $goalAverage = $goals->count() > 0
            ? (int) round($goals->avg(function ($goal) {
                $target = max((float) ($goal->target ?? 0), 1);
                $current = max((float) ($goal->current ?? 0), 0);

                return min(100, ($current / $target) * 100);
            }))
            : 0;

        $streak = $this->buildStreak($tasks);
        $manualPerformance = collect($progress?->subjects ?? []);
        $bestSubject = $manualPerformance->sortByDesc('score')->first();
        $weakSubject = $manualPerformance->sortBy('score')->first();
        $examAverage = $examResults->count() > 0
            ? round($examResults->avg(fn ($row) => ((float) $row->marks / max((float) $row->out_of, 1)) * 100), 1)
            : 0;
        $examBySubject = $examResults
            ->groupBy('subject')
            ->map(function ($rows, $subject) {
                $avg = round($rows->avg(fn ($row) => ((float) $row->marks / max((float) $row->out_of, 1)) * 100), 1);
                return ['subject' => $subject, 'avg' => $avg];
            })
            ->values();
        $examTrend = $examResults->map(function ($row) {
            $percent = round(((float) $row->marks / max((float) $row->out_of, 1)) * 100, 1);
            return [
                'label' => trim((string) (($row->exam_name ?? 'Exam').' '.($row->term_name ?? ''))),
                'date' => optional($row->exam_date)->format('d M Y'),
                'score' => $percent,
            ];
        });

        return view('student.progress-hub.index', [
            'student' => $student,
            'school' => $school,
            'class' => $class,
            'tasks' => $tasks,
            'assignments' => $assignments,
            'goals' => $goals,
            'progress' => $progress,
            'notices' => $notices,
            'teachers' => $teachers,
            'taskCompletionRate' => $taskCompletionRate,
            'overdueAssignments' => $overdueAssignments,
            'goalAverage' => $goalAverage,
            'streak' => $streak,
            'bestSubject' => $bestSubject,
            'weakSubject' => $weakSubject,
            'examResults' => $examResults,
            'examAverage' => $examAverage,
            'examBySubject' => $examBySubject,
            'examTrend' => $examTrend,
            'parentAccess' => $parentAccess,
        ]);
    }

    public function storeTask(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['nullable', 'in:low,medium,high'],
        ]);

        StudentTask::query()->create([
            'user_id' => Auth::id(),
            'title' => $data['title'],
            'due_date' => $data['due_date'] ?? null,
            'priority' => $data['priority'] ?? 'medium',
            'is_completed' => false,
        ]);

        return back()->with('success', 'Task added.');
    }

    public function toggleTask(StudentTask $task): RedirectResponse
    {
        abort_if((string) $task->user_id !== (string) Auth::id(), 403);
        $task->is_completed = ! (bool) $task->is_completed;
        $task->save();

        return back()->with('success', 'Task updated.');
    }

    public function storeAssignment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'deadline' => ['nullable', 'date'],
            'status' => ['nullable', 'in:pending,in_progress,submitted'],
        ]);

        StudentAssignment::query()->create([
            'user_id' => Auth::id(),
            'title' => $data['title'],
            'subject' => $data['subject'],
            'deadline' => $data['deadline'] ?? null,
            'status' => $data['status'] ?? 'pending',
        ]);

        return back()->with('success', 'Assignment added.');
    }

    public function updateAssignment(Request $request, StudentAssignment $assignment): RedirectResponse
    {
        abort_if((string) $assignment->user_id !== (string) Auth::id(), 403);
        $data = $request->validate([
            'status' => ['required', 'in:pending,in_progress,submitted'],
        ]);

        $assignment->status = $data['status'];
        $assignment->save();

        return back()->with('success', 'Assignment status updated.');
    }

    public function storeGoal(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'target' => ['required', 'numeric', 'min:1'],
            'current' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:30'],
        ]);

        StudentGoal::query()->create([
            'user_id' => Auth::id(),
            'title' => $data['title'],
            'target' => (float) $data['target'],
            'current' => (float) ($data['current'] ?? 0),
            'unit' => $data['unit'] ?? '%',
        ]);

        return back()->with('success', 'Goal added.');
    }

    public function updateGoal(Request $request, StudentGoal $goal): RedirectResponse
    {
        abort_if((string) $goal->user_id !== (string) Auth::id(), 403);
        $data = $request->validate([
            'current' => ['required', 'numeric', 'min:0'],
        ]);

        $goal->current = (float) $data['current'];
        $goal->save();

        return back()->with('success', 'Goal progress updated.');
    }

    public function saveParentPortal(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'parent_name' => ['required', 'string', 'max:255'],
            'relation' => ['required', 'string', 'max:100'],
            'contact' => ['nullable', 'string', 'max:100'],
        ]);

        $existing = ParentPortalAccess::query()->firstWhere('student_user_id', Auth::id());
        $code = $existing?->access_code ?: strtoupper(substr(md5((string) Auth::id().now()->timestamp), 0, 10));

        ParentPortalAccess::query()->updateOrCreate(
            ['student_user_id' => Auth::id()],
            [
                'access_code' => $code,
                'parent_name' => $data['parent_name'],
                'relation' => $data['relation'],
                'contact' => $data['contact'] ?? null,
                'is_active' => true,
            ]
        );

        return back()->with('success', 'Parent portal saved.');
    }

    private function buildStreak($tasks): int
    {
        $dates = $tasks->where('is_completed', true)
            ->map(function ($task) {
                return optional($task->updated_at)->format('Y-m-d');
            })
            ->filter()
            ->unique()
            ->values();

        $streak = 0;
        $cursor = Carbon::today();
        while ($dates->contains($cursor->format('Y-m-d'))) {
            $streak++;
            $cursor->subDay();
        }

        return $streak;
    }
}
