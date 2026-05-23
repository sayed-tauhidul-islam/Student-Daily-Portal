<x-app-layout>
    @php
        $isEdit = !is_null($rating);
        $targetType = old('target_type', $rating?->target_type ?? 'teacher');
        $targetValue = old('target_id', $rating?->target_id);
    @endphp
    <x-slot name="header"><div><p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Admin</p><h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">{{ $isEdit ? 'Edit rating' : 'Add rating' }}</h2></div></x-slot>
    <div class="py-8"><div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8"><form action="{{ $action }}" method="POST" class="space-y-4 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">@csrf @if($method !== 'POST') @method($method) @endif <select name="target_type" id="rating-target-type" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5"><option value="teacher" @selected($targetType === 'teacher')>Teacher</option><option value="school" @selected($targetType === 'school')>School / College</option></select><input name="target_id" id="rating-target-id" value="{{ $targetValue }}" placeholder="Search and choose name" list="teacher-target-list" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5"><datalist id="teacher-target-list">@foreach($teacherNames as $name)<option value="{{ $name }}">@endforeach</datalist><datalist id="school-target-list">@foreach($schoolNames as $name)<option value="{{ $name }}">@endforeach</datalist><input type="number" step="0.1" min="1" max="5" name="rating" value="{{ old('rating', $rating?->rating ?? 5) }}" placeholder="Rating" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5"><textarea name="comment" rows="5" placeholder="Comment" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5">{{ old('comment', $rating?->comment) }}</textarea><label class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5"><input type="checkbox" name="verified" value="1" @checked(old('verified', $rating?->verified ?? false))> Verified</label><button class="rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white">Save</button></form></div></div>

    <script>
        (() => {
            const targetType = document.getElementById('rating-target-type');
            const targetInput = document.getElementById('rating-target-id');
            const teacherList = document.getElementById('teacher-target-list');
            const schoolList = document.getElementById('school-target-list');

            if (!targetType || !targetInput || !teacherList || !schoolList) {
                return;
            }

            const syncList = () => {
                const isSchool = targetType.value === 'school';
                targetInput.setAttribute('list', isSchool ? 'school-target-list' : 'teacher-target-list');
                targetInput.placeholder = isSchool ? 'Search school or college name' : 'Search teacher name';
            };

            targetType.addEventListener('change', syncList);
            syncList();
        })();
    </script>
</x-app-layout>
