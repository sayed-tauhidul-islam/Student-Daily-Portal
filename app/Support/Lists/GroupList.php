<?php

namespace App\Support\Lists;

use App\Models\Group;
use Illuminate\Support\Collection;

class GroupList
{
    public static function all(): Collection
    {
        $groups = collect([
            'Science',
            'Commerce',
            'Arts',
            'Vocational',
        ])
            ->merge(Group::query()->orderBy('name')->pluck('name')->filter()->values())
            ->filter()
            ->map(fn ($group) => trim((string) $group))
            ->unique()
            ->sort()
            ->values();

        return $groups;
    }
}
