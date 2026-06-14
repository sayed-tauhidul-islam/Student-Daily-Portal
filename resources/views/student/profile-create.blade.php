<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Student profile</p>
                <h2 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">Complete your profile</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500 sm:text-base">Pick your school from the database, choose multiple subjects, and finish your learning profile in one go.</p>
            </div>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.14),_transparent_34%),radial-gradient(circle_at_right_bottom,_rgba(37,99,235,0.10),_transparent_30%),linear-gradient(180deg,_#f8fbff_0%,_#eef4fb_100%)] py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @php
                $avatarUrl = auth()->user()?->image_url;
            @endphp

            @php
                $schoolPayload = $schools->map(function ($school) {
                    return [
                        'name' => $school->name,
                        'area' => $school->area,
                        'type' => $school->type,
                        'rating' => $school->rating,
                    ];
                })->values()->all();

                $subjectPayload = $subjects->pluck('name')->values()->all();
                $teacherPayload = $teachers->values()->all();
                $areaPayload = $areas->values()->all();
                $groupPayload = $groups->values()->all();
                $classPayload = $classes->values()->all();
                $initialPayload = [
                    'school' => old('school', $profile?->school ?? ''),
                    'subjects' => old('subjects', $profile?->subjects ?? []),
                    'teacher' => old('preferred_teacher', $profile?->preferred_teacher ?? ''),
                    'area' => old('area', $profile?->area ?? ''),
                    'class' => old('class', $profile?->class ?? ''),
                    'group' => old('group', $profile?->group ?? ''),
                ];
            @endphp

            <form action="{{ route('student.profile.store') }}" method="POST" enctype="multipart/form-data" x-data='profileForm(
                @json($schoolPayload),
                @json($subjectPayload),
                @json($teacherPayload),
                @json($areaPayload),
                @json($groupPayload),
                @json($classPayload),
                @json($initialPayload)
            )' x-init="init()" class="rounded-[2.25rem] border border-slate-200/80 bg-white/95 p-6 shadow-[0_24px_60px_rgba(15,23,42,0.10)] backdrop-blur sm:p-8">
                @csrf

                <div class="mb-6 rounded-[1.75rem] border border-sky-200/70 bg-gradient-to-r from-sky-50 to-cyan-50 p-5">
                    <div class="flex flex-col gap-5 md:flex-row md:items-center">
                        <label for="student_image" class="flex h-24 w-24 cursor-pointer items-center justify-center overflow-hidden rounded-[1.5rem] bg-slate-900 text-3xl font-black text-white shadow-[0_14px_30px_rgba(15,23,42,0.18)]">
                            @if ($avatarUrl)
                                <img id="student-avatar-preview" src="{{ $avatarUrl }}" alt="{{ auth()->user()?->name }}" class="h-full w-full object-cover">
                                <span id="student-avatar-fallback" class="hidden">{{ strtoupper(substr(auth()->user()?->name ?? 'S', 0, 1)) }}</span>
                            @else
                                <img id="student-avatar-preview" src="" alt="{{ auth()->user()?->name }}" class="hidden h-full w-full object-cover">
                                <span id="student-avatar-fallback">{{ strtoupper(substr(auth()->user()?->name ?? 'S', 0, 1)) }}</span>
                            @endif
                        </label>
                        <div class="flex-1">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Profile image</p>
                            <p class="mt-2 text-sm text-slate-600">Tap the photo icon to choose a profile image.</p>
                            <input id="student_image" type="file" name="image" accept="image/*" class="sr-only">
                            <p class="mt-3 text-xs text-slate-500">JPG, PNG, WEBP up to 2 MB</p>
                        </div>
                    </div>
                    @error('image')
                        <p class="mt-3 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 md:grid-cols-2">

                    {{-- CLASS --}}
                    <div>
                        <label for="class" class="mb-2 block text-sm font-semibold text-slate-700">Class</label>
                        <div class="relative" @click.outside="openClassList = false">
                            <input
                                id="class" type="text" name="class"
                                x-model="classQuery"
                                @focus="showAllClasses()"
                                @input.debounce.200ms="filterClasses()"
                                autocomplete="off" required
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 pr-10 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100"
                                placeholder="Example: 10"
                            >
                            <button type="button"
                                @click.stop="if(openClassList){ openClassList = false } else { showAllClasses() }"
                                class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full p-1 text-slate-400 hover:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
                            </button>
                            <div x-show="openClassList && classMatches.length" x-transition
                                class="absolute z-20 mt-2 max-h-60 w-full overflow-auto rounded-2xl border border-slate-200 bg-white shadow-[0_16px_40px_rgba(15,23,42,0.12)]">
                                <template x-for="item in classMatches" :key="item">
                                    <button type="button" @click="chooseClass(item)"
                                        class="w-full border-b border-slate-100 px-4 py-3 text-left hover:bg-slate-50">
                                        <span x-text="item"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- GROUP --}}
                    <div x-show="shouldShowGroup()" x-transition>
                        <label for="group" class="mb-2 block text-sm font-semibold text-slate-700">Group</label>
                        <div class="relative" @click.outside="openGroupList = false">
                            <input
                                id="group" type="text" name="group"
                                x-model="groupQuery"
                                @focus="showAllGroups()"
                                @input.debounce.200ms="filterGroups()"
                                autocomplete="off"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 pr-10 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100"
                                placeholder="Example: Science"
                            >
                            <button type="button"
                                @click.stop="if(openGroupList){ openGroupList = false } else { showAllGroups() }"
                                class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full p-1 text-slate-400 hover:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
                            </button>
                            <div x-show="openGroupList && groupMatches.length" x-transition
                                class="absolute z-20 mt-2 max-h-60 w-full overflow-auto rounded-2xl border border-slate-200 bg-white shadow-[0_16px_40px_rgba(15,23,42,0.12)]">
                                <template x-for="item in groupMatches" :key="item">
                                    <button type="button" @click="chooseGroup(item)"
                                        class="w-full border-b border-slate-100 px-4 py-3 text-left hover:bg-slate-50">
                                        <span x-text="item"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- SCHOOL --}}
                    <div class="md:col-span-2">
                        <label for="school" class="mb-2 block text-sm font-semibold text-slate-700">School/College</label>
                        <div class="relative" @click.outside="openSchoolList = false">
                            <input
                                id="school" type="text" name="school"
                                x-model="schoolQuery"
                                @focus="openSchoolList = true; filterSchools()"
                                @input.debounce.200ms="filterSchools()"
                                autocomplete="off" required
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 pr-10 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100"
                                placeholder="Type the first letters to search from Khulna schools"
                            >
                            <button type="button"
                                @click.stop="if(openSchoolList){ openSchoolList = false } else { showAllSchools() }"
                                class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full p-1 text-slate-400 hover:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
                            </button>
                            <div x-show="openSchoolList && schoolMatches.length" x-transition
                                class="absolute z-20 mt-2 max-h-80 w-full overflow-auto rounded-2xl border border-slate-200 bg-white shadow-[0_16px_40px_rgba(15,23,42,0.12)]">
                                <template x-for="school in schoolMatches" :key="school.name">
                                    <button type="button" @click="chooseSchool(school)"
                                        class="flex w-full items-start justify-between gap-4 border-b border-slate-100 px-4 py-3 text-left transition last:border-b-0 hover:bg-slate-50">
                                        <div>
                                            <div class="font-semibold text-slate-900" x-text="school.name"></div>
                                            <div class="text-xs text-slate-500">
                                                <span x-text="school.area"></span> • <span x-text="school.type"></span> • Rating <span x-text="school.rating"></span>
                                            </div>
                                        </div>
                                        <span class="text-xs font-semibold text-sky-600">Select</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        @error('school')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- SUBJECTS --}}
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Subjects</label>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 focus-within:border-sky-400 focus-within:bg-white focus-within:ring-4 focus-within:ring-sky-100">
                            <div class="flex flex-wrap gap-2">
                                <template x-for="subject in selectedSubjects" :key="subject">
                                    <span class="inline-flex items-center gap-2 rounded-full bg-slate-950 px-3 py-2 text-sm font-semibold text-white shadow-sm">
                                        <span x-text="subject"></span>
                                        <button type="button" @click="removeSubject(subject)" class="text-white/70 transition hover:text-white">×</button>
                                        <input type="hidden" name="subjects[]" :value="subject">
                                    </span>
                                </template>
                                <div class="relative flex-1" @click.outside="openSubjectList = false">
                                    <input type="text"
                                        x-model="subjectQuery"
                                        @focus="showAllSubjects()"
                                        @input.debounce.200ms="filterSubjects()"
                                        @keydown.enter.prevent="addSubjectFromInput()"
                                        autocomplete="off"
                                        class="w-full border-0 bg-transparent px-1 py-2 text-slate-900 outline-none placeholder:text-slate-400"
                                        placeholder="Search and add multiple subjects"
                                    >
                                    <button type="button"
                                        @click.stop="if(openSubjectList){ openSubjectList = false } else { showAllSubjects() }"
                                        class="absolute right-0 top-1/2 -translate-y-1/2 rounded-full p-1 text-slate-400 hover:text-slate-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
                                    </button>
                                    <div x-show="openSubjectList && subjectMatches.length" x-transition
                                        class="absolute z-20 mt-2 max-h-80 w-full overflow-auto rounded-2xl border border-slate-200 bg-white shadow-[0_16px_40px_rgba(15,23,42,0.12)]">
                                        <template x-for="subject in subjectMatches" :key="subject">
                                            <button type="button" @click="addSubject(subject)"
                                                class="flex w-full items-start justify-between gap-4 border-b border-slate-100 px-4 py-3 text-left transition last:border-b-0 hover:bg-slate-50">
                                                <div>
                                                    <div class="font-semibold text-slate-900" x-text="subject"></div>
                                                    <div class="text-xs text-slate-500">Tap to add</div>
                                                </div>
                                                <span class="text-xs font-semibold text-sky-600">Add</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('subjects')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                        @error('subjects.*')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- TEACHER --}}
                    <div class="md:col-span-2">
                        <label for="preferred_teacher" class="mb-2 block text-sm font-semibold text-slate-700">Preferred Teacher</label>
                        <div class="relative" @click.outside="openTeacherList = false">
                            <input
                                id="preferred_teacher" type="text" name="preferred_teacher"
                                x-model="teacherQuery"
                                @focus="showAllTeachers()"
                                @input.debounce.200ms="filterTeachers()"
                                autocomplete="off"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 pr-10 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100"
                                placeholder="Choose teacher from database"
                            >
                            <button type="button"
                                @click.stop="if(openTeacherList){ openTeacherList = false } else { showAllTeachers() }"
                                class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full p-1 text-slate-400 hover:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
                            </button>
                            <div x-show="openTeacherList && teacherMatches.length" x-transition
                                class="absolute z-20 mt-2 max-h-80 w-full overflow-auto rounded-2xl border border-slate-200 bg-white shadow-[0_16px_40px_rgba(15,23,42,0.12)]">
                                <template x-for="teacher in teacherMatches" :key="teacher.value">
                                    <button type="button" @click="chooseTeacher(teacher)"
                                        class="flex w-full items-start justify-between gap-4 border-b border-slate-100 px-4 py-3 text-left transition last:border-b-0 hover:bg-slate-50">
                                        <div>
                                            <div class="font-semibold text-slate-900" x-text="teacher.name"></div>
                                            <div class="text-xs text-slate-500">
                                                <span x-text="teacher.institution || 'Institution N/A'"></span> • <span x-text="teacher.subject || 'General Studies'"></span> • <span x-text="teacher.area || 'Khulna Sadar'"></span>
                                            </div>
                                        </div>
                                        <span class="text-xs font-semibold text-sky-600">Select</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        @error('preferred_teacher')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- AREA — FIX: button এখন input-এর relative div-এর ভেতরে --}}
                    <div>
                        <label for="area" class="mb-2 block text-sm font-semibold text-slate-700">Area</label>
                        <div class="relative" @click.outside="openAreaList = false">
                            <input
                                id="area" type="text" name="area"
                                value="{{ old('area', $profile?->area) }}"
                                x-model="areaQuery"
                                @focus="showAllAreas()"
                                @input.debounce.200ms="filterSchools(); filterAreas()"
                                autocomplete="off" required
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 pr-10 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100"
                                placeholder="Your location area"
                            >
                            <button type="button"
                                @click.stop="if(openAreaList){ openAreaList = false } else { showAllAreas() }"
                                class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full p-1 text-slate-400 hover:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
                            </button>
                            <div x-show="openAreaList && areaMatches.length" x-transition
                                class="absolute z-20 mt-2 max-h-72 w-full overflow-auto rounded-2xl border border-slate-200 bg-white shadow-[0_16px_40px_rgba(15,23,42,0.12)]">
                                <template x-for="area in areaMatches" :key="area">
                                    <button type="button" @click="chooseArea(area)"
                                        class="flex w-full items-center justify-between gap-4 border-b border-slate-100 px-4 py-3 text-left transition last:border-b-0 hover:bg-slate-50">
                                        <span class="font-semibold text-slate-900" x-text="area"></span>
                                        <span class="text-xs font-semibold text-sky-600">Select</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- PHONE --}}
                    <div>
                        <label for="phone" class="mb-2 block text-sm font-semibold text-slate-700">Phone</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone', $profile?->phone) }}"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100"
                            placeholder="Contact number">
                    </div>

                </div>

                {{-- BIO --}}
                <div class="mt-5">
                    <label for="bio" class="mb-2 block text-sm font-semibold text-slate-700">Bio</label>
                    <textarea id="bio" name="bio" rows="5"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100"
                        placeholder="Write a short introduction about yourself">{{ old('bio', $profile?->bio) }}</textarea>
                </div>

                <div class="relative z-30 mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">Suggestions are loaded from the database and grouped alphabetically, with schools narrowing further when you type your area.</p>
                    <button type="submit" class="relative z-40 inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-slate-900 to-sky-700 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-slate-950/15 transition hover:opacity-90">
                        Save profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const studentImageInput = document.getElementById('student_image');
        const studentAvatarPreview = document.getElementById('student-avatar-preview');
        const studentAvatarFallback = document.getElementById('student-avatar-fallback');

        if (studentImageInput && studentAvatarPreview) {
            studentImageInput.addEventListener('change', () => {
                const file = studentImageInput.files?.[0];
                if (!file) return;

                const previewUrl = URL.createObjectURL(file);
                studentAvatarPreview.src = previewUrl;
                studentAvatarPreview.classList.remove('hidden');

                if (studentAvatarFallback) {
                    studentAvatarFallback.classList.add('hidden');
                }
            });
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('profileForm', (schools, subjects, teachers, areas, groups, classes, initial) => ({
                schoolList:      schools,
                subjectList:     subjects,
                teacherList:     teachers,
                areaList:        areas,
                groupList:       groups,
                classList:       classes,

                schoolQuery:  initial.school || '',
                teacherQuery: initial.teacher || '',
                areaQuery:    initial.area   || '',
                groupQuery:   initial.group  || '',   // ✅ FIX: আগে swap ছিল
                classQuery:   initial.class  || '',   // ✅ FIX: আগে swap ছিল
                subjectQuery: '',

                schoolMatches:  [],
                subjectMatches: [],
                teacherMatches: [],
                areaMatches:    [],
                groupMatches:   [],
                classMatches:   [],

                openAreaList:    false,
                openSchoolList:  false,
                openSubjectList: false,
                openTeacherList: false,
                openGroupList:   false,
                openClassList:   false,

                selectedSubjects: Array.isArray(initial.subjects) ? initial.subjects : [],

                filterSchools() {
                    const query     = this.schoolQuery.trim().toLowerCase();
                    const areaQuery = this.areaQuery.trim().toLowerCase();
                    const filtered  = this.schoolList.filter((school) => {
                        const name  = school.name.toLowerCase();
                        const area  = (school.area || '').toLowerCase();
                        const matchName = !query     || name.startsWith(query)     || name.includes(query);
                        const matchArea = !areaQuery || area.startsWith(areaQuery) || area.includes(areaQuery);
                        return matchName && matchArea;
                    });
                    this.schoolMatches = filtered.slice(0, 10);
                },
                filterGroups() {
                    const query    = this.groupQuery.trim().toLowerCase();
                    const filtered = this.groupList.filter((g) =>
                        !query || g.toLowerCase().startsWith(query) || g.toLowerCase().includes(query)
                    );
                    this.groupMatches = filtered.slice(0, 10);
                },
                filterClasses() {
                    const query    = this.classQuery.trim().toLowerCase();
                    const filtered = this.classList.filter((c) =>
                        !query || c.toLowerCase().startsWith(query) || c.toLowerCase().includes(query)
                    );
                    this.classMatches = filtered.slice(0, 10);
                },
                filterSubjects() {
                    const query    = this.subjectQuery.trim().toLowerCase();
                    const selected = new Set(this.selectedSubjects.map((s) => s.toLowerCase()));
                    const filtered = this.subjectList.filter((subject) => {
                        const val = subject.toLowerCase();
                        return (!query || val.startsWith(query) || val.includes(query)) && !selected.has(val);
                    });
                    this.subjectMatches = filtered.slice(0, 10);
                },
                filterTeachers() {
                    const query = this.teacherQuery.trim().toLowerCase();
                    const selectedSchool = this.schoolQuery.trim().toLowerCase();
                    const filtered = this.teacherList.filter((teacher) => {
                        const name = (teacher.name || '').toLowerCase();
                        const institution = (teacher.institution || '').toLowerCase();
                        const subject = (teacher.subject || '').toLowerCase();

                        const matchText = !query || name.includes(query) || institution.includes(query) || subject.includes(query);
                        const matchSchool = selectedSchool && institution && (
                            institution === selectedSchool ||
                            institution.includes(selectedSchool) ||
                            selectedSchool.includes(institution)
                        );

                        return matchText && matchSchool;
                    });

                    this.teacherMatches = filtered.slice(0, 20);
                },
                filterAreas() {
                    const query    = this.areaQuery.trim().toLowerCase();
                    const filtered = this.areaList.filter((area) => {
                        const val = (area || '').toLowerCase();
                        return !query || val.startsWith(query) || val.includes(query);
                    });
                    this.areaMatches = filtered.slice(0, 10);
                },

                // ✅ FIX: সব showAll* method এখন dropdown খুলে দেয়
                showAllSchools() {
                    this.schoolMatches  = this.schoolList.slice(0, 100);
                    this.openSchoolList = true;
                },
                showAllSubjects() {
                    const selected      = new Set(this.selectedSubjects.map((s) => s.toLowerCase()));
                    this.subjectMatches = this.subjectList.filter((s) => !selected.has(s.toLowerCase())).slice(0, 200);
                    this.openSubjectList = true;
                },
                showAllTeachers() {
                    this.filterTeachers();
                    this.openTeacherList = true;
                },
                showAllAreas() {
                    this.areaMatches  = this.areaList.slice(0, 200);
                    this.openAreaList  = true;
                },
                showAllGroups() {
                    this.groupMatches  = this.groupList.slice(0, 50);
                    this.openGroupList = true;
                },
                showAllClasses() {
                    this.classMatches  = this.classList.slice(0, 50);
                    this.openClassList = true;
                },

                chooseSchool(school) {
                    this.schoolQuery    = school.name;
                    if (school.area) { this.areaQuery = school.area; }
                    this.teacherQuery = '';
                    this.openSchoolList = false;
                    this.schoolMatches  = [];
                    this.filterAreas();
                    this.filterTeachers();
                },
                chooseGroup(group) {
                    this.groupQuery    = group;
                    this.openGroupList = false;
                    this.groupMatches  = [];
                },
                chooseClass(cls) {
                    this.classQuery    = cls;
                    if (!this.shouldShowGroup()) {
                        this.groupQuery = '';
                    }
                    this.openClassList = false;
                    this.classMatches  = [];
                },
                chooseArea(area) {
                    this.areaQuery    = area;
                    this.openAreaList = false;
                    this.areaMatches  = [];
                    this.filterSchools();
                    this.filterTeachers();
                },
                chooseTeacher(teacher) {
                    this.teacherQuery = teacher.value;
                    this.openTeacherList = false;
                    this.teacherMatches = [];
                },
                addSubject(subject) {
                    if (!this.selectedSubjects.includes(subject)) {
                        this.selectedSubjects.push(subject);
                    }
                    this.subjectQuery = '';
                    this.filterSubjects();
                    this.openSubjectList = true;
                },
                addSubjectFromInput() {
                    if (!this.subjectQuery.trim()) return;
                    const exact = this.subjectList.find(
                        (s) => s.toLowerCase() === this.subjectQuery.trim().toLowerCase()
                    );
                    if (exact) { this.addSubject(exact); }
                },
                removeSubject(subject) {
                    this.selectedSubjects = this.selectedSubjects.filter((item) => item !== subject);
                    this.filterSubjects();
                },
                classNumber() {
                    const match = String(this.classQuery || '').match(/\d+/);
                    return match ? Number(match[0]) : null;
                },
                shouldShowGroup() {
                    const number = this.classNumber();
                    return number === null || number < 1 || number > 8;
                },

                init() {
                    if (!this.shouldShowGroup()) {
                        this.groupQuery = '';
                    }
                    this.filterSchools();
                    this.filterSubjects();
                    this.filterTeachers();
                    this.filterAreas();
                    this.filterGroups();
                    this.filterClasses();
                },
            }));
        });
    </script>
</x-app-layout>
