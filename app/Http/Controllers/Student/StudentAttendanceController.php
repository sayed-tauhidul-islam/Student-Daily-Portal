<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $student = Student::query()->firstWhere('user_id', Auth::id());
        $institute = trim((string) ($student?->school ?? ''));

        $monthDate = $this->resolveMonth($request->query('month'));
        $selectedMonth = $monthDate->format('Y-m');

        if ($institute === '') {
            return view('student.attendance.index', [
                'student' => $student,
                'records' => collect(),
                'institute' => null,
                'selectedMonth' => $selectedMonth,
                'monthLabel' => $monthDate->format('F Y'),
                'monthStartWeekday' => (int) $monthDate->copy()->startOfMonth()->dayOfWeekIso,
                'daysInMonth' => (int) $monthDate->daysInMonth,
                'calendarStatusByDay' => [],
                'stats' => ['present' => 0, 'absent' => 0, 'late' => 0],
                'prevMonth' => $monthDate->copy()->subMonth()->format('Y-m'),
                'nextMonth' => $monthDate->copy()->addMonth()->format('Y-m'),
            ]);
        }

        $allRecords = Attendance::query()
            ->where('student_user_id', Auth::id())
            ->get()
            ->filter(fn ($record) => $this->normalizeInstitute((string) ($record->institute ?? '')) === $this->normalizeInstitute($institute))
            ->values();

        $records = $allRecords
            ->filter(function ($record) use ($selectedMonth) {
                $date = $this->recordDate($record);

                return $date && $date->format('Y-m') === $selectedMonth;
            })
            ->sortByDesc(function ($record) {
                return (string) ($record->date ?? '');
            })
            ->values();

        $teacherIds = $records->pluck('teacher_user_id')->filter()->unique()->values();
        $teachers = User::query()->whereIn('_id', $teacherIds->all())->get(['_id', 'name']);
        $teacherNames = $teachers->mapWithKeys(fn ($user) => [(string) $user->getKey() => (string) $user->name]);

        $records = $records->map(function ($record) use ($teacherNames) {
            $record->teacher_name = $teacherNames[(string) ($record->teacher_user_id ?? '')] ?? 'Teacher';

            return $record;
        });

        $calendarStatusByDay = [];
        foreach ($records as $record) {
            $date = $this->recordDate($record);
            if (! $date) {
                continue;
            }

            $day = (int) $date->format('j');
            $status = strtolower((string) ($record->status ?? ''));
            $current = $calendarStatusByDay[$day] ?? null;

            // Priority: absent > late > present when multiple records exist for a date.
            $weight = ['present' => 1, 'late' => 2, 'absent' => 3];
            if (! $current || (($weight[$status] ?? 0) > ($weight[$current] ?? 0))) {
                $calendarStatusByDay[$day] = $status;
            }
        }

        $stats = [
            'present' => $records->where('status', 'present')->count(),
            'absent' => $records->where('status', 'absent')->count(),
            'late' => $records->where('status', 'late')->count(),
        ];

        return view('student.attendance.index', [
            'student' => $student,
            'records' => $records,
            'institute' => $institute,
            'selectedMonth' => $selectedMonth,
            'monthLabel' => $monthDate->format('F Y'),
            'monthStartWeekday' => (int) $monthDate->copy()->startOfMonth()->dayOfWeekIso,
            'daysInMonth' => (int) $monthDate->daysInMonth,
            'calendarStatusByDay' => $calendarStatusByDay,
            'stats' => $stats,
            'prevMonth' => $monthDate->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $monthDate->copy()->addMonth()->format('Y-m'),
        ]);
    }

    private function resolveMonth(?string $value): Carbon
    {
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}$/', $value)) {
            return now()->startOfMonth();
        }

        try {
            return Carbon::createFromFormat('Y-m', $value)->startOfMonth();
        } catch (\Throwable $e) {
            return now()->startOfMonth();
        }
    }

    private function recordDate($record): ?Carbon
    {
        $value = $record->date ?? null;
        if ($value instanceof Carbon) {
            return $value;
        }

        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
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
