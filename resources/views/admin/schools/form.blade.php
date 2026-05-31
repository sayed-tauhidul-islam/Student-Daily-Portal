<x-app-layout>
    @php $isEdit = !is_null($school); @endphp
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Admin</p>
            <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">{{ $isEdit ? 'Edit school' : 'Add school' }}</h2>
            <p class="mt-2 max-w-2xl text-sm text-slate-500">Update the school name, area, type, rating, and student count. These fields power the school directory and teacher matching.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <form action="{{ $action }}" method="POST" class="space-y-6 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)] sm:p-8">
                @csrf
                @if($method !== 'POST') @method($method) @endif

                <div class="grid gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="name">School name</label>
                        <input id="name" name="name" value="{{ old('name', $school?->name) }}" placeholder="Boyra Secondary School" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="area">Area</label>
                        <input id="area" name="area" value="{{ old('area', $school?->area) }}" placeholder="Boyra, Khulna" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="type">Type</label>
                        <input id="type" name="type" value="{{ old('type', $school?->type) }}" placeholder="School / College / Madrasah" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="rating">Rating</label>
                        <input id="rating" name="rating" type="number" step="0.1" min="0" max="5" value="{{ old('rating', $school?->rating ?? 0) }}" placeholder="4.5" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="students">Student count</label>
                        <input id="students" name="students" type="number" min="0" value="{{ old('students', $school?->students ?? 0) }}" placeholder="920" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button class="rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-slate-800">Save school</button>
                    <a href="{{ route('admin.schools.index') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back to list</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
