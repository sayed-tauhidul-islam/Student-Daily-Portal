<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Head teacher panel</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Teacher profile</h2>
                <p class="mt-2 text-sm text-slate-500">{{ $school }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('teacher-admin.teachers.edit', $teacher) }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Edit profile</a>
                <a href="{{ route('teacher-admin.teachers.index') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 py-6">
        <section class="rounded-3xl border border-slate-200 bg-white p-6">
            <div class="flex flex-col gap-5 md:flex-row md:items-center">
                <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-3xl bg-slate-900 text-3xl font-black text-white">
                    @if($user?->image_url)
                        <img src="{{ $user->image_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                    @else
                        {{ strtoupper(substr($user?->name ?? $teacher->name ?? 'T', 0, 1)) }}
                    @endif
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-black text-slate-900">{{ $user?->name ?? $teacher->name ?? 'Teacher' }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $user?->email ?? 'No email' }} | {{ $teacher->institution ?? '-' }}</p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-4">
                        <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Subject</p><p class="font-bold text-slate-900">{{ $teacher->subject ?? '-' }}</p></div>
                        <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Qualification</p><p class="font-bold text-slate-900">{{ $teacher->qualification ?? '-' }}</p></div>
                        <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Experience</p><p class="font-bold text-slate-900">{{ $teacher->experience ?? '-' }}</p></div>
                        <div class="rounded-2xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Area</p><p class="font-bold text-slate-900">{{ $teacher->area ?? '-' }}</p></div>
                    </div>
                </div>
            </div>
            <div class="mt-5 grid gap-3 md:grid-cols-3">
                <div class="rounded-2xl bg-slate-50 p-4"><p class="text-xs text-slate-500">Salary</p><p class="font-bold text-slate-900">{{ $teacher->salary ?? 0 }}</p></div>
                <div class="rounded-2xl bg-slate-50 p-4"><p class="text-xs text-slate-500">Availability</p><p class="font-bold text-slate-900">{{ $teacher->availability ?? '-' }}</p></div>
                <div class="rounded-2xl bg-slate-50 p-4"><p class="text-xs text-slate-500">Verification</p><p class="font-bold text-slate-900">{{ ucfirst($teacher->verification_status ?? 'pending') }}</p></div>
            </div>
            @if($teacher->bio)
                <p class="mt-5 rounded-2xl bg-slate-50 p-4 text-sm leading-6 text-slate-600">{{ $teacher->bio }}</p>
            @endif
        </section>
    </div>
</x-app-layout>
