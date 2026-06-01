<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherPost;
use App\Models\PaymentConfirmation;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class StudentDashboardController extends Controller
{
    public function index(): View
    {
        $profile = Student::where('user_id', Auth::id())->first();
        $teachers = Teacher::query()->get();

        $profileFields = ['class', 'group', 'school', 'subject', 'subjects', 'area', 'bio', 'phone'];
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

        $selectedSubjects = $profile?->subjects ?: array_values(array_filter(array_map('trim', explode(',', (string) ($profile->subject ?? '')))));
        $schoolRecord = $profile && $profile->school
            ? School::query()->where('name', $profile->school)->first()
            : null;
        $schoolRating = $schoolRecord->rating ?? null;
        $teacherCount = 0;
        if (! empty($profile?->school)) {
            $studentInstitute = (string) $profile->school;
            $teacherCount = $teachers->filter(function ($teacher) use ($studentInstitute) {
                return $this->institutesMatch((string) ($teacher->institution ?? ''), $studentInstitute);
            })->count();
        }
        $teacherMatches = $teachers->filter(function ($teacher) use ($profile, $selectedSubjects) {
            $areaMatch = ! $profile || empty($profile->area)
                ? true
                : str_contains(strtolower((string) ($teacher->area ?? '')), strtolower((string) $profile->area));

            if (! $areaMatch) {
                return false;
            }

            if (empty($selectedSubjects)) {
                return true;
            }

            $teacherSubjects = collect($teacher->subjects ?? [$teacher->subject ?? ''])
                ->filter()
                ->map(fn ($subject) => strtolower((string) $subject));

            foreach ($selectedSubjects as $subject) {
                if ($teacherSubjects->contains(fn ($teacherSubject) => str_contains($teacherSubject, strtolower($subject)))) {
                    return true;
                }
            }

            return false;
        })->sortByDesc('rating')->take(4)->values();

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

        return view('dashboard', compact(
            'profile',
            'profileCompleteness',
            'missingFields',
            'teacherCount',
            'teacherMatches',
            'selectedSubjects',
            'schoolRecord',
            'schoolRating',
            'posts',
            'postAuthors',
            'tuitionCleared',
            'currentMonth'
        ));
    }

    private function institutesMatch(string $left, string $right): bool
    {
        $a = $this->normalizeInstitute($left);
        $b = $this->normalizeInstitute($right);

        if ($a === '' || $b === '') {
            return false;
        }

        return $a === $b || str_contains($a, $b) || str_contains($b, $a);
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