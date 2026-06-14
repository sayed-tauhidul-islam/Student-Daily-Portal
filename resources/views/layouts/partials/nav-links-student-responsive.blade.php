<x-responsive-nav-link :href="route('dashboard', ['portal' => 'student'])" :active="request()->routeIs('dashboard') || request()->routeIs('student.dashboard')">
    {{ __('Dashboard') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('student.school-members')" :active="request()->routeIs('student.school-members')">
    {{ __('School Members') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('student.messages')" :active="request()->routeIs('student.messages*')">
    {{ __('Messenger') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('student.complaints')" :active="request()->routeIs('student.complaints*')">
    {{ __('Complaint') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('student.leaves')" :active="request()->routeIs('student.leaves*')">
    {{ __('Leave Apply') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('student.reading-logs')" :active="request()->routeIs('student.reading-logs*')">
    {{ __('Reading Logs') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('student.payments')" :active="request()->routeIs('student.payments*')">
    {{ __('Fee Status') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teachers.index')" :active="request()->routeIs('teachers.index')">
    {{ __('Find Teacher') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('student.progress-hub.index')" :active="request()->routeIs('student.progress-hub.*')">
    {{ __('Progress Hub') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('student.attendance.index')" :active="request()->routeIs('student.attendance.*')">
    {{ __('Attendance') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('student.institute-teachers.index')" :active="request()->routeIs('student.institute-teachers.*')">
    {{ __('Institute Teachers') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('student.notices.index')" :active="request()->routeIs('student.notices.*')">
    {{ __('Notices') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('student.profile.create')" :active="request()->routeIs('student.profile.create')">
    {{ __('Student Profile') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('profile.edit', ['portal' => 'student'])" :active="request()->routeIs('profile.*')">
    {{ __('Profile Settings') }}
</x-responsive-nav-link>
