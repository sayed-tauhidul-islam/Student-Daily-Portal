<x-app-layout>
    @php
        $panel = $panel ?? (request()->routeIs('teacher-admin.*') ? 'teacher-admin' : 'teacher');
    @endphp
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Teacher</p>
            <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Institute Notice</h2>
            <p class="mt-2 text-sm text-slate-500">Publish notices that students of your institute can view.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if(!$institute)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-amber-800">
                    Complete your teacher profile and set your institute first.
                </div>
            @else
                <form method="POST" action="{{ route($panel.'.notices.store') }}" enctype="multipart/form-data" class="space-y-4 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    @csrf
                    <input name="title" value="{{ old('title') }}" placeholder="Notice title" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" required>
                    <textarea name="body" rows="5" placeholder="Write notice details..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" required>{{ old('body') }}</textarea>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Attach files</label>
                        <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,.xls,.xlsx,.doc,.docx,.txt,.csv,.md" class="w-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-600 file:mr-4 file:rounded-full file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white">
                        <p class="mt-2 text-xs text-slate-500">Images, PDF, Excel, Word, TXT, CSV, and Markdown files. Max 10 files, 10 MB each.</p>
                    </div>
                    <button class="rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white">Publish notice</button>
                </form>

                <form method="GET" class="rounded-[1.5rem] border border-slate-200 bg-white p-4 shadow-[0_12px_30px_rgba(15,23,42,0.06)]">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center">
                        <input name="q" value="{{ $search ?? '' }}" placeholder="Search title or content..." class="flex-1 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                        <div class="flex gap-2">
                            <button class="rounded-2xl bg-slate-950 px-4 py-3.5 text-sm font-semibold text-white">Search</button>
                            @if(!empty($search))
                                <a href="{{ route($panel.'.notices.index') }}" class="rounded-2xl border border-slate-200 px-4 py-3.5 text-sm font-semibold text-slate-700">Reset</a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="space-y-4">
                    @forelse($notices as $notice)
                        <article class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-xl font-black text-slate-900">{{ $notice->title }}</h3>
                                    <p class="mt-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ \Illuminate\Support\Carbon::parse($notice->published_at ?? $notice->created_at)->format('d M Y, h:i A') }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route($panel.'.notices.edit', $notice) }}" class="rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">Edit</a>
                                    <form method="POST" action="{{ route($panel.'.notices.destroy', $notice) }}" data-confirm-delete>
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-full border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700">Delete</button>
                                    </form>
                                </div>
                            </div>
                            <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $notice->body }}</p>
                            @if(!empty($notice->attachments))
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach($notice->attachments as $attachment)
                                        <a href="{{ $attachment['url'] ?? \Illuminate\Support\Facades\Storage::disk('public')->url($attachment['path'] ?? '') }}" target="_blank" class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700">
                                            {{ $attachment['name'] ?? 'Attachment' }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @empty
                        <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                            No notices yet.
                        </div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
