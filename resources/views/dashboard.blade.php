<x-app-layout>
    @php
        $user = Auth::user();
        $avatarUrl = $user?->image ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->image) : null;
    @endphp

    <div class="py-8 lg:py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="app-surface rounded-2xl px-5 py-4 text-sm font-medium text-[color:var(--app-success)]">
                    {{ session('success') }}
                </div>
            @endif

            <section class="app-surface overflow-hidden rounded-[2.25rem] border border-sky-200/40 bg-gradient-to-br from-white/95 to-sky-50/80 p-6 sm:p-8">
                <div class="grid gap-6 lg:grid-cols-[1.35fr_0.65fr]">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.32em] app-accent">TutorLink BD</p>
                        <h1 class="mt-3 text-3xl font-black tracking-tight text-[color:var(--app-text)] sm:text-4xl lg:text-5xl">Welcome back, {{ $user?->name ?? 'User' }}</h1>
                        <p class="mt-4 max-w-2xl text-sm leading-7 app-muted sm:text-base">Manage your academic profile, check school ratings, and jump to teachers or schools from one clean dashboard.</p>

                        <!-- <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ route('student.profile.create') }}" class="rounded-full bg-[color:var(--app-primary)] px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-slate-950/10 transition hover:opacity-90">{{ $profile ? 'Update Profile' : 'Complete Profile' }}</a>
                            <a href="{{ route('teachers.index') }}" class="rounded-full border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-2.5 text-sm font-semibold text-[color:var(--app-text)] transition hover:bg-[color:var(--app-soft)]">Find Teacher</a>
                        </div> -->
                    </div>

                    <div class="rounded-[2rem] border border-sky-200/40 bg-gradient-to-br from-white to-sky-50/70 p-5">
                        <div class="flex items-center gap-4">
                            <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl bg-[color:var(--app-soft)] text-xl font-black text-[color:var(--app-primary)]">
                                @if ($avatarUrl)
                                    <img src="{{ $avatarUrl }}" alt="{{ $user?->name }}" class="h-full w-full object-cover">
                                @else
                                    {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] app-primary">Your profile</p>
                                <p class="text-lg font-bold text-[color:var(--app-text)]">{{ $user?->name ?? 'Student' }}</p>
                                <p class="text-sm app-muted">{{ $user?->area ?? 'Add your area from Settings' }}</p>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl bg-[color:var(--app-soft)] px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] app-muted">Profile</p>
                                <p class="mt-1 text-sm font-semibold text-[color:var(--app-text)]">{{ $profileCompleteness ?? 0 }}% complete</p>
                            </div>
                            <div class="rounded-2xl bg-[color:var(--app-soft)] px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] app-muted">School rating</p>
                                <p class="mt-1 text-sm font-semibold text-[color:var(--app-text)]">{{ $schoolRating ? number_format($schoolRating, 1) : 'No rating yet' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $teacherMatchFilters = array_filter([
                        'area' => $profile?->area ?? null,
                        'subject' => $selectedSubjects[0] ?? $profile?->subject ?? null,
                        'class' => $profile?->class ?? null,
                        'institution' => $profile?->school ?? null,
                    ], fn ($value) => filled($value));
                @endphp

                <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <a href="{{ route('student.profile.create') }}" class="rounded-[1.5rem] border border-sky-200/40 bg-white/95 p-5 transition hover:-translate-y-1 hover:border-sky-300 hover:shadow-[0_18px_45px_rgba(15,23,42,0.12)]">
                        <p class="text-sm app-muted">Profile completeness</p>
                        <p class="mt-3 text-3xl font-black text-[color:var(--app-text)]">{{ $profileCompleteness ?? 0 }}%</p>
                        <p class="mt-2 text-sm text-[color:var(--app-success)]">{{ !empty($missingFields) ? 'Missing: ' . implode(', ', array_slice($missingFields, 0, 3)) : 'Fully completed' }}</p>
                    </a>
                    <a href="{{ $schoolRecord ? route('schools.show', $schoolRecord) : route('schools.index') }}" class="rounded-[1.5rem] border border-sky-200/40 bg-white/95 p-5 transition hover:-translate-y-1 hover:border-emerald-300 hover:shadow-[0_18px_45px_rgba(15,23,42,0.12)]">
                        <p class="text-sm app-muted">School rating</p>
                        <p class="mt-3 text-3xl font-black text-[color:var(--app-text)]">{{ $schoolRating ? number_format($schoolRating, 1) : '—' }}</p>
                        <p class="mt-2 text-sm app-primary">{{ $schoolRecord ? $schoolRecord->name : 'Select your school' }}</p>
                    </a>
                    <a href="{{ route('teachers.index', $teacherMatchFilters) }}" class="rounded-[1.5rem] border border-sky-200/40 bg-white/95 p-5 transition hover:-translate-y-1 hover:border-cyan-300 hover:shadow-[0_18px_45px_rgba(15,23,42,0.12)]">
                        <p class="text-sm app-muted">Teacher matches</p>
                        <p class="mt-3 text-3xl font-black text-[color:var(--app-text)]">{{ $teacherMatches->count() }}</p>
                        <p class="mt-2 text-sm text-[color:var(--app-accent)]">Found for your area + subjects</p>
                    </a>
                </div>
            </section>

            <div class="grid gap-6 xl:grid-cols-[1.4fr_0.6fr]">
                <section class="space-y-6">
                    <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
                        <div class="app-surface rounded-[2rem] border border-sky-200/40 bg-white/95 p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.24em] app-primary">Profile state</p>
                                    <h2 class="mt-2 text-2xl font-bold text-[color:var(--app-text)]">Your student control center</h2>
                                </div>
                                <div class="rounded-2xl bg-[color:var(--app-soft)] px-4 py-3 text-right">
                                    <p class="text-xs font-semibold uppercase tracking-[0.24em] app-muted">Status</p>
                                    <p class="mt-1 text-lg font-black text-[color:var(--app-text)]">{{ $profile ? 'Profile Ready' : 'Needs Setup' }}</p>
                                </div>
                            </div>

                            <div class="mt-6 rounded-3xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] p-5">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-[color:var(--app-text)]">Completion progress</p>
                                        <p class="mt-1 text-sm app-muted">Finish the missing fields to improve teacher matching.</p>
                                    </div>
                                    <span class="rounded-full bg-[color:var(--app-soft)] px-3 py-2 text-sm font-semibold app-primary">{{ $profileCompleteness ?? 0 }}%</span>
                                </div>
                                <div class="mt-4 h-3 rounded-full bg-slate-100">
                                    <div class="h-3 rounded-full bg-[color:var(--app-primary)]" style="width: {{ $profileCompleteness ?? 0 }}%"></div>
                                </div>
                                <div class="mt-5 flex flex-wrap gap-2">
                                    @forelse ($missingFields ?? [] as $field)
                                        <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700">Missing {{ ucfirst($field) }}</span>
                                    @empty
                                        <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700">All profile fields complete</span>
                                    @endforelse
                                </div>
                            </div>

                            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                                <a href="{{ route('student.profile.create') }}" class="rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-4 transition hover:bg-[color:var(--app-soft)]">
                                    <p class="text-sm font-semibold text-[color:var(--app-text)]">Open profile form</p>
                                    <p class="mt-1 text-sm app-muted">Complete class, school, subjects, and area.</p>
                                </a>
                                <a href="{{ route('teachers.index') }}" class="rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-4 transition hover:bg-[color:var(--app-soft)]">
                                    <p class="text-sm font-semibold text-[color:var(--app-text)]">Find teacher</p>
                                    <p class="mt-1 text-sm app-muted">Search by subject, area, and rating.</p>
                                </a>
                            </div>
                        </div>

                        <div class="app-surface rounded-[2rem] border border-cyan-200/40 bg-gradient-to-br from-white/95 to-cyan-50/70 p-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] app-primary">Theme profile</p>
                            <h3 class="mt-2 text-2xl font-bold text-[color:var(--app-text)]">Choose your dashboard mood</h3>
                            <p class="mt-3 text-sm leading-6 app-muted">Switch between default, light, and dark styles from your profile area without leaving the dashboard.</p>

                            <div class="mt-5 grid gap-3">
                                <button type="button" onclick="window.setDashboardTheme('default')" class="flex items-center justify-between rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-left transition hover:bg-sky-100">
                                    <span>
                                        <span class="block text-sm font-semibold text-sky-800">Default</span>
                                        <span class="block text-xs text-sky-700/80">White and blue</span>
                                    </span>
                                    <span class="text-sm font-bold text-sky-700">Active</span>
                                </button>
                                <button type="button" onclick="window.setDashboardTheme('light')" class="flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-left transition hover:bg-emerald-100">
                                    <span>
                                        <span class="block text-sm font-semibold text-emerald-800">Light</span>
                                        <span class="block text-xs text-emerald-700/80">White and green</span>
                                    </span>
                                    <span class="text-sm font-bold text-emerald-700">Calm</span>
                                </button>
                                <button type="button" onclick="window.setDashboardTheme('dark')" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-left transition hover:bg-slate-100">
                                    <span>
                                        <span class="block text-sm font-semibold text-slate-800">Dark</span>
                                        <span class="block text-xs text-slate-700/80">Dark blue, black, and red</span>
                                    </span>
                                    <span class="text-sm font-bold text-slate-700">Bold</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="app-surface rounded-[2rem] p-6">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.24em] app-primary">Your subjects</p>
                                    <h3 class="mt-2 text-xl font-bold text-[color:var(--app-text)]">Selected study areas</h3>
                                </div>
                                <a href="{{ route('student.profile.create') }}" class="rounded-full border border-[color:var(--app-border)] px-3 py-2 text-xs font-semibold text-[color:var(--app-text)] transition hover:bg-[color:var(--app-soft)]">Edit</a>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                @forelse ($selectedSubjects ?? [] as $subject)
                                    <span class="rounded-full bg-[color:var(--app-soft)] px-3 py-2 text-sm font-semibold app-primary">{{ $subject }}</span>
                                @empty
                                    <span class="text-sm app-muted">No subjects selected yet.</span>
                                @endforelse
                            </div>

                            <div class="mt-6 rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] p-4">
                                <p class="text-sm font-semibold text-[color:var(--app-text)]">Top school insight</p>
                                <p class="mt-2 text-sm leading-6 app-muted">
                                    @if($schoolRecord)
                                        {{ $schoolRecord->name }} is rated {{ number_format($schoolRating, 1) }}/5 in the database and sits in {{ $schoolRecord->area }}.
                                    @else
                                        Add your school name to see the rating and matching school insight here.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="app-surface rounded-[2rem] p-6">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.24em] app-primary">Matched teachers</p>
                                    <h3 class="mt-2 text-xl font-bold text-[color:var(--app-text)]">Best fit for your profile</h3>
                                </div>
                                <a href="{{ route('teachers.index') }}" class="rounded-full border border-[color:var(--app-border)] px-3 py-2 text-xs font-semibold text-[color:var(--app-text)] transition hover:bg-[color:var(--app-soft)]">View all</a>
                            </div>

                            <div class="mt-4 space-y-3">
                                @forelse($teacherMatches as $teacher)
                                    <a href="{{ route('teachers.index') }}" class="block rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-4 transition hover:bg-[color:var(--app-soft)]">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="text-sm font-semibold text-[color:var(--app-text)]">{{ $teacher->name }}</p>
                                                <p class="mt-1 text-sm app-muted">{{ $teacher->qualification }} • {{ $teacher->experience }} • {{ $teacher->area }}</p>
                                                <p class="mt-2 text-xs app-muted">{{ implode(', ', $teacher->subjects ?? [$teacher->subject ?? '']) }}</p>
                                            </div>
                                            <div class="rounded-full bg-[color:var(--app-primary)] px-3 py-1 text-xs font-semibold text-white">{{ number_format((float) ($teacher->rating ?? 0), 1) }}</div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-8 text-center text-sm app-muted">
                                        No teacher matches yet. Add more subject and area details to improve results.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="space-y-6">
                    <div class="app-surface rounded-[2rem] p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] app-primary">Quick actions</p>
                        <h3 class="mt-2 text-2xl font-bold text-[color:var(--app-text)]">Access everything fast</h3>
                        <p class="mt-3 text-sm leading-6 app-muted">Jump directly to profile, teacher search, and schools without extra clicks.</p>

                        <div class="mt-5 space-y-3">
                            <a href="{{ route('student.profile.create') }}" class="flex items-center justify-between rounded-2xl bg-[color:var(--app-primary)] px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                                <span>Open profile form</span>
                                <span>→</span>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center justify-between rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-3 text-sm font-semibold text-[color:var(--app-text)] transition hover:bg-[color:var(--app-soft)]">
                                <span>Settings</span>
                                <span>→</span>
                            </a>
                            <a href="{{ route('teachers.index') }}" class="flex items-center justify-between rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-3 text-sm font-semibold text-[color:var(--app-text)] transition hover:bg-[color:var(--app-soft)]">
                                <span>Find teacher</span>
                                <span>→</span>
                            </a>
                            <a href="{{ route('schools.index') }}" class="flex items-center justify-between rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-3 text-sm font-semibold text-[color:var(--app-text)] transition hover:bg-[color:var(--app-soft)]">
                                <span>Browse schools</span>
                                <span>→</span>
                            </a>
                        </div>
                    </div>

                    <div class="mt-6 app-surface rounded-[2rem] p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] app-primary">Teacher posts</p>
                        <h3 class="mt-2 text-2xl font-bold text-[color:var(--app-text)]">Latest posts from teachers</h3>
                        <p class="mt-3 text-sm leading-6 app-muted">Teachers looking for students will appear here. Send a request if interested.</p>

                        <div class="mt-4 space-y-3">
                            @forelse($posts as $post)
                                @php $author = \App\Models\Teacher::where('user_id', $post->user_id)->first(); @endphp
                                <div class="rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-[color:var(--app-text)]">{{ $post->title }}</p>
                                            <p class="mt-1 text-xs app-muted">By <a href="{{ $author ? route('teachers.show', $author) : route('teachers.index') }}" class="font-semibold underline">{{ $author?->name ?? 'Teacher' }}</a> • {{ $post->category ?? 'General' }}</p>
                                            <p class="mt-2 text-sm app-muted">{{ \Illuminate\Support\Str::limit($post->body, 100) }}</p>
                                        </div>
                                        <div class="flex flex-col items-end gap-2" x-data="{ open: false }">
                                            @auth
                                                @if(Auth::user()?->role === 'student')
                                                    <button @click="open = !open" class="rounded-2xl bg-[color:var(--app-primary)] px-3 py-2 text-sm font-semibold text-white">Request</button>
                                                    <div x-show="open" x-cloak class="mt-2 w-64 rounded-2xl border border-slate-200 bg-white p-3">
                                                        <form method="POST" action="{{ route('posts.request', $post) }}">
                                                            @csrf
                                                            <textarea name="message" rows="3" class="w-full rounded-2xl border border-slate-200 px-3 py-2 text-sm" placeholder="Optional message to the teacher"></textarea>
                                                            <div class="mt-2 text-right">
                                                                <button type="submit" class="rounded-2xl bg-[color:var(--app-primary)] px-3 py-2 text-sm font-semibold text-white">Send</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endauth
                                            <span class="text-xs app-muted">{{ $post->published_at?->format('M j') ?? '' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-8 text-center text-sm app-muted">
                                    No posts yet from teachers.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="app-surface rounded-[2rem] p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] app-primary">Progress checklist</p>
                        <div class="mt-5 space-y-4">
                            @foreach (['class', 'school', 'subjects', 'area', 'phone'] as $field)
                                <div class="flex items-center justify-between rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-3">
                                    <span class="text-sm font-medium text-[color:var(--app-text)]">{{ ucfirst($field) }}</span>
                                    <span class="text-xs font-semibold {{ in_array($field, $missingFields ?? []) ? 'text-slate-700' : 'text-emerald-600' }}">{{ in_array($field, $missingFields ?? []) ? 'Missing' : 'Done' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
