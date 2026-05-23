<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Admin</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Teachers</h2>
            </div>
            <a href="{{ route('admin.teachers.create') }}" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">Add teacher</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800">{{ session('success') }}</div>
            @endif

            <form method="GET" class="mb-6 rounded-[1.5rem] border border-slate-200 bg-white p-4 shadow-[0_12px_30px_rgba(15,23,42,0.06)]">
                <div class="flex flex-col gap-3 md:flex-row md:items-center">
                    <input name="q" value="{{ $search ?? '' }}" placeholder="Search by name, email, subject, institution, area..." class="flex-1 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                    <div class="flex gap-2">
                        <button class="rounded-2xl bg-slate-950 px-4 py-3.5 text-sm font-semibold text-white">Search</button>
                        @if(!empty($search))
                            <a href="{{ route('admin.teachers.index') }}" class="rounded-2xl border border-slate-200 px-4 py-3.5 text-sm font-semibold text-slate-700">Reset</a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-4 py-3">Teacher</th><th class="px-4 py-3">Profile</th><th class="px-4 py-3">Area</th><th class="px-4 py-3">Actions</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($teachers as $teacher)
                                @php $user = \App\Models\User::find($teacher->user_id); @endphp
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-slate-900">{{ $user?->name ?? $teacher->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $user?->email ?? '' }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">{{ $teacher->subject }} • {{ $teacher->rating ?? 0 }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $teacher->area }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('admin.teachers.edit', $teacher) }}" class="rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">Edit</a>
                                            <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" data-confirm-delete>@csrf @method('DELETE')<button class="rounded-full border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700">Delete</button></form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-10 text-center text-slate-500">No teachers found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
