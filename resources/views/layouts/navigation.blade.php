<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-[color:var(--app-border)] bg-[color:var(--app-surface)] backdrop-blur-xl shadow-[0_10px_30px_rgba(15,23,42,0.04)]">
    @php
        $user = Auth::user();
        $role = $user?->role;
        $avatarUrl = null;
        if ($user?->image) {
            $avatarPath = $user->image;
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($avatarPath)) {
                $avatarUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($avatarPath);
            }
        }
        $unreadCount = 0; // Disabled: unreadNotifications() breaks with MongoDB Eloquent (SQL connection unavailable).
        $portalLabel = match ($role) {
            'teacher' => 'Teacher portal',
            'teacher_admin' => 'Head teacher panel',
            'admin' => 'Admin panel',
            default => 'Student portal',
        };
    @endphp
    @php
        $isStudentRole = $role === 'student';
    @endphp
    <!-- Primary Navigation Menu -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @if($isStudentRole)
            <div class="flex h-16 items-center gap-3">
                <button @click="open = !open" type="button" aria-label="Open student menu" class="inline-flex items-center justify-center rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] p-2.5 text-[color:var(--app-text)] shadow-sm transition hover:bg-[color:var(--app-soft)] focus:outline-none focus:ring-4 focus:ring-[color:var(--app-soft)]">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-2xl px-2 py-1.5 transition hover:bg-[color:var(--app-soft)]">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[color:var(--app-primary)] text-white shadow-lg shadow-slate-950/15 ring-1 ring-white/60">
                        <span class="text-xs font-black tracking-[0.18em]">TL</span>
                    </div>
                    <div class="leading-tight">
                        <div class="text-sm font-extrabold tracking-wide text-[color:var(--app-text)]">TutorLink BD</div>
                        <div class="text-[11px] font-medium text-[color:var(--app-muted)]">{{ $portalLabel }}</div>
                    </div>
                </a>
            </div>

            <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 z-[80]">
                <div @click="open = false" class="absolute inset-0 bg-slate-950/40"></div>

                <aside x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed left-0 top-0 h-screen w-[min(20rem,88vw)] overflow-y-auto border-r border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] shadow-[0_24px_70px_rgba(15,23,42,0.22)]">
                    <div class="flex items-center justify-between border-b border-[color:var(--app-border)] px-5 py-4">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[color:var(--app-primary)] text-white shadow-lg shadow-slate-950/15 ring-1 ring-white/60">
                                <span class="text-xs font-black tracking-[0.18em]">TL</span>
                            </div>
                            <div>
                                <div class="text-sm font-extrabold tracking-wide text-[color:var(--app-text)]">TutorLink BD</div>
                                <div class="text-[11px] font-medium text-[color:var(--app-muted)]">{{ $portalLabel }}</div>
                            </div>
                        </a>

                        <button type="button" @click="open = false" class="rounded-full p-2 text-[color:var(--app-muted)] transition hover:bg-[color:var(--app-soft)] hover:text-[color:var(--app-text)]" aria-label="Close student menu">
                            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-4 py-4">
                        @auth
                            <div class="space-y-1">
                                @include('layouts.partials.nav-links-student-responsive')
                            </div>

                            @php
                                $latestNotifications = [];
                                $unreadCount = 0;
                                try {
                                    if ($user) {
                                        $notifiableType = addslashes(get_class($user));
                                        $notifiableId = (string) $user->getKey();
                                        $latestNotifications = \Illuminate\Support\Facades\DB::table('notifications')
                                            ->where('notifiable_type', $notifiableType)
                                            ->where('notifiable_id', $notifiableId)
                                            ->orderBy('created_at', 'desc')
                                            ->limit(5)
                                            ->get();

                                        $unreadCount = \Illuminate\Support\Facades\DB::table('notifications')
                                            ->where('notifiable_type', $notifiableType)
                                            ->where('notifiable_id', $notifiableId)
                                            ->whereNull('read_at')
                                            ->count();
                                    }
                                } catch (\Throwable $e) {
                                    $latestNotifications = [];
                                    $unreadCount = 0;
                                }
                            @endphp

                            <div class="mt-6 rounded-3xl border border-[color:var(--app-border)] bg-[color:var(--app-soft)] p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[color:var(--app-muted)]">Notifications</p>
                                        <p class="mt-1 text-sm font-semibold text-[color:var(--app-text)]">Recent updates</p>
                                    </div>
                                    @if($unreadCount > 0)
                                        <span class="inline-flex items-center justify-center rounded-full bg-rose-600 px-2 py-0.5 text-[10px] font-bold text-white">{{ $unreadCount }}</span>
                                    @endif
                                </div>

                                <div class="mt-3 max-h-52 space-y-2 overflow-auto pr-1">
                                    @forelse($latestNotifications as $note)
                                        @php $data = json_decode($note->data, true) ?? []; @endphp
                                        <a href="{{ $data['url'] ?? route('notifications.index') }}" class="block rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-3 transition hover:bg-[color:var(--app-soft)]">
                                            <p class="text-sm font-medium text-[color:var(--app-text)]">{{ ucfirst(str_replace('_', ' ', $data['type'] ?? 'Notification')) }}</p>
                                            <p class="mt-1 text-xs text-[color:var(--app-muted)]">{{ $data['student_name'] ?? '' }}</p>
                                        </a>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-4 text-sm text-[color:var(--app-muted)]">No notifications</div>
                                    @endforelse
                                </div>

                                <a href="{{ route('notifications.index') }}" class="mt-3 inline-flex text-sm font-semibold text-[color:var(--app-primary)]">View all notifications</a>
                            </div>

                            <div class="mt-6 rounded-3xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[color:var(--app-muted)]">Theme</p>
                                <div class="mt-3 grid grid-cols-3 gap-2 text-xs font-semibold">
                                    <button type="button" @click="window.setDashboardTheme('default')" class="rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-sky-700 transition hover:bg-sky-100">Default</button>
                                    <button type="button" @click="window.setDashboardTheme('light')" class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-emerald-700 transition hover:bg-emerald-100">Light</button>
                                    <button type="button" @click="window.setDashboardTheme('dark')" class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-rose-700 transition hover:bg-rose-100">Dark</button>
                                </div>
                            </div>

                            <div class="mt-6 rounded-3xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] p-4">
                                <div class="text-sm font-semibold text-[color:var(--app-text)]">{{ $user?->name }}</div>
                                <div class="text-xs text-[color:var(--app-muted)]">{{ $user?->email }}</div>

                                <div class="mt-4 space-y-1">
                                    <x-responsive-nav-link :href="route('profile.edit')">
                                        {{ __('Settings') }}
                                    </x-responsive-nav-link>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-responsive-nav-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-responsive-nav-link>
                                    </form>
                                </div>
                            </div>
                        @endauth
                    </div>
                </aside>
            </div>
        @else
            <div class="flex h-16 items-center justify-between gap-4">
                <div class="flex items-center gap-8">
                    <!-- Logo -->
                    <div class="flex shrink-0 items-center">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-2xl px-2 py-1.5 transition hover:bg-[color:var(--app-soft)]">
                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[color:var(--app-primary)] text-white shadow-lg shadow-slate-950/15 ring-1 ring-white/60">
                                <span class="text-xs font-black tracking-[0.18em]">TL</span>
                            </div>
                            <div class="leading-tight">
                                <div class="text-sm font-extrabold tracking-wide text-[color:var(--app-text)]">TutorLink BD</div>
                                <div class="text-[11px] font-medium text-[color:var(--app-muted)]">{{ $portalLabel }}</div>
                            </div>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden items-center gap-2 sm:flex">
                        @auth
                            @if ($role === 'student')
                                @include('layouts.partials.nav-links-student')
                            @elseif ($role === 'teacher')
                                @include('layouts.partials.nav-links-teacher')
                            @elseif ($role === 'teacher_admin')
                                @include('layouts.partials.nav-links-teacher-admin')
                            @elseif ($role === 'admin')
                                @include('layouts.partials.nav-links-admin')
                            @else
                                <x-nav-link :href="route('home')" :active="request()->routeIs('home')">{{ __('Home') }}</x-nav-link>
                            @endif
                        @else
                            <x-nav-link :href="route('home')" :active="request()->routeIs('home')">{{ __('Home') }}</x-nav-link>
                        @endauth
                    </div>
                </div>

                <!-- Notifications + Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:gap-4">
                    @auth
                        @php
                            $latestNotifications = [];
                            $unreadCount = 0;
                            try {
                                if ($user) {
                                    $notifiableType = addslashes(get_class($user));
                                    $notifiableId = (string) $user->getKey();
                                    $latestNotifications = \Illuminate\Support\Facades\DB::table('notifications')
                                        ->where('notifiable_type', $notifiableType)
                                        ->where('notifiable_id', $notifiableId)
                                        ->orderBy('created_at', 'desc')
                                        ->limit(5)
                                        ->get();

                                    $unreadCount = \Illuminate\Support\Facades\DB::table('notifications')
                                        ->where('notifiable_type', $notifiableType)
                                        ->where('notifiable_id', $notifiableId)
                                        ->whereNull('read_at')
                                        ->count();
                                }
                            } catch (\Throwable $e) {
                                $latestNotifications = [];
                                $unreadCount = 0;
                            }
                        @endphp

                        <div class="relative" x-data="{open:false}">
                            <button @click="open = !open" class="inline-flex items-center justify-center rounded-full p-2 hover:bg-slate-100">
                                <!-- bell icon -->
                                <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                @if($unreadCount > 0)
                                    <span class="-ml-2 -mt-3 inline-flex items-center justify-center rounded-full bg-rose-600 px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $unreadCount }}</span>
                                @endif
                            </button>

                            <div x-show="open" @click.outside="open=false" x-cloak class="absolute right-0 mt-2 w-80 rounded-lg border border-slate-200 bg-white shadow-lg z-50">
                                <div class="p-3">
                                    <h4 class="text-sm font-semibold">Notifications</h4>
                                </div>
                                <div class="max-h-64 overflow-auto">
                                    @forelse($latestNotifications as $note)
                                        @php $data = json_decode($note->data, true) ?? []; @endphp
                                        <a href="{{ $data['url'] ?? route('notifications.index') }}" class="block px-4 py-3 hover:bg-slate-50 border-t border-slate-100">
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <p class="text-sm font-medium">{{ ucfirst(str_replace('_', ' ', $data['type'] ?? 'Notification')) }}</p>
                                                    <p class="text-xs text-slate-500 mt-1">{{ $data['student_name'] ?? '' }}</p>
                                                </div>
                                                <div class="text-xs text-slate-400">{{ \Illuminate\Support\Carbon::parse($note->created_at)->diffForHumans() }}</div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="p-4 text-sm text-slate-500">No notifications</div>
                                    @endforelse
                                </div>
                                <div class="border-t border-slate-100 p-2 text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-sm text-sky-600">View all</a>
                                </div>
                            </div>
                        </div>

                        <div class="hidden rounded-full border border-[color:var(--app-border)] bg-[color:var(--app-soft)] px-3 py-1 text-xs font-semibold text-[color:var(--app-primary)] md:inline-flex">
                            Secure session
                        </div>
                        @auth
                            <x-dropdown align="right" width="48">

                                <x-slot name="trigger">
                                    <button type="button" class="group inline-flex items-center gap-3 rounded-full border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-3 py-2.5 text-sm font-medium leading-4 text-[color:var(--app-text)] transition hover:bg-[color:var(--app-soft)] focus:outline-none focus:ring-4 focus:ring-[color:var(--app-soft)]">
                                        <div class="relative">
                                            <div id="nav-avatar" class="flex h-8 w-8 items-center justify-center overflow-hidden rounded-full bg-[color:var(--app-primary)] text-xs font-bold text-white shadow-sm cursor-pointer">
                                                <span id="nav-avatar-fallback">{{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}</span>
                                                <img id="nav-avatar-image" src="{{ $avatarUrl ?? '' }}" alt="{{ $user?->name }}" class="hidden h-full w-full object-cover">
                                            </div>
                                            @if($unreadCount > 0)
                                                <span class="absolute -top-1 -right-1 inline-flex items-center justify-center rounded-full bg-rose-600 px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $unreadCount }}</span>
                                            @endif
                                        </div>
                                        <input id="avatar-input" type="file" accept="image/*" class="hidden" />
                                        <div class="text-left">
                                            <div class="text-sm font-semibold text-[color:var(--app-text)]">{{ $user?->name }}</div>
                                            <div class="text-[11px] text-[color:var(--app-muted)]">Account menu</div>
                                        </div>
                                        <svg class="h-4 w-4 shrink-0 fill-current text-[color:var(--app-muted)] transition group-hover:text-[color:var(--app-text)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Settings') }}
                                    </x-dropdown-link>

                                    <div class="border-t border-slate-200 px-4 py-3">
                                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Theme</p>
                                        <div class="mt-3 grid grid-cols-3 gap-2 text-xs font-semibold">
                                            <button type="button" onclick="window.setDashboardTheme('default')" class="rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-sky-700 transition hover:bg-sky-100">Default</button>
                                            <button type="button" onclick="window.setDashboardTheme('light')" class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-emerald-700 transition hover:bg-emerald-100">Light</button>
                                            <button type="button" onclick="window.setDashboardTheme('dark')" class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-rose-700 transition hover:bg-rose-100">Dark</button>
                                        </div>
                                    </div>

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                            <script>


                                (function(){
                                    const avatar = document.getElementById('nav-avatar');
                                    const input = document.getElementById('avatar-input');
                                    const fallback = document.getElementById('nav-avatar-fallback');
                                    const avatarImage = document.getElementById('nav-avatar-image');
                                    const initialAvatarUrl = @json($avatarUrl);

                                    if (avatarImage && fallback && initialAvatarUrl) {
                                        const preloader = new Image();
                                        preloader.onload = function () {
                                            avatarImage.src = initialAvatarUrl;
                                            avatarImage.classList.remove('hidden');
                                            fallback.classList.add('hidden');
                                        };
                                        preloader.onerror = function () {
                                            avatarImage.classList.add('hidden');
                                            fallback.classList.remove('hidden');
                                        };
                                        preloader.src = initialAvatarUrl;
                                    }

                                    if(avatar && input){
                                        avatar.addEventListener('click', ()=> input.click());
                                        input.addEventListener('change', async (e) => {
                                            const file = e.target.files[0];
                                            if(!file) return;
                                            const form = new FormData();
                                            form.append('image', file);

                                            // small inline feedback
                                            const feedback = document.createElement('div');
                                            feedback.className = 'absolute -bottom-8 left-1/2 -translate-x-1/2 rounded-xl bg-slate-950 px-3 py-1.5 text-xs font-semibold text-white shadow';
                                            feedback.textContent = 'Uploading...';
                                            avatar.parentElement.style.position = 'relative';
                                            avatar.parentElement.appendChild(feedback);

                                            try{
                                                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                                                const res = await fetch('{{ route('teacher.profile.avatar') }}', {
                                                    method: 'POST',
                                                    headers: { 'X-CSRF-TOKEN': token },
                                                    body: form
                                                });

                                                const data = await res.json().catch(() => ({}));

                                                if(res.ok){
                                                    // update avatar in nav
                                                    if (avatarImage) {
                                                        avatarImage.onload = function () {
                                                            avatarImage.classList.remove('hidden');
                                                            fallback?.classList.add('hidden');
                                                        };
                                                        avatarImage.onerror = function () {
                                                            avatarImage.classList.add('hidden');
                                                            fallback?.classList.remove('hidden');
                                                        };
                                                        avatarImage.src = data.url;
                                                    }

                                                    feedback.textContent = 'Uploaded successfully';
                                                    feedback.className = 'absolute -bottom-8 left-1/2 -translate-x-1/2 rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow';
                                                } else {
                                                    feedback.textContent = 'Upload failed';
                                                    feedback.className = 'absolute -bottom-8 left-1/2 -translate-x-1/2 rounded-xl bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white shadow';
                                                }
                                            } catch(err){
                                                feedback.textContent = 'Upload error';
                                                feedback.className = 'absolute -bottom-8 left-1/2 -translate-x-1/2 rounded-xl bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white shadow';
                                                console.error(err);
                                            } finally {
                                                setTimeout(() => feedback.remove(), 2500);
                                                input.value = '';
                                            }
                                        });
                                    }
                                })();
                            </script>
                        @else
                            <div class="flex items-center gap-2">
                                <a href="{{ route('login') }}" class="rounded-full border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-2 text-sm font-semibold text-[color:var(--app-text)] transition hover:bg-[color:var(--app-soft)]">Log in</a>
                                <a href="{{ route('register') }}" class="rounded-full bg-[color:var(--app-primary)] px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-slate-950/10 transition hover:opacity-90">Register</a>
                            </div>
                        @endauth
                    @endauth
                    </div>

                    <!-- Hamburger -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button @click="open = ! open" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white p-2.5 text-slate-500 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-sky-100">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-[color:var(--app-border)] bg-[color:var(--app-surface)] sm:hidden">
        <div class="space-y-1 px-4 pb-3 pt-3">
            @auth
                @if($role === 'student')
                    @include('layouts.partials.nav-links-student-responsive')
                @elseif($role === 'teacher')
                    @include('layouts.partials.nav-links-teacher-responsive')
                @elseif($role === 'teacher_admin')
                    @include('layouts.partials.nav-links-teacher-admin-responsive')
                @elseif($role === 'admin')
                    @include('layouts.partials.nav-links-admin-responsive')
                @else
                    <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">{{ __('Home') }}</x-responsive-nav-link>
                @endif
            @else
                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">{{ __('Home') }}</x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="border-t border-[color:var(--app-border)] pb-2 pt-4">
            <div class="mx-4 rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-3">
                <div class="text-base font-semibold text-[color:var(--app-text)]">{{ $user?->name }}</div>
                <div class="text-sm text-[color:var(--app-muted)]">{{ $user?->email }}</div>
            </div>

            <div class="mx-4 mt-3 rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[color:var(--app-muted)]">Theme</p>
                <div class="mt-3 grid grid-cols-3 gap-2 text-xs font-semibold">
                    <button type="button" onclick="window.setDashboardTheme('default')" class="rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-sky-700 transition hover:bg-sky-100">Default</button>
                    <button type="button" onclick="window.setDashboardTheme('light')" class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-emerald-700 transition hover:bg-emerald-100">Light</button>
                    <button type="button" onclick="window.setDashboardTheme('dark')" class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-rose-700 transition hover:bg-rose-100">Dark</button>
                </div>
            </div>

            <div class="mt-3 space-y-1 px-2">
                @auth
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Settings') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                @else
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Log in') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('register')">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                @endauth
            </div>
        </div>
    </div>
    @endif
</nav>
