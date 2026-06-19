<x-app-layout>
    @php
        $panel = request()->routeIs('teacher-admin.*') ? 'teacher-admin' : 'teacher';
    @endphp
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Teacher Panel</p>
            <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Student Progress Tracking</h2>
        </div>
    </x-slot>

    <div class="space-y-6 py-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-5">
            <p class="text-sm text-slate-500">School scope: {{ $school !== '' ? $school : 'Not assigned' }}</p>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b bg-slate-50 text-xs uppercase tracking-[0.2em] text-slate-500">
                    <tr><th class="px-4 py-3">Student</th><th class="px-4 py-3">Class</th><th class="px-4 py-3">Overall</th><th class="px-4 py-3">Weak Subject</th><th class="px-4 py-3">Action</th></tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        @php
                            $user = $userMap[(string) ($student->user_id ?? '')] ?? null;
                            $progressKey = trim((string) ($student->user_id ?? '')) !== '' ? (string) $student->user_id : 'student:'.$student->getKey();
                            $progress = $progressMap[$progressKey] ?? null;
                            $weak = collect($progress?->subjects ?? [])->sortBy('score')->first();
                        @endphp
                        <tr class="border-b">
                            <td class="px-4 py-3">{{ $user?->name ?? 'Student' }}<div class="text-xs text-slate-500">{{ $user?->email ?? '' }}</div></td>
                            <td class="px-4 py-3">{{ $student->class ?? '-' }}</td>
                            <td class="px-4 py-3">{{ (int) ($progress->overall_score ?? 0) }}%</td>
                            <td class="px-4 py-3">{{ $weak['name'] ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route($panel.'.progress.edit', $student) }}" class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">Track / Edit</a>
                                    @if($panel === 'teacher-admin')
                                        <form method="POST" action="{{ route('teacher-admin.progress.destroy', $student) }}" data-confirm-delete>
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">Delete progress</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No students found in your school.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
