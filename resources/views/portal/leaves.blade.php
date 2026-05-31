<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] app-accent">Leave Desk</p>
            <h2 class="mt-2 text-3xl font-black text-[color:var(--app-text)]">Advance Leave & Absence</h2>
            <p class="mt-2 text-sm app-muted">Only PDF/DOCX documents are accepted for leave applications.</p>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[0.42fr_0.58fr]">
        @if(auth()->user()?->role !== 'teacher_admin')
            <section class="app-surface rounded-2xl p-5">
                <h3 class="text-xl font-black">Apply for Leave</h3>
                <form method="POST" enctype="multipart/form-data" action="{{ request()->routeIs('student.leaves*') ? route('student.leaves.store') : route('teacher.leaves.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="text-sm font-semibold">Leave Type</label>
                        <select name="leave_type" required class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                            <option value="advance">Leave in Advance</option>
                            <option value="absence">Leave of Absence</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm font-semibold">From</label>
                            <input type="date" name="from_date" required class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                        </div>
                        <div>
                            <label class="text-sm font-semibold">To</label>
                            <input type="date" name="to_date" required class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Reason</label>
                        <textarea name="reason" rows="4" required maxlength="3000" class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]"></textarea>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Document (PDF or DOCX)</label>
                        <input type="file" name="document" required accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                    </div>
                    <button type="submit" class="rounded-full bg-[color:var(--app-primary)] px-5 py-2.5 text-sm font-semibold text-white">Submit Application</button>
                </form>
            </section>
        @endif

        <section class="app-surface rounded-2xl p-5 {{ auth()->user()?->role === 'teacher_admin' ? 'xl:col-span-2' : '' }}">
            <h3 class="text-xl font-black">Leave Applications</h3>
            <div class="mt-4 space-y-4">
                @forelse($leaves as $leave)
                    @php $u = $users[(string) ($leave->user_id ?? '')] ?? null; @endphp
                    <div class="rounded-2xl border border-[color:var(--app-border)] p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="font-bold">{{ $u?->name ?? 'User' }} ({{ ucfirst((string) ($leave->role ?? 'unknown')) }})</p>
                                <p class="text-xs app-muted">Type: {{ ucfirst((string) ($leave->leave_type ?? 'N/A')) }}</p>
                                <p class="text-xs app-muted">{{ $leave->from_date }} to {{ $leave->to_date }}</p>
                                <p class="text-xs app-muted">Status: {{ ucfirst((string) ($leave->status ?? 'pending')) }}</p>
                            </div>
                            @if(!empty($leave->document_path))
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($leave->document_path) }}" target="_blank" class="text-sm font-semibold app-primary">Open Document</a>
                            @endif
                        </div>
                        <p class="mt-3 text-sm leading-6">{{ $leave->reason }}</p>

                        @if(auth()->user()?->role === 'teacher_admin')
                            <form method="POST" action="{{ route('teacher-admin.leaves.update', $leave) }}" class="mt-4 grid gap-3 sm:grid-cols-[0.25fr_0.75fr_auto]">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                                    @foreach(['pending','approved','rejected'] as $status)
                                        <option value="{{ $status }}" @selected($status === ($leave->status ?? 'pending'))>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="review_note" maxlength="1000" placeholder="Review note" class="rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                                <button type="submit" class="rounded-full bg-[color:var(--app-primary)] px-4 py-2 text-sm font-semibold text-white">Save</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-sm app-muted">No leave records yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
