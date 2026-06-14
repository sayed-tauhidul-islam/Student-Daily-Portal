@php
    // teacher links partial
@endphp
<x-nav-link :href="route('dashboard', ['portal' => 'teacher'])" :active="request()->routeIs('dashboard')">
    {{ __('Dashboard') }}
</x-nav-link>
<x-nav-link :href="route('teacher.messages')" :active="request()->routeIs('teacher.messages*')">
    {{ __('Messenger') }}
</x-nav-link>
<x-nav-link :href="route('teacher.complaints')" :active="request()->routeIs('teacher.complaints*')">
    {{ __('Complaint') }}
</x-nav-link>
<x-nav-link :href="route('teacher.leaves')" :active="request()->routeIs('teacher.leaves*')">
    {{ __('Leave Apply') }}
</x-nav-link>
<x-nav-link :href="route('teacher.payments')" :active="request()->routeIs('teacher.payments*')">
    {{ __('Salary Status') }}
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
<x-nav-link :href="route('teacher.progress.index')" :active="request()->routeIs('teacher.progress.*')">
    {{ __('Student Progress') }}
</x-nav-link>
<x-nav-link :href="route('teacher.students.index')" :active="request()->routeIs('teacher.students.*')">
    {{ __('Student Details') }}
</x-nav-link>
<x-nav-link :href="route('teacher.profile.create')" :active="request()->routeIs('teacher.profile.create')">
    {{ __('Teacher Profile') }}
</x-nav-link>
<x-nav-link :href="route('profile.edit', ['portal' => 'teacher'])" :active="request()->routeIs('profile.*')">
    {{ __('Profile Settings') }}
</x-nav-link>
