<x-app-layout>
    @php
        $avatarUrl = ($profile?->image ?? null) ? \Illuminate\Support\Facades\Storage::disk('public')->url($profile->image) : null;
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Teacher profile</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Complete your tutor profile</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500 sm:text-base">Add your subject, area, fee range, and verification details so students can find and trust you.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M11.3 1.046a1 1 0 00-2.6 0l-.2 1.101a7.002 7.002 0 00-1.47.607l-.93-.62a1 1 0 00-1.32.122l-.62.62a1 1 0 00-.122 1.32l.62.93c-.25.47-.46.96-.607 1.47l-1.101.2a1 1 0 000 2.6l1.101.2c.147.51.357 1 .607 1.47l-.62.93a1 1 0 00.122 1.32l.62.62a1 1 0 001.32.122l.93-.62c.47.25.96.46 1.47.607l.2 1.101a1 1 0 002.6 0l.2-1.101c.51-.147 1-.357 1.47-.607l.93.62a1 1 0 001.32-.122l.62-.62a1 1 0 00.122-1.32l-.62-.93c.25-.47.46-.96.607-1.47l1.101-.2a1 1 0 000-2.6l-1.101-.2a7.002 7.002 0 00-.607-1.47l.62-.93a1 1 0 00-.122-1.32l-.62-.62a1 1 0 00-1.32-.122l-.93.62a7.002 7.002 0 00-1.47-.607l-.2-1.101ZM10 13a3 3 0 100-6 3 3 0 000 6Z" clip-rule="evenodd" />
                    </svg>
                    <span>Settings</span>
                </a>
                <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">Back to dashboard</a>
            </div>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.10),_transparent_34%),linear-gradient(180deg,_#f8fbff_0%,_#eef4fb_100%)] py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('teacher.profile.store') }}" enctype="multipart/form-data" class="space-y-6 rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                @csrf

                <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-5">
                    <div class="flex flex-col gap-5 md:flex-row md:items-center">
                        <label class="flex h-24 w-24 cursor-pointer items-center justify-center overflow-hidden rounded-[1.5rem] bg-slate-900 text-3xl font-black text-white shadow-[0_14px_30px_rgba(15,23,42,0.18)]">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="{{ $profile?->name ?? auth()->user()->name }}" class="h-full w-full object-cover">
                            @else
                                {{ strtoupper(substr($profile?->name ?? auth()->user()->name ?? 'T', 0, 1)) }}
                            @endif
                            <input type="file" name="image" accept="image/*" class="hidden">
                        </label>
                        <div class="flex-1">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Profile image</p>
                            <p class="mt-2 text-sm text-slate-600">Upload a visible profile photo so students can recognize you easily.</p>
                            <div class="mt-4 inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500">
                                JPG, PNG, WEBP up to 2 MB
                            </div>
                        </div>
                    </div>
                    @error('image')
                        <p class="mt-3 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="name">Name</label>
                        <input id="name" name="name" value="{{ old('name', optional($profile)->name ?? auth()->user()->name ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="qualification">Qualification</label>
                        <input id="qualification" name="qualification" value="{{ old('qualification', optional($profile)->qualification ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100" />
                    </div>
                </div>

                <div class="grid gap-5 lg:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="experience">Experience</label>
                        <input id="experience" name="experience" value="{{ old('experience', optional($profile)->experience ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100" placeholder="5 years" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="salary">Expected salary</label>
                        <input id="salary" name="salary" type="number" value="{{ old('salary', optional($profile)->salary ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="area">Area</label>
                        <input id="area" name="area" value="{{ old('area', optional($profile)->area ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100" />
                    </div>
                </div>

                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="subject">Primary subject</label>
                        <select id="subject" name="subject" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                            @foreach($subjects as $subject)
                                <option value="{{ $subject }}" @selected(old('subject', optional($profile)->subject ?? '') === $subject)>{{ $subject }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="availability">Availability</label>
                        <input id="availability" name="availability" value="{{ old('availability', $profile->availability ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100" placeholder="Evening, Weekend, Flexible" />
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700" for="subjects">Subjects</label>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($subjects as $subject)
                            <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700">
                                <input type="checkbox" name="subjects[]" value="{{ $subject }}" @checked(collect(old('subjects', optional($profile)->subjects ?? []))->contains($subject))>
                                <span>{{ $subject }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-5 lg:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="gender">Gender</label>
                        <select id="gender" name="gender" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                            <option value="">Select</option>
                            <option value="Male" @selected(old('gender', optional($profile)->gender ?? '') === 'Male')>Male</option>
                            <option value="Female" @selected(old('gender', optional($profile)->gender ?? '') === 'Female')>Female</option>
                            <option value="Other" @selected(old('gender', optional($profile)->gender ?? '') === 'Other')>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="institution">Institution</label>
                        <input id="institution" name="institution" value="{{ old('institution', optional($profile)->institution ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100" placeholder="Your college, university, or school" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="class_level">Class level</label>
                        <input id="class_level" name="class_level" value="{{ old('class_level', optional($profile)->class_level ?? '') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100" placeholder="Class 6 to HSC" />
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700" for="bio">Bio</label>
                    <textarea id="bio" name="bio" rows="5" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">{{ old('bio', optional($profile)->bio ?? '') }}</textarea>
                </div>

                <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700">
                    <input type="checkbox" name="online" value="1" @checked(old('online', optional($profile)->online ?? false))>
                    <span>Available for online teaching</span>
                </label>

                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-slate-950/15 transition hover:bg-slate-800">
                    Save profile
                </button>
            </form>
        </div>
    </div>
</x-app-layout>