<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] app-accent">Head Search</p>
            <h2 class="mt-2 text-3xl font-black text-[color:var(--app-text)]">Teacher / Student Search Hub</h2>
            <p class="mt-2 text-sm app-muted">Search any user by name/email and instantly view progress and attendance insights.</p>
        </div>
    </x-slot>

    <section class="app-surface rounded-2xl p-5">
        <form method="GET" action="{{ route('teacher-admin.search') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-[16rem] flex-1">
                <label for="q" class="text-sm font-semibold">Search by name or email</label>
                <input id="q" type="text" name="q" value="{{ $term }}" placeholder="Write student/teacher name" class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
            </div>
            <button type="submit" class="rounded-full bg-[color:var(--app-primary)] px-5 py-2.5 text-sm font-semibold text-white">Search</button>
        </form>
    </section>

    <section class="mt-6 space-y-4">
        @if($term !== '')
            @forelse($results as $user)
                @php
                    $student = $students->firstWhere('user_id', (string) $user->getKey());
                    $teacher = $teachers->firstWhere('user_id', (string) $user->getKey());
                    $userProgress = $progress[(string) $user->getKey()] ?? null;
                    $teacherAttendance = $attendance->where('teacher_user_id', (string) $user->getKey());
                @endphp
                <div class="app-surface rounded-2xl p-5">
                    <div class="grid gap-4 lg:grid-cols-[0.45fr_0.55fr]">
                        <div>
                            <p class="text-xl font-black">{{ $user->name }}</p>
                            <p class="text-sm app-muted">Role: {{ ucfirst(str_replace('_', ' ', (string) $user->role)) }}</p>
                            <p class="text-sm app-muted">Email: {{ $user->email }}</p>
                            <p class="text-sm app-muted">Phone: {{ $user->phone ?? 'N/A' }}</p>
                            <p class="text-sm app-muted">Area: {{ $user->area ?? 'N/A' }}</p>
                            @if($student)
                                <p class="text-sm app-muted mt-2">Class: {{ $student->class ?? 'N/A' }} | Group: {{ $student->group ?? 'N/A' }}</p>
                            @endif
                            @if($teacher)
                                <p class="text-sm app-muted mt-2">Subject: {{ $teacher->subject ?? 'N/A' }}</p>
                            @endif
                        </div>
                        <div>
                            @if($user->role === 'student')
                                <h3 class="text-sm font-bold uppercase tracking-wide app-accent">Progress Report</h3>
                                <p class="text-sm app-muted mt-2">Last result: {{ $userProgress->last_result ?? 'N/A' }}</p>
                                <p class="text-sm app-muted">Attendance trend: {{ $userProgress->attendance_trend ?? 'N/A' }}</p>
                                <p class="text-sm app-muted">Teacher note: {{ $userProgress->teacher_note ?? 'N/A' }}</p>
                            @elseif($user->role === 'teacher')
                                <h3 class="text-sm font-bold uppercase tracking-wide app-accent">Teacher Attendance</h3>
                                <p class="text-sm app-muted mt-2">Total attendance logs: {{ $teacherAttendance->count() }}</p>
                                <p class="text-sm app-muted">Class taken: {{ $teacherAttendance->where('status', 'present')->count() }}</p>
                                <p class="text-sm app-muted">Leave / absent: {{ $teacherAttendance->whereIn('status', ['absent','leave'])->count() }}</p>
                            @else
                                <p class="text-sm app-muted">No extra analytics for this role.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="app-surface rounded-2xl p-5 text-sm app-muted">No user matched your query.</div>
            @endforelse
        @else
            <div class="app-surface rounded-2xl p-5 text-sm app-muted">Type a name and start searching.</div>
        @endif
    </section>
</x-app-layout>
