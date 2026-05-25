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
