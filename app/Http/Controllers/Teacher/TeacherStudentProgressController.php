<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentExamResult;
use App\Models\StudentProgress;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeacherStudentProgressController extends Controller
{
    public function index(): View
    {
        $school = trim((string) (Auth::user()?->school ?? ''));
        $students = Student::query()->get()->filter(function (Student $student) use ($school) {
            return $school !== '' && str_contains(strtolower((string) ($student->school ?? '')), strtolower($school));
        })->values();

        $userMap = User::query()->whereIn('_id', $students->pluck('user_id')->filter()->values()->all())->get()
            ->mapWithKeys(fn ($user) => [(string) $user->getKey() => $user]);
        $progressMap = StudentProgress::query()->whereIn('student_user_id', $students->pluck('user_id')->filter()->values()->all())->get()
            ->mapWithKeys(fn ($item) => [(string) $item->student_user_id => $item]);

        return view('teacher.progress.index', compact('students', 'userMap', 'progressMap', 'school'));
    }

    public function edit(Student $student): View
    {
        $school = trim((string) (Auth::user()?->school ?? ''));
        abort_if(! str_contains(strtolower((string) ($student->school ?? '')), strtolower($school)), 403);

        $user = User::query()->find($student->user_id);
        $progress = StudentProgress::query()->firstWhere('student_user_id', $student->user_id);
        $examResults = StudentExamResult::query()
            ->where('student_user_id', $student->user_id)
            ->orderBy('exam_date', 'desc')
            ->get();

        return view('teacher.progress.form', compact('student', 'user', 'progress', 'examResults'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $school = trim((string) (Auth::user()?->school ?? ''));
        abort_if(! str_contains(strtolower((string) ($student->school ?? '')), strtolower($school)), 403);

        $data = $request->validate([
            'overall_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'attendance_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'behavior_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'exam_goal' => ['nullable', 'string', 'max:255'],
            'motivation_note' => ['nullable', 'string', 'max:2000'],
            'teacher_comment' => ['nullable', 'string', 'max:2000'],
            'subject_names' => ['nullable', 'array'],
            'subject_names.*' => ['nullable', 'string', 'max:255'],
            'subject_scores' => ['nullable', 'array'],
            'subject_scores.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'subject_comments' => ['nullable', 'array'],
            'subject_comments.*' => ['nullable', 'string', 'max:500'],
        ]);

        $names = $data['subject_names'] ?? [];
        $scores = $data['subject_scores'] ?? [];
        $comments = $data['subject_comments'] ?? [];
        $subjects = [];

        foreach ($names as $index => $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }

            $score = isset($scores[$index]) ? (float) $scores[$index] : 0;
            $status = $score >= 80 ? 'strong' : ($score >= 50 ? 'average' : 'weak');
            $subjects[] = [
                'name' => $name,
                'score' => $score,
                'status' => $status,
                'comment' => trim((string) ($comments[$index] ?? '')),
            ];
        }

        StudentProgress::query()->updateOrCreate(
            ['student_user_id' => $student->user_id],
            [
                'school' => $student->school,
                'class' => $student->class,
                'overall_score' => isset($data['overall_score']) ? (float) $data['overall_score'] : null,
                'attendance_score' => isset($data['attendance_score']) ? (float) $data['attendance_score'] : null,
                'behavior_score' => isset($data['behavior_score']) ? (float) $data['behavior_score'] : null,
                'exam_goal' => $data['exam_goal'] ?? null,
                'motivation_note' => $data['motivation_note'] ?? null,
                'teacher_comment' => $data['teacher_comment'] ?? null,
                'subjects' => $subjects,
                'updated_by' => (string) Auth::id(),
            ]
        );

        return redirect()->route('teacher.progress.index')->with('success', 'Student progress updated.');
    }

    public function storeExamResult(Request $request, Student $student): RedirectResponse
    {
        $school = trim((string) (Auth::user()?->school ?? ''));
        abort_if(! str_contains(strtolower((string) ($student->school ?? '')), strtolower($school)), 403);

        $data = $request->validate([
            'exam_name' => ['required', 'string', 'max:255'],
            'term_name' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'marks' => ['required', 'numeric', 'min:0'],
            'out_of' => ['required', 'numeric', 'min:1'],
            'exam_date' => ['nullable', 'date'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        StudentExamResult::query()->create([
            'student_user_id' => $student->user_id,
            'exam_name' => $data['exam_name'],
            'term_name' => $data['term_name'] ?? null,
            'subject' => $data['subject'],
            'marks' => (float) $data['marks'],
            'out_of' => (float) $data['out_of'],
            'exam_date' => $data['exam_date'] ?? null,
            'comment' => $data['comment'] ?? null,
            'entered_by' => (string) Auth::id(),
        ]);

        return back()->with('success', 'Exam result added.');
    }

    public function destroyExamResult(Student $student, StudentExamResult $result): RedirectResponse
    {
        abort_if((string) $result->student_user_id !== (string) $student->user_id, 404);
        $school = trim((string) (Auth::user()?->school ?? ''));
        abort_if(! str_contains(strtolower((string) ($student->school ?? '')), strtolower($school)), 403);
        $result->delete();

        return back()->with('success', 'Exam result deleted.');
    }
}
