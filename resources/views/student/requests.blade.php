<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Tuition marketplace</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Post a tuition request</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500 sm:text-base">Publish requirements and track applications from interested teachers.</p>
            </div>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.14),_transparent_34%),radial-gradient(circle_at_right_bottom,_rgba(37,99,235,0.10),_transparent_28%),linear-gradient(180deg,_#f8fbff_0%,_#eef4fb_100%)] py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8">
            <section class="space-y-6">
                @if (session('success'))
                    <div class="rounded-[1.75rem] border border-emerald-200 bg-gradient-to-r from-emerald-50 to-lime-50 px-5 py-4 text-emerald-800 shadow-sm">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('student.requests.store') }}" class="space-y-5 rounded-[2rem] border border-slate-200/80 bg-white/95 p-6 shadow-[0_22px_55px_rgba(15,23,42,0.10)] backdrop-blur">
                    @csrf
                    <div class="grid gap-5 lg:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Title</label>
                            <input name="title" value="{{ old('title') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100" placeholder="Need a Physics tutor" />
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Subject</label>
                            <select name="subject" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                                <option value="">Select</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject }}" @selected(old('subject') === $subject)>{{ $subject }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-5 lg:grid-cols-3">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Class</label>
                            <select name="class_level" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                                <option value="">Select</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class }}" @selected(old('class_level') === $class)>{{ $class }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Area</label>
                            <select name="area" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                                <option value="">Select</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area }}" @selected(old('area') === $area)>{{ $area }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Budget</label>
                            <input name="budget" type="number" value="{{ old('budget') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100" placeholder="6000" />
                        </div>
                    </div>

                    <div class="grid gap-5 lg:grid-cols-3">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">School</label>
                            <select name="school" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                                <option value="">Select</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school }}" @selected(old('school') === $school)>{{ $school }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Gender preference</label>
                            <select name="gender" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                                <option value="">Any</option>
                                <option value="Male" @selected(old('gender') === 'Male')>Male</option>
                                <option value="Female" @selected(old('gender') === 'Female')>Female</option>
                                <option value="Any" @selected(old('gender') === 'Any')>Any</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Deadline</label>
                            <input name="deadline" type="date" value="{{ old('deadline') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100" />
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Description</label>
                        <textarea name="description" rows="5" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100" placeholder="Explain what kind of tutor you need.">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid gap-5 lg:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Phone</label>
                            <input name="phone" value="{{ old('phone') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100" />
                        </div>
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700">
                            <input type="checkbox" name="online" value="1" @checked(old('online'))>
                            <span>Prefer online teaching</span>
                        </label>
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-slate-900 to-sky-700 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-slate-950/15 transition hover:opacity-90">Publish request</button>
                </form>

                <div class="rounded-[2rem] border border-slate-200/80 bg-white/95 p-6 shadow-[0_22px_55px_rgba(15,23,42,0.10)] backdrop-blur">
                    <h3 class="text-2xl font-black text-slate-900">Your posted requests</h3>
                    <div class="mt-5 space-y-4">
                        @forelse($requests as $request)
                            <article class="rounded-2xl border border-slate-200 bg-gradient-to-r from-slate-50 to-sky-50/60 px-4 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h4 class="text-lg font-bold text-slate-900">{{ $request->title }}</h4>
                                        <p class="mt-1 text-sm text-slate-500">{{ $request->subject }} • {{ $request->class_level }} • {{ $request->area }}</p>
                                    </div>
                                    <span class="rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold text-white">{{ ucfirst($request->status ?? 'pending') }}</span>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $request->description }}</p>
                                <p class="mt-3 text-xs text-slate-500">Applications: {{ count($request->applications ?? []) }}</p>
                            </article>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">No tuition request posted yet.</div>
                        @endforelse
                    </div>
                </div>
            </section>

            <aside class="space-y-6">
                <div class="rounded-[2rem] border border-slate-200/80 bg-white/95 p-6 shadow-[0_22px_55px_rgba(15,23,42,0.10)] backdrop-blur">
                    <h3 class="text-2xl font-black text-slate-900">Public feed</h3>
                    <div class="mt-5 space-y-4">
                        @forelse($feed as $item)
                            <article class="rounded-2xl border border-slate-200 bg-gradient-to-r from-slate-50 to-indigo-50/50 px-4 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h4 class="text-base font-bold text-slate-900">{{ $item->title }}</h4>
                                        <p class="mt-1 text-xs text-slate-500">{{ $item->subject }} • {{ $item->area }} • ৳{{ number_format((int) $item->budget) }}</p>
                                    </div>
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold text-emerald-700">{{ count($item->applications ?? []) }} applications</span>
                                </div>
                                <p class="mt-3 text-sm text-slate-600">{{ $item->description }}</p>
                            </article>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">No public requests yet.</div>
                        @endforelse
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>