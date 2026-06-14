<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeacherAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $institute = $this->teacherInstitute();
        $search = trim((string) $request->query('q', ''));

        if ($institute === '') {
            return view('teacher.attendance.index', [
                'institute' => null,
                'panel' => $this->panel(),
                'students' => collect(),
                'records' => collect(),
                'search' => $search,
            ]);
        }

        $students = Student::query()->get()
            ->filter(fn ($student) => $this->normalizeInstitute((string) ($student->school ?? '')) === $this->normalizeInstitute($institute))
            ->values();

        $studentUsers = User::query()->whereIn('_id', $students->pluck('user_id')->filter()->all())->get(['_id', 'name']);
        $studentNameMap = $studentUsers->mapWithKeys(fn ($user) => [(string) $user->getKey() => (string) $user->name]);

        $records = Attendance::query()->get()
            ->filter(fn ($record) => $this->normalizeInstitute((string) ($record->institute ?? '')) === $this->normalizeInstitute($institute))
            ->map(function ($record) use ($studentNameMap) {
                $record->student_name = $studentNameMap[(string) ($record->student_user_id ?? '')] ?? 'Student';

                return $record;
            })
            ->filter(function ($record) use ($search) {
                if ($search === '') {
                    return true;
                }

                $needle = strtolower($search);

                return str_contains(strtolower((string) ($record->student_name ?? '')), $needle)
                    || str_contains(strtolower((string) ($record->status ?? '')), $needle)
                    || str_contains(strtolower((string) ($record->date ?? '')), $needle);
            })
            ->sortByDesc(fn ($record) => (string) ($record->date ?? ''))
            ->values();

        return view('teacher.attendance.index', [
            'institute' => $institute,
            'panel' => $this->panel(),
            'students' => $students,
            'studentNameMap' => $studentNameMap,
            'records' => $records,
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $institute = $this->teacherInstitute();
        if ($institute === '') {
            return back()->with('error', 'Complete your teacher profile with institute first.');
        }

        $data = $request->validate([
            'student_user_id' => ['required', 'string'],
            'date' => ['required', 'date'],
            'status' => ['required', 'in:present,absent,late'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! $this->studentBelongsToInstitute($data['student_user_id'], $institute)) {
            return back()->with('error', 'Selected student does not belong to your institute.');
        }

        Attendance::create([
            'institute' => $institute,
            'student_user_id' => $data['student_user_id'],
            'teacher_user_id' => Auth::id(),
            'date' => $data['date'],
            'status' => $data['status'],
            'note' => $data['note'] ?? null,
        ]);

        return back()->with('success', 'Attendance added.');
    }

    public function edit(Attendance $attendance): View
    {
        $institute = $this->teacherInstitute();

        if (! $this->canManage($attendance, $institute)) {
            abort(403, 'Not allowed to edit this attendance.');
        }

        $students = Student::query()->get()
            ->filter(fn ($student) => $this->normalizeInstitute((string) ($student->school ?? '')) === $this->normalizeInstitute($institute))
            ->values();

        $studentUsers = User::query()->whereIn('_id', $students->pluck('user_id')->filter()->all())->get(['_id', 'name']);
        $studentNameMap = $studentUsers->mapWithKeys(fn ($user) => [(string) $user->getKey() => (string) $user->name]);

        return view('teacher.attendance.edit', [
            'attendance' => $attendance,
            'students' => $students,
            'studentNameMap' => $studentNameMap,
            'institute' => $institute,
            'panel' => $this->panel(),
        ]);
    }

    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        $institute = $this->teacherInstitute();

        if (! $this->canManage($attendance, $institute)) {
            abort(403, 'Not allowed to update this attendance.');
        }

        $data = $request->validate([
            'student_user_id' => ['required', 'string'],
            'date' => ['required', 'date'],
            'status' => ['required', 'in:present,absent,late'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! $this->studentBelongsToInstitute($data['student_user_id'], $institute)) {
            return back()->with('error', 'Selected student does not belong to your institute.');
        }

        $attendance->update([
            'student_user_id' => $data['student_user_id'],
            'date' => $data['date'],
            'status' => $data['status'],
            'note' => $data['note'] ?? null,
        ]);

            return redirect()->route($this->routeName('attendance.index'))->with('success', 'Attendance updated.');
    }

    public function destroy(Attendance $attendance): RedirectResponse
    {
        $institute = $this->teacherInstitute();

        if (! $this->canManage($attendance, $institute)) {
            abort(403, 'Not allowed to delete this attendance.');
        }

        $attendance->delete();

        return back()->with('success', 'Attendance deleted.');
    }

    private function teacherInstitute(): string
    {
        if ((Auth::user()?->role ?? '') === 'teacher_admin') {
            return trim((string) (Auth::user()?->school ?? ''));
        }

        $teacher = Teacher::query()->firstWhere('user_id', Auth::id());

        return trim((string) ($teacher?->institution ?? Auth::user()?->school ?? ''));
    }

    private function studentBelongsToInstitute(string $studentUserId, string $institute): bool
    {
        $student = Student::query()->firstWhere('user_id', $studentUserId);

        return $student && $this->normalizeInstitute((string) ($student->school ?? '')) === $this->normalizeInstitute($institute);
    }

    private function canManage(Attendance $attendance, string $institute): bool
    {
        return $institute !== ''
            && $this->normalizeInstitute((string) ($attendance->institute ?? '')) === $this->normalizeInstitute($institute);
    }

    private function normalizeInstitute(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace(['&'], ' and ', $value);
        $value = preg_replace('/[^a-z0-9\s]/', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value);
    }

    private function panel(): string
    {
        return (Auth::user()?->role ?? '') === 'teacher_admin' ? 'teacher-admin' : 'teacher';
    }

    private function routeName(string $name): string
    {
        return $this->panel().'.'.$name;
    }
}
