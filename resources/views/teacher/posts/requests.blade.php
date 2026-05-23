@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-6xl p-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold">Requests for: {{ $post->title }}</h2>
            <a href="{{ route('teacher.posts.index') }}" class="text-sm text-sky-600">Back to posts</a>
        </div>

        <div class="mt-6 space-y-4">
            @forelse($requests as $r)
                <div class="rounded-lg border border-slate-200 p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-semibold">{{ $r->student_name ?? 'Student' }}</p>
                            <p class="text-sm text-slate-600 mt-1">{{ $r->description }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-slate-500">Status: {{ $r->status }}</p>
                            <p class="text-xs text-slate-400">{{ $r->created_at }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-lg border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">No requests for this post yet.</div>
            @endforelse
        </div>
    </div>
@endsection
