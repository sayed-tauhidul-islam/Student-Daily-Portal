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
