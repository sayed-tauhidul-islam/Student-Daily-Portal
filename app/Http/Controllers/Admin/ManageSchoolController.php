<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManageSchoolController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $schools = School::query()->get()->filter(function ($school) use ($search) {
            if ($search === '') {
                return true;
            }

            $needle = strtolower($search);

            return str_contains(strtolower((string) ($school->name ?? '')), $needle)
                || str_contains(strtolower((string) ($school->area ?? '')), $needle)
                || str_contains(strtolower((string) ($school->type ?? '')), $needle);
        })->sortBy('name')->values();

        return view('admin.schools.index', compact('schools', 'search'));
    }

    public function show(School $school): View
    {
        $teachers = Teacher::query()->get()->filter(function ($teacher) use ($school) {
            return $this->institutesMatch((string) ($teacher->institution ?? ''), (string) $school->name);
        })->sortBy(function ($teacher) {
            return strtolower((string) ($teacher->name ?? ''));
        })->values();

        return view('admin.schools.show', [
            'school' => $school,
            'teachers' => $teachers,
            'teacherCount' => $teachers->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.schools.form', [
            'school' => null,
            'action' => route('admin.schools.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'students' => ['nullable', 'integer', 'min:0'],
        ]);

        School::create($data + [
            'rating' => $request->input('rating', 0),
            'students' => $request->input('students', 0),
        ]);

        return redirect()->route('admin.schools.index')->with('success', 'School created.');
    }

    public function edit(School $school): View
    {
        return view('admin.schools.form', [
            'school' => $school,
            'action' => route('admin.schools.update', $school),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, School $school): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'students' => ['nullable', 'integer', 'min:0'],
        ]);

        $school->update($data + [
            'rating' => $request->input('rating', 0),
            'students' => $request->input('students', 0),
        ]);

        return redirect()->route('admin.schools.index')->with('success', 'School updated.');
    }

    public function destroy(School $school): RedirectResponse
    {
        $school->delete();

        return back()->with('success', 'School deleted.');
    }

    private function institutesMatch(string $left, string $right): bool
    {
        $a = $this->normalizeInstitute($left);
        $b = $this->normalizeInstitute($right);

        if ($a === '' || $b === '') {
            return false;
        }

        return $a === $b;
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
