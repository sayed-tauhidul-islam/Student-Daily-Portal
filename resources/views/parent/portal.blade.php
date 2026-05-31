<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Portal - TutorLink BD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-900">
    <main class="mx-auto max-w-5xl space-y-6 px-4 py-8">
        <section class="rounded-3xl border border-slate-200 bg-white p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-700">Parent Portal</p>
            <h1 class="mt-2 text-3xl font-black">Student Progress Summary</h1>
            <p class="mt-2 text-sm text-slate-500">Guardian: {{ $access->parent_name }} ({{ $access->relation }})</p>
            <p class="text-sm text-slate-500">Student: {{ $studentUser?->name ?? 'N/A' }} • Class {{ $student?->class ?? '-' }} • School {{ $student?->school ?? '-' }}</p>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Overall score</p><p class="mt-2 text-3xl font-black">{{ (int) ($progress->overall_score ?? 0) }}%</p></div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Attendance score</p><p class="mt-2 text-3xl font-black">{{ (int) ($progress->attendance_score ?? 0) }}%</p></div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Exam average</p><p class="mt-2 text-3xl font-black">{{ $overallAvg }}%</p></div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black">Subject Performance</h2>
            <div class="mt-4 space-y-2">
                @forelse(($progress->subjects ?? []) as $subject)
                    <div class="rounded-2xl border border-slate-200 p-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold">{{ $subject['name'] }}</p>
                            <p class="text-xs font-bold">{{ ucfirst($subject['status'] ?? 'average') }} • {{ $subject['score'] ?? 0 }}%</p>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">{{ $subject['comment'] ?? '' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No subject evaluation yet.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-black">Exam Records</h2>
            <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200">
                <table class="min-w-full text-left text-xs">
                    <thead class="bg-slate-50 uppercase text-slate-500"><tr><th class="px-3 py-2">Exam</th><th class="px-3 py-2">Subject</th><th class="px-3 py-2">Marks</th><th class="px-3 py-2">%</th></tr></thead>
                    <tbody>
                        @forelse($results as $result)
                            @php $p = round(((float) $result->marks / max((float) $result->out_of,1))*100,1); @endphp
                            <tr class="border-t"><td class="px-3 py-2">{{ $result->exam_name }} {{ $result->term_name }}</td><td class="px-3 py-2">{{ $result->subject }}</td><td class="px-3 py-2">{{ $result->marks }}/{{ $result->out_of }}</td><td class="px-3 py-2">{{ $p }}%</td></tr>
                        @empty
                            <tr><td colspan="4" class="px-3 py-4 text-center text-slate-500">No exam records available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 rounded-2xl bg-emerald-50 p-4 text-sm text-emerald-900">
                <strong>Motivation Note:</strong> {{ $progress->motivation_note ?? 'Keep consistent study habits and follow teacher guidance.' }}
            </div>
        </section>
    </main>
</body>
</html>

