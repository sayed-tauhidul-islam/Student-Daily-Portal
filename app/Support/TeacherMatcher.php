<?php

namespace App\Support;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Collection;

class TeacherMatcher
{
    public static function forStudent(?Student $student, ?Collection $teachers = null): Collection
    {
        $teachers ??= Teacher::query()->get();

        $school = self::normalize((string) ($student?->school ?? ''));
        $area = self::normalize((string) ($student?->area ?? ''));
        $subjects = self::studentSubjects($student);

        return $teachers
            ->filter(function (Teacher $teacher) use ($school, $area, $subjects) {
                if ($school !== '' && ! self::containsNormalized((string) ($teacher->institution ?? ''), $school)) {
                    return false;
                }

                if ($area !== '' && ! self::containsNormalized((string) ($teacher->area ?? ''), $area)) {
                    return false;
                }

                if ($subjects->isNotEmpty() && ! self::teacherHasAnySubject($teacher, $subjects)) {
                    return false;
                }

                return true;
            })
            ->sortByDesc(fn (Teacher $teacher) => (float) ($teacher->rating ?? 0))
            ->values();
    }

    public static function schoolTeachers(?Student $student, ?Collection $teachers = null): Collection
    {
        $teachers ??= Teacher::query()->get();
        $school = self::normalize((string) ($student?->school ?? ''));

        if ($school === '') {
            return collect();
        }

        return $teachers
            ->filter(fn (Teacher $teacher) => self::containsNormalized((string) ($teacher->institution ?? ''), $school))
            ->sortBy(fn (Teacher $teacher) => strtolower((string) ($teacher->name ?? '')))
            ->values();
    }

    public static function studentSubjects(?Student $student): Collection
    {
        $subjects = collect($student?->subjects ?? [])
            ->merge(explode(',', (string) ($student?->subject ?? '')))
            ->map(fn ($subject) => trim((string) $subject))
            ->filter()
            ->unique()
            ->values();

        return $subjects;
    }

    private static function teacherHasAnySubject(Teacher $teacher, Collection $subjects): bool
    {
        $teacherSubjects = collect($teacher->subjects ?? [])
            ->merge(explode(',', (string) ($teacher->subject ?? '')))
            ->map(fn ($subject) => self::normalize((string) $subject))
            ->filter()
            ->values();

        return $subjects->contains(function ($subject) use ($teacherSubjects) {
            $subject = self::normalize((string) $subject);

            return $teacherSubjects->contains(
                fn (string $teacherSubject) => str_contains($teacherSubject, $subject) || str_contains($subject, $teacherSubject)
            );
        });
    }

    private static function containsNormalized(string $value, string $needle): bool
    {
        $value = self::normalize($value);

        return $value !== '' && $needle !== '' && ($value === $needle || str_contains($value, $needle) || str_contains($needle, $value));
    }

    private static function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace('&', ' and ', $value);
        $value = preg_replace('/[^a-z0-9\s]/', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value);
    }
}
