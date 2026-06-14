<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] app-accent">Notifications</p>
            <h2 class="mt-2 text-3xl font-black text-[color:var(--app-text)]">All Alerts</h2>
            <p class="mt-2 text-sm app-muted">Latest notices, post requests, and action updates from your portal.</p>
        </div>
    </x-slot>

    <section class="app-surface rounded-2xl p-5">
        @if(($portalFeed ?? collect())->isNotEmpty())
            <div class="mb-6">
                <h3 class="text-lg font-black text-[color:var(--app-text)]">Portal History</h3>
                <div class="mt-3 space-y-3">
                    @foreach($portalFeed as $item)
                        <article class="rounded-2xl border border-[color:var(--app-border)] p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-bold">{{ $item['title'] }}</p>
                                        @unless($item['seen'])
                                            <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-bold uppercase text-rose-700">New</span>
                                        @endunless
                                    </div>
                                    <p class="mt-1 text-sm app-muted">{{ \Illuminate\Support\Str::limit($item['body'], 140) }}</p>
                                    <a href="{{ $item['url'] }}" class="mt-2 inline-block text-xs font-semibold app-primary">Open related page</a>
                                </div>
                                <p class="text-xs app-muted">{{ $item['created_at'] ? \Illuminate\Support\Carbon::parse($item['created_at'])->format('d M Y h:i A') : '' }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="space-y-3">
            @forelse($notifications as $note)
                <div class="rounded-2xl border p-4 {{ $note->read_at ? 'border-[color:var(--app-border)]' : 'border-sky-300 bg-sky-50/20' }}">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="font-bold">{{ ucfirst(str_replace('_', ' ', $note->data['type'] ?? 'Notification')) }}</p>
                            <p class="text-sm app-muted mt-1">{{ $note->data['message'] ?? $note->data['student_name'] ?? 'No details provided.' }}</p>
                            @if(!empty($note->data['url']))
                                <a href="{{ $note->data['url'] }}" class="mt-2 inline-block text-xs font-semibold app-primary">Open related page</a>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-xs app-muted">{{ optional($note->created_at)->format('d M Y h:i A') }}</p>
                            @if(!$note->read_at)
                                <form method="POST" action="{{ route('notifications.read', $note->id) }}" class="mt-2">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold app-primary">Mark as read</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-[color:var(--app-border)] p-6 text-center text-sm app-muted">No notifications yet.</div>
            @endforelse
        </div>

        @if(method_exists($notifications, 'links'))
            <div class="mt-5">
                {{ $notifications->links() }}
            </div>
        @endif
    </section>
</x-app-layout>
