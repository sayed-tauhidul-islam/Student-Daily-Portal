<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Support\Lists\TeacherList;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherFinderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'area' => trim((string) $request->get('area', '')),
            'subject' => trim((string) $request->get('subject', '')),
            'class' => trim((string) $request->get('class', '')),
            'institution' => trim((string) $request->get('institution', '')),
            'gender' => trim((string) $request->get('gender', '')),
            'online' => trim((string) $request->get('online', '')),
            'experience' => trim((string) $request->get('experience', '')),
            'rating' => trim((string) $request->get('rating', '')),
            'budget' => trim((string) $request->get('budget', '')),
        ];

        $teachers = TeacherList::filter($filters);

        return view('teacher.index', [
            'teachers' => $teachers,
            'filters' => $filters,
        ]);
    }
}
