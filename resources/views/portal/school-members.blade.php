<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] app-accent">Institute Directory</p>
            <h2 class="mt-2 text-3xl font-black text-[color:var(--app-text)]">{{ $school !== '' ? $school : 'School / College Members' }}</h2>
            <p class="mt-2 text-sm app-muted">All teachers and students in your institute with contact details.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="app-surface rounded-2xl p-5"><p class="text-sm app-muted">Total Students</p><p class="mt-1 text-3xl font-black">{{ $students->count() }}</p></div>
            <div class="app-surface rounded-2xl p-5"><p class="text-sm app-muted">Total Teachers</p><p class="mt-1 text-3xl font-black">{{ $teachers->count() }}</p></div>
            <div class="app-surface rounded-2xl p-5"><p class="text-sm app-muted">Your Classmates</p><p class="mt-1 text-3xl font-black">{{ $classmates->count() }}</p></div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="app-surface rounded-2xl p-5">
                <h3 class="text-xl font-black">Teachers</h3>
                <div class="mt-4 space-y-3">
                    @forelse($teachers as $teacher)
                        @php $user = $usersById[(string) ($teacher->user_id ?? '')] ?? null; @endphp
                        <div class="rounded-xl border border-[color:var(--app-border)] p-4">
                            <p class="font-bold">{{ $teacher->name ?? $user?->name ?? 'Teacher' }}</p>
                            <p class="text-sm app-muted">Subject: {{ $teacher->subject ?? 'N/A' }}</p>
                            <p class="text-sm app-muted">Phone: {{ $user?->phone ?? 'N/A' }}</p>
                            <p class="text-sm app-muted">Address/Area: {{ $user?->area ?? $teacher->area ?? 'N/A' }}</p>
                        </div>
                    @empty
                        <p class="text-sm app-muted">No teacher found for this institute.</p>
                    @endforelse
                </div>
            </section>

            <section class="app-surface rounded-2xl p-5">
                <h3 class="text-xl font-black">Students</h3>
                <div class="mt-4 space-y-3 max-h-[34rem] overflow-auto pr-1">
                    @forelse($students as $student)
                        @php $user = $usersById[(string) ($student->user_id ?? '')] ?? null; @endphp
                        <div class="rounded-xl border border-[color:var(--app-border)] p-4">
                            <p class="font-bold">{{ $user?->name ?? 'Student' }}</p>
                            <p class="text-sm app-muted">Class: {{ $student->class ?? 'N/A' }} | Group: {{ $student->group ?? 'N/A' }}</p>
                            <p class="text-sm app-muted">Phone: {{ $user?->phone ?? 'N/A' }}</p>
                            <p class="text-sm app-muted">Address/Area: {{ $user?->area ?? $student->area ?? 'N/A' }}</p>
                        </div>
                    @empty
                        <p class="text-sm app-muted">No students found for this institute.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
