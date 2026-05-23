<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeacherNoticeController extends Controller
{
    public function index(Request $request): View
    {
        $institute = $this->teacherInstitute();
        $search = trim((string) $request->query('q', ''));

        if ($institute === '') {
            return view('teacher.notices.index', [
                'institute' => null,
                'notices' => collect(),
                'search' => $search,
            ]);
        }

        $notices = Notice::query()->get()
            ->filter(fn ($notice) => $this->normalizeInstitute((string) ($notice->institute ?? '')) === $this->normalizeInstitute($institute))
            ->filter(function ($notice) use ($search) {
                if ($search === '') {
                    return true;
                }

                $needle = strtolower($search);

                return str_contains(strtolower((string) ($notice->title ?? '')), $needle)
                    || str_contains(strtolower((string) ($notice->body ?? '')), $needle);
            })
            ->sortByDesc(fn ($notice) => (string) ($notice->published_at ?? $notice->created_at ?? ''))
            ->values();

        return view('teacher.notices.index', [
            'institute' => $institute,
            'notices' => $notices,
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $institute = $this->teacherInstitute();
        if ($institute === '') {
            return back()->with('error', 'Complete your teacher profile with institute first.');
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        Notice::create([
            'institute' => $institute,
            'teacher_user_id' => Auth::id(),
            'title' => $data['title'],
            'body' => $data['body'],
            'published_at' => now(),
        ]);

        return back()->with('success', 'Notice published.');
    }

    public function edit(Notice $notice): View
    {
        $institute = $this->teacherInstitute();
        if (! $this->canManage($notice, $institute)) {
            abort(403, 'Not allowed to edit this notice.');
        }

        return view('teacher.notices.edit', [
            'notice' => $notice,
            'institute' => $institute,
        ]);
    }

    public function update(Request $request, Notice $notice): RedirectResponse
    {
        $institute = $this->teacherInstitute();
        if (! $this->canManage($notice, $institute)) {
            abort(403, 'Not allowed to update this notice.');
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $notice->update([
            'title' => $data['title'],
            'body' => $data['body'],
            'published_at' => now(),
        ]);

        return redirect()->route('teacher.notices.index')->with('success', 'Notice updated.');
    }

    public function destroy(Notice $notice): RedirectResponse
    {
        $institute = $this->teacherInstitute();
        if (! $this->canManage($notice, $institute)) {
            abort(403, 'Not allowed to delete this notice.');
        }

        $notice->delete();

        return back()->with('success', 'Notice deleted.');
    }

    private function teacherInstitute(): string
    {
        $teacher = Teacher::query()->firstWhere('user_id', Auth::id());

        return trim((string) ($teacher?->institution ?? ''));
    }

    private function canManage(Notice $notice, string $institute): bool
    {
        return $institute !== ''
            && $this->normalizeInstitute((string) ($notice->institute ?? '')) === $this->normalizeInstitute($institute);
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
