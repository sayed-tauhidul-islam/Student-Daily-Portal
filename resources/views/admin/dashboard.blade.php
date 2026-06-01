<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Super admin control center</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Platform administration</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500 sm:text-base">Manage schools, colleges, head teachers, and system access from one private console.</p>
            </div>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.10),_transparent_34%),linear-gradient(180deg,_#f8fbff_0%,_#eef4fb_100%)] py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_18px_50px_rgba(15,23,42,0.08)]"><p class="text-sm text-slate-500">Users</p><p class="mt-2 text-3xl font-black text-slate-900">{{ $users }}</p></div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_18px_50px_rgba(15,23,42,0.08)]"><p class="text-sm text-slate-500">Students</p><p class="mt-2 text-3xl font-black text-slate-900">{{ $students }}</p></div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_18px_50px_rgba(15,23,42,0.08)]"><p class="text-sm text-slate-500">Teachers</p><p class="mt-2 text-3xl font-black text-slate-900">{{ $teachers }}</p></div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_18px_50px_rgba(15,23,42,0.08)]"><p class="text-sm text-slate-500">Head teachers</p><p class="mt-2 text-3xl font-black text-slate-900">{{ $headTeachersCount }}</p></div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    <h3 class="text-2xl font-black text-slate-900">Moderation summary</h3>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">Verified teachers: <span class="font-bold text-slate-900">{{ $verifiedTeachers }}</span></div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">Pending requests: <span class="font-bold text-slate-900">{{ $pendingRequests }}</span></div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">Ratings: <span class="font-bold text-slate-900">{{ $ratings }}</span></div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">Schools: <span class="font-bold text-slate-900">{{ $schools }}</span></div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">Subjects: <span class="font-bold text-slate-900">{{ $subjects }}</span></div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">Groups: <span class="font-bold text-slate-900">{{ $groups }}</span></div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    <h3 class="text-2xl font-black text-slate-900">Admin actions</h3>
                    <div class="mt-5 space-y-3 text-sm text-slate-600">
                        <a href="{{ route('admin.students.index') }}" class="block rounded-2xl bg-slate-50 px-4 py-3">Manage students and student images.</a>
                        <a href="{{ route('admin.teachers.index') }}" class="block rounded-2xl bg-slate-50 px-4 py-3">Manage teachers and teacher images.</a>
                        <a href="{{ route('admin.head-teachers.index') }}" class="block rounded-2xl bg-slate-50 px-4 py-3">Add and review head teachers.</a>
                        <a href="{{ route('admin.schools.index') }}" class="block rounded-2xl bg-slate-50 px-4 py-3">Manage schools, subjects, groups, and ratings.</a>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Schools</p>
                            <h3 class="mt-2 text-2xl font-black text-slate-900">School roster</h3>
                        </div>
                        <a href="{{ route('admin.schools.create') }}" class="rounded-full bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Add school</a>
                    </div>
                    <div class="mt-5 space-y-3">
                        @forelse($schoolNames as $school)
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">{{ $school->name }}</div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-sm text-slate-500">No school records yet.</div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-600">Colleges</p>
                            <h3 class="mt-2 text-2xl font-black text-slate-900">College roster</h3>
                        </div>
                        <a href="{{ route('admin.teachers.create', ['role' => 'teacher_admin']) }}" class="rounded-full bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Add head teacher</a>
                    </div>
                    <div class="mt-5 space-y-3">
                        @forelse($collegeNames as $college)
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">{{ $college->name }}</div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-sm text-slate-500">No college records yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>