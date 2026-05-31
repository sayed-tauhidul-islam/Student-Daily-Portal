<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Admin</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Teachers</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500 sm:text-base">Create, edit, delete teacher accounts and their profiles. Search by name, subject, institution, or area and preview avatars directly in the roster.</p>
            </div>
            <a href="{{ route('admin.teachers.create') }}" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">Add teacher</a>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-[radial-gradient(circle_at_top,_rgba(34,197,94,0.10),_transparent_35%),linear-gradient(180deg,_#f8fbff_0%,_#eef4fb_100%)] py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800 shadow-sm">{{ session('success') }}</div>
            @endif

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Visible teachers</p>
                    <h3 class="mt-2 text-3xl font-black text-slate-950">{{ $teachers->total() }}</h3>
                    <p class="mt-2 text-sm text-slate-500">All matched records in the current search.</p>
                </div>
                <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-600">Current page</p>
                    <h3 class="mt-2 text-3xl font-black text-slate-950">{{ $teachers->currentPage() }}/{{ $teachers->lastPage() }}</h3>
                    <p class="mt-2 text-sm text-slate-500">Compact browsing for large rosters.</p>
                </div>
                <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-600">Quick action</p>
                    <h3 class="mt-2 text-2xl font-black text-slate-950">Fast editing</h3>
                    <p class="mt-2 text-sm text-slate-500">Open a teacher card and update details right away.</p>
                </div>
            </div>

            <form method="GET" class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
                    <div class="flex-1">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Search teachers</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">⌕</span>
                            <input name="q" value="{{ $search ?? '' }}" placeholder="Search by name, email, subject, institution, area..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 py-3.5 pl-11 pr-4 text-sm outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button class="rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-slate-800">Search</button>
                        @if(!empty($search))
                            <a href="{{ route('admin.teachers.index') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                <div class="flex flex-col gap-3 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Teacher roster</p>
                        <h3 class="mt-1 text-2xl font-black text-slate-950">Manage teacher profiles</h3>
                    </div>
                    <p class="text-sm text-slate-500">Showing {{ $teachers->firstItem() ?? 0 }}-{{ $teachers->lastItem() ?? 0 }} of {{ $teachers->total() }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                            <tr><th class="px-6 py-4">Teacher</th><th class="px-6 py-4">Profile</th><th class="px-6 py-4">Area</th><th class="px-6 py-4">Actions</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                            @forelse($teachers as $teacher)
                                @php
                                    $user = \App\Models\User::find($teacher->user_id);
                                    $avatarUrl = $user?->image_url;
                                @endphp
                                <tr class="transition hover:bg-slate-50/80">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-slate-900 text-lg font-black text-white ring-1 ring-slate-900/10">
                                                @if($avatarUrl)
                                                    <img src="{{ $avatarUrl }}" alt="{{ $user?->name ?? 'Teacher' }}" class="h-full w-full object-cover">
                                                @else
                                                    {{ strtoupper(substr($user?->name ?? 'T', 0, 1)) }}
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-semibold text-slate-950">{{ $user?->name ?? $teacher->name }}</div>
                                                <div class="mt-1 text-xs text-slate-500">{{ $user?->email ?? 'No email' }}</div>
                                                <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-500">
                                                    <span class="rounded-full bg-slate-100 px-2.5 py-1">{{ $teacher->institution ?? 'No institution' }}</span>
                                                    <span class="rounded-full bg-slate-100 px-2.5 py-1">{{ $teacher->verification_status ?? 'pending' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="font-medium text-slate-900">{{ $teacher->subject ?? '—' }}</div>
                                        <div class="mt-1 text-xs text-slate-500">{{ $teacher->qualification ?? 'No qualification' }}</div>
                                    </td>
                                    <td class="px-6 py-5">{{ $teacher->area ?? '—' }}</td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('admin.teachers.edit', $teacher) }}" class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700 transition hover:bg-sky-100">Edit</a>
                                            <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" data-confirm-delete>@csrf @method('DELETE')<button class="rounded-full border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">Delete</button></form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">No teachers found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($teachers->hasPages())
                    <div class="border-t border-slate-200 bg-slate-50 px-6 py-4">
                        {{ $teachers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
