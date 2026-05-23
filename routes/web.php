<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminTeacherController;
use App\Http\Controllers\Admin\ManageGroupController;
use App\Http\Controllers\Admin\ManageRatingController;
use App\Http\Controllers\Admin\ManageSchoolController;
use App\Http\Controllers\Admin\ManageSubjectController;
use App\Http\Controllers\Student\StudentRequestController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\TeacherFinderController;
use App\Http\Controllers\Teacher\TeacherAttendanceController;
use App\Http\Controllers\Teacher\TeacherNoticeController;
use App\Http\Controllers\Teacher\TeacherProfileController;
use App\Http\Controllers\Teacher\TeacherPostController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\StudentProfileController;
use App\Http\Controllers\Student\StudentAttendanceController;
use App\Http\Controllers\Student\StudentInstituteTeacherController;
use App\Http\Controllers\Student\StudentNoticeController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Frontend\SchoolController;
use App\Http\Controllers\Frontend\TeacherController;
use App\Http\Controllers\Frontend\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    $role = Auth::user()?->role ?? 'student';

    return match ($role) {
        'teacher' => redirect()->route('teacher.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        default => redirect()->route('student.dashboard'),
    };
})
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('/schools', [SchoolController::class, 'index'])
    ->name('schools.index');

Route::get('/teachers', [TeacherController::class, 'index'])
    ->name('teachers.index');

Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])
    ->name('teachers.show');

Route::middleware(['auth', 'student'])->group(function () {
    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])
        ->name('student.dashboard');

    Route::get('/student/profile/create', [StudentProfileController::class, 'create'])
        ->name('student.profile.create');

    Route::post('/student/profile/store', [StudentProfileController::class, 'store'])
        ->name('student.profile.store');

    Route::get('/student/tuition-requests', [StudentRequestController::class, 'index'])
        ->name('student.requests.index');

    Route::post('/student/tuition-requests', [StudentRequestController::class, 'store'])
        ->name('student.requests.store');

    // Student can request a teacher post
    Route::post('/posts/{post}/request', [StudentRequestController::class, 'applyToPost'])
        ->name('posts.request');

    Route::get('/student/attendance', [StudentAttendanceController::class, 'index'])
        ->name('student.attendance.index');

    Route::get('/student/institute-teachers', [StudentInstituteTeacherController::class, 'index'])
        ->name('student.institute-teachers.index');

    Route::get('/student/notices', [StudentNoticeController::class, 'index'])
        ->name('student.notices.index');
});

Route::middleware(['auth', 'teacher'])->group(function () {
    Route::get('/teacher/dashboard', [TeacherDashboardController::class, 'index'])
        ->name('teacher.dashboard');

    Route::get('/teacher/profile', [TeacherProfileController::class, 'create'])
        ->name('teacher.profile.create');

    Route::post('/teacher/profile', [TeacherProfileController::class, 'store'])
        ->name('teacher.profile.store');

    Route::get('/teacher/finder', [TeacherFinderController::class, 'index'])
        ->name('teacher.finder');

    Route::post('/teacher/tuition-requests/{tuitionRequest}/apply', [StudentRequestController::class, 'apply'])
        ->name('teacher.requests.apply');
    
    // Teacher post creation (teachers post to find students)
    Route::get('/teacher/posts/create', [TeacherPostController::class, 'create'])
        ->name('teacher.posts.create');

    Route::post('/teacher/posts', [TeacherPostController::class, 'store'])
        ->name('teacher.posts.store');

    Route::get('/teacher/posts', [TeacherPostController::class, 'index'])
        ->name('teacher.posts.index');

    // View incoming student requests for a specific post
    Route::get('/teacher/posts/{post}/requests', [TeacherPostController::class, 'requests'])
        ->name('teacher.posts.requests');

    Route::delete('/teacher/posts/{post}', [TeacherPostController::class, 'destroy'])
        ->name('teacher.posts.destroy');

    // Teacher approves a student request
    Route::post('/teacher/tuition-requests/{studentRequest}/approve', [StudentRequestController::class, 'approve'])
        ->name('teacher.requests.approve');

    // AJAX avatar upload for teachers
    Route::post('/teacher/profile/avatar', [TeacherProfileController::class, 'uploadAvatar'])
        ->name('teacher.profile.avatar');

    Route::get('/teacher/attendance', [TeacherAttendanceController::class, 'index'])
        ->name('teacher.attendance.index');
    Route::post('/teacher/attendance', [TeacherAttendanceController::class, 'store'])
        ->name('teacher.attendance.store');
    Route::get('/teacher/attendance/{attendance}/edit', [TeacherAttendanceController::class, 'edit'])
        ->name('teacher.attendance.edit');
    Route::put('/teacher/attendance/{attendance}', [TeacherAttendanceController::class, 'update'])
        ->name('teacher.attendance.update');
    Route::delete('/teacher/attendance/{attendance}', [TeacherAttendanceController::class, 'destroy'])
        ->name('teacher.attendance.destroy');

    Route::get('/teacher/notices', [TeacherNoticeController::class, 'index'])
        ->name('teacher.notices.index');
    Route::post('/teacher/notices', [TeacherNoticeController::class, 'store'])
        ->name('teacher.notices.store');
    Route::get('/teacher/notices/{notice}/edit', [TeacherNoticeController::class, 'edit'])
        ->name('teacher.notices.edit');
    Route::put('/teacher/notices/{notice}', [TeacherNoticeController::class, 'update'])
        ->name('teacher.notices.update');
    Route::delete('/teacher/notices/{notice}', [TeacherNoticeController::class, 'destroy'])
        ->name('teacher.notices.destroy');
});

