<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Support\Lists\TeacherList;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
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

        return view('teacher.index', [
            'teachers' => TeacherList::filter($filters),
            'filters' => $filters,
            'area' => $filters['area'],
            'subject' => $filters['subject'],
        ]);
    }

    public function show(\App\Models\Teacher $teacher)
    {
        return view('teacher.show', ['teacher' => $teacher]);
    }
}