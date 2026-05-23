<?php

namespace App\Support\Lists;

use App\Models\School;
use Illuminate\Support\Collection;

class SchoolList
{
    public static function all(): Collection
    {
        return School::query()->get();
    }

    public static function sortedByRating(): Collection
    {
        return self::all()->sortByDesc('rating')->values();
    }

    public static function areas(): Collection
    {
        return self::all()
            ->pluck('area')
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

    public static function names(): Collection
    {
        return self::all()
            ->pluck('name')
            ->filter()
            ->values();
    }

    public static function search(string $query): Collection
    {
        $search = trim($query);

        if ($search === '') {
            return self::sortedByRating();
        }

        $needle = strtolower($search);

        return self::all()
            ->filter(fn ($school) => str_contains(strtolower((string) $school->name), $needle) || str_contains(strtolower((string) ($school->area ?? '')), $needle))
            ->sortByDesc('rating')
            ->values();
    }
}
