<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] app-accent">Complaint Desk</p>
            <h2 class="mt-2 text-3xl font-black text-[color:var(--app-text)]">Institute Complaint Management</h2>
            <p class="mt-2 text-sm app-muted">Submit complaints against students or teachers. Head authority can review and take actions.</p>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[0.42fr_0.58fr]">
        @if($role !== 'teacher_admin')
            <section class="app-surface rounded-2xl p-5">
                <h3 class="text-xl font-black">Submit Complaint</h3>
                <form method="POST" action="{{ request()->routeIs('student.complaints*') ? route('student.complaints.store') : route('teacher.complaints.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="text-sm font-semibold">Against (optional user)</label>
                        <select name="against_user_id" class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                            <option value="">Manual Name Entry</option>
                            @foreach($teachers as $teacher)
                                @php $u = $usersById[(string) ($teacher->user_id ?? '')] ?? null; @endphp
                                @if($u)
                                    <option value="{{ $u->getKey() }}">{{ $u->name }} (Teacher)</option>
                                @endif
                            @endforeach
                            @foreach($students as $student)
                                @php $u = $usersById[(string) ($student->user_id ?? '')] ?? null; @endphp
                                @if($u)
                                    <option value="{{ $u->getKey() }}">{{ $u->name }} (Student)</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Against Name (if manual)</label>
                        <input type="text" name="against_name" maxlength="255" class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Title</label>
                        <input type="text" name="title" required maxlength="255" class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Complaint Details</label>
                        <textarea name="body" rows="5" required maxlength="3000" class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]"></textarea>
                    </div>
                    <button type="submit" class="rounded-full bg-[color:var(--app-primary)] px-5 py-2.5 text-sm font-semibold text-white">Submit Complaint</button>
                </form>
            </section>
        @endif

        <section class="app-surface rounded-2xl p-5 {{ $role === 'teacher_admin' ? 'xl:col-span-2' : '' }}">
            <h3 class="text-xl font-black">Complaint Records</h3>
            <div class="mt-4 space-y-4">
                @forelse($complaints as $complaint)
                    <div class="rounded-2xl border border-[color:var(--app-border)] p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-lg font-bold">{{ $complaint->title }}</p>
                                <p class="text-xs app-muted">Against: {{ $complaint->against_name ?? 'N/A' }} ({{ ucfirst((string) ($complaint->against_role ?? 'unknown')) }})</p>
                                <p class="text-xs app-muted">Status: {{ ucfirst((string) ($complaint->status ?? 'open')) }}</p>
                            </div>
                            <p class="text-xs app-muted">{{ optional($complaint->created_at)->format('d M Y h:i A') }}</p>
                        </div>
                        <p class="mt-3 text-sm leading-6">{{ $complaint->body }}</p>

                        @if(!empty($complaint->action_note))
                            <div class="mt-3 rounded-xl bg-[color:var(--app-soft)] p-3 text-sm">
                                <p class="font-semibold">Action Note:</p>
                                <p>{{ $complaint->action_note }}</p>
                            </div>
                        @endif

                        @if($role === 'teacher_admin')
                            <form method="POST" action="{{ route('teacher-admin.complaints.update', $complaint) }}" class="mt-4 grid gap-3 sm:grid-cols-[0.2fr_0.8fr_auto]">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                                    @foreach(['open','reviewing','resolved','rejected'] as $status)
                                        <option value="{{ $status }}" @selected($status === ($complaint->status ?? 'open'))>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="action_note" maxlength="3000" placeholder="Action note and notice message" class="rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                                <button type="submit" class="rounded-full bg-[color:var(--app-primary)] px-4 py-2 text-sm font-semibold text-white">Save</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-sm app-muted">No complaints found.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
