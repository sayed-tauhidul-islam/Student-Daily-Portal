<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] app-accent">New Login Review</p>
            <h2 class="mt-2 text-3xl font-black text-[color:var(--app-text)]">Recent Login Monitor</h2>
            <p class="mt-2 text-sm app-muted">Block, unblock, or delete login records. Blocked users cannot access this school.</p>
        </div>
    </x-slot>

    <section class="app-surface rounded-2xl p-5">
        <div class="space-y-4">
            @forelse($reviews as $review)
                <div class="rounded-2xl border border-[color:var(--app-border)] p-4">
                    <div class="grid gap-4 lg:grid-cols-[0.7fr_0.3fr]">
                        <div>
                            <p class="text-lg font-black">{{ $review->name ?? 'Unknown' }} ({{ ucfirst(str_replace('_',' ', (string) ($review->role ?? 'unknown'))) }})</p>
                            <p class="text-sm app-muted">Email: {{ $review->email ?? 'N/A' }}</p>
                            <p class="text-sm app-muted">School: {{ $review->school ?? 'N/A' }}</p>
                            <p class="text-sm app-muted">Phone: {{ $review->phone ?? 'N/A' }} | Area: {{ $review->area ?? 'N/A' }}</p>
                            <p class="text-xs app-muted mt-1">IP: {{ $review->ip_address ?? 'N/A' }}</p>
                            <p class="text-xs app-muted">Agent: {{ \Illuminate\Support\Str::limit((string) ($review->user_agent ?? 'N/A'), 120) }}</p>
                            <p class="text-xs app-muted">Logged at: {{ optional($review->created_at)->format('d M Y h:i A') }}</p>
                        </div>
                        <div class="flex flex-wrap items-start justify-end gap-2">
                            @if(($review->status ?? 'allowed') !== 'blocked')
                                <form method="POST" action="{{ auth()->user()?->role === 'admin' ? route('admin.login-reviews.block', $review) : route('teacher-admin.login-reviews.block', $review) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-full bg-rose-600 px-4 py-2 text-xs font-semibold text-white">Block</button>
                                </form>
                            @else
                                <form method="POST" action="{{ auth()->user()?->role === 'admin' ? route('admin.login-reviews.unblock', $review) : route('teacher-admin.login-reviews.unblock', $review) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-full bg-emerald-600 px-4 py-2 text-xs font-semibold text-white">Unblock</button>
                                </form>
                            @endif

                            <form method="POST" action="{{ auth()->user()?->role === 'admin' ? route('admin.login-reviews.delete', $review) : route('teacher-admin.login-reviews.delete', $review) }}" class="space-y-2">
                                @csrf
                                @method('DELETE')
                                <label class="flex items-center gap-2 text-xs app-muted">
                                    <input type="checkbox" name="delete_user" value="1" class="rounded border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)]">
                                    Delete user too
                                </label>
                                <button type="submit" data-confirm-delete class="rounded-full border border-[color:var(--app-border)] px-4 py-2 text-xs font-semibold">Delete Record</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm app-muted">No login review records found.</p>
            @endforelse
        </div>
    </section>
</x-app-layout>
