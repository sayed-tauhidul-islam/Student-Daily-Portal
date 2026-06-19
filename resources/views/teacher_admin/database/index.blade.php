<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Head teacher panel</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">School Database</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500 sm:text-base">All records shown below are restricted to {{ $school !== '' ? $school : 'your assigned school' }}.</p>
            </div>
            <a href="{{ route('teacher-admin.dashboard') }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">Back to dashboard</a>
        </div>
    </x-slot>

    <div class="space-y-6 py-8">
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">School users</p><p class="mt-2 text-3xl font-black text-slate-900">{{ $usersById->count() }}</p></div>
            <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Teachers</p><p class="mt-2 text-3xl font-black text-slate-900">{{ $teachers->count() }}</p></div>
            <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">Students</p><p class="mt-2 text-3xl font-black text-slate-900">{{ $students->count() }}</p></div>
        </div>

        <div class="rounded-[2rem] border border-slate-200 bg-white p-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-2xl font-black text-slate-900">Teachers Table</h3>
                <a href="{{ route('teacher-admin.teachers.create') }}" class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">Add teacher</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b bg-slate-50 text-xs uppercase tracking-[0.2em] text-slate-500">
                        <tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Subject</th><th class="px-4 py-3">Action</th></tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                            @php
                                $user = $usersById[(string) ($teacher->user_id ?? '')] ?? null;
                            @endphp
                            <tr class="border-b">
                                <td class="px-4 py-3">{{ $user?->name ?? $teacher->name ?? 'Teacher' }}</td>
                                <td class="px-4 py-3">{{ $user?->email ?? 'No email' }}</td>
                                <td class="px-4 py-3">{{ $teacher->subject ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <a href="{{ route('teacher-admin.teachers.edit', $teacher) }}" class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">Edit</a>
                                        <form method="POST" action="{{ route('teacher-admin.teachers.destroy', $teacher) }}" data-confirm-delete>
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">No teacher data found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-[2rem] border border-slate-200 bg-white p-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-2xl font-black text-slate-900">Students Table</h3>
                <a href="{{ route('teacher-admin.students.create') }}" class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">Add student</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b bg-slate-50 text-xs uppercase tracking-[0.2em] text-slate-500">
                        <tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Class</th><th class="px-4 py-3">Action</th></tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            @php
                                $user = $usersById[(string) ($student->user_id ?? '')] ?? null;
                            @endphp
                            <tr class="border-b">
                                <td class="px-4 py-3">{{ $user?->name ?? 'Student' }}</td>
                                <td class="px-4 py-3">{{ $user?->email ?? 'No email' }}</td>
                                <td class="px-4 py-3">{{ $student->class ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <a href="{{ route('teacher-admin.students.edit', $student) }}" class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">Edit</a>
                                        <form method="POST" action="{{ route('teacher-admin.students.destroy', $student) }}" data-confirm-delete>
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">No student data found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
