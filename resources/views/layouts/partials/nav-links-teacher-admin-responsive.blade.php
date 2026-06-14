<x-responsive-nav-link :href="route('teacher-admin.dashboard')" :active="request()->routeIs('teacher-admin.dashboard')">
    {{ __('Dashboard') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher-admin.teachers.index')" :active="request()->routeIs('teacher-admin.teachers.*')">
    {{ __('Control Teacher') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher-admin.students.index')" :active="request()->routeIs('teacher-admin.students.*')">
    {{ __('Control Student') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher-admin.database')" :active="request()->routeIs('teacher-admin.database')">
    {{ __('School Database') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher-admin.attendance.index')" :active="request()->routeIs('teacher-admin.attendance.*')">
    {{ __('Attendance') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher-admin.notices.index')" :active="request()->routeIs('teacher-admin.notices.*')">
    {{ __('Notices') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher-admin.progress.index')" :active="request()->routeIs('teacher-admin.progress.*')">
    {{ __('Progress') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher-admin.reading-logs')" :active="request()->routeIs('teacher-admin.reading-logs*')">
    {{ __('Reading Logs') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher-admin.messages')" :active="request()->routeIs('teacher-admin.messages*')">
    {{ __('Messenger') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher-admin.complaints')" :active="request()->routeIs('teacher-admin.complaints*')">
    {{ __('Complaints') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher-admin.leaves')" :active="request()->routeIs('teacher-admin.leaves*')">
    {{ __('Leave Reviews') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher-admin.payments')" :active="request()->routeIs('teacher-admin.payments*')">
    {{ __('Payments') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher-admin.search')" :active="request()->routeIs('teacher-admin.search')">
    {{ __('Search Hub') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher-admin.login-reviews')" :active="request()->routeIs('teacher-admin.login-reviews*')">
    {{ __('New Logins') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('profile.edit', ['portal' => 'teacher-admin'])" :active="request()->routeIs('profile.*')">
    {{ __('Profile Settings') }}
</x-responsive-nav-link>
