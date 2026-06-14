<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Head teacher panel</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">School control center</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500 sm:text-base">Manage students and teachers only inside your school or college.</p>
            </div>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.10),_transparent_34%),linear-gradient(180deg,_#f8fbff_0%,_#eef4fb_100%)] py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Assigned school</p>
                <h3 class="mt-2 text-2xl font-black text-slate-950">{{ $school !== '' ? $school : 'School not assigned yet' }}</h3>
                <p class="mt-3 text-sm text-slate-500">Super admin can assign your school manually from the teacher management panel.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_18px_50px_rgba(15,23,42,0.08)]"><p class="text-sm text-slate-500">Teachers</p><p class="mt-2 text-3xl font-black text-slate-900">{{ $teacherCount }}</p></div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_18px_50px_rgba(15,23,42,0.08)]"><p class="text-sm text-slate-500">Students</p><p class="mt-2 text-3xl font-black text-slate-900">{{ $studentCount }}</p></div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_18px_50px_rgba(15,23,42,0.08)]"><p class="text-sm text-slate-500">Control teachers</p><p class="mt-2 text-3xl font-black text-slate-900">{{ $pendingTeacherEdits }}</p></div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_18px_50px_rgba(15,23,42,0.08)]"><p class="text-sm text-slate-500">Control students</p><p class="mt-2 text-3xl font-black text-slate-900">{{ $pendingStudentEdits }}</p></div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <a href="{{ route('teacher-admin.teachers.index') }}" class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)] transition hover:-translate-y-0.5 hover:border-sky-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Control teacher</p>
                    <h3 class="mt-2 text-2xl font-black text-slate-950">Manage teachers in your school</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Edit teacher profiles, update details, and delete records inside this institution only.</p>
                </a>

                <a href="{{ route('teacher-admin.students.index') }}" class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)] transition hover:-translate-y-0.5 hover:border-sky-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-600">Control student</p>
                    <h3 class="mt-2 text-2xl font-black text-slate-950">Manage students in your school</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Edit student data, update contact information, or remove records as needed.</p>
                </a>

                <a href="{{ route('teacher-admin.database') }}" class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)] transition hover:-translate-y-0.5 hover:border-sky-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-600">School database</p>
                    <h3 class="mt-2 text-2xl font-black text-slate-950">View all school records</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Open a single panel to review school users, teachers, and students with edit/delete/add access.</p>
                </a>

                <a href="{{ route('teacher-admin.notices.index') }}" class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)] transition hover:-translate-y-0.5 hover:border-sky-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-violet-600">Notice board</p>
                    <h3 class="mt-2 text-2xl font-black text-slate-950">Post institute notices</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Publish text notices with images, PDF, Excel, Word, CSV, TXT, or Markdown attachments.</p>
                </a>

                <a href="{{ route('teacher-admin.messages') }}" class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)] transition hover:-translate-y-0.5 hover:border-sky-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-fuchsia-600">Conversations</p>
                    <h3 class="mt-2 text-2xl font-black text-slate-950">Inspect chats</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Review teacher-student conversations and contact everyone from one secure messenger.</p>
                </a>

                <a href="{{ route('teacher-admin.complaints') }}" class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)] transition hover:-translate-y-0.5 hover:border-sky-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-rose-600">Complaints</p>
                    <h3 class="mt-2 text-2xl font-black text-slate-950">Resolve complaints</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">See complaints from students and teachers, add actions, and publish decision notices.</p>
                </a>

                <a href="{{ route('teacher-admin.leaves') }}" class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)] transition hover:-translate-y-0.5 hover:border-sky-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-indigo-600">Leave management</p>
                    <h3 class="mt-2 text-2xl font-black text-slate-950">Approve leave requests</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Handle advance leave and absence applications from all institute members.</p>
                </a>

                <a href="{{ route('teacher-admin.payments') }}" class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)] transition hover:-translate-y-0.5 hover:border-sky-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-600">Payment center</p>
                    <h3 class="mt-2 text-2xl font-black text-slate-950">Confirm fee and salary</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Mark student tuition clear and teacher salary disbursed month by month.</p>
                </a>

                <a href="{{ route('teacher-admin.search') }}" class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)] transition hover:-translate-y-0.5 hover:border-sky-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-600">Search hub</p>
                    <h3 class="mt-2 text-2xl font-black text-slate-950">Find any user quickly</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Search teacher or student by name and check details, progress, and attendance instantly.</p>
                </a>

                <a href="{{ route('teacher-admin.login-reviews') }}" class="rounded-[2rem] border border-slate-200/80 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)] transition hover:-translate-y-0.5 hover:border-sky-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-600">New logins</p>
                    <h3 class="mt-2 text-2xl font-black text-slate-950">Review new sign-ins</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Monitor recent login users and block/unblock suspicious accounts immediately.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
