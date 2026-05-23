<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Student</p>
            <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Notice</h2>
            <p class="mt-2 text-sm text-slate-500">See published notices from teachers of your school/college.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            @if(!$institute)
                <div class="rounded-[1.75rem] border border-amber-200 bg-gradient-to-r from-amber-50 to-yellow-50 px-5 py-4 text-amber-800 shadow-sm">
                    Complete your profile and select your school/college to see institute notices.
                </div>
            @else
                <div class="mb-5 rounded-[1.75rem] border border-indigo-200/70 bg-gradient-to-r from-indigo-50 to-sky-50 px-5 py-4 text-sm text-slate-700">
                    <span class="font-semibold text-slate-900">Institute:</span> {{ $institute }}
                </div>

                <div class="space-y-4">
                    @forelse($notices as $notice)
                        <article class="rounded-[2rem] border border-slate-200/80 bg-white/95 p-6 shadow-[0_20px_55px_rgba(15,23,42,0.10)] backdrop-blur">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="text-xl font-black text-slate-900">{{ $notice->title }}</h3>
                                <span class="rounded-full bg-gradient-to-r from-slate-100 to-sky-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                    {{ \Illuminate\Support\Carbon::parse($notice->published_at ?? $notice->created_at)->format('d M Y') }}
                                </span>
                            </div>
                            <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $notice->body }}</p>
                        </article>
                    @empty
                        <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                            No notices published yet.
                        </div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
