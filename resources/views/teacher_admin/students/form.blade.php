<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Head teacher panel</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">{{ $method === 'POST' ? 'Add student' : 'Edit student' }}</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500 sm:text-base">Update student details for {{ $school !== '' ? $school : 'your school' }} only.</p>
            </div>
            <a href="{{ route('teacher-admin.students.index') }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">Back to list</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)] sm:p-8">
                @csrf
                @if($method !== 'POST') @method($method) @endif

                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Name</label>
                        <input name="name" value="{{ old('name', $user?->name) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                        <input name="email" value="{{ old('email', $user?->email) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Password {{ $method !== 'POST' ? '(leave blank to keep)' : '' }}</label>
                        <input type="password" name="password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Class</label>
                        <input name="class" value="{{ old('class', $student?->class) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Group</label>
                        <input name="group" value="{{ old('group', $student?->group) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Area</label>
                        <input name="area" value="{{ old('area', $student?->area ?? $user?->area) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">School</label>
                        <input value="{{ $school }}" disabled class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3.5 text-slate-500" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Preferred teacher</label>
                        <input name="preferred_teacher" value="{{ old('preferred_teacher', $student?->preferred_teacher) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
                    </div>
                    <div class="lg:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Subject list</label>
                        <input name="subject" value="{{ old('subject', $student?->subject) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" placeholder="Math, English, Physics" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Phone</label>
                        <input name="phone" value="{{ old('phone', $student?->phone ?? $user?->phone) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Bio</label>
                    <textarea name="bio" rows="5" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5">{{ old('bio', $student?->bio) }}</textarea>
                </div>

                <button class="rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-slate-950/15">{{ $method === 'POST' ? 'Add student' : 'Save student' }}</button>
            </form>
        </div>
    </div>
</x-app-layout>
