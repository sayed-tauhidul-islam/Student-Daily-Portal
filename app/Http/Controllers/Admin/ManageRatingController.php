<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\School;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManageRatingController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $ratings = Rating::query()->get()->map(function ($rating) {
            if ($rating->target_type === 'teacher') {
                $teacher = Teacher::find($rating->target_id) ?: Teacher::query()->firstWhere('name', $rating->target_id);
                $rating->target_label = $teacher?->name ?? $rating->target_id;
                $rating->target_meta = trim((string) ($teacher?->area ?? ''));
            } elseif ($rating->target_type === 'school') {
                $school = School::find($rating->target_id) ?: School::query()->firstWhere('name', $rating->target_id);
                $rating->target_label = $school?->name ?? $rating->target_id;
                $rating->target_meta = trim((string) ($school?->area ?? ''));
            } else {
                $rating->target_label = $rating->target_id;
                $rating->target_meta = '';
            }

            return $rating;
        })->filter(function ($rating) use ($search) {
            if ($search === '') {
                return true;
            }

            $needle = strtolower($search);

            return str_contains(strtolower((string) ($rating->target_type ?? '')), $needle)
                || str_contains(strtolower((string) ($rating->target_label ?? '')), $needle)
                || str_contains(strtolower((string) ($rating->target_meta ?? '')), $needle)
                || str_contains(strtolower((string) ($rating->comment ?? '')), $needle);
        })->sortByDesc('created_at')->values();

        return view('admin.ratings.index', compact('ratings', 'search'));
    }

    public function create(): View
    {
        return view('admin.ratings.form', [
            'rating' => null,
            'teachers' => Teacher::query()->orderBy('name')->get(),
            'schools' => School::query()->orderBy('name')->get(),
            'teacherNames' => Teacher::query()->orderBy('name')->pluck('name')->values(),
            'schoolNames' => School::query()->orderBy('name')->pluck('name')->values(),
            'action' => route('admin.ratings.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'target_type' => ['required', 'in:teacher,school'],
            'target_id' => ['required', 'string', 'max:255'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'verified' => ['nullable', 'boolean'],
        ]);

        Rating::create([
            'user_id' => auth()->id(),
            'target_type' => $data['target_type'],
            'target_id' => $data['target_id'],
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'verified' => $request->boolean('verified'),
        ]);

        return redirect()->route('admin.ratings.index')->with('success', 'Rating created.');
    }

    public function edit(Rating $rating): View
    {
        return view('admin.ratings.form', [
            'rating' => $rating,
            'teachers' => Teacher::query()->orderBy('name')->get(),
            'schools' => School::query()->orderBy('name')->get(),
            'teacherNames' => Teacher::query()->orderBy('name')->pluck('name')->values(),
            'schoolNames' => School::query()->orderBy('name')->pluck('name')->values(),
            'action' => route('admin.ratings.update', $rating),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Rating $rating): RedirectResponse
    {
        $data = $request->validate([
            'target_type' => ['required', 'in:teacher,school'],
            'target_id' => ['required', 'string', 'max:255'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'verified' => ['nullable', 'boolean'],
        ]);

        $rating->update([
            'target_type' => $data['target_type'],
            'target_id' => $data['target_id'],
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'verified' => $request->boolean('verified'),
        ]);

        return redirect()->route('admin.ratings.index')->with('success', 'Rating updated.');
    }

    public function destroy(Rating $rating): RedirectResponse
    {
        $rating->delete();

        return back()->with('success', 'Rating deleted.');
    }
}
