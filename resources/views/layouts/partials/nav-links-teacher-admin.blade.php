<x-nav-link :href="route('teacher-admin.dashboard')" :active="request()->routeIs('teacher-admin.dashboard')">
    {{ __('Dashboard') }}
</x-nav-link>
<x-nav-link :href="route('teacher-admin.teachers.index')" :active="request()->routeIs('teacher-admin.teachers.*')">
    {{ __('Control Teacher') }}
</x-nav-link>
<x-nav-link :href="route('teacher-admin.students.index')" :active="request()->routeIs('teacher-admin.students.*')">
    {{ __('Control Student') }}
</x-nav-link>
<x-nav-link :href="route('teacher-admin.database')" :active="request()->routeIs('teacher-admin.database')">
    {{ __('School Database') }}
</x-nav-link>
<x-nav-link :href="route('teacher-admin.messages')" :active="request()->routeIs('teacher-admin.messages*')">
    {{ __('Messenger') }}
</x-nav-link>
<x-nav-link :href="route('teacher-admin.complaints')" :active="request()->routeIs('teacher-admin.complaints*')">
    {{ __('Complaints') }}
</x-nav-link>
<x-nav-link :href="route('teacher-admin.leaves')" :active="request()->routeIs('teacher-admin.leaves*')">
    {{ __('Leave Reviews') }}
</x-nav-link>
<x-nav-link :href="route('teacher-admin.payments')" :active="request()->routeIs('teacher-admin.payments*')">
    {{ __('Payments') }}
</x-nav-link>
<x-nav-link :href="route('teacher-admin.search')" :active="request()->routeIs('teacher-admin.search')">
    {{ __('Search Hub') }}
</x-nav-link>
<x-nav-link :href="route('teacher-admin.login-reviews')" :active="request()->routeIs('teacher-admin.login-reviews*')">
    {{ __('New Logins') }}
</x-nav-link>
<x-nav-link :href="route('profile.edit', ['portal' => 'teacher-admin'])" :active="request()->routeIs('profile.*')">
    {{ __('Profile Settings') }}
</x-nav-link>
