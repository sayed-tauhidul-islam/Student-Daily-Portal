<x-app-layout>
    @php
        $panel = $panel ?? (request()->routeIs('teacher-admin.*') ? 'teacher-admin' : 'teacher');
    @endphp
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Teacher</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Attendance Manage</h2>
                <p class="mt-2 text-sm text-slate-500">Add, update, or remove attendance for students of your institute only.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if(!$institute)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-amber-800">
                    Complete your teacher profile and set your institute first.
                </div>
            @else
                <form method="POST" action="{{ route($panel.'.attendance.store') }}" class="grid gap-4 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)] lg:grid-cols-5">
                    @csrf
                    <select name="student_user_id" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100 lg:col-span-2" required>
                        <option value="">Select student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->user_id }}" @selected(old('student_user_id') == $student->user_id)>
                                {{ $studentNameMap[(string) $student->user_id] ?? 'Student' }}
                            </option>
                        @endforeach
                    </select>
                    <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100" required>
                    <select name="status" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100" required>
                        <option value="present" @selected(old('status') === 'present')>Present</option>
                        <option value="absent" @selected(old('status') === 'absent')>Absent</option>
                        <option value="late" @selected(old('status') === 'late')>Late</option>
                    </select>
                    <button class="rounded-2xl bg-slate-950 px-4 py-3.5 text-sm font-semibold text-white">Add</button>
                    <textarea name="note" rows="2" placeholder="Optional note" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100 lg:col-span-5">{{ old('note') }}</textarea>
                </form>

                <form method="GET" class="rounded-[1.5rem] border border-slate-200 bg-white p-4 shadow-[0_12px_30px_rgba(15,23,42,0.06)]">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center">
                        <input name="q" value="{{ $search ?? '' }}" placeholder="Search by student, status, date..." class="flex-1 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                        <div class="flex gap-2">
                            <button class="rounded-2xl bg-slate-950 px-4 py-3.5 text-sm font-semibold text-white">Search</button>
                            @if(!empty($search))
                                <a href="{{ route($panel.'.attendance.index') }}" class="rounded-2xl border border-slate-200 px-4 py-3.5 text-sm font-semibold text-slate-700">Reset</a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-left text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Student</th>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Note</th>
                                    <th class="px-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($records as $record)
                                    <tr>
                                        <td class="px-4 py-4 font-semibold text-slate-900">{{ $record->student_name }}</td>
                                        <td class="px-4 py-4 text-slate-700">{{ \Illuminate\Support\Carbon::parse($record->date)->format('d M Y') }}</td>
                                        <td class="px-4 py-4 text-slate-700">{{ ucfirst($record->status) }}</td>
                                        <td class="px-4 py-4 text-slate-600">{{ $record->note ?: '—' }}</td>
                                        <td class="px-4 py-4">
                                            <div class="flex gap-2">
                                                <a href="{{ route($panel.'.attendance.edit', $record) }}" class="rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">Edit</a>
                                                <form method="POST" action="{{ route($panel.'.attendance.destroy', $record) }}" data-confirm-delete>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="rounded-full border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-10 text-center text-slate-500">No attendance records yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
