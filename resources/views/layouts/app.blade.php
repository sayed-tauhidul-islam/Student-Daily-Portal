<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Student Daily Portal</title>

        <link rel="icon" type="image/svg+xml" href="{{ asset('tb-favicon.svg') }}">
        <link rel="shortcut icon" href="{{ asset('tb-favicon.svg') }}">
        <link rel="apple-touch-icon" href="{{ asset('tb-favicon.svg') }}">

        <script>
            (function () {
                const storageKey = 'dashboard-theme';
                const theme = localStorage.getItem(storageKey) || 'default';

                document.documentElement.dataset.theme = theme;
                window.setDashboardTheme = function (nextTheme) {
                    document.documentElement.dataset.theme = nextTheme;
                    localStorage.setItem(storageKey, nextTheme);
                };
            })();
        </script>

        <style>
            :root {
                --app-bg: #101826;
                --app-surface: #1a2740;
                --app-surface-solid: #1a2740;
                --app-text: #e2e8f0;
                --app-muted: #93a2bb;
                --app-border: #30415f;
                --app-primary: #36d399;
                --app-accent: #60a5fa;
                --app-soft: #253a57;
                --app-success: #34d399;
                --app-danger: #fb7185;
                --app-shadow: 0 18px 50px rgba(0, 0, 0, 0.35);
                --sidebar-bg-top: #111827;
                --sidebar-bg-bottom: #0b1629;
                --sidebar-border: #253247;
                --sidebar-muted: #9fb0c9;
                --sidebar-card-bg: #15233a;
                --sidebar-card-border: #273752;
            }

            html[data-theme="dark"] {
                --app-bg: #02050d;
                --app-surface: #0a1220;
                --app-surface-solid: #0a1220;
                --app-text: #e2e8f0;
                --app-muted: #8ea1be;
                --app-border: #1c2a40;
                --app-primary: #34d399;
                --app-accent: #38bdf8;
                --app-soft: #102036;
                --app-success: #34d399;
                --app-danger: #fb7185;
                --app-shadow: 0 22px 60px rgba(0, 0, 0, 0.5);
                --sidebar-bg-top: #090f1a;
                --sidebar-bg-bottom: #050a13;
                --sidebar-border: #1c2a40;
                --sidebar-muted: #8ea1be;
                --sidebar-card-bg: #0f1a2c;
                --sidebar-card-border: #1c2a40;
            }

            html[data-theme="light"] {
                --app-bg: #f5f7fb;
                --app-surface: #ffffff;
                --app-surface-solid: #ffffff;
                --app-text: #0f172a;
                --app-muted: #475569;
                --app-border: #dbe2ea;
                --app-primary: #2563eb;
                --app-accent: #38bdf8;
                --app-soft: #e7f0ff;
                --app-success: #15803d;
                --app-danger: #dc2626;
                --app-shadow: 0 14px 40px rgba(15, 23, 42, 0.10);
                --sidebar-bg-top: #f8fbff;
                --sidebar-bg-bottom: #eef3fa;
                --sidebar-border: #dbe2ea;
                --sidebar-muted: #334155;
                --sidebar-card-bg: #ffffff;
                --sidebar-card-border: #dbe2ea;
            }

            html[data-theme="default"] {
                --app-bg: #111111;
                --app-surface: #1a1a1a;
                --app-surface-solid: #1a1a1a;
                --app-text: #f6e05e;
                --app-muted: #d9cf6e;
                --app-border: #2e7d32;
                --app-primary: #c62828;
                --app-accent: #2e7d32;
                --app-soft: #4e342e;
                --app-success: #2e7d32;
                --app-danger: #c62828;
                --app-shadow: 0 20px 55px rgba(0, 0, 0, 0.45);
                --sidebar-bg-top: #1a1a1a;
                --sidebar-bg-bottom: #101810;
                --sidebar-border: #2e7d32;
                --sidebar-muted: #f6e05e;
                --sidebar-card-bg: #1f2a1f;
                --sidebar-card-border: #c62828;
            }

            .topbar-shell {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.75rem;
                padding: 0.75rem 1rem;
                border-bottom: 1px solid var(--app-border);
                background:
                    linear-gradient(120deg, color-mix(in srgb, var(--app-surface) 90%, #000 10%), color-mix(in srgb, var(--app-soft) 55%, #000 45%));
                position: sticky;
                top: 0;
                z-index: 40;
                backdrop-filter: blur(10px);
            }

            .topbar-icon-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 9999px;
                border: 1px solid var(--app-border);
                background: var(--app-surface-solid);
                color: var(--app-text);
                transition: 0.2s ease;
            }

            .topbar-icon-btn:hover {
                background: var(--app-soft);
                transform: translateY(-1px);
            }

            .topbar-avatar-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 2.75rem;
                height: 2.75rem;
                overflow: hidden;
                border-radius: 9999px;
                border: 1px solid var(--app-border);
                background: var(--app-surface-solid);
                color: var(--app-text);
                transition: 0.2s ease;
            }

            .topbar-avatar-btn:hover {
                background: var(--app-soft);
                transform: translateY(-1px);
            }

            .menu-launcher {
                display: inline-flex;
                align-items: center;
                gap: 0.65rem;
                min-height: 3rem;
                padding: 0.45rem 0.9rem 0.45rem 0.7rem;
                border-radius: 9999px;
                border: 1px solid color-mix(in srgb, var(--app-primary) 38%, var(--app-border) 62%);
                background:
                    linear-gradient(135deg, color-mix(in srgb, var(--app-primary) 20%, var(--app-surface-solid) 80%), color-mix(in srgb, var(--app-accent) 16%, var(--app-surface-solid) 84%));
                box-shadow: 0 14px 30px color-mix(in srgb, var(--app-primary) 22%, transparent);
                color: var(--app-text);
                transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            }

            .menu-launcher:hover {
                transform: translateY(-1px);
                box-shadow: 0 18px 36px color-mix(in srgb, var(--app-primary) 28%, transparent);
                filter: saturate(1.05);
            }

            .home-launcher {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 3rem;
                height: 3rem;
                border-radius: 9999px;
                border: 1px solid color-mix(in srgb, var(--app-accent) 30%, var(--app-border) 70%);
                background: linear-gradient(135deg, color-mix(in srgb, var(--app-accent) 18%, var(--app-surface-solid) 82%), color-mix(in srgb, var(--app-primary) 12%, var(--app-surface-solid) 88%));
                color: var(--app-text);
                box-shadow: 0 12px 26px color-mix(in srgb, var(--app-accent) 18%, transparent);
                transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            }

            .home-launcher:hover {
                transform: translateY(-1px);
                box-shadow: 0 16px 34px color-mix(in srgb, var(--app-accent) 24%, transparent);
                filter: saturate(1.05);
            }

            .home-launcher svg {
                width: 1.15rem;
                height: 1.15rem;
            }

            .menu-launcher-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 2rem;
                height: 2rem;
                border-radius: 9999px;
                background: linear-gradient(135deg, var(--app-primary), var(--app-accent));
                color: #04111f;
                box-shadow: inset 0 1px 0 rgba(255,255,255,0.25);
            }

            @keyframes fadeRise {
                from { opacity: 0; transform: translateY(8px); }
                to { opacity: 1; transform: translateY(0); }
            }

            @keyframes glowPulse {
                0%, 100% { box-shadow: 0 0 0 rgba(0,0,0,0); }
                50% { box-shadow: 0 0 0 6px color-mix(in srgb, var(--app-primary) 20%, transparent); }
            }

            .dashboard-shell {
                background:
                    radial-gradient(circle at 10% -5%, rgba(56, 189, 248, 0.10), transparent 30%),
                    radial-gradient(circle at 100% 0%, rgba(74, 222, 128, 0.09), transparent 34%),
                    linear-gradient(180deg, #040b18 0%, #020611 100%);
                color: var(--app-text);
                min-height: 100vh;
            }
            html[data-theme="light"] .dashboard-shell {
                background:
                    radial-gradient(circle at 5% 0%, rgba(37, 99, 235, 0.10), transparent 34%),
                    radial-gradient(circle at 100% 0%, rgba(14, 165, 233, 0.08), transparent 30%),
                    var(--app-bg);
            }
            html[data-theme="dark"] .dashboard-shell {
                background:
                    radial-gradient(circle at 12% -5%, rgba(52, 211, 153, 0.08), transparent 34%),
                    radial-gradient(circle at 100% 0%, rgba(56, 189, 248, 0.08), transparent 30%),
                    var(--app-bg);
            }
            html[data-theme="default"] .dashboard-shell {
                background:
                    radial-gradient(circle at 8% 0%, rgba(198, 40, 40, 0.20), transparent 34%),
                    radial-gradient(circle at 100% 0%, rgba(46, 125, 50, 0.20), transparent 30%),
                    linear-gradient(180deg, #121212 0%, #181414 100%);
            }

            .app-surface {
                background: var(--app-surface);
                border: 1px solid var(--app-border);
                box-shadow: var(--app-shadow);
                backdrop-filter: blur(18px);
            }

            .app-surface-solid {
                background: var(--app-surface-solid);
                border: 1px solid var(--app-border);
            }

            .app-muted {
                color: var(--app-muted);
            }

            .app-primary {
                color: var(--app-primary);
            }

            .app-accent {
                color: var(--app-accent);
            }

            .app-primary-bg {
                background: var(--app-primary);
            }

            .app-soft-bg {
                background: var(--app-soft);
            }

            .dashboard-layout { display: flex; min-height: 100vh; }
            .dashboard-sidebar {
                width: 280px;
                background: linear-gradient(180deg, var(--sidebar-bg-top) 0%, var(--sidebar-bg-bottom) 100%);
                border-right: 1px solid var(--sidebar-border);
                position: sticky;
                top: 0;
                height: 100vh;
                overflow: hidden;
            }
            .dashboard-workspace { flex: 1; min-width: 0; }
            .dashboard-mobile-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                border-bottom: 1px solid var(--sidebar-border);
                padding: 0.75rem 1rem;
                background: var(--sidebar-bg-bottom);
            }
            .brand-badge {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 48px;
                height: 48px;
                border-radius: 14px;
                background: linear-gradient(140deg, var(--app-accent), var(--app-primary));
                color: #03111f;
                font-weight: 900;
                letter-spacing: 0.04em;
            }
            .sidebar-icon-btn {
                background: color-mix(in srgb, var(--sidebar-card-bg) 88%, #000 12%);
                color: var(--app-text);
                border: 1px solid var(--sidebar-card-border);
                border-radius: 10px;
                padding: 0.5rem;
            }
            .sidebar-footer { border-top: 1px solid var(--sidebar-border); padding: 1rem; }
            .sidebar-theme-btn {
                border: 1px solid var(--sidebar-card-border);
                background: color-mix(in srgb, var(--sidebar-card-bg) 88%, #000 12%);
                color: var(--sidebar-muted);
                border-radius: 12px;
                padding: 0.45rem 0.85rem;
                font-size: 0.85rem;
                font-weight: 700;
            }
            .sidebar-user-card {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                background: var(--sidebar-card-bg);
                border: 1px solid var(--sidebar-card-border);
                border-radius: 14px;
                padding: 0.7rem;
            }
            .user-avatar {
                width: 40px;
                height: 40px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(140deg, var(--app-accent), var(--app-primary));
                color: #03111f;
                font-weight: 900;
            }
            .sidebar-mini-link {
                display: inline-flex;
                border: 1px solid var(--sidebar-card-border);
                border-radius: 10px;
                padding: 0.35rem 0.75rem;
                color: var(--app-text);
                font-size: 0.78rem;
                font-weight: 700;
            }
            @media (max-width: 1023px) {
                .dashboard-sidebar { width: 100%; height: auto; position: static; border-right: 0; }
            }

            body[data-role="student"] .dashboard-workspace,
            body[data-role="teacher"] .dashboard-workspace,
            body[data-role="teacher_admin"] .dashboard-workspace {
                font-family: 'Sora', 'Manrope', sans-serif;
            }

            body[data-role="student"] .dashboard-workspace main > *,
            body[data-role="teacher"] .dashboard-workspace main > *,
            body[data-role="teacher_admin"] .dashboard-workspace main > * {
                animation: fadeRise 0.45s ease;
            }

            body[data-role="student"] .dashboard-workspace .bg-white,
            body[data-role="student"] .dashboard-workspace .bg-white\/95,
            body[data-role="teacher"] .dashboard-workspace .bg-white,
            body[data-role="teacher"] .dashboard-workspace .bg-white\/95,
            body[data-role="teacher_admin"] .dashboard-workspace .bg-white,
            body[data-role="teacher_admin"] .dashboard-workspace .bg-white\/95 {
                background: linear-gradient(140deg, color-mix(in srgb, var(--app-surface) 92%, white 8%), color-mix(in srgb, var(--app-soft) 28%, transparent)) !important;
                border-color: var(--app-border) !important;
                color: var(--app-text) !important;
            }

            body[data-role="student"] .dashboard-workspace .text-slate-900,
            body[data-role="student"] .dashboard-workspace .text-slate-950,
            body[data-role="teacher"] .dashboard-workspace .text-slate-900,
            body[data-role="teacher"] .dashboard-workspace .text-slate-950,
            body[data-role="teacher_admin"] .dashboard-workspace .text-slate-900,
            body[data-role="teacher_admin"] .dashboard-workspace .text-slate-950 {
                color: var(--app-text) !important;
            }

            body[data-role="student"] .dashboard-workspace .text-slate-500,
            body[data-role="student"] .dashboard-workspace .text-slate-600,
            body[data-role="teacher"] .dashboard-workspace .text-slate-500,
            body[data-role="teacher"] .dashboard-workspace .text-slate-600,
            body[data-role="teacher_admin"] .dashboard-workspace .text-slate-500,
            body[data-role="teacher_admin"] .dashboard-workspace .text-slate-600 {
                color: var(--app-muted) !important;
            }

            body[data-role="student"] .dashboard-workspace [class*="border-slate-200"],
            body[data-role="teacher"] .dashboard-workspace [class*="border-slate-200"],
            body[data-role="teacher_admin"] .dashboard-workspace [class*="border-slate-200"] {
                border-color: color-mix(in srgb, var(--app-border) 88%, white 12%) !important;
            }

            body[data-role="student"] .dashboard-workspace a.rounded-2xl,
            body[data-role="teacher"] .dashboard-workspace a.rounded-2xl,
            body[data-role="teacher_admin"] .dashboard-workspace a.rounded-2xl {
                transition: transform 0.2s ease, box-shadow 0.25s ease;
            }

            body[data-role="student"] .dashboard-workspace a.rounded-2xl:hover,
            body[data-role="teacher"] .dashboard-workspace a.rounded-2xl:hover,
            body[data-role="teacher_admin"] .dashboard-workspace a.rounded-2xl:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 28px color-mix(in srgb, var(--app-primary) 20%, transparent);
            }

            body[data-role="student"] .dashboard-workspace button,
            body[data-role="teacher"] .dashboard-workspace button,
            body[data-role="teacher_admin"] .dashboard-workspace button {
                animation: glowPulse 2.4s ease-in-out infinite;
            }
        </style>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|sora:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body data-role="{{ Auth::user()?->role ?? 'guest' }}" class="antialiased text-[color:var(--app-text)]" style="background: var(--app-bg); font-family: 'Manrope', sans-serif;">
        @php
            $currentUser = Auth::user();
            $avatarUrl = $currentUser?->image_url;
            $isStudentRole = ($currentUser?->role ?? '') === 'student';
            $isAdminRole = in_array(($currentUser?->role ?? ''), ['admin', 'super_admin'], true);
            $dashboardPortal = match ($currentUser?->role) {
                'teacher' => 'teacher',
                'teacher_admin' => 'teacher-admin',
                'admin', 'super_admin' => 'admin',
                default => 'student',
            };
            $flashMessage = session('success') ?? session('status');
            $flashType = session('success') ? 'success' : (session('error') ? 'error' : 'info');
        @endphp

        @if($flashMessage)
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3500)" x-show="show" x-transition class="fixed right-4 top-4 z-[60] max-w-md rounded-2xl border px-5 py-4 shadow-[0_18px_50px_rgba(15,23,42,0.16)] border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 h-2.5 w-2.5 rounded-full {{ $flashType === 'success' ? 'bg-emerald-500' : ($flashType === 'error' ? 'bg-rose-500' : 'bg-amber-400') }}"></div>
                    <div class="flex-1 text-sm font-medium">{{ $flashMessage }}</div>
                    <button type="button" @click="show = false" class="text-xs font-semibold uppercase tracking-wide text-slate-400 hover:text-slate-600">Close</button>
                </div>
            </div>
        @endif

        <div x-data="{ menuOpen: false }" @keydown.escape.window="menuOpen = false" class="dashboard-shell dashboard-layout">
            @include('layouts.navigation')
            <div class="dashboard-workspace">
                <div class="topbar-shell">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('dashboard', ['portal' => $dashboardPortal]) }}" class="home-launcher" aria-label="Home">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M3 11.5 12 4l9 7.5M5.5 10.5V20h13v-9.5" />
                            </svg>
                        </a>
                        <button type="button" @click="menuOpen = !menuOpen" class="menu-launcher" aria-label="Open menu" :aria-expanded="menuOpen.toString()">
                            <span class="menu-launcher-badge">
                                <svg class="h-4.5 w-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </span>
                            <span class="text-sm font-bold tracking-wide">Menu</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        @unless($isAdminRole)
                            <x-notification-bell-dropdown />
                        @endunless

                        <a href="{{ route('profile.edit', ['portal' => $dashboardPortal]) }}" class="topbar-avatar-btn" aria-label="Open profile settings">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="{{ $currentUser?->name }}" class="h-full w-full object-cover">
                            @else
                                <span class="text-sm font-black">{{ strtoupper(substr($currentUser?->name ?? 'U', 0, 1)) }}</span>
                            @endif
                        </a>

                        <form method="POST" action="{{ route('logout', ['portal' => $dashboardPortal]) }}">
                            @csrf
                            <button type="submit" class="topbar-icon-btn" aria-label="Logout">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                @isset($header)
                    <header class="border-b border-[color:var(--app-border)] bg-[color:var(--app-surface)] backdrop-blur-xl">
                        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset
                <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    {{ $slot }}
                </main>
                @include('layouts.footer')
            </div>
        </div>

        <div id="confirm-delete-modal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-slate-950/60 px-4">
            <div class="w-full max-w-md rounded-[2rem] bg-white p-6 shadow-[0_24px_80px_rgba(15,23,42,0.32)]">
                <h3 class="text-xl font-black text-slate-900">Delete this item?</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">This action cannot be undone. Confirm to permanently remove the record.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" id="confirm-delete-cancel" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="button" id="confirm-delete-accept" class="rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Delete</button>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const modal = document.getElementById('confirm-delete-modal');
                const cancelButton = document.getElementById('confirm-delete-cancel');
                const acceptButton = document.getElementById('confirm-delete-accept');
                let activeForm = null;

                document.addEventListener('submit', function (event) {
                    const form = event.target;
                    if (!form || !form.matches('form[data-confirm-delete]')) return;
                    event.preventDefault();
                    activeForm = form;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });

                const closeModal = () => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    activeForm = null;
                };

                cancelButton?.addEventListener('click', closeModal);
                modal?.addEventListener('click', function (event) {
                    if (event.target === modal) closeModal();
                });
                acceptButton?.addEventListener('click', function () {
                    if (activeForm) activeForm.submit();
                    closeModal();
                });
            })();
        </script>
    </body>
</html>
