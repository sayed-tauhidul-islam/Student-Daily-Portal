<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">School details</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">{{ $school->name }}</h2>
                <p class="mt-2 max-w-3xl text-sm text-slate-500 sm:text-base">{{ $school->area }} • {{ $school->type }}</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ $mapUrl }}" target="_blank" rel="noreferrer" class="rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Open in Google Maps</a>
                <a href="{{ route('teachers.index', ['institution' => $school->name]) }}" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">View matched teachers</a>
            </div>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.10),_transparent_34%),linear-gradient(180deg,_#f8fbff_0%,_#eef4fb_100%)] py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Overview</p>
                            <h3 class="mt-2 text-2xl font-black text-slate-900">{{ $school->name }}</h3>
                            <p class="mt-2 text-sm text-slate-500">{{ $school->area }} • {{ $school->type }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-950 px-4 py-3 text-right text-white">
                            <div class="text-xs uppercase tracking-[0.18em] text-sky-300/80">Rating</div>
                            <div class="text-2xl font-black">{{ number_format((float) ($school->rating ?? 0), 1) }}</div>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Students</p>
                            <p class="mt-1 text-lg font-bold text-slate-900">{{ number_format((int) ($school->students ?? 0)) }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Teacher list</p>
                            <p class="mt-1 text-lg font-bold text-slate-900">{{ $teacherCount }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Map</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">Google Maps search</p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-[1.75rem] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-slate-900">School location</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $mapQuery }}</p>
                        <div class="mt-4 overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white">
                            <iframe title="{{ $school->name }} map" src="{{ $embedUrl }}" class="h-[22rem] w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-sky-200/40 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-600">Matched teachers</p>
                    <h3 class="mt-2 text-2xl font-black text-slate-900">Teachers linked to this school</h3>
                    <p class="mt-2 text-sm text-slate-500">Teachers matched by institution name or area.</p>

                    <div class="mt-5 max-h-[34rem] space-y-3 overflow-auto pr-1">
                        @forelse($teachers as $teacher)
                            <a href="{{ route('teachers.show', $teacher) }}" class="block rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 transition hover:border-slate-300 hover:bg-white">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">{{ $teacher->name }}</p>
                                        <p class="mt-1 text-xs uppercase tracking-[0.18em] text-slate-500">{{ $teacher->qualification ?? 'Teacher' }}</p>
                                        <p class="mt-2 text-sm text-slate-600">{{ $teacher->subjects ? implode(', ', $teacher->subjects) : ($teacher->subject ?? '') }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-slate-950 px-3 py-2 text-right text-white">
                                        <div class="text-xs uppercase tracking-[0.18em] text-sky-300/80">Rating</div>
                                        <div class="text-lg font-black">{{ number_format((float) ($teacher->rating ?? 0), 1) }}</div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-sm text-slate-500">
                                No teachers found for this school yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>