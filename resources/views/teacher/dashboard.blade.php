<x-app-layout>
    @php
        $user = Auth::user();
        $avatarUrl = $user?->image ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->image) : null;
        $selectedSubjects = $profile?->subjects ?: array_values(array_filter(array_map('trim', explode(',', (string) ($profile->subject ?? '')))));
        $hasAverageRating = ! empty($averageRating) && $averageRating > 0;
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.32em] text-cyan-300/80">Teacher dashboard</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl lg:text-5xl">Your tutoring workspace</h2>
                <p class="mt-3 max-w-2xl text-sm text-slate-500 sm:text-base">See requests, profile readiness, matched students, and rating activity in one focused space.</p>
            </div>
            <!-- Action buttons removed per user request -->
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.12),_transparent_30%),radial-gradient(circle_at_right_bottom,_rgba(99,102,241,0.10),_transparent_25%),linear-gradient(180deg,_#f8fbff_0%,_#eef4fb_100%)] py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)] sm:p-8">
                <div class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-center">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.32em] text-sky-600">Tutor workspace</p>
                        <h3 class="mt-3 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Welcome back, {{ $user?->name ?? 'Teacher' }}</h3>
                        <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">Keep your teaching profile current, respond to new tuition requests, and monitor how your profile performs across the platform.</p>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ route('teacher.profile.create') }}" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-950/15 transition hover:bg-slate-800">Edit profile</a>
                            <a href="{{ route('teacher.posts.create') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Post</a>
                        </div>
                    </div>

                    <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-5 shadow-[0_20px_60px_rgba(15,23,42,0.08)]">
                        <div class="flex items-center gap-4">
                            <a href="{{ route('teacher.profile.create') }}" class="relative block h-20 w-20 overflow-hidden rounded-[1.5rem] bg-slate-900 text-3xl font-black text-white ring-4 ring-white shadow-[0_14px_30px_rgba(15,23,42,0.18)]">
                                @if ($avatarUrl)
                                    <img src="{{ $avatarUrl }}" alt="{{ $user?->name }}" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center">{{ strtoupper(substr($user?->name ?? 'T', 0, 1)) }}</div>
                                @endif
                            </a>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Profile</p>
                                <p class="text-lg font-bold text-slate-950">{{ $profile ? 'Published' : 'Needs setup' }}</p>
                                <p class="text-sm text-slate-500">{{ $profile?->area ?? 'Add your area and bio in Settings' }}</p>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl bg-white px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Requests</p>
                                <p class="mt-1 text-2xl font-black text-slate-950">{{ $requestCount }}</p>
                            </div>
                            <div class="rounded-2xl bg-white px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Average rating</p>
                                <div class="mt-1 flex items-end gap-2">
                                    <p class="text-2xl font-black text-slate-950">{{ $hasAverageRating ? number_format($averageRating, 1) : '—' }}</p>
                                    <span class="rounded-full bg-amber-100 px-2 py-1 text-[11px] font-semibold text-amber-700">{{ $ratingCount }} reviews</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm text-slate-500">Profile status</p>
                        <p class="mt-2 text-3xl font-black text-slate-950">{{ $profile ? 'Ready' : 'Incomplete' }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm text-slate-500">Pending requests</p>
                        <p class="mt-2 text-3xl font-black text-slate-950">{{ $requestCount }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm text-slate-500">Your posts</p>
                        <p class="mt-2 text-3xl font-black text-slate-950">{{ $postCount }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm text-slate-500">Matched requests</p>
                        <p class="mt-2 text-3xl font-black text-slate-950">{{ $matchCount }}</p>
                    </div>
                </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
                <section class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Live requests</p>
                            <h3 class="mt-2 text-2xl font-black text-slate-950">Recommended tuition requests</h3>
                        </div>
                        <a href="{{ route('teacher.finder') }}" class="rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">View all</a>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse($featuredRequests as $request)
                            <article class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 transition hover:border-sky-200 hover:bg-white">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h4 class="text-lg font-bold text-slate-950">{{ $request->title }}</h4>
                                        <p class="mt-1 text-sm text-slate-500">{{ $request->subject }} • {{ $request->class_level }} • {{ $request->area }}</p>
                                    </div>
                                    <span class="rounded-full bg-slate-950 px-3 py-1 text-xs font-semibold text-white">৳{{ number_format((int) $request->budget) }}</span>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $request->description }}</p>
                            </article>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">No requests available yet.</div>
                        @endforelse
                    </div>
                </section>

                <aside class="space-y-6">
                    <section class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">My students</p>
                        <h3 class="mt-2 text-2xl font-black text-slate-950">Student leads</h3>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Students</p>
                                <p class="mt-1 text-2xl font-black text-slate-950">{{ $studentCount }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Requests</p>
                                <p class="mt-1 text-2xl font-black text-slate-950">{{ $matchCount }}</p>
                            </div>
                        </div>

                        <div class="mt-5 space-y-3">
                            @forelse($topStudents as $student)
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-bold text-slate-950">{{ $student->name }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ $student->subject }} • {{ $student->area }}</p>
                                        </div>
                                        <span class="rounded-full bg-slate-950 px-3 py-1 text-xs font-semibold text-white">৳{{ number_format($student->budget) }}</span>
                                    </div>
                                    <p class="mt-3 text-xs font-medium text-slate-500">{{ $student->requests }} request(s)</p>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">No student leads matched yet.</div>
                            @endforelse
                        </div>
                    </section>

                    <section class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Earnings</p>
                        <h3 class="mt-2 text-2xl font-black text-slate-950">Estimated earnings</h3>

                        <div class="mt-5 rounded-[1.5rem] bg-gradient-to-br from-slate-950 to-slate-800 p-5 text-white shadow-[0_18px_50px_rgba(15,23,42,0.20)]">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-300/80">From matched requests</p>
                            <p class="mt-3 text-4xl font-black">৳{{ number_format($estimatedEarnings) }}</p>
                            <p class="mt-2 text-sm text-slate-300">This is the combined budget value of requests that match your profile.</p>
                        </div>
                    </section>

                    <section class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Profile summary</p>
                        <h3 class="mt-2 text-2xl font-black text-slate-950">Teacher snapshot</h3>

                        <div class="mt-5 space-y-3 text-sm text-slate-600">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3"><span class="font-semibold text-slate-950">Qualification:</span> {{ $profile?->qualification ?? 'No qualification saved yet.' }}</div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3"><span class="font-semibold text-slate-950">Experience:</span> {{ $profile?->experience ?? 'No experience saved yet.' }}</div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3"><span class="font-semibold text-slate-950">Area:</span> {{ $profile?->area ?? 'No area saved yet.' }}</div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3"><span class="font-semibold text-slate-950">Rating records:</span> {{ $ratingCount }}</div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3"><span class="font-semibold text-slate-950">Subjects:</span> {{ ! empty($selectedSubjects) ? implode(', ', $selectedSubjects) : 'No subjects selected yet.' }}</div>
                        </div>
                    </section>

                    <section class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Quick actions</p>
                        <h3 class="mt-2 text-2xl font-black text-slate-950">One-click tools</h3>

                        <div class="mt-5 space-y-3">
                            <a href="{{ route('teacher.profile.create') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M5 13.5V15h1.5L15 6.5 13.5 5 5 13.5Zm9.207-9.207a1 1 0 0 1 1.414 0l.586.586a1 1 0 0 1 0 1.414l-1.122 1.122L13.086 5.414l1.121-1.121Z" />
                                </svg>
                                <span>Edit teaching profile</span>
                            </a>
                            <a href="{{ route('teacher.finder') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.394 9.823l3.641 3.64a1 1 0 0 0 1.414-1.414l-3.64-3.64A5.5 5.5 0 0 0 9 3.5Zm-3.5 5.5a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0Z" clip-rule="evenodd" />
                                </svg>
                                <span>Browse requests</span>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M11.3 1.046a1 1 0 00-2.6 0l-.2 1.101a7.002 7.002 0 00-1.47.607l-.93-.62a1 1 0 00-1.32.122l-.62.62a1 1 0 00-.122 1.32l.62.93c-.25.47-.46.96-.607 1.47l-1.101.2a1 1 0 000 2.6l1.101.2c.147.51.357 1 .607 1.47l-.62.93a1 1 0 00.122 1.32l.62.62a1 1 0 001.32.122l.93-.62c.47.25.96.46 1.47.607l.2 1.101a1 1 0 002.6 0l.2-1.101c.51-.147 1-.357 1.47-.607l.93.62a1 1 0 001.32-.122l.62-.62a1 1 0 00.122-1.32l-.62-.93c.25-.47.46-.96.607-1.47l1.101-.2a1 1 0 000-2.6l-1.101-.2a7.002 7.002 0 00-.607-1.47l.62-.93a1 1 0 00-.122-1.32l-.62-.62a1 1 0 00-1.32-.122l-.93.62a7.002 7.002 0 00-1.47-.607l-.2-1.101ZM10 13a3 3 0 100-6 3 3 0 000 6Z" clip-rule="evenodd" />
                                </svg>
                                <span>Settings</span>
                            </a>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>