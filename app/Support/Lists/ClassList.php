<?php

namespace App\Support\Lists;

use Illuminate\Support\Collection;

class ClassList
{
    public static function all(): Collection
    {
        return collect(range(1, 12))->map(fn ($value) => (string) $value)->values();
    }
}
