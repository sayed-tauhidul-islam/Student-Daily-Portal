<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] app-accent">Reading Tracker</p>
            <h2 class="mt-2 text-3xl font-black text-[color:var(--app-text)]">Daily Book Reading Log</h2>
            <p class="mt-2 text-sm app-muted">Track what book you read and how much time you spent.</p>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[0.4fr_0.6fr]">
        @if(auth()->user()?->role === 'student')
        <section class="app-surface rounded-2xl p-5">
            <h3 class="text-xl font-black">Add Reading Session</h3>
            <form method="POST" action="{{ route('student.reading-logs.store') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-semibold">Book Name</label>
                    <input type="text" name="book_name" required maxlength="255" class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                </div>
                <div>
                    <label class="text-sm font-semibold">Subject</label>
                    <input type="text" name="subject" maxlength="255" class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                </div>
                <div>
                    <label class="text-sm font-semibold">Read Date</label>
                    <input type="date" name="read_date" required class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-semibold">Start Time</label>
                        <input type="time" name="start_time" required class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">End Time</label>
                        <input type="time" name="end_time" required class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                    </div>
                </div>
                <div>
                    <label class="text-sm font-semibold">Note</label>
                    <textarea name="note" rows="3" maxlength="1000" class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]"></textarea>
                </div>
                <button type="submit" class="rounded-full bg-[color:var(--app-primary)] px-5 py-2.5 text-sm font-semibold text-white">Save Reading Log</button>
            </form>
        </section>
        @endif

        <section class="app-surface rounded-2xl p-5 {{ auth()->user()?->role === 'student' ? '' : 'xl:col-span-2' }}">
            <h3 class="text-xl font-black">Saved Sessions</h3>
            <div class="mt-4 space-y-3 max-h-[35rem] overflow-auto pr-1">
                @forelse($logs as $log)
                    @php $studentUser = $usersById[(string) ($log->student_user_id ?? '')] ?? null; @endphp
                    <div class="rounded-2xl border border-[color:var(--app-border)] p-4">
                        <p class="font-bold">{{ $log->book_name }}</p>
                        @if(auth()->user()?->role !== 'student')
                            <p class="text-xs app-muted">Student: {{ $studentUser?->name ?? 'Student' }}</p>
                        @endif
                        <p class="text-xs app-muted">Subject: {{ $log->subject ?? 'N/A' }}</p>
                        <p class="text-xs app-muted">Date: {{ $log->read_date }}</p>
                        <p class="text-xs app-muted">Time: {{ $log->start_time }} - {{ $log->end_time }} ({{ $log->duration_minutes ?? 0 }} minutes)</p>
                        @if(!empty($log->note))
                            <p class="mt-2 text-sm">{{ $log->note }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm app-muted">No reading logs yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
