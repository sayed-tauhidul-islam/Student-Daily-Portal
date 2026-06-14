<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Account settings</p>
            <h2 class="mt-2 text-3xl font-black text-slate-900">Settings</h2>
            <p class="mt-2 max-w-2xl text-sm text-slate-500">Update your profile image, name, email, phone, and area from one place.</p>
        </div>
    </x-slot>

    <div class="py-8 lg:py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="app-surface rounded-[2rem] p-4 sm:p-8">
                <div class="max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="app-surface rounded-[2rem] p-4 sm:p-8">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] app-primary">Dashboard mood</p>
                    <h3 class="mt-2 text-2xl font-black text-[color:var(--app-text)]">Choose your dashboard mood</h3>
                    <div class="mt-5 grid gap-3 sm:grid-cols-3">
                        <button type="button" onclick="window.setDashboardTheme('default')" class="rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-3 text-left text-sm font-bold text-[color:var(--app-text)] transition hover:bg-[color:var(--app-soft)]">Retro</button>
                        <button type="button" onclick="window.setDashboardTheme('light')" class="rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-3 text-left text-sm font-bold text-[color:var(--app-text)] transition hover:bg-[color:var(--app-soft)]">Light</button>
                        <button type="button" onclick="window.setDashboardTheme('dark')" class="rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-4 py-3 text-left text-sm font-bold text-[color:var(--app-text)] transition hover:bg-[color:var(--app-soft)]">Dark</button>
                    </div>
                </div>
            </div>

            <div class="app-surface rounded-[2rem] p-4 sm:p-8">
                <div class="max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="app-surface rounded-[2rem] p-4 sm:p-8">
                <div class="max-w-2xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
