<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManageTeacherController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $teachers = Teacher::query()->get();
        $usersById = User::query()
            ->whereIn('_id', $teachers->pluck('user_id')->filter()->unique()->values()->all())
            ->get()
            ->mapWithKeys(fn ($user) => [(string) $user->getKey() => $user]);

        $headTeachers = $teachers->filter(function ($teacher) use ($search, $usersById) {
            $user = $usersById[(string) $teacher->user_id] ?? null;

            if (! $user || ($user->role ?? '') !== 'teacher_admin') {
                return false;
            }

            if ($search === '') {
                return true;
            }

            $needle = strtolower($search);
            $teacherSubjects = collect($teacher->subjects ?? [$teacher->subject ?? '']);

            return str_contains(strtolower((string) ($user?->name ?? '')), $needle)
                || str_contains(strtolower((string) ($user?->email ?? '')), $needle)
                || str_contains(strtolower((string) ($teacher->name ?? '')), $needle)
                || str_contains(strtolower((string) ($teacher->qualification ?? '')), $needle)
                || str_contains(strtolower((string) ($teacher->experience ?? '')), $needle)
                || str_contains(strtolower((string) ($teacher->area ?? '')), $needle)
                || str_contains(strtolower((string) ($teacher->institution ?? '')), $needle)
                || $teacherSubjects->contains(fn ($subject) => str_contains(strtolower((string) $subject), $needle));
        })->sortBy(function ($teacher) use ($usersById) {
            $user = $usersById[(string) $teacher->user_id] ?? null;

            return strtolower((string) ($user?->school ?? $teacher->institution ?? $teacher->name ?? ''));
        })->values();

        $headTeachers = $this->paginateCollection($headTeachers, $request, 8);

        return view('admin.head-teachers.index', compact('headTeachers', 'search'));
    }

    private function paginateCollection($items, Request $request, int $perPage = 8)
    {
        $page = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $items = $items->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return $paginator->appends($request->query());
    }
}