// Dev / preview routes
Route::get('/dev/email/post-requested', function () {
    if (! app()->environment('local') && ! app()->runningUnitTests()) {
        abort(404);
    }

    $sr = \App\Models\StudentRequest::query()->orderBy('created_at', -1)->first();
    if (! $sr) {
        // create a sample payload
        $sr = new \App\Models\StudentRequest();
        $sr->student_name = 'Sample Student';
        $sr->description = 'Hi, I\'m interested in your post.';
    }

    $post = null;
    if (! empty($sr->post_id)) {
        $post = \App\Models\TeacherPost::find($sr->post_id);
    }

    $student = \App\Models\User::find($sr->user_id) ?: (object) ['name' => $sr->student_name ?? 'Sample Student'];

    return new \App\Mail\PostRequested($post, $student, $sr);
});

// Notifications routes
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('/admin/students', [AdminStudentController::class, 'index'])->name('admin.students.index');
    Route::get('/admin/students/create', [AdminStudentController::class, 'create'])->name('admin.students.create');
    Route::post('/admin/students', [AdminStudentController::class, 'store'])->name('admin.students.store');
    Route::get('/admin/students/{student}/edit', [AdminStudentController::class, 'edit'])->name('admin.students.edit');
    Route::put('/admin/students/{student}', [AdminStudentController::class, 'update'])->name('admin.students.update');
    Route::delete('/admin/students/{student}', [AdminStudentController::class, 'destroy'])->name('admin.students.destroy');

    Route::get('/admin/teachers', [AdminTeacherController::class, 'index'])->name('admin.teachers.index');
    Route::get('/admin/teachers/create', [AdminTeacherController::class, 'create'])->name('admin.teachers.create');
    Route::post('/admin/teachers', [AdminTeacherController::class, 'store'])->name('admin.teachers.store');
    Route::get('/admin/teachers/{teacher}/edit', [AdminTeacherController::class, 'edit'])->name('admin.teachers.edit');
    Route::put('/admin/teachers/{teacher}', [AdminTeacherController::class, 'update'])->name('admin.teachers.update');
    Route::delete('/admin/teachers/{teacher}', [AdminTeacherController::class, 'destroy'])->name('admin.teachers.destroy');

    Route::get('/admin/schools', [ManageSchoolController::class, 'index'])->name('admin.schools.index');
    Route::get('/admin/schools/{school}', [ManageSchoolController::class, 'show'])->name('admin.schools.show');
    Route::get('/admin/schools/create', [ManageSchoolController::class, 'create'])->name('admin.schools.create');
    Route::post('/admin/schools', [ManageSchoolController::class, 'store'])->name('admin.schools.store');
    Route::get('/admin/schools/{school}/edit', [ManageSchoolController::class, 'edit'])->name('admin.schools.edit');
    Route::put('/admin/schools/{school}', [ManageSchoolController::class, 'update'])->name('admin.schools.update');
    Route::delete('/admin/schools/{school}', [ManageSchoolController::class, 'destroy'])->name('admin.schools.destroy');

    Route::get('/admin/subjects', [ManageSubjectController::class, 'index'])->name('admin.subjects.index');
    Route::get('/admin/subjects/create', [ManageSubjectController::class, 'create'])->name('admin.subjects.create');
    Route::post('/admin/subjects', [ManageSubjectController::class, 'store'])->name('admin.subjects.store');
    Route::get('/admin/subjects/{subject}/edit', [ManageSubjectController::class, 'edit'])->name('admin.subjects.edit');
    Route::put('/admin/subjects/{subject}', [ManageSubjectController::class, 'update'])->name('admin.subjects.update');
    Route::delete('/admin/subjects/{subject}', [ManageSubjectController::class, 'destroy'])->name('admin.subjects.destroy');

    Route::get('/admin/groups', [ManageGroupController::class, 'index'])->name('admin.groups.index');
    Route::get('/admin/groups/create', [ManageGroupController::class, 'create'])->name('admin.groups.create');
    Route::post('/admin/groups', [ManageGroupController::class, 'store'])->name('admin.groups.store');
    Route::get('/admin/groups/{group}/edit', [ManageGroupController::class, 'edit'])->name('admin.groups.edit');
    Route::put('/admin/groups/{group}', [ManageGroupController::class, 'update'])->name('admin.groups.update');
    Route::delete('/admin/groups/{group}', [ManageGroupController::class, 'destroy'])->name('admin.groups.destroy');

    Route::get('/admin/ratings', [ManageRatingController::class, 'index'])->name('admin.ratings.index');
    Route::get('/admin/ratings/create', [ManageRatingController::class, 'create'])->name('admin.ratings.create');
    Route::post('/admin/ratings', [ManageRatingController::class, 'store'])->name('admin.ratings.store');
    Route::get('/admin/ratings/{rating}/edit', [ManageRatingController::class, 'edit'])->name('admin.ratings.edit');
    Route::put('/admin/ratings/{rating}', [ManageRatingController::class, 'update'])->name('admin.ratings.update');
    Route::delete('/admin/ratings/{rating}', [ManageRatingController::class, 'destroy'])->name('admin.ratings.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
