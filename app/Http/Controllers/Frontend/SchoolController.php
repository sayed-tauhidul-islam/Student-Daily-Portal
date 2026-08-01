<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Teacher;
use App\Support\Lists\SchoolList;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->get('q', ''));

        return view('frontend.schools', [
            'schools' => SchoolList::search($query),
            'query' => $query,
        ]);
    }

    public function show(School $school): View
    {
        $teachers = Teacher::query()->get()->filter(function ($teacher) use ($school) {
            return $this->institutesMatch((string) ($teacher->institution ?? ''), (string) ($school->name ?? ''))
                || $this->institutesMatch((string) ($teacher->institution ?? ''), (string) ($school->area ?? ''));
        })->sortByDesc('rating')->values();

        $mapQuery = trim(implode(', ', array_filter([
            $school->name,
            $school->area,
            $school->type,
        ])));

        return view('frontend.school', [
            'school' => $school,
            'teachers' => $teachers,
            'teacherCount' => $teachers->count(),
            'mapQuery' => $mapQuery,
            'mapUrl' => 'https://www.google.com/maps/search/?api=1&query=' . urlencode($mapQuery),
            'embedUrl' => 'https://www.google.com/maps?q=' . urlencode($mapQuery) . '&output=embed',
        ]);
    }

    private function institutesMatch(string $left, string $right): bool
    {
        $left = strtolower(trim($left));
        $right = strtolower(trim($right));

        if ($left === '' || $right === '') {
            return false;
        }

        $left = preg_replace('/\s+/', ' ', preg_replace('/[^a-z0-9\s]/', ' ', $left) ?? $left) ?? $left;
        $right = preg_replace('/\s+/', ' ', preg_replace('/[^a-z0-9\s]/', ' ', $right) ?? $right) ?? $right;

        return $left === $right;
    }
}
