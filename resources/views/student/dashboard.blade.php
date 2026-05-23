<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">TutorLink BD</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Student Dashboard</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500 sm:text-base">
                    Complete your profile, search teachers, and manage tuition requests from one place.
                </p>
            </div>

            <a href="{{ route('student.profile.create') }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                {{ $profile ? 'Update Profile' : 'Complete Profile' }}
            </a>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.15),_transparent_34%),radial-gradient(circle_at_right_bottom,_rgba(37,99,235,0.12),_transparent_28%),linear-gradient(180deg,_#f8fbff_0%,_#eef4fb_100%)] py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if($profile)
                <div class="rounded-[1.75rem] border border-emerald-200 bg-gradient-to-r from-emerald-50 to-lime-50 px-5 py-4 text-emerald-800 shadow-sm">
                    Profile completed successfully.
                </div>
            @else
                <div class="rounded-[1.75rem] border border-sky-200 bg-gradient-to-r from-sky-50 to-cyan-50 px-5 py-4 text-slate-800 shadow-sm">
                    Profile not completed yet.
                    <a href="{{ route('student.profile.create') }}" class="font-semibold underline underline-offset-4">Complete now</a>
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-[2rem] border border-slate-200/80 bg-white/95 p-6 shadow-[0_22px_55px_rgba(15,23,42,0.10)] backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Profile</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">Complete your profile</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Add class, school, subjects, area, phone, and bio so tutors can understand your needs.</p>
                </div>
                <div class="rounded-[2rem] border border-slate-200/80 bg-white/95 p-6 shadow-[0_22px_55px_rgba(15,23,42,0.10)] backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-600">Teachers</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">Find nearby teachers</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Filter by subject, location, and rating to discover the right match.</p>
                </div>
                <div class="rounded-[2rem] border border-slate-200/80 bg-white/95 p-6 shadow-[0_22px_55px_rgba(15,23,42,0.10)] backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-violet-600">Requests</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">Post tuition requests</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Keep track of your active requests and replies in one dashboard.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>