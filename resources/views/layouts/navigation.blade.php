@php
    $user = Auth::user();
    $role = $user?->role;
    $avatarUrl = $user?->image_url;
    $dashboardPortal = match ($role) {
        'teacher' => 'teacher',
        'teacher_admin' => 'teacher-admin',
        'admin', 'super_admin' => 'admin',
        default => 'student',
    };
    $portalLabel = match ($role) {
        'teacher' => 'Teacher portal',
        'teacher_admin' => 'Head teacher panel',
        'admin', 'super_admin' => 'Admin panel',
        default => 'Student portal',
    };
@endphp

<div x-show="menuOpen" x-cloak class="fixed inset-0 z-[55]">
    <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-sm" @click="menuOpen = false"></div>

    <aside class="absolute left-0 top-0 h-full w-[min(22rem,88vw)] overflow-hidden border-r border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] shadow-[0_30px_80px_rgba(15,23,42,0.32)]">
        <div class="flex items-center justify-between border-b border-[color:var(--app-border)] px-5 py-4">
            <a href="{{ route('dashboard', ['portal' => $dashboardPortal]) }}" class="flex items-center gap-3">
                <div class="brand-badge">TL</div>
                <div>
                    <p class="text-lg font-black text-[color:var(--app-text)]">TutorLink BD</p>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[color:var(--app-muted)]">{{ $portalLabel }}</p>
                </div>
            </a>
            <button type="button" @click="menuOpen = false" class="topbar-icon-btn" aria-label="Close menu">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex h-[calc(100%-4.5rem)] flex-col">
            <div class="flex-1 overflow-y-auto px-4 py-4">
                <div class="rounded-[1.5rem] border border-[color:var(--app-border)] bg-[color:var(--app-soft)]/30 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[color:var(--app-muted)]">Quick Links</p>
                    <p class="mt-1 text-sm text-[color:var(--app-muted)]">All portal tools are inside this menu.</p>
                </div>

                <div class="mt-4 space-y-2">
                    @if ($role === 'student')
                        @include('layouts.partials.nav-links-student-responsive')
                    @elseif ($role === 'teacher')
                        @include('layouts.partials.nav-links-teacher-responsive')
                    @elseif ($role === 'teacher_admin')
                        @include('layouts.partials.nav-links-teacher-admin-responsive')
                    @elseif ($role === 'admin' || $role === 'super_admin')
                        @include('layouts.partials.nav-links-admin-responsive')
                    @else
                        <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">{{ __('Home') }}</x-responsive-nav-link>
                    @endif
                </div>
            </div>

            <div class="border-t border-[color:var(--app-border)] px-4 py-4">
                <div class="mb-3 flex gap-2">
                    <button type="button" onclick="window.setDashboardTheme('dark')" class="sidebar-theme-btn">Dark</button>
                    <button type="button" onclick="window.setDashboardTheme('light')" class="sidebar-theme-btn">Light</button>
                    <button type="button" onclick="window.setDashboardTheme('default')" class="sidebar-theme-btn">Retro</button>
                </div>

                <div class="sidebar-user-card">
                    <a href="{{ route('profile.edit', ['portal' => $dashboardPortal]) }}" class="user-avatar overflow-hidden" aria-label="Open profile settings">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="{{ $user?->name }}" class="h-full w-full object-cover">
                        @else
                            {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                        @endif
                    </a>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-[color:var(--app-text)]">{{ $user?->name }}</p>
                        <p class="truncate text-xs text-[color:var(--app-muted)]">{{ $user?->area ?? $user?->email }}</p>
                        <a href="{{ route('profile.edit', ['portal' => $dashboardPortal]) }}" class="mt-2 inline-flex items-center gap-2 rounded-full border border-[color:var(--sidebar-card-border)] bg-[color:var(--sidebar-card-bg)] px-3 py-1.5 text-xs font-semibold text-[color:var(--app-text)] transition hover:bg-[color:var(--app-soft)]">
                            <span class="inline-block h-2 w-2 rounded-full bg-[color:var(--app-primary)]"></span>
                            Profile Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</div>
