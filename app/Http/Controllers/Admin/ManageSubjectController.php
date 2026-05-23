<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManageSubjectController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $subjects = Subject::query()->get()->filter(function ($subject) use ($search) {
            if ($search === '') {
                return true;
            }

            $needle = strtolower($search);

            return str_contains(strtolower((string) ($subject->name ?? '')), $needle)
                || str_contains(strtolower((string) ($subject->category ?? '')), $needle);
        })->sortBy('name')->values();

        return view('admin.subjects.index', compact('subjects', 'search'));
    }

    public function create(): View
    {
        return view('admin.subjects.form', [
            'subject' => null,
            'action' => route('admin.subjects.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
        ]);

        Subject::create($data);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject created.');
    }

    public function edit(Subject $subject): View
    {
        return view('admin.subjects.form', [
            'subject' => $subject,
            'action' => route('admin.subjects.update', $subject),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
        ]);

        $subject->update($data);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject updated.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return back()->with('success', 'Subject deleted.');
    }
}
