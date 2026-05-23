<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Posts</p>
            <h2 class="mt-2 text-3xl font-black text-slate-900">Your posts</h2>
            <p class="mt-2 text-sm text-slate-500">Manage posts you've published to find students.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4">
            @if(session('success'))
                <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">{{ session('success') }}</div>
            @endif

            <div class="mb-4 text-right">
                <a href="{{ route('teacher.posts.create') }}" class="rounded-2xl bg-slate-950 px-4 py-2 text-sm font-semibold text-white">New post</a>
            </div>

            <div class="space-y-3">
                @forelse($posts as $post)
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $post->title }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $post->category ?? 'General' }} • {{ $post->published_at?->format('M j, Y') ?? '' }}</p>
                            <p class="mt-2 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($post->body, 160) }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <form action="{{ route('teacher.posts.destroy', $post) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-2xl border border-slate-200 px-3 py-2 text-sm text-slate-700">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-500">No posts yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>