@php
    $portal = $portal ?? request('portal', 'student');
    $portalMeta = [
        'student' => [
            'label' => 'Student Panel',
            'eyebrow' => 'Student access',
            'title' => 'Sign in to your student dashboard',
            'description' => 'Use your student credentials to access attendance, notices, and your profile tools.',
            'button' => 'Log in as Student',
        ],
        'teacher-admin' => [
            'label' => 'Teacher Admin Panel',
            'eyebrow' => 'Head teacher access',
            'title' => 'Sign in to your school control panel',
            'description' => 'Head teachers can manage only their school or college students and teachers from this panel.',
            'button' => 'Log in as Head Teacher',
        ],
        'teacher' => [
            'label' => 'Teacher Panel',
            'eyebrow' => 'Teacher access',
            'title' => 'Sign in to your teacher workspace',
            'description' => 'Teachers can manage teaching tools, posts, notices, attendance, and tuition requests.',
            'button' => 'Log in as Teacher',
        ],
        'super-admin' => [
            'label' => 'Super Admin Panel',
            'eyebrow' => 'Private system access',
            'title' => 'Sign in to the private super admin panel',
            'description' => 'This panel is hidden from regular users and controls the whole system.',
            'button' => 'Log in as Super Admin',
        ],
    ];
    $meta = $portalMeta[$portal] ?? $portalMeta['student'];
@endphp

<x-guest-layout>
    <div class="mb-8">
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-sky-600">{{ $meta['eyebrow'] }}</p>
        <h2 class="mt-2 text-3xl font-black text-slate-950">{{ $meta['title'] }}</h2>
        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $meta['description'] }}</p>

        <div class="mt-6 flex flex-wrap gap-2 text-xs font-semibold" id="portal-tabs">
            <button type="button" data-portal="student" class="portal-btn rounded-full border px-3 py-1.5 {{ $portal === 'student' ? 'border-sky-500 bg-sky-50 text-sky-700' : 'border-slate-200 bg-white text-slate-600' }}">Student Panel</button>
            <button type="button" data-portal="teacher-admin" class="portal-btn rounded-full border px-3 py-1.5 {{ $portal === 'teacher-admin' ? 'border-slate-950 bg-slate-950 text-white' : 'border-slate-200 bg-white text-slate-600' }}">Teacher Admin Panel</button>
            <button type="button" data-portal="teacher" class="portal-btn rounded-full border px-3 py-1.5 {{ $portal === 'teacher' ? 'border-sky-500 bg-sky-50 text-sky-700' : 'border-slate-200 bg-white text-slate-600' }}">Teacher Panel</button>
            <button type="button" data-portal="super-admin" class="portal-btn hidden rounded-full border px-3 py-1.5 {{ $portal === 'super-admin' ? 'border-slate-950 bg-slate-950 text-white' : 'border-slate-200 bg-white text-slate-600' }}">Super Admin</button>
        </div>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="portal" value="{{ $portal }}">

        <div>
            <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                   class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100"
                   placeholder="you@example.com">
            @error('email')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
            <div class="relative">
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 pr-12 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100"
                       placeholder="Enter your password">
                <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 flex items-center justify-center px-4 text-slate-500 transition hover:text-slate-800" aria-label="Show password" aria-pressed="false">
                    <svg id="password-eye-open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0Z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z"></path>
                    </svg>
                    <svg id="password-eye-closed" class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.147-3.368m3.231-2.757A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.978 9.978 0 01-4.293 5.477M15 12a3 3 0 01-3 3m0 0a3 3 0 01-3-3m3 3l5 5m-5-5L6 6"></path>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between gap-3">
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                Remember me
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-sky-600 transition hover:text-sky-700">
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-slate-950/15 transition hover:bg-slate-800">
            {{ $meta['button'] }}
        </button>
    </form>

    <script>
        (() => {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('toggle-password');
            const eyeOpen = document.getElementById('password-eye-open');
            const eyeClosed = document.getElementById('password-eye-closed');

            if (passwordInput && toggleButton && eyeOpen && eyeClosed) {
                toggleButton.addEventListener('click', () => {
                    const isHidden = passwordInput.type === 'password';
                    passwordInput.type = isHidden ? 'text' : 'password';
                    eyeOpen.classList.toggle('hidden', !isHidden);
                    eyeClosed.classList.toggle('hidden', isHidden);
                    toggleButton.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
                    toggleButton.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
                });
            }

            // Portal metadata (mirror of server-side $portalMeta)
            const portalMeta = {
                student: {
                    eyebrow: 'Student access',
                    title: 'Sign in to your student dashboard',
                    description: 'Use your student credentials to access attendance, notices, and your profile tools.',
                    button: 'Log in as Student'
                },
                'teacher-admin': {
                    eyebrow: 'Head teacher access',
                    title: 'Sign in to your school control panel',
                    description: 'Head teachers can manage only their school or college students and teachers from this panel.',
                    button: 'Log in as Head Teacher'
                },
                teacher: {
                    eyebrow: 'Teacher access',
                    title: 'Sign in to your teacher workspace',
                    description: 'Teachers can manage teaching tools, posts, notices, attendance, and tuition requests.',
                    button: 'Log in as Teacher'
                },
                'super-admin': {
                    eyebrow: 'Private system access',
                    title: 'Sign in to the private super admin panel',
                    description: 'This panel is hidden from regular users and controls the whole system.',
                    button: 'Log in as Super Admin'
                }
            };

            const portalInput = document.querySelector('input[name="portal"]');
            const portalBtns = document.querySelectorAll('.portal-btn');
            const eyebrowEl = document.querySelector('p.text-xs.font-semibold.uppercase');
            const titleEl = document.querySelector('h2.text-3xl.font-black');
            const descEl = document.querySelector('p.mt-3.text-sm.leading-6');
            const submitBtn = document.querySelector('form button[type="submit"]');

            function setPortal(p) {
                if (!portalMeta[p]) return;
                if (portalInput) portalInput.value = p;
                if (eyebrowEl) eyebrowEl.textContent = portalMeta[p].eyebrow;
                if (titleEl) titleEl.textContent = portalMeta[p].title;
                if (descEl) descEl.textContent = portalMeta[p].description;
                if (submitBtn) submitBtn.textContent = portalMeta[p].button;

                portalBtns.forEach(b => {
                    const active = b.dataset.portal === p;
                    b.classList.toggle('border-sky-500', active && (p === 'student' || p === 'teacher'));
                    b.classList.toggle('bg-sky-50', active && (p === 'student' || p === 'teacher'));
                    b.classList.toggle('text-sky-700', active && (p === 'student' || p === 'teacher'));
                    b.classList.toggle('border-slate-950', active && p === 'teacher-admin');
                    b.classList.toggle('bg-slate-950', active && p === 'teacher-admin');
                    b.classList.toggle('text-white', active && p === 'teacher-admin');
                    b.classList.toggle('border-slate-200', !active);
                    b.classList.toggle('bg-white', !active);
                    b.classList.toggle('text-slate-600', !active);
                });
            }

            portalBtns.forEach(b => b.addEventListener('click', () => setPortal(b.dataset.portal)));

            // initialize from server-provided portal value
            setPortal('{{ $portal }}');
        })();
    </script>

    <p class="mt-6 text-center text-sm text-slate-600">
        Don’t have an account?
        <a href="{{ route('register') }}" class="font-semibold text-sky-600 transition hover:text-sky-700">Create one now</a>
    </p>
</x-guest-layout>
