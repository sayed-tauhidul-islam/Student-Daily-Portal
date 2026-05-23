<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">School directory</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Khulna school ratings</h2>
                <p class="mt-2 max-w-3xl text-sm text-slate-500 sm:text-base">Search by school name or area and compare institutions by rating, type, and student count.</p>
            </div>

            <form class="flex w-full max-w-xl gap-3" method="GET" action="{{ route('schools.index') }}">
                <input type="text" name="q" value="{{ $query }}" class="w-full rounded-full border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100" placeholder="Search schools or areas">
                <button class="rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Search</button>
            </form>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.10),_transparent_34%),linear-gradient(180deg,_#f8fbff_0%,_#eef4fb_100%)] py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-5 flex flex-wrap gap-2">
                @foreach(range('A', 'Z') as $letter)
                    <a href="#school-{{ $letter }}" class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">{{ $letter }}</a>
                @endforeach
            </div>

            @php
                $groupedSchools = $schools->groupBy(function ($school) {
                    return strtoupper(substr($school->name ?? 'S', 0, 1));
                })->sortKeys();
            @endphp

            <div class="space-y-8">
                @forelse($groupedSchools as $schoolInitial => $schoolGroup)
                    <section id="school-{{ $schoolInitial }}" class="scroll-mt-28">
                        <div class="mb-3 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-950 text-sm font-black text-white">{{ $schoolInitial }}</div>
                            <div>
                                <h3 class="text-2xl font-black text-slate-900">{{ $schoolInitial }} group</h3>
                                <p class="text-sm text-slate-500">Schools starting with {{ $schoolInitial }}</p>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @foreach($schoolGroup as $school)
                                <article class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">{{ $school->type }}</p>
                                            <h4 class="mt-2 text-xl font-bold text-slate-900">{{ $school->name }}</h4>
                                            <p class="mt-2 text-sm text-slate-500">{{ $school->area }}</p>
                                        </div>
                                        <div class="rounded-2xl bg-slate-950 px-3 py-2 text-right text-white">
                                            <div class="text-xs uppercase tracking-[0.18em] text-sky-300/80">Rating</div>
                                            <div class="text-lg font-black">{{ number_format((float) $school->rating, 1) }}</div>
                                        </div>
                                    </div>

                                    <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                            <div class="text-slate-500">Students</div>
                                            <div class="mt-1 font-bold text-slate-900">{{ number_format((int) ($school->students ?? 0)) }}</div>
                                        </div>
                                        <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                            <div class="text-slate-500">Category</div>
                                            <div class="mt-1 font-bold text-slate-900">{{ $school->type }}</div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                        No schools found for this search.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
