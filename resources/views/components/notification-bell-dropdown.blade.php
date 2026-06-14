@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Notifications\DatabaseNotification;
    use App\Support\PortalNotificationFeed;

    $user = Auth::user();
    $portalFeed = PortalNotificationFeed::forUser($user, 5);
    $databaseUnreadCount = 0;
    try {
        $databaseUnreadCount = $user
            ? DatabaseNotification::query()
                ->where('notifiable_type', get_class($user))
                ->where('notifiable_id', (string) $user->getKey())
                ->whereNull('read_at')
                ->count()
            : 0;
    } catch (\Throwable $throwable) {
        $databaseUnreadCount = 0;
    }
    $unreadCount = $portalFeed->where('seen', false)->count() + $databaseUnreadCount;
    $activePortal = request()->query('portal')
        ?? match (session('active_guard')) {
            'teacher' => 'teacher',
            'teacher_admin' => 'teacher-admin',
            'admin' => 'admin',
            default => 'student',
        };
@endphp

<x-dropdown align="right" width="96">
    <x-slot name="trigger">
        <a href="{{ route('notifications.index', ['portal' => $activePortal]) }}"
           class="relative inline-flex items-center justify-center rounded-full border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-2 py-2 text-sm font-medium leading-4 text-[color:var(--app-text)] transition hover:bg-[color:var(--app-soft)] focus:outline-none focus:ring-4 focus:ring-[color:var(--app-soft)]">
            <svg class="h-5 w-5 text-[color:var(--app-muted)] group-hover:text-[color:var(--app-text)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10 2a6 6 0 00-6 6v2.586l-.707.707A1 1 0 004 13h12a1 1 0 00.707-1.707L16 12.586V8a6 6 0 00-6-6z" />
                <path d="M7 16a3 3 0 006 0H7z" />
            </svg>

            @if($unreadCount > 0)
                <span class="absolute -top-1 -right-1 inline-flex items-center justify-center rounded-full bg-rose-600 px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $unreadCount }}</span>
            @endif
        </a>
    </x-slot>

    <x-slot name="content">
        <div class="w-80">
            <div class="px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Notifications</p>
                <p class="mt-1 text-sm text-[color:var(--app-muted)]">Recent school notices and portal alerts.</p>
            </div>

            <div class="border-t border-slate-200"></div>

            <div class="max-h-72 overflow-y-auto p-3">
                @forelse($portalFeed as $item)
                    <a href="{{ route('notifications.index', ['portal' => $activePortal]) }}" class="block rounded-xl px-3 py-2 transition hover:bg-slate-100">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-bold text-slate-900">{{ $item['title'] }}</p>
                            @unless($item['seen'])
                                <span class="mt-1 h-2 w-2 rounded-full bg-rose-500"></span>
                            @endunless
                        </div>
                        <p class="mt-1 text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($item['body'], 72) }}</p>
                    </a>
                @empty
                    <p class="px-3 py-4 text-sm text-slate-500">No notifications yet.</p>
                @endforelse
            </div>

            <div class="p-4">
                <a href="{{ route('notifications.index', ['portal' => $activePortal]) }}" class="block rounded-xl bg-slate-950 px-3 py-2 text-center text-sm font-semibold text-white transition hover:bg-slate-800">
                    View all
                </a>
            </div>
        </div>
    </x-slot>
</x-dropdown>

