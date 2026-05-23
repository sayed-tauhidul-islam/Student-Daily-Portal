<?php

namespace App\Support\Lists;

use App\Models\Teacher;
use Illuminate\Support\Collection;

class TeacherList
{
    public static function all(): Collection
    {
        return Teacher::query()->get();
    }

    public static function search(string $area, string $subject): Collection
    {
        return self::filter([
            'area' => $area,
            'subject' => $subject,
        ]);
    }

    public static function filter(array $filters): Collection
    {
        $teachers = self::all();

        $teachers = $teachers->filter(function ($teacher) use ($filters) {
            $areaFilter = trim((string) ($filters['area'] ?? ''));
            if ($areaFilter !== '' && ! str_contains(strtolower((string) ($teacher->area ?? '')), strtolower($areaFilter))) {
                return false;
            }

            $subjectFilter = trim((string) ($filters['subject'] ?? ''));
            if ($subjectFilter !== '') {
                $teacherSubjects = collect($teacher->subjects ?? [$teacher->subject ?? ''])->filter();

                if (! $teacherSubjects->contains(fn ($value) => str_contains(strtolower((string) $value), strtolower($subjectFilter)))) {
                    return false;
                }
            }

            $institutionFilter = trim((string) ($filters['institution'] ?? ''));
            if ($institutionFilter !== '' && ! str_contains(strtolower((string) ($teacher->institution ?? '')), strtolower($institutionFilter))) {
                return false;
            }

            $classFilter = trim((string) ($filters['class'] ?? ''));
            if ($classFilter !== '' && ! str_contains(strtolower((string) ($teacher->class_level ?? $teacher->class ?? '')), strtolower($classFilter))) {
                return false;
            }

            $genderFilter = trim((string) ($filters['gender'] ?? ''));
            if ($genderFilter !== '' && ! str_contains(strtolower((string) ($teacher->gender ?? '')), strtolower($genderFilter))) {
                return false;
            }

            $onlineFilter = trim((string) ($filters['online'] ?? ''));
            if ($onlineFilter !== '' && $onlineFilter !== 'any') {
                $isOnline = filter_var($teacher->online ?? false, FILTER_VALIDATE_BOOL);
                if ($onlineFilter === 'online' && ! $isOnline) {
                    return false;
                }

                if ($onlineFilter === 'offline' && $isOnline) {
                    return false;
                }
            }

            $minRating = (float) ($filters['rating'] ?? 0);
            if ($minRating > 0 && (float) ($teacher->rating ?? 0) < $minRating) {
                return false;
            }

            $maxBudget = (int) ($filters['budget'] ?? 0);
            if ($maxBudget > 0 && (int) ($teacher->salary ?? 0) > $maxBudget) {
                return false;
            }

            $experienceFilter = (int) ($filters['experience'] ?? 0);
            if ($experienceFilter > 0) {
                preg_match('/(\d+)/', (string) ($teacher->experience ?? ''), $matches);
                $years = (int) ($matches[1] ?? 0);

                if ($years < $experienceFilter) {
                    return false;
                }
            }

            return true;
        });

        return $teachers->sortByDesc('rating')->values();
    }
}
