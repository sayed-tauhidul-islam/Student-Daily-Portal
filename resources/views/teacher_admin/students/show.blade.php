<x-app-layout>
    @php
        $panel = $panel ?? (request()->routeIs('teacher-admin.*') ? 'teacher-admin' : 'teacher');
    @endphp
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Head teacher panel</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Student profile</h2>
                <p class="mt-2 text-sm text-slate-500">{{ $school }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route($panel.'.progress.edit', $student) }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Update progress</a>
                <a href="{{ route($panel.'.students.edit', $student) }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">Edit profile</a>
                <a href="{{ route($panel.'.students.index') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 py-6">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-800">{{ session('error') }}</div>
        @endif

        <section class="rounded-3xl border border-slate-200 bg-white p-6">
            <div class="flex flex-col gap-5 md:flex-row md:items-center">
                <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-3xl bg-slate-900 text-3xl font-black text-white">
                    @if($user?->image_url)
                        <img src="{{ $user->image_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                    @else
                        {{ strtoupper(substr($user?->name ?? 'S', 0, 1)) }}
                    @endif
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-black text-slate-900">{{ $user?->name ?? 'Student' }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $user?->email ?? 'No email' }} | {{ $student->phone ?? $user?->phone ?? 'No phone' }}</p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-4">
                        <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Class</p><p class="font-bold text-slate-900">{{ $student->class ?? '-' }}</p></div>
                        <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Group</p><p class="font-bold text-slate-900">{{ $student->group ?? 'N/A' }}</p></div>
                        <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Area</p><p class="font-bold text-slate-900">{{ $student->area ?? '-' }}</p></div>
                        <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Subjects</p><p class="font-bold text-slate-900">{{ $student->subject ?? '-' }}</p></div>
                    </div>
                </div>
            </div>
            @if($student->bio)
                <p class="mt-5 rounded-2xl bg-slate-50 p-4 text-sm leading-6 text-slate-600">{{ $student->bio }}</p>
            @endif
        </section>

        <section class="grid gap-6 xl:grid-cols-3">
            <form method="POST" action="{{ route($panel.'.attendance.store') }}" class="rounded-3xl border border-slate-200 bg-white p-5">
                @csrf
                <input type="hidden" name="student_user_id" value="{{ $student->user_id }}">
                <h3 class="text-xl font-black text-slate-900">Add attendance</h3>
                <div class="mt-4 space-y-3">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Date</label>
                        <input type="date" name="date" value="{{ now()->toDateString() }}" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Status</label>
                        <select name="status" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Note</label>
                        <textarea name="note" rows="3" maxlength="1000" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"></textarea>
                    </div>
                    <button class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white">Save attendance</button>
                </div>
            </form>

            @if($panel === 'teacher-admin')
                <form method="POST" action="{{ route('teacher-admin.students.fees.store', $student) }}" class="rounded-3xl border border-slate-200 bg-white p-5">
                    @csrf
                    <h3 class="text-xl font-black text-slate-900">Monthly fee</h3>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Month</label>
                            <input name="month" required maxlength="30" placeholder="2026-06" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Amount</label>
                            <input type="number" step="0.01" min="0" name="amount" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-sm font-semibold text-slate-700">Status</label>
                            <select name="status" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                                <option value="pending">Not clear</option>
                                <option value="approved">Clear / approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-sm font-semibold text-slate-700">Note</label>
                            <textarea name="note" rows="2" maxlength="1000" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>
                    <button class="mt-3 w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white">Save fee month</button>
                </form>

                <div class="rounded-3xl border border-slate-200 bg-white p-5">
                    <h3 class="text-xl font-black text-slate-900">Guardian contact</h3>
                    <div class="mt-4 rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                        <p class="font-bold text-slate-900">{{ $guardian?->parent_name ?? 'Guardian not added' }}</p>
                        <p class="mt-1">{{ $guardian?->relation ?? 'Relation unavailable' }}</p>
                        <p class="mt-1">{{ $guardian?->contact ?? 'No guardian contact saved yet.' }}</p>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @if($guardian?->contact)
                            <a href="tel:{{ $guardian->contact }}" class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">Call guardian</a>
                        @endif
                        @if($user?->email)
                            <a href="mailto:{{ $user->email }}" class="rounded-full border border-sky-200 bg-sky-50 px-4 py-2 text-sm font-semibold text-sky-700">Email student</a>
                        @endif
                        <a href="{{ route('teacher-admin.messages', ['with' => $user?->getKey()]) }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">Message student</a>
                    </div>
                </div>
            @endif
        </section>

        <section class="grid gap-6 xl:grid-cols-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-xl font-black text-slate-900">Progress</h3>
                    @if($progress)
                        <form method="POST" action="{{ route($panel.'.progress.destroy', $student) }}" data-confirm-delete>
                            @csrf
                            @method('DELETE')
                            <button class="rounded-full border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700">Delete</button>
                        </form>
                    @endif
                </div>
                <div class="mt-4 space-y-3">
                    <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Overall</p><p class="text-2xl font-black text-slate-900">{{ (int) ($progress->overall_score ?? 0) }}%</p></div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Attendance</p><p class="font-bold">{{ (int) ($progress->attendance_score ?? 0) }}%</p></div>
                        <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Behaviour</p><p class="font-bold">{{ (int) ($progress->behavior_score ?? 0) }}%</p></div>
                    </div>
                    <p class="rounded-2xl bg-slate-50 p-3 text-sm text-slate-600">{{ $progress->teacher_comment ?? $progress->motivation_note ?? 'No progress note yet.' }}</p>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 xl:col-span-2">
                <h3 class="text-xl font-black text-slate-900">Attendance records</h3>
                <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-3 py-2">Date</th><th class="px-3 py-2">Status</th><th class="px-3 py-2">Note</th><th class="px-3 py-2">Action</th></tr></thead>
                        <tbody>
                            @forelse($attendance as $record)
                                <tr class="border-t">
                                    <td class="px-3 py-2">{{ \Illuminate\Support\Carbon::parse($record->date)->format('d M Y') }}</td>
                                    <td class="px-3 py-2">{{ ucfirst($record->status ?? '-') }}</td>
                                    <td class="px-3 py-2">{{ $record->note ?? '-' }}</td>
                                    <td class="px-3 py-2">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route($panel.'.attendance.edit', $record) }}" class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">Edit</a>
                                            <form method="POST" action="{{ route($panel.'.attendance.destroy', $record) }}" data-confirm-delete>
                                                @csrf
                                                @method('DELETE')
                                                <button class="rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-3 py-8 text-center text-slate-500">No attendance records yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        @if($panel === 'teacher-admin')
            <section class="grid gap-6 xl:grid-cols-2">
                <div class="rounded-3xl border border-slate-200 bg-white p-5">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-xl font-black text-slate-900">Fee records</h3>
                        <a href="{{ route('teacher-admin.payments') }}" class="text-sm font-semibold text-sky-700">Payment board</a>
                    </div>
                    <div class="mt-4 space-y-3">
                        @forelse($payments as $payment)
                            @php
                                $status = (string) ($payment->status ?? 'pending');
                            @endphp
                            <form method="POST" action="{{ route('teacher-admin.students.fees.update', [$student, $payment]) }}" class="rounded-2xl border border-slate-200 p-4">
                                @csrf
                                @method('PATCH')
                                <div class="grid gap-3 sm:grid-cols-4">
                                    <div>
                                        <p class="text-xs text-slate-500">Month</p>
                                        <p class="font-bold text-slate-900">{{ $payment->month }}</p>
                                    </div>
                                    <input type="number" step="0.01" min="0" name="amount" value="{{ $payment->amount }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm sm:col-span-1">
                                    <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                                        <option value="pending" @selected($status === 'pending')>Not clear</option>
                                        <option value="approved" @selected($status === 'approved')>Clear</option>
                                        <option value="rejected" @selected($status === 'rejected')>Rejected</option>
                                    </select>
                                    <button class="rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Update</button>
                                    <textarea name="note" rows="2" maxlength="1000" class="rounded-xl border border-slate-200 px-3 py-2 text-sm sm:col-span-4">{{ $payment->note }}</textarea>
                                </div>
                            </form>
                        @empty
                            <p class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">No monthly fee record yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-5">
                    <h3 class="text-xl font-black text-slate-900">Special notice and task</h3>
                    <form method="POST" action="{{ route('teacher-admin.students.notices.store', $student) }}" class="mt-4 space-y-3 rounded-2xl bg-slate-50 p-4">
                        @csrf
                        <input name="title" required maxlength="255" placeholder="Notice title" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <textarea name="body" required rows="3" maxlength="5000" placeholder="Special notice for this student only" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"></textarea>
                        <button class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white">Send notice</button>
                    </form>
                    <form method="POST" action="{{ route('teacher-admin.students.tasks.store', $student) }}" class="mt-4 grid gap-3 rounded-2xl bg-slate-50 p-4 sm:grid-cols-4">
                        @csrf
                        <input name="title" required maxlength="255" placeholder="Task title" class="rounded-xl border border-slate-200 px-3 py-2 text-sm sm:col-span-2">
                        <input type="date" name="due_date" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <select name="priority" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="low">Low</option>
                        </select>
                        <button class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white sm:col-span-4">Assign task</button>
                    </form>
                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div>
                            <p class="mb-2 text-sm font-bold text-slate-900">Latest notices</p>
                            @forelse($notices->take(3) as $notice)
                                <div class="mb-2 rounded-xl border border-slate-200 p-3 text-sm">
                                    <p class="font-semibold">{{ $notice->title }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ optional($notice->published_at)->format('d M Y') }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">No special notice yet.</p>
                            @endforelse
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-bold text-slate-900">Open tasks</p>
                            @forelse($tasks->where('is_completed', false)->take(3) as $task)
                                <div class="mb-2 rounded-xl border border-slate-200 p-3 text-sm">
                                    <p class="font-semibold">{{ $task->title }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $task->priority ?? 'medium' }} {{ $task->due_date ? '| '.$task->due_date->format('d M Y') : '' }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">No open task yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <section class="rounded-3xl border border-slate-200 bg-white p-5">
            <h3 class="text-xl font-black text-slate-900">Exam results</h3>
            <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @forelse($examResults as $result)
                    @php
                        $percent = round(((float) $result->marks / max((float) $result->out_of, 1)) * 100, 1);
                    @endphp
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="font-bold text-slate-900">{{ $result->exam_name }} {{ $result->term_name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $result->subject }} | {{ $result->marks }}/{{ $result->out_of }} ({{ $percent }}%)</p>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500 md:col-span-2 xl:col-span-3">No exam results yet.</div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
