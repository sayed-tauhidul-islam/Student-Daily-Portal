<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>TutorLink BD</title>

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
                --app-bg: #f8fbff;
                --app-surface: rgba(255, 255, 255, 0.9);
                --app-surface-solid: #ffffff;
                --app-text: #0f172a;
                --app-muted: #64748b;
                --app-border: rgba(148, 163, 184, 0.25);
                --app-primary: #2563eb;
                --app-accent: #38bdf8;
                --app-soft: rgba(37, 99, 235, 0.08);
                --app-success: #16a34a;
                --app-danger: #dc2626;
                --app-shadow: 0 18px 50px rgba(15, 23, 42, 0.08);
            }

            html[data-theme="light"] {
                --app-bg: #f8fff9;
                --app-surface: rgba(255, 255, 255, 0.92);
                --app-surface-solid: #ffffff;
                --app-text: #0f172a;
                --app-muted: #5b6575;
                --app-border: rgba(34, 197, 94, 0.18);
                --app-primary: #16a34a;
                --app-accent: #22c55e;
                --app-soft: rgba(22, 163, 74, 0.08);
                --app-success: #15803d;
                --app-danger: #dc2626;
                --app-shadow: 0 18px 50px rgba(15, 23, 42, 0.08);
            }

            html[data-theme="dark"] {
                --app-bg: #050816;
                --app-surface: rgba(8, 15, 31, 0.9);
                --app-surface-solid: #0b1224;
                --app-text: #f8fafc;
                --app-muted: #94a3b8;
                --app-border: rgba(148, 163, 184, 0.16);
                --app-primary: #ef4444;
                --app-accent: #38bdf8;
                --app-soft: rgba(239, 68, 68, 0.12);
                --app-success: #22c55e;
                --app-danger: #fb7185;
                --app-shadow: 0 22px 60px rgba(0, 0, 0, 0.35);
            }

            .dashboard-shell {
                background:
                    radial-gradient(circle at top, color-mix(in srgb, var(--app-accent) 16%, transparent), transparent 34%),
                    radial-gradient(circle at right bottom, color-mix(in srgb, var(--app-primary) 10%, transparent), transparent 30%),
                    var(--app-bg);
                color: var(--app-text);
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

            .student-header-skin {
                background:
                    linear-gradient(120deg, rgba(14, 165, 233, 0.14), rgba(59, 130, 246, 0.08)),
                    var(--app-surface);
                border-bottom: 1px solid color-mix(in srgb, var(--app-accent) 28%, var(--app-border));
            }

            .student-main-wrap {
                position: relative;
                border-radius: 2.25rem;
                background:
                    radial-gradient(circle at 8% 0%, rgba(14, 165, 233, 0.10), transparent 34%),
                    radial-gradient(circle at 100% 96%, rgba(37, 99, 235, 0.10), transparent 30%),
                    rgba(255, 255, 255, 0.42);
                border: 1px solid color-mix(in srgb, var(--app-border) 72%, rgba(14, 165, 233, 0.22));
                box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
                padding: 1rem;
                overflow: clip;
            }

            .student-main-wrap::after {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: inherit;
                pointer-events: none;
                background-image: linear-gradient(to right, rgba(148, 163, 184, 0.07) 1px, transparent 1px), linear-gradient(to bottom, rgba(148, 163, 184, 0.07) 1px, transparent 1px);
                background-size: 22px 22px;
                opacity: 0.3;
            }

            .student-main-content {
                position: relative;
                z-index: 1;
                border-radius: 1.6rem;
                background: rgba(255, 255, 255, 0.72);
                border: 1px solid rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(10px);
                padding: 0.35rem;
            }

            html[data-theme="dark"] .student-main-wrap {
                background:
                    radial-gradient(circle at 8% 0%, rgba(56, 189, 248, 0.16), transparent 34%),
                    radial-gradient(circle at 100% 96%, rgba(239, 68, 68, 0.14), transparent 30%),
                    rgba(8, 15, 31, 0.35);
                border-color: rgba(148, 163, 184, 0.28);
            }

            html[data-theme="dark"] .student-main-content {
                background: rgba(8, 15, 31, 0.70);
                border-color: rgba(148, 163, 184, 0.22);
            }
        </style>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-[color:var(--app-text)]" style="background: var(--app-bg);">
        @php
            $currentUser = Auth::user();
            $isStudentRole = ($currentUser?->role ?? '') === 'student';
            $flashMessage = session('success') ?? session('status');
            $flashType = session('success') ? 'success' : (session('error') ? 'error' : 'info');
        @endphp

        @if($flashMessage)
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3500)" x-show="show" x-transition class="fixed right-4 top-4 z-[60] max-w-md rounded-2xl border px-5 py-4 shadow-[0_18px_50px_rgba(15,23,42,0.16)] {{ $flashType === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : ($flashType === 'error' ? 'border-rose-200 bg-rose-50 text-rose-800' : 'border-slate-200 bg-white text-slate-800') }}">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 h-2.5 w-2.5 rounded-full {{ $flashType === 'success' ? 'bg-emerald-500' : ($flashType === 'error' ? 'bg-rose-500' : 'bg-sky-500') }}"></div>
                    <div class="flex-1 text-sm font-medium">{{ $flashMessage }}</div>
                    <button type="button" @click="show = false" class="text-xs font-semibold uppercase tracking-wide text-slate-400 hover:text-slate-600">Close</button>
                </div>
            </div>
        @endif

        <div class="min-h-screen dashboard-shell">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="border-b border-[color:var(--app-border)] bg-[color:var(--app-surface)] backdrop-blur-xl shadow-[0_10px_30px_rgba(15,23,42,0.04)] {{ $isStudentRole ? 'student-header-skin' : '' }}">
                    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                @if($isStudentRole)
                    <div class="student-main-wrap">
                        <div class="student-main-content">
                            {{ $slot }}
                        </div>
                    </div>
                @else
                    {{ $slot }}
                @endif
            </main>

            @include('layouts.footer')
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
