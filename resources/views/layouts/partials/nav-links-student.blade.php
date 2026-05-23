@php
    // student links partial
@endphp
<x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
    {{ __('Dashboard') }}
</x-nav-link>
<x-nav-link :href="route('student.profile.create')" :active="request()->routeIs('student.profile.create')">
    {{ __('Complete Profile') }}
</x-nav-link>
<!-- Tuition Requests removed from student nav per request -->
<x-nav-link :href="route('teachers.index')" :active="request()->routeIs('teachers.index')">
    {{ __('Find Teacher') }}
</x-nav-link>
<x-nav-link :href="route('student.attendance.index')" :active="request()->routeIs('student.attendance.*')">
    {{ __('Attendance') }}
</x-nav-link>
<x-nav-link :href="route('student.institute-teachers.index')" :active="request()->routeIs('student.institute-teachers.*')">
    {{ __('Teachers of My Institute') }}
</x-nav-link>
<x-nav-link :href="route('student.notices.index')" :active="request()->routeIs('student.notices.*')">
    {{ __('Notice') }}
</x-nav-link>
