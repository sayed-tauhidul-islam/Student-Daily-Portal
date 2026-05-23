<x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
    {{ __('Dashboard') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('student.profile.create')" :active="request()->routeIs('student.profile.create')">
    {{ __('Complete Profile') }}
</x-responsive-nav-link>
<!-- Tuition Requests removed from student responsive nav per request -->
<x-responsive-nav-link :href="route('teachers.index')" :active="request()->routeIs('teachers.index')">
    {{ __('Find Teacher') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('student.attendance.index')" :active="request()->routeIs('student.attendance.*')">
    {{ __('Attendance') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('student.institute-teachers.index')" :active="request()->routeIs('student.institute-teachers.*')">
    {{ __('Teachers of My Institute') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('student.notices.index')" :active="request()->routeIs('student.notices.*')">
    {{ __('Notice') }}
</x-responsive-nav-link>
