@props(['contentWidth' => 'max-w-md', 'showSidebar' => true])

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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=sora:400,500,600,700,800|outfit:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased text-slate-900" style="font-family: 'Outfit', sans-serif;">
        <div class="min-h-screen bg-[radial-gradient(circle_at_15%_10%,_rgba(34,197,94,0.18),_transparent_30%),radial-gradient(circle_at_90%_0%,_rgba(14,165,233,0.22),_transparent_36%),linear-gradient(140deg,_#f7fbff_0%,_#eef3ff_55%,_#fdf7ff_100%)]">
            <div class="absolute inset-0 -z-10 opacity-40 [background-image:linear-gradient(rgba(15,23,42,0.06)_1px,transparent_1px),linear-gradient(90deg,rgba(15,23,42,0.06)_1px,transparent_1px)] [background-size:72px_72px]"></div>

            <div class="mx-auto flex min-h-screen w-full max-w-7xl flex-col px-4 py-6 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <a href="{{ url('/') }}" class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-950 text-white shadow-lg shadow-slate-950/15">
                            <span class="text-sm font-black tracking-[0.2em]">TL</span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-sky-600">TutorLink BD</p>
                            <p class="text-sm font-medium text-slate-600">Student Daily Portal</p>
                        </div>
                    </a>

                    <a href="{{ url('/') }}" class="hidden rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 sm:inline-flex">
                        Home
                    </a>
                </div>

                <div class="mt-8 grid flex-1 items-center gap-8 {{ $showSidebar ? 'lg:grid-cols-[0.9fr_1.1fr]' : '' }}">
                    <section class="hidden rounded-[2rem] border border-sky-200/70 bg-gradient-to-br from-white to-sky-50 p-8 shadow-[0_20px_55px_rgba(14,116,144,0.12)] lg:block {{ $showSidebar ? '' : 'lg:hidden' }}">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-700">Modern student workflow</p>
                        <h1 class="mt-4 text-4xl font-black leading-tight text-slate-950">
                            Clean login, registration, and profile management in one place.
                        </h1>
                        <p class="mt-4 text-base leading-7 text-slate-600">
                            TutorLink BD is built for students who need fast onboarding, simple profile setup, and a calm dashboard experience.
                        </p>

                        <div class="mt-8 grid gap-4">
                            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/80 p-4">
                                <p class="text-sm font-semibold text-slate-900">Fast onboarding</p>
                                <p class="mt-1 text-sm text-slate-600">Create an account and land on your dashboard immediately.</p>
                            </div>
                            <div class="rounded-2xl border border-cyan-100 bg-cyan-50/80 p-4">
                                <p class="text-sm font-semibold text-slate-900">Profile-first flow</p>
                                <p class="mt-1 text-sm text-slate-600">Complete class, school, subject, and area details when ready.</p>
                            </div>
                            <div class="rounded-2xl border border-violet-100 bg-violet-50/80 p-4">
                                <p class="text-sm font-semibold text-slate-900">Mobile friendly</p>
                                <p class="mt-1 text-sm text-slate-600">The UI scales cleanly on desktop, tablet, and phone.</p>
                            </div>
                        </div>
                    </section>

                    <div class="mx-auto w-full {{ $contentWidth }} {{ $showSidebar ? 'rounded-[2rem] border border-sky-200/80 bg-gradient-to-br from-white to-slate-50 p-6 shadow-[0_20px_55px_rgba(14,116,144,0.14)] sm:p-8' : 'rounded-none border-0 bg-transparent p-0 shadow-none' }}">
                        {{ $slot }}
                    </div>
                </div>

                @include('layouts.footer')
            </div>
        </div>
    </body>
</html>
