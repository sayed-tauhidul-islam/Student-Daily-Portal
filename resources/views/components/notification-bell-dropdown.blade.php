@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
    $unreadCount = 0;
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
                <p class="mt-1 text-sm text-[color:var(--app-muted)]">Open the notification center to review recent alerts.</p>
            </div>

            <div class="border-t border-slate-200"></div>

            <div class="p-4">
                <a href="{{ route('notifications.index', ['portal' => $activePortal]) }}" class="block rounded-xl bg-slate-950 px-3 py-2 text-center text-sm font-semibold text-white transition hover:bg-slate-800">
                    View all
                </a>
            </div>
        </div>
    </x-slot>
</x-dropdown>

