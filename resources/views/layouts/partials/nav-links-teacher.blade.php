@php
    // teacher links partial
@endphp
<x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
    {{ __('Dashboard') }}
</x-nav-link>
<x-nav-link :href="route('teacher.profile.create')" :active="request()->routeIs('teacher.profile.create')">
    {{ __('Teacher Profile') }}
</x-nav-link>
<x-nav-link :href="route('teacher.posts.create')" :active="request()->routeIs('teacher.posts.create')">
    {{ __('Post') }}
</x-nav-link>
<x-nav-link :href="route('teacher.finder')" :active="request()->routeIs('teacher.finder')">
    {{ __('Tuition Requests') }}
</x-nav-link>
<x-nav-link :href="route('teacher.attendance.index')" :active="request()->routeIs('teacher.attendance.*')">
    {{ __('Attendance Manage') }}
</x-nav-link>
<x-nav-link :href="route('teacher.notices.index')" :active="request()->routeIs('teacher.notices.*')">
    {{ __('Institute Notice') }}
</x-nav-link>
