<x-app-layout>
    @php($panel = $panel ?? (request()->routeIs('teacher-admin.*') ? 'teacher-admin' : 'teacher'))
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Teacher</p>
            <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Edit Notice</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route($panel.'.notices.update', $notice) }}" enctype="multipart/form-data" class="space-y-4 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                @csrf
                @method('PUT')

                <input name="title" value="{{ old('title', $notice->title) }}" placeholder="Notice title" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" required>
                <textarea name="body" rows="8" placeholder="Notice details" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5" required>{{ old('body', $notice->body) }}</textarea>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Add attachments</label>
                    <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,.xls,.xlsx,.doc,.docx,.txt,.csv,.md" class="w-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-600 file:mr-4 file:rounded-full file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white">
                </div>

                @if(!empty($notice->attachments))
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-slate-700">Current attachments</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($notice->attachments as $attachment)
                                <a href="{{ $attachment['url'] ?? \Illuminate\Support\Facades\Storage::disk('public')->url($attachment['path'] ?? '') }}" target="_blank" class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700">
                                    {{ $attachment['name'] ?? 'Attachment' }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex gap-3">
                    <button class="rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white">Save</button>
                    <a href="{{ route($panel.'.notices.index') }}" class="rounded-2xl border border-slate-200 px-5 py-3.5 text-sm font-semibold text-slate-700">Back</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
