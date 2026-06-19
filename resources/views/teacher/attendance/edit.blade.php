<x-app-layout>
    @php
        $panel = $panel ?? (request()->routeIs('teacher-admin.*') ? 'teacher-admin' : 'teacher');
    @endphp
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Teacher</p>
            <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Edit Attendance</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route($panel.'.attendance.update', $attendance) }}" class="space-y-4 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                @csrf
                @method('PUT')

                <select name="student_user_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" required>
                    @foreach($students as $student)
                        <option value="{{ $student->user_id }}" @selected(old('student_user_id', $attendance->student_user_id) == $student->user_id)>
                            {{ $studentNameMap[(string) $student->user_id] ?? 'Student' }}
                        </option>
                    @endforeach
                </select>

                <input type="date" name="date" value="{{ old('date', \Illuminate\Support\Carbon::parse($attendance->date)->toDateString()) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" required>

                <select name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" required>
                    <option value="present" @selected(old('status', $attendance->status) === 'present')>Present</option>
                    <option value="absent" @selected(old('status', $attendance->status) === 'absent')>Absent</option>
                    <option value="late" @selected(old('status', $attendance->status) === 'late')>Late</option>
                </select>

                <textarea name="note" rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" placeholder="Optional note">{{ old('note', $attendance->note) }}</textarea>

                <div class="flex gap-3">
                    <button class="rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white">Save</button>
                    <a href="{{ route($panel.'.attendance.index') }}" class="rounded-2xl border border-slate-200 px-5 py-3.5 text-sm font-semibold text-slate-700">Back</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
