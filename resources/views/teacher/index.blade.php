<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Teacher directory</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Find a teacher in Khulna</h2>
                <p class="mt-2 max-w-3xl text-sm text-slate-500 sm:text-base">Filter by area, subject, class level, fee, and more to quickly see teachers that match your profile or tuition request.</p>
            </div>

            <form class="grid w-full max-w-5xl gap-3 sm:grid-cols-2 xl:grid-cols-5" method="GET" action="{{ route('teachers.index') }}">
                <input type="text" name="area" value="{{ $filters['area'] ?? $area ?? '' }}" class="rounded-full border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" placeholder="Area">
                <input type="text" name="subject" value="{{ $filters['subject'] ?? $subject ?? '' }}" class="rounded-full border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" placeholder="Subject">
                <input type="text" name="institution" value="{{ $filters['institution'] ?? '' }}" class="rounded-full border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" placeholder="Institution">
                <input type="text" name="class" value="{{ $filters['class'] ?? '' }}" class="rounded-full border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" placeholder="Class">
                <select name="online" class="rounded-full border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                    <option value="">Any mode</option>
                    <option value="online" @selected(($filters['online'] ?? '') === 'online')>Online only</option>
                    <option value="offline" @selected(($filters['online'] ?? '') === 'offline')>Offline only</option>
                </select>
                <input type="number" name="experience" value="{{ $filters['experience'] ?? '' }}" class="rounded-full border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" placeholder="Min exp">
                <input type="number" step="0.1" min="0" max="5" name="rating" value="{{ $filters['rating'] ?? '' }}" class="rounded-full border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" placeholder="Min rating">
                <input type="number" name="budget" value="{{ $filters['budget'] ?? '' }}" class="rounded-full border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" placeholder="Budget max">
                <button class="rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 xl:col-span-1">Filter</button>
            </form>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.10),_transparent_34%),linear-gradient(180deg,_#f8fbff_0%,_#eef4fb_100%)] py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-5 flex flex-wrap gap-2">
                @forelse(range('A', 'Z') as $letter)
                    <a href="#teacher-{{ $letter }}" class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">{{ $letter }}</a>
                @empty
                @endforelse
            </div>

            @php
                $groupedTeachers = $teachers->groupBy(function ($teacher) {
                    return strtoupper(substr($teacher->name ?? 'T', 0, 1));
                })->sortKeys();
            @endphp

            <div class="space-y-8">
                @forelse($groupedTeachers as $teacherInitial => $teacherGroup)
                    <section id="teacher-{{ $teacherInitial }}" class="scroll-mt-28">
                        <div class="mb-3 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-950 text-sm font-black text-white">{{ $teacherInitial }}</div>
                            <div>
                                <h3 class="text-2xl font-black text-slate-900">{{ $teacherInitial }} group</h3>
                                <p class="text-sm text-slate-500">Teachers starting with {{ $teacherInitial }}</p>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @foreach($teacherGroup as $teacher)
                                <article class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-600">{{ $teacher->availability ?? 'Flexible' }}</p>
                                            <h4 class="mt-2 text-xl font-bold text-slate-900">{{ $teacher->name }}</h4>
                                            <p class="mt-2 text-sm text-slate-500">{{ $teacher->qualification }} • {{ $teacher->experience }}</p>
                                        </div>
                                        <div class="rounded-2xl bg-slate-950 px-3 py-2 text-right text-white">
                                            <div class="text-xs uppercase tracking-[0.18em] text-sky-300/80">Rating</div>
                                            <div class="text-lg font-black">{{ number_format((float) $teacher->rating, 1) }}</div>
                                        </div>
                                    </div>

                                    <div class="mt-5 space-y-3">
                                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                                            <span class="font-semibold text-slate-900">Area:</span> {{ $teacher->area }}
                                        </div>
                                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                                            <span class="font-semibold text-slate-900">Subjects:</span> {{ implode(', ', $teacher->subjects ?? [$teacher->subject ?? '']) }}
                                        </div>
                                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                                            <span class="font-semibold text-slate-900">Fee:</span> ৳{{ number_format((int) ($teacher->salary ?? 0)) }}
                                        </div>
                                    </div>

                                    <p class="mt-4 text-sm leading-6 text-slate-500">{{ $teacher->bio }}</p>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                        No teachers found for these filters.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>