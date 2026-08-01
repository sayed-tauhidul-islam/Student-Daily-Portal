<x-app-layout>
    @php
        $user = Auth::user();
        $attendanceTotal = max((int) ($attendanceStats['total'] ?? 0), 1);
        $presentRate = (int) round((($attendanceStats['present'] ?? 0) / $attendanceTotal) * 100);
        $nextAssignment = $upcomingAssignments->first();
        $nextExam = $upcomingExams->first();
        $profileCta = $profile ? 'Update profile' : 'Complete profile';
        $profileStatus = empty($missingFields)
            ? 'Profile ready'
            : 'Missing '.implode(', ', array_slice($missingFields, 0, 2));
    @endphp

    <div class="space-y-6">
        @if (session('success'))
            <div class="app-surface rounded-lg px-5 py-4 text-sm font-semibold text-[color:var(--app-success)]">
                {{ session('success') }}
            </div>
        @endif

        <section class="app-surface overflow-hidden rounded-lg">
            <div class="grid gap-0 lg:grid-cols-[1.35fr_0.65fr]">
                <div class="p-5 sm:p-7">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-lg border border-[color:var(--app-border)] bg-[color:var(--app-soft)] px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] app-accent">Student dashboard</span>
                        <span class="rounded-lg border border-[color:var(--app-border)] px-3 py-1 text-xs font-bold app-muted">{{ now()->format('d M Y') }}</span>
                    </div>
                    <h1 class="mt-5 max-w-3xl text-3xl font-black tracking-tight text-[color:var(--app-text)] sm:text-4xl lg:text-5xl">
                        Welcome back, {{ $user?->name ?? 'Student' }}
                    </h1>
                    <p class="mt-4 max-w-2xl text-sm leading-6 app-muted sm:text-base">
                        Your classes, progress, notices, payments, and teacher matches are ready in one focused workspace.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('student.progress-hub.index') }}" class="inline-flex items-center justify-center rounded-lg bg-[color:var(--app-primary)] px-4 py-3 text-sm font-black text-white transition hover:opacity-90">
                            Open progress hub
                        </a>
                        <a href="{{ route('student.profile.create') }}" class="inline-flex items-center justify-center rounded-lg border border-[color:var(--app-border)] px-4 py-3 text-sm font-black text-[color:var(--app-text)] transition hover:bg-[color:var(--app-soft)]">
                            {{ $profileCta }}
                        </a>
                    </div>
                </div>

                <div class="border-t border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] p-5 sm:p-7 lg:border-l lg:border-t-0">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] app-primary">Today at a glance</p>
                    <div class="mt-5 space-y-4">
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-bold text-[color:var(--app-text)]">Profile</span>
                                <span class="app-muted">{{ $profileCompleteness ?? 0 }}%</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-[color:var(--app-soft)]">
                                <div class="h-2 rounded-full bg-[color:var(--app-primary)]" style="width: {{ min(100, max(0, (int) ($profileCompleteness ?? 0))) }}%"></div>
                            </div>
                            <p class="mt-2 text-xs app-muted">{{ $profileStatus }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-lg border border-[color:var(--app-border)] p-4">
                                <p class="text-xs app-muted">Attendance</p>
                                <p class="mt-1 text-2xl font-black text-[color:var(--app-text)]">{{ $presentRate }}%</p>
                            </div>
                            <div class="rounded-lg border border-[color:var(--app-border)] p-4">
                                <p class="text-xs app-muted">Teachers</p>
                                <p class="mt-1 text-2xl font-black text-[color:var(--app-text)]">{{ $teacherMatchCount ?? $teacherMatches->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <a href="{{ $schoolRecord ? route('schools.show', $schoolRecord) : route('schools.index') }}" class="app-surface rounded-lg p-5 transition hover:-translate-y-0.5">
                <p class="text-sm font-semibold app-muted">School</p>
                <p class="mt-2 text-xl font-black text-[color:var(--app-text)]">{{ $profile?->school ?? 'Not selected' }}</p>
                <p class="mt-3 text-sm app-primary">Rating: {{ $schoolRating ? number_format($schoolRating, 1) : 'No rating yet' }}</p>
            </a>
            <a href="{{ route('student.attendance.index') }}" class="app-surface rounded-lg p-5 transition hover:-translate-y-0.5">
                <p class="text-sm font-semibold app-muted">Attendance</p>
                <p class="mt-2 text-3xl font-black text-[color:var(--app-text)]">{{ $presentRate }}%</p>
                <p class="mt-3 text-sm app-primary">{{ $attendanceStats['present'] ?? 0 }} present, {{ $attendanceStats['absent'] ?? 0 }} absent</p>
            </a>
            <a href="{{ route('student.progress-hub.index') }}" class="app-surface rounded-lg p-5 transition hover:-translate-y-0.5">
                <p class="text-sm font-semibold app-muted">Next assignment</p>
                <p class="mt-2 text-xl font-black text-[color:var(--app-text)]">{{ $nextAssignment?->title ?? 'All clear' }}</p>
                <p class="mt-3 text-sm app-primary">{{ $nextAssignment?->deadline ? \Illuminate\Support\Carbon::parse($nextAssignment->deadline)->format('d M Y') : 'No pending deadline' }}</p>
            </a>
            <a href="{{ route('student.payments') }}" class="app-surface rounded-lg p-5 transition hover:-translate-y-0.5">
                <p class="text-sm font-semibold app-muted">Tuition fee</p>
                <p class="mt-2 text-3xl font-black text-[color:var(--app-text)]">{{ $tuitionCleared ? 'Paid' : 'Due' }}</p>
                <p class="mt-3 text-sm {{ $tuitionCleared ? 'text-emerald-500' : 'text-amber-500' }}">{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $currentMonth)->format('F Y') }}</p>
            </a>
        </section>

        <section class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="space-y-6">
                <div class="app-surface rounded-lg p-5 sm:p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.18em] app-primary">Academic progress</p>
                            <h2 class="mt-2 text-2xl font-black text-[color:var(--app-text)]">Student Performance</h2>
                        </div>
                        <a href="{{ route('student.progress-hub.index') }}" class="rounded-lg border border-[color:var(--app-border)] px-3 py-2 text-xs font-black text-[color:var(--app-text)]">Details</a>
                    </div>

                    <div class="mt-6 grid gap-5 lg:grid-cols-[auto_1fr] lg:items-center">
                        <div class="mx-auto grid h-40 w-40 place-items-center rounded-full shadow-[inset_0_0_0_10px_rgba(255,255,255,0.12)]" style="background: conic-gradient({{ $dashboardProgressGradient }});">
                            <div class="grid h-28 w-28 place-items-center rounded-full bg-[color:var(--app-surface-solid)] text-center">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] app-muted">Progress</p>
                                    <p class="text-3xl font-black text-[color:var(--app-text)]">{{ $dashboardProgressScore }}%</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            @forelse($dashboardProgressBreakdown as $item)
                                <div>
                                    <div class="flex items-center justify-between gap-3 text-sm">
                                        <span class="flex items-center gap-2 font-bold text-[color:var(--app-text)]">
                                            <span class="h-2.5 w-2.5 rounded-full" style="background: {{ $item['color'] }}"></span>
                                            {{ $item['label'] }}
                                        </span>
                                        <span class="app-muted">{{ $item['score'] }}%</span>
                                    </div>
                                    <div class="mt-2 h-2.5 rounded-full bg-[color:var(--app-soft)]">
                                        <div class="h-2.5 rounded-full" style="width: {{ min(100, max(0, $item['score'])) }}%; background: {{ $item['color'] }}"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-lg border border-dashed border-[color:var(--app-border)] px-4 py-8 text-center text-sm app-muted">
                                    No teacher progress marks yet.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <p class="mt-6 rounded-lg border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] p-4 text-sm leading-6 app-muted">
                        {{ $progress?->teacher_comment ?? $progress?->motivation_note ?? 'No teacher progress note yet.' }}
                    </p>
                </div>

                <div class="app-surface rounded-lg p-5 sm:p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.18em] app-primary">Matched teachers</p>
                            <h2 class="mt-2 text-2xl font-black text-[color:var(--app-text)]">Best for your profile</h2>
                        </div>
                        <a href="{{ route('student.institute-teachers.index') }}" class="rounded-lg border border-[color:var(--app-border)] px-3 py-2 text-xs font-black text-[color:var(--app-text)]">View all</a>
                    </div>
                    <div class="mt-5 space-y-3">
                        @forelse($teacherMatches as $teacher)
                            <a href="{{ route('teachers.show', $teacher) }}" class="flex items-center justify-between gap-4 rounded-lg border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-3">
                                <div class="min-w-0">
                                    <p class="truncate font-bold text-[color:var(--app-text)]">{{ $teacher->name }}</p>
                                    <p class="mt-1 truncate text-sm app-muted">{{ $teacher->area }} - {{ implode(', ', $teacher->subjects ?? [$teacher->subject ?? '']) }}</p>
                                </div>
                                <span class="shrink-0 rounded-lg bg-[color:var(--app-soft)] px-3 py-1 text-xs font-black app-primary">Open</span>
                            </a>
                        @empty
                            <div class="rounded-lg border border-dashed border-[color:var(--app-border)] px-4 py-8 text-center text-sm app-muted">No teacher matches yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2 xl:grid-cols-1">
                <div class="app-surface rounded-lg p-5 sm:p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.18em] app-primary">Assignments</p>
                            <h2 class="mt-2 text-2xl font-black text-[color:var(--app-text)]">Upcoming work</h2>
                        </div>
                        <a href="{{ route('student.progress-hub.index') }}" class="rounded-lg border border-[color:var(--app-border)] px-3 py-2 text-xs font-black text-[color:var(--app-text)]">Open hub</a>
                    </div>
                    <div class="mt-5 space-y-3">
                        @forelse($upcomingAssignments as $assignment)
                            <div class="rounded-lg border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-3">
                                <p class="font-bold text-[color:var(--app-text)]">{{ $assignment->title }}</p>
                                <p class="mt-1 text-sm app-muted">{{ $assignment->subject }} - {{ $assignment->deadline ? \Illuminate\Support\Carbon::parse($assignment->deadline)->format('d M Y') : 'No deadline' }}</p>
                            </div>
                        @empty
                            <div class="rounded-lg border border-dashed border-[color:var(--app-border)] px-4 py-8 text-center text-sm app-muted">No pending assignments.</div>
                        @endforelse
                    </div>
                </div>

                <div class="app-surface rounded-lg p-5 sm:p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.18em] app-primary">Exams</p>
                            <h2 class="mt-2 text-2xl font-black text-[color:var(--app-text)]">Exam calendar</h2>
                        </div>
                        <span class="rounded-lg bg-[color:var(--app-soft)] px-3 py-2 text-xs font-black app-primary">Avg {{ $examAverage !== null ? $examAverage.'%' : 'N/A' }}</span>
                    </div>
                    <div class="mt-5 space-y-3">
                        @forelse($upcomingExams as $exam)
                            <div class="rounded-lg border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-3">
                                <p class="font-bold text-[color:var(--app-text)]">{{ trim(($exam->exam_name ?? '').' '.($exam->term_name ?? '')) }}</p>
                                <p class="mt-1 text-sm app-muted">{{ $exam->subject }} - {{ $exam->exam_date ? \Illuminate\Support\Carbon::parse($exam->exam_date)->format('d M Y') : 'No date' }}</p>
                            </div>
                        @empty
                            <div class="rounded-lg border border-dashed border-[color:var(--app-border)] px-4 py-8 text-center text-sm app-muted">No upcoming exams found.</div>
                        @endforelse
                    </div>
                </div>

                <div class="app-surface rounded-lg p-5 sm:p-6 lg:col-span-2 xl:col-span-1">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.18em] app-primary">School notices</p>
                            <h2 class="mt-2 text-2xl font-black text-[color:var(--app-text)]">Latest updates</h2>
                        </div>
                        <a href="{{ route('student.notices.index') }}" class="rounded-lg border border-[color:var(--app-border)] px-3 py-2 text-xs font-black text-[color:var(--app-text)]">All notices</a>
                    </div>
                    <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-1">
                        @forelse($schoolNotices->take(4) as $notice)
                            <div class="rounded-lg border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-3">
                                <p class="font-bold text-[color:var(--app-text)]">{{ $notice->title }}</p>
                                <p class="mt-1 text-sm leading-5 app-muted">{{ \Illuminate\Support\Str::limit($notice->body, 95) }}</p>
                            </div>
                        @empty
                            <div class="rounded-lg border border-dashed border-[color:var(--app-border)] px-4 py-8 text-center text-sm app-muted md:col-span-2 xl:col-span-1">No school notice yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
