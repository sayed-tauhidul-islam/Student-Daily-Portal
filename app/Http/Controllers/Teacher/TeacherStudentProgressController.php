<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentExamResult;
use App\Models\StudentProgress;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeacherStudentProgressController extends Controller
{
    public function index(): View
    {
        $school = $this->currentSchool();
        $students = Student::query()->get()->filter(function (Student $student) use ($school) {
            return $school !== '' && $this->sameSchool((string) ($student->school ?? ''), $school);
        })->values();
        $studentUserIds = $students->pluck('user_id')->filter()->map(fn ($id) => (string) $id)->values();
        $progressKeys = $students->map(fn (Student $student) => $this->progressKey($student))->filter()->values();

        $userMap = User::query()->whereIn('_id', $studentUserIds->all())->get()
            ->mapWithKeys(fn ($user) => [(string) $user->getKey() => $user]);
        $progressMap = StudentProgress::query()->whereIn('student_user_id', $progressKeys->all())->get()
            ->mapWithKeys(fn ($item) => [(string) $item->student_user_id => $item]);

        return view('teacher.progress.index', compact('students', 'userMap', 'progressMap', 'school'));
    }

    public function edit(Student $student): View
    {
        abort_if(! $this->sameSchool((string) ($student->school ?? ''), $this->currentSchool()), 403);

        $user = User::query()->find($student->user_id);
        $progressKey = $this->progressKey($student);
        $progress = StudentProgress::query()->firstWhere('student_user_id', $progressKey);
        $examResults = StudentExamResult::query()
            ->where('student_user_id', $progressKey)
            ->orderBy('exam_date', 'desc')
            ->get();

        return view('teacher.progress.form', compact('student', 'user', 'progress', 'examResults'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        abort_if(! $this->sameSchool((string) ($student->school ?? ''), $this->currentSchool()), 403);

        $data = $request->validate([
            'overall_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'attendance_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'reading_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'writing_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'assignment_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
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

        $categoryScores = collect([
            $data['attendance_score'] ?? null,
            $data['reading_score'] ?? null,
            $data['writing_score'] ?? null,
            $data['assignment_score'] ?? null,
            $data['behavior_score'] ?? null,
        ])->filter(fn ($score) => $score !== null && $score !== '');

        $subjectAverage = collect($subjects)->avg('score');
        if ($subjectAverage !== null) {
            $categoryScores->push($subjectAverage);
        }

        $overallScore = isset($data['overall_score'])
            ? (float) $data['overall_score']
            : ($categoryScores->isNotEmpty() ? round($categoryScores->avg(), 1) : null);
        $progressKey = $this->progressKey($student);

        StudentProgress::query()->updateOrCreate(
            ['student_user_id' => $progressKey],
            [
                'school' => $student->school,
                'class' => $student->class,
                'overall_score' => $overallScore,
                'attendance_score' => isset($data['attendance_score']) ? (float) $data['attendance_score'] : null,
                'reading_score' => isset($data['reading_score']) ? (float) $data['reading_score'] : null,
                'writing_score' => isset($data['writing_score']) ? (float) $data['writing_score'] : null,
                'assignment_score' => isset($data['assignment_score']) ? (float) $data['assignment_score'] : null,
                'behavior_score' => isset($data['behavior_score']) ? (float) $data['behavior_score'] : null,
                'exam_goal' => $data['exam_goal'] ?? null,
                'motivation_note' => $data['motivation_note'] ?? null,
                'teacher_comment' => $data['teacher_comment'] ?? null,
                'subjects' => $subjects,
                'updated_by' => (string) Auth::id(),
            ]
        );

        return redirect()->route($this->routeName('progress.index'))->with('success', 'Student progress updated.');
    }

    public function storeExamResult(Request $request, Student $student): RedirectResponse
    {
        abort_if(! $this->sameSchool((string) ($student->school ?? ''), $this->currentSchool()), 403);

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
            'student_user_id' => $this->progressKey($student),
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
        abort_if((string) $result->student_user_id !== (string) $this->progressKey($student), 404);
        abort_if(! $this->sameSchool((string) ($student->school ?? ''), $this->currentSchool()), 403);
        $result->delete();

        return back()->with('success', 'Exam result deleted.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        abort_if(! $this->sameSchool((string) ($student->school ?? ''), $this->currentSchool()), 403);

        StudentProgress::query()
            ->where('student_user_id', (string) $this->progressKey($student))
            ->delete();

        return back()->with('success', 'Student progress deleted.');
    }

    private function currentSchool(): string
    {
        if ((Auth::user()?->role ?? '') === 'teacher') {
            return trim((string) (Teacher::query()->firstWhere('user_id', Auth::id())?->institution ?? Auth::user()?->school ?? ''));
        }

        return trim((string) (Auth::user()?->school ?? ''));
    }

    private function progressKey(Student $student): string
    {
        $userId = trim((string) ($student->user_id ?? ''));

        return $userId !== '' ? $userId : 'student:'.$student->getKey();
    }

    private function panel(): string
    {
        return (Auth::user()?->role ?? '') === 'teacher_admin' ? 'teacher-admin' : 'teacher';
    }

    private function routeName(string $name): string
    {
        return $this->panel().'.'.$name;
    }

    private function sameSchool(string $left, string $right): bool
    {
        $left = $this->normalize($left);
        $right = $this->normalize($right);

        return $left !== '' && $right !== '' && $left === $right;
    }

    private function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9\s]/', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value);
    }
}
