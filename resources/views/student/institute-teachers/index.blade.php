<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Student</p>
            <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Teachers of My Institute</h2>
            <p class="mt-2 text-sm text-slate-500">Teachers are matched from your school, area, and selected subjects.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(!$institute)
                <div class="rounded-[1.75rem] border border-amber-200 bg-gradient-to-r from-amber-50 to-yellow-50 px-5 py-4 text-amber-800 shadow-sm">
                    Complete your profile and select your school/college to view your institute teachers.
                </div>
            @else
                <div class="mb-5 rounded-[1.75rem] border border-cyan-200/70 bg-gradient-to-r from-cyan-50 to-sky-50 px-5 py-4 text-sm text-slate-700">
                    <span class="font-semibold text-slate-900">Institute:</span> {{ $institute }}
                    <span class="mt-1 block text-slate-600">{{ $matchSummary ?? '' }}</span>
                </div>

                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @forelse($teachers as $teacher)
                        <article class="rounded-[2rem] border border-slate-200/80 bg-white/95 p-6 shadow-[0_20px_55px_rgba(15,23,42,0.10)] backdrop-blur">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-xl font-black text-slate-900">{{ $teacher->name }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">{{ $teacher->qualification ?: 'Teacher' }}</p>
                                </div>
                                <span class="rounded-full bg-gradient-to-r from-slate-900 to-slate-700 px-3 py-1 text-xs font-semibold text-white">{{ number_format((float) ($teacher->rating ?? 0), 1) }}</span>
                            </div>

                            <div class="mt-4 space-y-2 text-sm text-slate-600">
                                <p><span class="font-semibold text-slate-900">Subjects:</span> {{ implode(', ', $teacher->subjects ?? [$teacher->subject ?? '']) }}</p>
                                <p><span class="font-semibold text-slate-900">Area:</span> {{ $teacher->area ?: '-' }}</p>
                                <p><span class="font-semibold text-slate-900">Institute:</span> {{ $teacher->institution ?: '-' }}</p>
                                <p><span class="font-semibold text-slate-900">Availability:</span> {{ $teacher->availability ?: 'Flexible' }}</p>
                                <p><span class="font-semibold text-slate-900">Experience:</span> {{ $teacher->experience ?: 'Not added' }}</p>
                            </div>

                            <div class="mt-5 flex flex-wrap gap-2">
                                <a href="{{ route('teachers.show', $teacher) }}" class="rounded-full border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">View profile</a>
                                @if(!empty($teacher->contact_email))
                                    <a href="mailto:{{ $teacher->contact_email }}" class="rounded-full bg-gradient-to-r from-sky-600 to-cyan-600 px-3 py-2 text-xs font-semibold text-white transition hover:opacity-90">Contact</a>
                                @elseif(!empty($teacher->contact_phone))
                                    <a href="tel:{{ $teacher->contact_phone }}" class="rounded-full bg-gradient-to-r from-sky-600 to-cyan-600 px-3 py-2 text-xs font-semibold text-white transition hover:opacity-90">Contact</a>
                                @else
                                    <span class="rounded-full border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-400">No contact</span>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-[2rem] border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                            No teachers matched your profile yet. Update your area and subjects, or ask your school admin to add teachers.
                        </div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
