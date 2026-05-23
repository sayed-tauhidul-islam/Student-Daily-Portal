<x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
    {{ __('Dashboard') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher.profile.create')" :active="request()->routeIs('teacher.profile.create')">
    {{ __('Teacher Profile') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher.posts.create')" :active="request()->routeIs('teacher.posts.create')">
    {{ __('Post') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher.finder')" :active="request()->routeIs('teacher.finder')">
    {{ __('Tuition Requests') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher.attendance.index')" :active="request()->routeIs('teacher.attendance.*')">
    {{ __('Attendance Manage') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher.notices.index')" :active="request()->routeIs('teacher.notices.*')">
    {{ __('Institute Notice') }}
</x-responsive-nav-link>
