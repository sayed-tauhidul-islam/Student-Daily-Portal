<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Post</p>
            <h2 class="mt-2 text-3xl font-black text-slate-900">Create a post to find students</h2>
            <p class="mt-2 text-sm text-slate-500">Create a short post describing the students you are looking for. This will appear on student dashboards.</p>
        </div>
    </x-slot>

    @php
        $subjects = \App\Support\Lists\SubjectList::allNames();
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4">
            @if(session('success'))
                <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">{{ session('success') }}</div>
            @endif

            <form action="{{ route('teacher.posts.store') }}" method="POST" class="space-y-4 rounded-2xl bg-white p-6">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Title</label>
                    <input name="title" required class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" />
                </div>
                <div class="grid gap-4 lg:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Category / Subject</label>
                        <select name="category" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                            <option value="">Select</option>
                            @foreach($subjects as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Class level</label>
                        <input name="class_level" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" placeholder="e.g. Class 6 - HSC" />
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Experience in subject (years)</label>
                        <input name="experience" type="number" min="0" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Budget (optional)</label>
                        <input name="budget" type="number" min="0" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Description</label>
                    <textarea name="body" rows="6" required class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3"></textarea>
                </div>

                <div class="grid gap-4 lg:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Tags (comma separated)</label>
                        <input name="tags" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" placeholder="e.g. SSC,Math,Tuition" />
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">
                            <input type="checkbox" name="online" value="1">
                            <span>Available for online teaching</span>
                        </label>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <a href="{{ route('teacher.dashboard') }}" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm">Cancel</a>
                    <button class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-semibold text-white">Publish post</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>