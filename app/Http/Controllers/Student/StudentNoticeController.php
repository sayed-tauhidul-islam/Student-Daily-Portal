<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentNoticeController extends Controller
{
    public function index(): View
    {
        $student = Student::query()->firstWhere('user_id', Auth::id());
        $institute = trim((string) ($student?->school ?? ''));

        if ($institute === '') {
            return view('student.notices.index', [
                'student' => $student,
                'institute' => null,
                'notices' => collect(),
            ]);
        }

        $notices = Notice::query()->get()
            ->filter(fn ($notice) => $this->normalizeInstitute((string) ($notice->institute ?? '')) === $this->normalizeInstitute($institute))
            ->sortByDesc(function ($notice) {
                return (string) ($notice->published_at ?? $notice->created_at ?? '');
            })
            ->values();

        return view('student.notices.index', [
            'student' => $student,
            'institute' => $institute,
            'notices' => $notices,
        ]);
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
