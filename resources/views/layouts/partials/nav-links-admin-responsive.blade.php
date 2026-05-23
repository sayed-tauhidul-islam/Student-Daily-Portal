<x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">{{ __('Dashboard') }}</x-responsive-nav-link>
<x-responsive-nav-link :href="route('admin.students.index')" :active="request()->routeIs('admin.students.*')">{{ __('Students') }}</x-responsive-nav-link>
<x-responsive-nav-link :href="route('admin.teachers.index')" :active="request()->routeIs('admin.teachers.*')">{{ __('Teachers') }}</x-responsive-nav-link>
<x-responsive-nav-link :href="route('admin.schools.index')" :active="request()->routeIs('admin.schools.*')">{{ __('Schools') }}</x-responsive-nav-link>
<x-responsive-nav-link :href="route('admin.subjects.index')" :active="request()->routeIs('admin.subjects.*')">{{ __('Subjects') }}</x-responsive-nav-link>
<x-responsive-nav-link :href="route('admin.groups.index')" :active="request()->routeIs('admin.groups.*')">{{ __('Groups') }}</x-responsive-nav-link>
<x-responsive-nav-link :href="route('admin.ratings.index')" :active="request()->routeIs('admin.ratings.*')">{{ __('Ratings') }}</x-responsive-nav-link>
