<x-app-layout>
    @php
        $isEdit = !is_null($student);
        $currentImageUrl = $user?->image_url;
    @endphp

    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Admin</p>
            <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">{{ $isEdit ? 'Edit student' : 'Add student' }}</h2>
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
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Password {{ $isEdit ? '(leave blank to keep)' : '' }}</label>
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
                        <select name="school" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5">
                            <option value="">Select</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->name }}" @selected(old('school', $student?->school) === $school->name)>{{ $school->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Preferred teacher</label>
                        <select name="preferred_teacher" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5">
                            <option value="">Select</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->name }}" @selected(old('preferred_teacher', $student?->preferred_teacher) === $teacher->name)>{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Subjects (comma separated)</label>
                        <input name="subjects_text" value="{{ old('subjects_text', $student ? implode(', ', $student->subjects ?? []) : $student?->subject) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" placeholder="Math, English, Physics" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Phone</label>
                        <input name="phone" value="{{ old('phone', $student?->phone ?? $user?->phone) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
                    </div>
                </div>

                <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-5">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center">
                        <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-[1.5rem] bg-slate-900 text-3xl font-black text-white">
                            @if($currentImageUrl)
                                <img id="student-admin-preview" src="{{ $currentImageUrl }}" class="h-full w-full object-cover">
                                <span id="student-admin-fallback" class="hidden">{{ strtoupper(substr($user?->name ?? 'S', 0, 1)) }}</span>
                            @else
                                <img id="student-admin-preview" src="" class="hidden h-full w-full object-cover">
                                <span id="student-admin-fallback">{{ strtoupper(substr($user?->name ?? 'S', 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label class="inline-flex cursor-pointer items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-950/15 transition hover:bg-slate-800" for="student_admin_image">Choose image</label>
                            <input id="student_admin_image" type="file" name="image" accept="image/*" class="sr-only">
                            <label class="mt-3 flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300">
                                Remove existing image
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Bio</label>
                    <textarea name="bio" rows="5" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5">{{ old('bio', $student?->bio) }}</textarea>
                </div>

                <button class="rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-slate-950/15">Save</button>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const input = document.getElementById('student_admin_image');
            const preview = document.getElementById('student-admin-preview');
            const fallback = document.getElementById('student-admin-fallback');
            if (!input || !preview) return;
            input.addEventListener('change', () => {
                const file = input.files?.[0];
                if (!file) return;
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
                fallback?.classList.add('hidden');
            });
        })();
    </script>
</x-app-layout>
