<x-responsive-nav-link :href="route('dashboard', ['portal' => 'teacher'])" :active="request()->routeIs('dashboard')">
    {{ __('Dashboard') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher.messages')" :active="request()->routeIs('teacher.messages*')">
    {{ __('Messenger') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher.complaints')" :active="request()->routeIs('teacher.complaints*')">
    {{ __('Complaint') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher.leaves')" :active="request()->routeIs('teacher.leaves*')">
    {{ __('Leave Apply') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher.payments')" :active="request()->routeIs('teacher.payments*')">
    {{ __('Salary Status') }}
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
<x-responsive-nav-link :href="route('teacher.progress.index')" :active="request()->routeIs('teacher.progress.*')">
    {{ __('Student Progress') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher.students.index')" :active="request()->routeIs('teacher.students.*')">
    {{ __('Student Details') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teacher.profile.create')" :active="request()->routeIs('teacher.profile.create')">
    {{ __('Teacher Profile') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('profile.edit', ['portal' => 'teacher'])" :active="request()->routeIs('profile.*')">
    {{ __('Profile Settings') }}
</x-responsive-nav-link>
