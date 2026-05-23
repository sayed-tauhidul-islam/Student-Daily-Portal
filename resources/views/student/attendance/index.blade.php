<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Student</p>
            <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Attendance</h2>
            <p class="mt-2 text-sm text-slate-500">View-only attendance history for your institute.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            @if(!$institute)
                <div class="rounded-[1.75rem] border border-amber-200 bg-gradient-to-r from-amber-50 to-yellow-50 px-5 py-4 text-amber-800 shadow-sm">
                    Complete your profile and select your school/college to see attendance.
                </div>
            @else
                <div class="mb-5 rounded-[1.75rem] border border-sky-200/60 bg-gradient-to-r from-sky-50 to-cyan-50 px-5 py-4 text-sm text-slate-700">
                    <span class="font-semibold text-slate-900">Institute:</span> {{ $institute }}
                </div>

                <div class="mb-5 flex flex-col gap-3 rounded-[1.75rem] border border-slate-200/80 bg-white/90 px-5 py-4 shadow-sm md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Monthly filter</p>
                        <p class="mt-1 text-lg font-black text-slate-900">{{ $monthLabel }}</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('student.attendance.index', ['month' => $prevMonth]) }}" class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Prev</a>
                        <form method="GET" class="flex items-center gap-2">
                            <input type="month" name="month" value="{{ $selectedMonth }}" class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                            <button class="rounded-2xl bg-slate-950 px-3 py-2 text-xs font-semibold text-white">Apply</button>
                        </form>
                        <a href="{{ route('student.attendance.index', ['month' => $nextMonth]) }}" class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Next</a>
                    </div>
                </div>

                <div class="mb-5 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Present</p>
                        <p class="mt-1 text-2xl font-black text-emerald-800">{{ $stats['present'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Late</p>
                        <p class="mt-1 text-2xl font-black text-amber-800">{{ $stats['late'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-rose-700">Absent</p>
                        <p class="mt-1 text-2xl font-black text-rose-800">{{ $stats['absent'] ?? 0 }}</p>
                    </div>
                </div>

                @php
                    $startOffset = max(($monthStartWeekday ?? 1) - 1, 0);
                    $daysTotal = $daysInMonth ?? 30;
                    $weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                @endphp

                <div class="mb-5 overflow-hidden rounded-[2rem] border border-slate-200/80 bg-white/90 shadow-[0_20px_55px_rgba(15,23,42,0.10)] backdrop-blur">
                    <div class="grid grid-cols-7 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-sky-50 text-center text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                        @foreach($weekdays as $weekday)
                            <div class="px-2 py-3">{{ $weekday }}</div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-7">
                        @for($i = 0; $i < $startOffset; $i++)
                            <div class="min-h-[78px] border-r border-b border-slate-100 bg-slate-50/40"></div>
                        @endfor

                        @for($day = 1; $day <= $daysTotal; $day++)
                            @php
                                $status = strtolower((string) ($calendarStatusByDay[$day] ?? ''));
                                $dayClass = $status === 'present'
                                    ? 'bg-emerald-50 text-emerald-700'
                                    : ($status === 'late'
                                        ? 'bg-amber-50 text-amber-700'
                                        : ($status === 'absent' ? 'bg-rose-50 text-rose-700' : 'bg-white text-slate-700'));
                            @endphp
                            <div class="min-h-[78px] border-r border-b border-slate-100 px-2 py-2 {{ $dayClass }}">
                                <div class="text-sm font-bold">{{ $day }}</div>
                                <div class="mt-2 text-[11px] font-semibold uppercase tracking-[0.14em]">{{ $status ?: '—' }}</div>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="overflow-hidden rounded-[2rem] border border-slate-200/80 bg-white/90 shadow-[0_20px_55px_rgba(15,23,42,0.10)] backdrop-blur">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-gradient-to-r from-slate-50 to-sky-50 text-left text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Marked By</th>
                                    <th class="px-4 py-3">Note</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($records as $record)
                                    <tr>
                                        <td class="px-4 py-4 text-slate-700">{{ \Illuminate\Support\Carbon::parse($record->date)->format('d M Y') }}</td>
                                        <td class="px-4 py-4">
                                            @php
                                                $status = strtolower((string) $record->status);
                                                $classes = $status === 'present'
                                                    ? 'bg-emerald-100 text-emerald-700'
                                                    : ($status === 'late' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700');
                                            @endphp
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $classes }}">{{ ucfirst($record->status) }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-slate-700">{{ $record->teacher_name ?? 'Teacher' }}</td>
                                        <td class="px-4 py-4 text-slate-600">{{ $record->note ?: '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-10 text-center text-slate-500">No attendance records yet.</td>
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
