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
