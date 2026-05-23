<?php

namespace App\Support\Lists;

use App\Models\Subject;
use Illuminate\Support\Collection;

class SubjectList
{
    public static function allNames(): Collection
    {
        return Subject::query()
            ->orderBy('name')
            ->pluck('name')
            ->filter()
            ->values();
    }
}
