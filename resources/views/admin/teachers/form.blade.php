<x-app-layout>
    @php
        $isEdit = !is_null($teacher);
        $currentImageUrl = $user?->image_url;
        $selectedRole = old('role', $user?->role ?? $defaultRole ?? 'teacher');
        $isHeadTeacher = $selectedRole === 'teacher_admin';
        $backRoute = $isHeadTeacher ? route('admin.head-teachers.index') : route('admin.teachers.index');
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Admin</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">
                    {{ $isEdit ? 'Edit ' . ($isHeadTeacher ? 'head teacher' : 'teacher') . ' profile' : 'Add ' . ($isHeadTeacher ? 'head teacher' : 'teacher') . ' profile' }}
                </h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                    Manage login details, professional profile, and avatar from one focused screen. Use this form for teachers or head teachers depending on the selected role.
                </p>
            </div>
            <div class="flex flex-wrap gap-2 text-xs font-semibold text-slate-600">
                <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5 shadow-sm">Role control</span>
                <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5 shadow-sm">Admin controlled</span>
                <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5 shadow-sm">Image sync enabled</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="overflow-hidden rounded-[2.25rem] border border-slate-200 bg-white shadow-[0_24px_70px_rgba(15,23,42,0.10)]">
                @csrf
                @if($method !== 'POST') @method($method) @endif
                <div class="grid gap-0 lg:grid-cols-[minmax(0,1.6fr)_minmax(320px,0.9fr)]">
                    <div class="space-y-6 border-b border-slate-200 p-6 sm:p-8 lg:border-b-0 lg:border-r">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-black text-slate-900">Identity</h3>
                                <p class="mt-1 text-sm text-slate-500">Login and display information for this teacher account.</p>
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Name</label>
                                <input name="name" value="{{ old('name', $user?->name ?? $teacher?->name) }}" placeholder="Teacher name" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                                <input name="email" value="{{ old('email', $user?->email) }}" placeholder="teacher@example.com" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Password {{ $isEdit ? '(leave blank to keep)' : '' }}</label>
                                <input type="password" name="password" placeholder="Set password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Area</label>
                                <input name="area" value="{{ old('area', $teacher?->area ?? $user?->area) }}" placeholder="Khulna Sadar" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Role</label>
                                <select name="role" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                                    <option value="teacher" @selected($selectedRole === 'teacher')>Teacher</option>
                                    <option value="teacher_admin" @selected($selectedRole === 'teacher_admin')>Head Teacher</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">School / College</label>
                                <input name="school" value="{{ old('school', $user?->school ?? $teacher?->institution) }}" placeholder="Boyra Secondary School" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                                <p class="mt-2 text-xs text-slate-500">This links the account to a school so the correct roster can show it later.</p>
                            </div>
                        </div>

                        <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-5">
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-lg font-black text-slate-900">Professional details</h3>
                                    <p class="mt-1 text-sm text-slate-500">Subject, qualification, and public profile details.</p>
                                </div>
                            </div>

                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Qualification</label>
                                    <input name="qualification" value="{{ old('qualification', $teacher?->qualification) }}" placeholder="MSc, BSc, PhD" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Experience</label>
                                    <input name="experience" value="{{ old('experience', $teacher?->experience) }}" placeholder="5 years" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Primary subject</label>
                                    <input name="subject" value="{{ old('subject', $teacher?->subject) }}" placeholder="Mathematics" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Subjects</label>
                                    <input name="subjects_text" value="{{ old('subjects_text', $teacher ? implode(', ', $teacher->subjects ?? []) : '') }}" placeholder="Math, Physics, ICT" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Salary</label>
                                    <input name="salary" type="number" value="{{ old('salary', $teacher?->salary) }}" placeholder="20000" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Class level</label>
                                    <input name="class_level" value="{{ old('class_level', $teacher?->class_level) }}" placeholder="Class 6-10" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Institution</label>
                                    <input name="institution" value="{{ old('institution', $teacher?->institution) }}" placeholder="School or college name" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Availability</label>
                                    <input name="availability" value="{{ old('availability', $teacher?->availability) }}" placeholder="Evening, weekend" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Rating</label>
                                    <input name="rating" type="number" step="0.1" min="0" max="5" value="{{ old('rating', $teacher?->rating ?? 0) }}" placeholder="4.8" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-3">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Gender</label>
                                <select name="gender" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                                    <option value="">Gender</option>
                                    <option value="Male" @selected(old('gender', $teacher?->gender) === 'Male')>Male</option>
                                    <option value="Female" @selected(old('gender', $teacher?->gender) === 'Female')>Female</option>
                                    <option value="Other" @selected(old('gender', $teacher?->gender) === 'Other')>Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Verification</label>
                                <select name="verification_status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                                    <option value="pending" @selected(old('verification_status', $teacher?->verification_status ?? 'pending') === 'pending')>Pending</option>
                                    <option value="verified" @selected(old('verification_status', $teacher?->verification_status) === 'verified')>Verified</option>
                                    <option value="rejected" @selected(old('verification_status', $teacher?->verification_status) === 'rejected')>Rejected</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <label class="flex w-full items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700">
                                    <input type="checkbox" name="online" value="1" @checked(old('online', $teacher?->online ?? false)) class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                    Online teacher
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Bio</label>
                            <textarea name="bio" rows="6" placeholder="Short bio, teaching style, and highlights" class="w-full rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">{{ old('bio', $teacher?->bio) }}</textarea>
                        </div>

                        <div class="flex flex-wrap gap-3">
                                <button class="rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-slate-950/15 transition hover:bg-slate-800">Save {{ $isHeadTeacher ? 'head teacher' : 'teacher' }}</button>
                                <a href="{{ $backRoute }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back to list</a>
                        </div>
                    </div>

                    <aside class="space-y-6 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 p-6 text-white sm:p-8">
                        <div class="rounded-[1.75rem] border border-white/10 bg-white/5 p-5 backdrop-blur-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200/80">Profile card</p>
                            <div class="mt-4 flex items-center gap-4">
                                <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-[1.75rem] bg-white text-4xl font-black text-slate-950 shadow-xl shadow-black/20 ring-1 ring-white/10">
                                    @if($currentImageUrl)
                                        <img id="teacher-admin-preview" src="{{ $currentImageUrl }}" class="h-full w-full object-cover">
                                        <span id="teacher-admin-fallback" class="hidden">{{ strtoupper(substr($user?->name ?? 'T', 0, 1)) }}</span>
                                    @else
                                        <img id="teacher-admin-preview" src="" class="hidden h-full w-full object-cover">
                                        <span id="teacher-admin-fallback">{{ strtoupper(substr($user?->name ?? 'T', 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-xl font-black">{{ $user?->name ?? 'Teacher profile' }}</h3>
                                    <p class="mt-1 text-sm leading-6 text-white/70">Upload a photo to make the admin card and public profile easier to recognize.</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-[1.75rem] border border-white/10 bg-white/5 p-5 backdrop-blur-sm">
                            <label for="teacher_admin_image" class="inline-flex cursor-pointer items-center justify-center rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-black/20 transition hover:bg-cyan-50">Choose image</label>
                            <input id="teacher_admin_image" type="file" name="image" accept="image/*" class="sr-only">
                            <label class="mt-4 flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white/80">
                                <input type="checkbox" name="remove_image" value="1" class="rounded border-white/20 bg-transparent text-rose-500 focus:ring-rose-500">
                                Remove existing image
                            </label>
                            <p class="mt-3 text-xs leading-6 text-white/55">Supported image uploads sync to the linked user account, so the navbar avatar updates everywhere.</p>
                        </div>

                        <div class="grid gap-3 text-sm text-white/75">
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">Fields are organized for quick admin editing.</div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">Use the right panel to preview and replace avatar images.</div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">Password can be left blank when editing an existing teacher.</div>
                        </div>
                    </aside>
                </div>
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
