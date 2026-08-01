<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Daily Portal</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('tb-favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('tb-favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('tb-favicon.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(56, 189, 248, 0.18), transparent 28%),
                radial-gradient(circle at bottom right, rgba(37, 99, 235, 0.14), transparent 30%),
                linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
            color: #0f172a;
        }

        .hero-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(148, 163, 184, 0.22);
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
        }

        .accent-text {
            background: linear-gradient(90deg, #0284c7 0%, #2563eb 45%, #0f172a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>
    <main class="mx-auto flex min-h-screen w-full max-w-6xl items-center px-4 py-8 sm:px-6 lg:px-8">
        <section class="hero-card w-full rounded-[2rem] p-6 sm:p-10 lg:p-14">
            <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-start">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.34em] text-sky-600">Student Daily Portal</p>
                    <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                        Welcome to Student Daily Portal
                    </h1>
                    <p class="mt-5 max-w-2xl text-base leading-8 text-slate-600 sm:text-lg">
                        A simple student-first platform for finding tutors, managing profiles, and keeping school information organized.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('login', ['portal' => 'student']) }}" class="inline-flex items-center justify-center rounded-2xl bg-sky-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-sky-600/20 transition hover:bg-sky-700">
                            Student Panel
                        </a>
                        <a href="{{ route('login', ['portal' => 'teacher-admin']) }}" class="inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-white px-6 py-3.5 text-sm font-semibold text-sky-700 transition hover:bg-sky-50">
                            Teacher Admin Panel
                        </a>
                        <a href="{{ route('login', ['portal' => 'teacher']) }}" class="inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-white px-6 py-3.5 text-sm font-semibold text-sky-700 transition hover:bg-sky-50">
                            Teacher Panel
                        </a>
                        <!-- <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-white px-6 py-3.5 text-sm font-semibold text-sky-700 transition hover:bg-sky-50">
                            Student Sign Up
                        </a> -->
                    </div>

                    <div class="mt-10 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Students</p>
                            <p class="mt-2 text-2xl font-black accent-text">24K+</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Teachers</p>
                            <p class="mt-2 text-2xl font-black accent-text">210</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Schools</p>
                            <p class="mt-2 text-2xl font-black accent-text">43</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-[0_12px_30px_rgba(15,23,42,0.06)]">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-sky-600">Important Notes</p>
                        <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-600">
                            <li>• Login/signup is the only action on this page. No navbar is shown here.</li>
                            <li>• Student, teacher, and school data are managed from inside the app after login.</li>
                            <li>• Teacher and school ratings can be updated manually from the database or an admin tool.</li>
                            <li>• Profile image, phone, area, and other user details are edited from Settings after registration.</li>
                        </ul>
                    </div>

                    <div class="rounded-[1.75rem] border border-sky-100 bg-sky-50/70 p-6">
                        <p class="text-sm font-semibold text-sky-700">Project focus</p>
                        <p class="mt-2 text-sm leading-7 text-slate-600">
                            Student Daily Portal is built to keep tutor discovery, student profiles, and school tracking in one clean workflow.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
