<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Student Progress Hub</p>
            <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Progress, Planner, Motivation</h2>
        </div>
    </x-slot>

    <div class="space-y-6 py-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Task completion</p><p class="mt-2 text-3xl font-black text-slate-900">{{ $taskCompletionRate }}%</p></div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Overdue assignments</p><p class="mt-2 text-3xl font-black text-rose-700">{{ $overdueAssignments }}</p></div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Goal progress avg</p><p class="mt-2 text-3xl font-black text-slate-900">{{ $goalAverage }}%</p></div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Consistency streak</p><p class="mt-2 text-3xl font-black text-emerald-700">{{ $streak }} day(s)</p></div>
        </div>

        <section class="rounded-3xl border border-slate-200 bg-white p-5">
            <div class="grid gap-6 lg:grid-cols-[0.36fr_0.64fr] lg:items-center">
                <div class="flex flex-col items-center">
                    <div class="grid h-52 w-52 place-items-center rounded-full shadow-[inset_0_0_0_16px_rgba(255,255,255,0.45)]" style="background: conic-gradient({{ $progressChartGradient }});">
                        <div class="grid h-36 w-36 place-items-center rounded-full bg-[color:var(--app-surface-solid)] text-center">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Progress</p>
                                <p class="text-4xl font-black text-slate-900">{{ $combinedProgress }}%</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-900">School Performance Overview</h3>
                    <p class="mt-1 text-sm text-slate-500">Scores from attendance, reading, writing, assignments, behaviour, and exam results are converted to percentage.</p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @forelse($progressBreakdown as $item)
                            <div class="rounded-2xl border border-slate-200 p-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2">
                                        <span class="h-3 w-3 rounded-full" style="background: {{ $item['color'] }}"></span>
                                        <span class="text-sm font-semibold text-slate-900">{{ $item['label'] }}</span>
                                    </div>
                                    <span class="text-sm font-black text-slate-900">{{ $item['score'] }}%</span>
                                </div>
                                <div class="mt-2 h-2 rounded bg-slate-200">
                                    <div class="h-2 rounded" style="width: {{ min(100, max(0, $item['score'])) }}%; background: {{ $item['color'] }}"></div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500 sm:col-span-2">No teacher progress marks yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-5">
            <h3 class="text-xl font-black text-slate-900">Exam Result Analytics Chart</h3>
            <p class="mt-1 text-sm text-slate-500">Teacher manually added exam results from school assessments.</p>
            <div class="mt-4 grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl bg-slate-50 p-4"><p class="text-xs text-slate-500">Overall exam average</p><p class="mt-1 text-3xl font-black text-slate-900">{{ $examAverage }}%</p></div>
                <div class="rounded-2xl bg-slate-50 p-4 md:col-span-2">
                    <p class="text-xs text-slate-500">Subject average bars</p>
                    <div class="mt-3 space-y-2">
                        @forelse($examBySubject as $row)
                            <div>
                                <div class="mb-1 flex justify-between text-xs"><span>{{ $row['subject'] }}</span><span>{{ $row['avg'] }}%</span></div>
                                <div class="h-2 rounded bg-slate-200"><div class="h-2 rounded {{ $row['avg'] >= 80 ? 'bg-emerald-500' : ($row['avg'] >= 50 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $row['avg'] }}%"></div></div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No exam data yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200">
                <table class="min-w-full text-left text-xs">
                    <thead class="bg-slate-50 uppercase text-slate-500"><tr><th class="px-3 py-2">Exam</th><th class="px-3 py-2">Date</th><th class="px-3 py-2">Score</th></tr></thead>
                    <tbody>
                        @forelse($examTrend as $point)
                            <tr class="border-t"><td class="px-3 py-2">{{ $point['label'] }}</td><td class="px-3 py-2">{{ $point['date'] }}</td><td class="px-3 py-2">{{ $point['score'] }}%</td></tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-4 text-center text-slate-500">No trend data available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-3">
            <section class="rounded-3xl border border-slate-200 bg-white p-5 xl:col-span-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-black text-slate-900">Smart Routine & Task Planner</h3>
                    <form method="POST" action="{{ route('student.progress-hub.tasks.store') }}" class="flex gap-2">
                        @csrf
                        <input name="title" placeholder="New task" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <input type="date" name="due_date" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <select name="priority" class="rounded-xl border border-slate-200 px-2 py-2 text-sm"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option></select>
                        <button class="rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Add</button>
                    </form>
                </div>
                <div class="mt-4 space-y-2">
                    @forelse($tasks as $task)
                        <form method="POST" action="{{ route('student.progress-hub.tasks.toggle', $task) }}" class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3">
                            @csrf
                            @method('PATCH')
                            <div>
                                <p class="font-semibold {{ $task->is_completed ? 'line-through text-slate-400' : 'text-slate-900' }}">{{ $task->title }}</p>
                                <p class="text-xs text-slate-500">{{ $task->due_date ? \Illuminate\Support\Carbon::parse($task->due_date)->format('d M Y') : 'No due date' }} • {{ ucfirst($task->priority ?? 'medium') }}</p>
                            </div>
                            <button class="rounded-full px-3 py-1 text-xs font-bold {{ $task->is_completed ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">{{ $task->is_completed ? 'Done' : 'Mark done' }}</button>
                        </form>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">No tasks yet.</div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-5">
                <h3 class="text-xl font-black text-slate-900">Goal System</h3>
                <form method="POST" action="{{ route('student.progress-hub.goals.store') }}" class="mt-3 space-y-2">
                    @csrf
                    <input name="title" placeholder="Goal title" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <div class="grid grid-cols-3 gap-2">
                        <input name="target" placeholder="Target" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <input name="current" placeholder="Current" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <input name="unit" placeholder="Unit %" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    </div>
                    <button class="w-full rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Add Goal</button>
                </form>
                <div class="mt-4 space-y-2">
                    @foreach($goals as $goal)
                        @php $percent = min(100, (int) round((($goal->current ?? 0)/max(($goal->target ?? 1),1))*100)); @endphp
                        <form method="POST" action="{{ route('student.progress-hub.goals.update', $goal) }}" class="rounded-2xl border border-slate-200 p-3">
                            @csrf
                            @method('PATCH')
                            <p class="text-sm font-semibold text-slate-900">{{ $goal->title }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $goal->current }}/{{ $goal->target }} {{ $goal->unit }}</p>
                            <div class="mt-2 h-2 rounded bg-slate-100"><div class="h-2 rounded bg-emerald-500" style="width: {{ $percent }}%"></div></div>
                            <div class="mt-2 flex gap-2"><input name="current" value="{{ $goal->current }}" class="w-full rounded-xl border border-slate-200 px-2 py-1 text-sm"><button class="rounded-xl bg-slate-900 px-3 py-1 text-xs font-semibold text-white">Update</button></div>
                        </form>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <section class="rounded-3xl border border-slate-200 bg-white p-5 xl:col-span-2">
                <h3 class="text-xl font-black text-slate-900">Assignment Tracker</h3>
                <form method="POST" action="{{ route('student.progress-hub.assignments.store') }}" class="mt-3 grid gap-2 md:grid-cols-5">
                    @csrf
                    <input name="title" placeholder="Assignment" class="rounded-xl border border-slate-200 px-3 py-2 text-sm md:col-span-2">
                    <input name="subject" placeholder="Subject" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <input type="date" name="deadline" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <button class="rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Add</button>
                </form>
                <div class="mt-4 space-y-2">
                    @forelse($assignments as $assignment)
                        <form method="POST" action="{{ route('student.progress-hub.assignments.update', $assignment) }}" class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3">
                            @csrf
                            @method('PATCH')
                            <div>
                                <p class="font-semibold text-slate-900">{{ $assignment->title }} <span class="text-xs text-slate-500">({{ $assignment->subject }})</span></p>
                                <p class="text-xs text-slate-500">Deadline: {{ $assignment->deadline ? \Illuminate\Support\Carbon::parse($assignment->deadline)->format('d M Y') : 'N/A' }}</p>
                            </div>
                            <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-200 px-2 py-1 text-xs font-semibold">
                                <option value="pending" @selected($assignment->status==='pending')>Pending</option>
                                <option value="in_progress" @selected($assignment->status==='in_progress')>In Progress</option>
                                <option value="submitted" @selected($assignment->status==='submitted')>Submitted</option>
                            </select>
                        </form>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">No assignments added yet.</div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-5">
                <h3 class="text-xl font-black text-slate-900">Manual Progress Insight</h3>
                <p class="mt-2 text-sm text-slate-500">Teacher updates this section manually based on your school study performance.</p>
                <div class="mt-4 space-y-2">
                    <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Overall score</p><p class="text-2xl font-black text-slate-900">{{ $combinedProgress }}%</p></div>
                    <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Best subject</p><p class="text-sm font-bold text-emerald-700">{{ $bestSubject['name'] ?? 'N/A' }} @if($bestSubject) ({{ $bestSubject['score'] }}%) @endif</p></div>
                    <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Needs focus</p><p class="text-sm font-bold text-rose-700">{{ $weakSubject['name'] ?? 'N/A' }} @if($weakSubject) ({{ $weakSubject['score'] }}%) @endif</p></div>
                    <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Motivation note</p><p class="text-sm text-slate-700">{{ $progress->motivation_note ?? 'Keep going, ask your teacher to update progress details.' }}</p></div>
                </div>
            </section>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <section class="rounded-3xl border border-slate-200 bg-white p-5">
                <h3 class="text-xl font-black text-slate-900">Subject-wise Performance</h3>
                <div class="mt-4 space-y-2">
                    @forelse(($progress->subjects ?? []) as $subject)
                        <div class="rounded-2xl border border-slate-200 p-3">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-slate-900">{{ $subject['name'] }}</p>
                                <span class="text-xs font-bold {{ ($subject['status'] ?? '') === 'strong' ? 'text-emerald-700' : (($subject['status'] ?? '') === 'weak' ? 'text-rose-700' : 'text-amber-700') }}">{{ ucfirst($subject['status'] ?? 'average') }}</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">Score: {{ $subject['score'] ?? 0 }}%</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $subject['comment'] ?? '' }}</p>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">Teacher has not added performance yet.</div>
                    @endforelse
                </div>
            </section>
            <section class="rounded-3xl border border-slate-200 bg-white p-5">
                <h3 class="text-xl font-black text-slate-900">Notice Priority Feed</h3>
                <div class="mt-4 space-y-2">
                    @forelse($notices as $notice)
                        @php $priority = str_contains(strtolower($notice->title), 'exam') || str_contains(strtolower($notice->title), 'urgent') ? 'urgent' : 'normal'; @endphp
                        <div class="rounded-2xl border border-slate-200 p-3">
                            <div class="flex items-center justify-between"><p class="text-sm font-semibold text-slate-900">{{ $notice->title }}</p><span class="text-[10px] font-bold {{ $priority === 'urgent' ? 'text-rose-700' : 'text-slate-500' }}">{{ strtoupper($priority) }}</span></div>
                            <p class="mt-1 text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($notice->body, 80) }}</p>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">No notices found for your institute.</div>
                    @endforelse
                </div>
            </section>
            <section class="rounded-3xl border border-slate-200 bg-white p-5">
                <h3 class="text-xl font-black text-slate-900">Teacher Connect & Resources</h3>
                <div class="mt-4 space-y-2">
                    @foreach($teachers as $teacher)
                        <div class="rounded-2xl border border-slate-200 p-3"><p class="text-sm font-semibold text-slate-900">{{ $teacher->name }}</p><p class="text-xs text-slate-500">{{ $teacher->subject ?? 'General' }} • {{ $teacher->area ?? 'N/A' }}</p></div>
                    @endforeach
                    <div class="rounded-2xl bg-slate-50 p-3 text-xs text-slate-600">
                        Resource hub: Build a daily revision list for {{ $class !== '' ? 'Class '.$class : 'your class' }} based on weak subjects, previous homework, and teacher feedback.
                    </div>
                    <div class="rounded-2xl bg-emerald-50 p-3 text-xs text-emerald-800">
                        Parent Snapshot: Overall {{ $combinedProgress }}%, Attendance {{ (int) ($progress->attendance_score ?? 0) }}%, Focus subject: {{ $weakSubject['name'] ?? 'N/A' }}.
                    </div>
                </div>
            </section>
        </div>

        <section class="rounded-3xl border border-slate-200 bg-white p-5">
            <h3 class="text-xl font-black text-slate-900">Parent Portal</h3>
            <p class="mt-1 text-sm text-slate-500">Generate a secure link so guardian can view your progress summary.</p>
            <form method="POST" action="{{ route('student.progress-hub.parent-portal.save') }}" class="mt-3 grid gap-2 md:grid-cols-4">
                @csrf
                <input name="parent_name" value="{{ old('parent_name', $parentAccess->parent_name ?? '') }}" placeholder="Parent name" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <input name="relation" value="{{ old('relation', $parentAccess->relation ?? '') }}" placeholder="Relation (Father/Mother)" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <input name="contact" value="{{ old('contact', $parentAccess->contact ?? '') }}" placeholder="Contact" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <button class="rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Save Parent Portal</button>
            </form>
            @if($parentAccess?->access_code)
                <div class="mt-3 rounded-2xl bg-slate-50 p-3 text-sm">
                    <p class="font-semibold text-slate-900">Parent Portal Link</p>
                    <a class="text-sky-700 underline break-all" href="{{ route('parent.portal', $parentAccess->access_code) }}" target="_blank">{{ route('parent.portal', $parentAccess->access_code) }}</a>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
