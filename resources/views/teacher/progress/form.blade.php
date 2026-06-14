<x-app-layout>
    @php($panel = request()->routeIs('teacher-admin.*') ? 'teacher-admin' : 'teacher')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Teacher Panel</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Track Student Progress</h2>
                <p class="mt-2 text-sm text-slate-500">{{ $user?->name ?? 'Student' }} • Class {{ $student->class ?? '-' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($progress)
                    <form method="POST" action="{{ route($panel.'.progress.destroy', $student) }}" data-confirm-delete>
                        @csrf
                        @method('DELETE')
                        <button class="rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700">Delete progress</button>
                    </form>
                @endif
                <a href="{{ route($panel.'.progress.index') }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <form method="POST" action="{{ route($panel.'.progress.update', $student) }}" class="space-y-6 rounded-3xl border border-slate-200 bg-white p-6">
            @csrf
            @method('PUT')

            <div class="grid gap-4 md:grid-cols-3">
                <div><label class="mb-1 block text-sm font-semibold">Overall Score</label><input name="overall_score" type="number" min="0" max="100" step="0.1" value="{{ old('overall_score', $progress->overall_score ?? '') }}" placeholder="Auto if blank" class="w-full rounded-xl border border-slate-200 px-3 py-2"></div>
                <div><label class="mb-1 block text-sm font-semibold">Attendance Score</label><input name="attendance_score" type="number" min="0" max="100" step="0.1" value="{{ old('attendance_score', $progress->attendance_score ?? '') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2"></div>
                <div><label class="mb-1 block text-sm font-semibold">Reading Score</label><input name="reading_score" type="number" min="0" max="100" step="0.1" value="{{ old('reading_score', $progress->reading_score ?? '') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2"></div>
                <div><label class="mb-1 block text-sm font-semibold">Writing Score</label><input name="writing_score" type="number" min="0" max="100" step="0.1" value="{{ old('writing_score', $progress->writing_score ?? '') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2"></div>
                <div><label class="mb-1 block text-sm font-semibold">Assignment Score</label><input name="assignment_score" type="number" min="0" max="100" step="0.1" value="{{ old('assignment_score', $progress->assignment_score ?? '') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2"></div>
                <div><label class="mb-1 block text-sm font-semibold">Behavior Score</label><input name="behavior_score" type="number" min="0" max="100" step="0.1" value="{{ old('behavior_score', $progress->behavior_score ?? '') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2"></div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div><label class="mb-1 block text-sm font-semibold">Exam Goal</label><input name="exam_goal" value="{{ old('exam_goal', $progress->exam_goal ?? '') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2"></div>
                <div><label class="mb-1 block text-sm font-semibold">Teacher Comment</label><input name="teacher_comment" value="{{ old('teacher_comment', $progress->teacher_comment ?? '') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2"></div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Motivation Note For Student</label>
                <textarea name="motivation_note" rows="4" class="w-full rounded-xl border border-slate-200 px-3 py-2">{{ old('motivation_note', $progress->motivation_note ?? '') }}</textarea>
            </div>

            @php $subjects = old('subject_names', collect($progress->subjects ?? [])->pluck('name')->all()); @endphp
            @php $scores = old('subject_scores', collect($progress->subjects ?? [])->pluck('score')->all()); @endphp
            @php $comments = old('subject_comments', collect($progress->subjects ?? [])->pluck('comment')->all()); @endphp
            @php $rows = max(6, count($subjects)); @endphp
            <div>
                <h3 class="text-lg font-bold text-slate-900">Subject Performance Input (Manual)</h3>
                <p class="mb-3 text-sm text-slate-500">Input each subject result; system auto-marks strong/average/weak for student motivation.</p>
                <div class="space-y-2">
                    @for($i = 0; $i < $rows; $i++)
                        <div class="grid gap-2 md:grid-cols-12">
                            <input name="subject_names[]" value="{{ $subjects[$i] ?? '' }}" placeholder="Subject name" class="rounded-xl border border-slate-200 px-3 py-2 md:col-span-4">
                            <input name="subject_scores[]" value="{{ $scores[$i] ?? '' }}" placeholder="Score %" class="rounded-xl border border-slate-200 px-3 py-2 md:col-span-2">
                            <input name="subject_comments[]" value="{{ $comments[$i] ?? '' }}" placeholder="Comment (good / need work)" class="rounded-xl border border-slate-200 px-3 py-2 md:col-span-6">
                        </div>
                    @endfor
                </div>
            </div>

            <button class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white">Save Progress</button>
        </form>

        <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6">
            <h3 class="text-lg font-bold text-slate-900">Exam Result Analytics Input</h3>
            <form method="POST" action="{{ route($panel.'.progress.results.store', $student) }}" class="mt-3 grid gap-2 md:grid-cols-8">
                @csrf
                <input name="exam_name" placeholder="Exam" class="rounded-xl border border-slate-200 px-3 py-2 text-sm md:col-span-2">
                <input name="term_name" placeholder="Term" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <input name="subject" placeholder="Subject" class="rounded-xl border border-slate-200 px-3 py-2 text-sm md:col-span-2">
                <input name="marks" placeholder="Marks" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <input name="out_of" placeholder="Out Of" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <button class="rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Add</button>
                <input type="date" name="exam_date" class="rounded-xl border border-slate-200 px-3 py-2 text-sm md:col-span-2">
                <input name="comment" placeholder="Comment" class="rounded-xl border border-slate-200 px-3 py-2 text-sm md:col-span-6">
            </form>

            <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200">
                <table class="min-w-full text-left text-xs">
                    <thead class="bg-slate-50 uppercase text-slate-500"><tr><th class="px-3 py-2">Exam</th><th class="px-3 py-2">Subject</th><th class="px-3 py-2">Marks</th><th class="px-3 py-2">%</th><th class="px-3 py-2">Date</th><th class="px-3 py-2">Action</th></tr></thead>
                    <tbody>
                        @forelse($examResults as $result)
                            @php $p = round(((float) $result->marks / max((float) $result->out_of,1))*100,1); @endphp
                            <tr class="border-t"><td class="px-3 py-2">{{ $result->exam_name }} {{ $result->term_name }}</td><td class="px-3 py-2">{{ $result->subject }}</td><td class="px-3 py-2">{{ $result->marks }}/{{ $result->out_of }}</td><td class="px-3 py-2">{{ $p }}%</td><td class="px-3 py-2">{{ optional($result->exam_date)->format('d M Y') }}</td><td class="px-3 py-2"><form method="POST" action="{{ route($panel.'.progress.results.destroy', [$student, $result]) }}" data-confirm-delete>@csrf @method('DELETE')<button class="rounded bg-rose-100 px-2 py-1 text-[10px] font-bold text-rose-700">Delete</button></form></td></tr>
                        @empty
                            <tr><td colspan="6" class="px-3 py-4 text-center text-slate-500">No exam result added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
