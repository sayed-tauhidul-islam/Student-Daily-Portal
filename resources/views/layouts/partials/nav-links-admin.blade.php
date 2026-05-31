<x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">{{ __('Dashboard') }}</x-nav-link>
<x-nav-link :href="route('admin.students.index')" :active="request()->routeIs('admin.students.*')">{{ __('Students') }}</x-nav-link>
<x-nav-link :href="route('admin.head-teachers.index')" :active="request()->routeIs('admin.head-teachers.*')">{{ __('Head Teachers') }}</x-nav-link>
<x-nav-link :href="route('admin.teachers.index')" :active="request()->routeIs('admin.teachers.*')">{{ __('Teachers') }}</x-nav-link>
<x-nav-link :href="route('admin.schools.index')" :active="request()->routeIs('admin.schools.*')">{{ __('Schools') }}</x-nav-link>
<x-nav-link :href="route('admin.subjects.index')" :active="request()->routeIs('admin.subjects.*')">{{ __('Subjects') }}</x-nav-link>
<x-nav-link :href="route('admin.groups.index')" :active="request()->routeIs('admin.groups.*')">{{ __('Groups') }}</x-nav-link>
<x-nav-link :href="route('admin.ratings.index')" :active="request()->routeIs('admin.ratings.*')">{{ __('Ratings') }}</x-nav-link>
<x-nav-link :href="route('profile.edit', ['portal' => 'admin'])" :active="request()->routeIs('profile.*')">{{ __('Profile Settings') }}</x-nav-link>
