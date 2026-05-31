<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Head teacher panel</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">{{ $method === 'POST' ? 'Add teacher' : 'Edit teacher' }}</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500 sm:text-base">Update teacher details for {{ $school !== '' ? $school : 'your school' }} only.</p>
            </div>
            <a href="{{ route('teacher-admin.teachers.index') }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">Back to list</a>
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
                        <input name="name" value="{{ old('name', $user?->name ?? $teacher?->name) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
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
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Area</label>
                        <input name="area" value="{{ old('area', $teacher?->area ?? $user?->area) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Institution</label>
                        <input value="{{ $school }}" disabled class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3.5 text-slate-500" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Primary subject</label>
                        <input name="subject" value="{{ old('subject', $teacher?->subject) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Qualification</label>
                        <input name="qualification" value="{{ old('qualification', $teacher?->qualification) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Experience</label>
                        <input name="experience" value="{{ old('experience', $teacher?->experience) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Salary</label>
                        <input name="salary" type="number" value="{{ old('salary', $teacher?->salary) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Class level</label>
                        <input name="class_level" value="{{ old('class_level', $teacher?->class_level) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Availability</label>
                        <input name="availability" value="{{ old('availability', $teacher?->availability) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Rating</label>
                        <input name="rating" type="number" step="0.1" min="0" max="5" value="{{ old('rating', $teacher?->rating ?? 0) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Verification</label>
                        <select name="verification_status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5">
                            <option value="pending" @selected(old('verification_status', $teacher?->verification_status ?? 'pending') === 'pending')>Pending</option>
                            <option value="verified" @selected(old('verification_status', $teacher?->verification_status) === 'verified')>Verified</option>
                            <option value="rejected" @selected(old('verification_status', $teacher?->verification_status) === 'rejected')>Rejected</option>
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Subjects (comma separated)</label>
                        <input name="subjects_text" value="{{ old('subjects_text', $teacher ? implode(', ', $teacher->subjects ?? []) : '') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" placeholder="Math, Physics, ICT" />
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Bio</label>
                    <textarea name="bio" rows="5" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5">{{ old('bio', $teacher?->bio) }}</textarea>
                </div>

                <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-[1.5rem] bg-slate-900 text-3xl font-black text-white">
                            @php($currentImageUrl = $user?->image_url)
                            @if($currentImageUrl)
                                <img id="teacher-admin-preview" src="{{ $currentImageUrl }}" class="h-full w-full object-cover">
                                <span id="teacher-admin-fallback" class="hidden">{{ strtoupper(substr($user?->name ?? 'T', 0, 1)) }}</span>
                            @else
                                <img id="teacher-admin-preview" src="" class="hidden h-full w-full object-cover">
                                <span id="teacher-admin-fallback">{{ strtoupper(substr($user?->name ?? 'T', 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label class="inline-flex cursor-pointer items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-950/15 transition hover:bg-slate-800" for="teacher_admin_image">Choose image</label>
                            <input id="teacher_admin_image" type="file" name="image" accept="image/*" class="sr-only">
                            <label class="mt-3 flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300">
                                Remove existing image
                            </label>
                        </div>
                    </div>
                </div>

                <button class="rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-slate-950/15">{{ $method === 'POST' ? 'Add teacher' : 'Save teacher' }}</button>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const input = document.getElementById('teacher_admin_image');
            const preview = document.getElementById('teacher-admin-preview');
            const fallback = document.getElementById('teacher-admin-fallback');
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
