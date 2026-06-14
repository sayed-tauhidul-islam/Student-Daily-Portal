<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
                'panel' => $this->panel(),
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
            'panel' => $this->panel(),
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
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,webp,gif,pdf,xls,xlsx,doc,docx,txt,csv,md'],
        ]);

        Notice::create([
            'institute' => $institute,
            'teacher_user_id' => Auth::id(),
            'title' => $data['title'],
            'body' => $data['body'],
            'attachments' => $this->storeAttachments($request),
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
            'panel' => $this->panel(),
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
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,webp,gif,pdf,xls,xlsx,doc,docx,txt,csv,md'],
        ]);

        $attachments = collect($notice->attachments ?? [])
            ->merge($this->storeAttachments($request))
            ->values()
            ->all();

        $notice->update([
            'title' => $data['title'],
            'body' => $data['body'],
            'attachments' => $attachments,
            'published_at' => now(),
        ]);

        return redirect()->route($this->routeName('notices.index'))->with('success', 'Notice updated.');
    }

    public function destroy(Notice $notice): RedirectResponse
    {
        $institute = $this->teacherInstitute();
        if (! $this->canManage($notice, $institute)) {
            abort(403, 'Not allowed to delete this notice.');
        }

        foreach (($notice->attachments ?? []) as $attachment) {
            if (! empty($attachment['path'])) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }

        $notice->delete();

        return back()->with('success', 'Notice deleted.');
    }

    private function teacherInstitute(): string
    {
        if ((Auth::user()?->role ?? '') === 'teacher_admin') {
            return trim((string) (Auth::user()?->school ?? ''));
        }

        $teacher = Teacher::query()->firstWhere('user_id', Auth::id());

        return trim((string) ($teacher?->institution ?? Auth::user()?->school ?? ''));
    }

    /**
     * @return array<int, array<string, string|int|null>>
     */
    private function storeAttachments(Request $request): array
    {
        if (! $request->hasFile('attachments')) {
            return [];
        }

        return collect($request->file('attachments'))
            ->filter()
            ->map(function ($file) {
                $path = $file->store('notice-attachments', 'public');

                return [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'url' => Storage::disk('public')->url($path),
                ];
            })
            ->values()
            ->all();
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

    private function panel(): string
    {
        return (Auth::user()?->role ?? '') === 'teacher_admin' ? 'teacher-admin' : 'teacher';
    }

    private function routeName(string $name): string
    {
        return $this->panel().'.'.$name;
    }
}
