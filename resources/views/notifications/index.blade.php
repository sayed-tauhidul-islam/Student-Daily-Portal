@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl p-6">
        <h2 class="text-2xl font-bold">Notifications</h2>

        <div class="mt-4 space-y-3">
            @forelse($notifications as $note)
                <div class="rounded-lg border border-slate-200 p-4 {{ $note->read_at ? 'bg-white' : 'bg-slate-50' }}">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $note->data['type'] ?? 'Notification')) }}</p>
                            <p class="text-sm text-slate-600 mt-1">{{ $note->data['student_name'] ?? '' }}</p>
                            @if(!empty($note->data['url']))
                                <a href="{{ $note->data['url'] }}" class="text-xs text-sky-600">View request</a>
                            @endif
                        </div>
                        <div class="text-right">
                            <form method="POST" action="{{ route('notifications.read', $note->id) }}">
                                @csrf
                                <button class="text-xs text-sky-600">Mark read</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-lg border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">No notifications yet.</div>
            @endforelse
        </div>
    </div>
@endsection
