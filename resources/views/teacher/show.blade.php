<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Teacher</p>
            <h2 class="mt-2 text-3xl font-black text-slate-900">{{ $teacher->name }}</h2>
            <p class="mt-2 text-sm text-slate-500">{{ $teacher->qualification ?? '' }} • {{ $teacher->experience ?? '' }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <div class="flex items-start gap-4">
                    <div class="h-24 w-24 overflow-hidden rounded-lg bg-slate-100">
                        @if(!empty($teacher->image))
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($teacher->image) }}" alt="{{ $teacher->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-xl font-bold text-slate-600">{{ strtoupper(substr($teacher->name ?? 'T', 0, 1)) }}</div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-semibold">{{ $teacher->name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ implode(', ', $teacher->subjects ?? [$teacher->subject ?? '']) }}</p>
                        <p class="mt-2 text-sm text-slate-600">{{ $teacher->bio ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>