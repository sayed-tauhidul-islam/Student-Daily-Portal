<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SchoolPortalController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminTeacherController;
use App\Http\Controllers\Admin\ManageTeacherController;
use App\Http\Controllers\Admin\ManageGroupController;
use App\Http\Controllers\Admin\ManageRatingController;
use App\Http\Controllers\Admin\ManageSchoolController;
use App\Http\Controllers\Admin\ManageSubjectController;
use App\Http\Controllers\Student\StudentRequestController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\TeacherFinderController;
use App\Http\Controllers\Teacher\TeacherAttendanceController;
use App\Http\Controllers\Teacher\TeacherNoticeController;
use App\Http\Controllers\Teacher\TeacherProfileController;
use App\Http\Controllers\Teacher\TeacherPostController;
use App\Http\Controllers\Teacher\TeacherStudentProgressController;
use App\Http\Controllers\TeacherAdmin\TeacherAdminDashboardController;
use App\Http\Controllers\TeacherAdmin\TeacherAdminStudentController;
use App\Http\Controllers\TeacherAdmin\TeacherAdminTeacherController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\StudentProfileController;
use App\Http\Controllers\Student\StudentAttendanceController;
use App\Http\Controllers\Student\StudentInstituteTeacherController;
use App\Http\Controllers\Student\StudentNoticeController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentProgressHubController;
use App\Http\Controllers\Frontend\SchoolController;
use App\Http\Controllers\Frontend\TeacherController;
use App\Http\Controllers\Frontend\HomeController;
use Illuminate\Http\Request;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function (Request $request) {
    $portal = $request->string('portal')->toString();

    $portal = in_array($portal, ['student', 'teacher', 'teacher-admin', 'admin', 'super-admin'], true)
        ? $portal
        : null;

    $activeGuard = (string) $request->session()->get('active_guard', '');
    $guard = in_array($activeGuard, ['admin', 'teacher_admin', 'teacher', 'student'], true)
        ? $activeGuard
        : collect(['admin', 'teacher_admin', 'teacher', 'student'])
            ->first(fn (string $guard) => Auth::guard($guard)->check());

    $role = $guard ? (Auth::guard($guard)->user()?->role ?? 'student') : 'student';
    $resolvedPortal = $portal ?? match ($role) {
        'teacher' => 'teacher',
        'teacher_admin' => 'teacher-admin',
        'admin', 'super_admin' => 'admin',
        default => 'student',
    };

    return match ($resolvedPortal) {
        'teacher' => redirect()->route('teacher.dashboard'),
        'teacher-admin' => redirect()->route('teacher-admin.dashboard'),
        'admin', 'super-admin' => redirect()->route('admin.dashboard'),
        default => redirect()->route('student.dashboard'),
    };
})
    ->middleware(['auth:student,teacher,teacher_admin,admin', 'portal_guard'])
    ->name('dashboard');

Route::get('/schools', [SchoolController::class, 'index'])
    ->name('schools.index');

Route::get('/schools/{school}', [SchoolController::class, 'show'])
    ->name('schools.show');

Route::get('/teachers', [TeacherController::class, 'index'])
    ->name('teachers.index');

Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])
    ->name('teachers.show');

Route::middleware(['auth:student', 'student'])->group(function () {
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
    Route::get('/student/progress-hub', [StudentProgressHubController::class, 'index'])
        ->name('student.progress-hub.index');
    Route::post('/student/progress-hub/tasks', [StudentProgressHubController::class, 'storeTask'])
        ->name('student.progress-hub.tasks.store');
    Route::patch('/student/progress-hub/tasks/{task}', [StudentProgressHubController::class, 'toggleTask'])
        ->name('student.progress-hub.tasks.toggle');
    Route::post('/student/progress-hub/assignments', [StudentProgressHubController::class, 'storeAssignment'])
        ->name('student.progress-hub.assignments.store');
    Route::patch('/student/progress-hub/assignments/{assignment}', [StudentProgressHubController::class, 'updateAssignment'])
        ->name('student.progress-hub.assignments.update');
    Route::post('/student/progress-hub/goals', [StudentProgressHubController::class, 'storeGoal'])
        ->name('student.progress-hub.goals.store');
    Route::patch('/student/progress-hub/goals/{goal}', [StudentProgressHubController::class, 'updateGoal'])
        ->name('student.progress-hub.goals.update');
    Route::post('/student/progress-hub/parent-portal', [StudentProgressHubController::class, 'saveParentPortal'])
        ->name('student.progress-hub.parent-portal.save');
    Route::delete('/student/progress-hub/parent-portal', [StudentProgressHubController::class, 'revokeParentPortal'])
        ->name('student.progress-hub.parent-portal.revoke');
    Route::get('/student/school-members', [SchoolPortalController::class, 'studentSchool'])
        ->name('student.school-members');
    Route::get('/student/messages', [SchoolPortalController::class, 'messages'])
        ->name('student.messages');
    Route::post('/student/messages', [SchoolPortalController::class, 'sendMessage'])
        ->name('student.messages.send');
    Route::patch('/student/messages/{message}', [SchoolPortalController::class, 'updateMessage'])
        ->name('student.messages.update');
    Route::delete('/student/messages/{message}', [SchoolPortalController::class, 'deleteMessage'])
        ->name('student.messages.delete');
    Route::get('/student/complaints', [SchoolPortalController::class, 'complaints'])
        ->name('student.complaints');
    Route::post('/student/complaints', [SchoolPortalController::class, 'storeComplaint'])
        ->name('student.complaints.store');
    Route::get('/student/leaves', [SchoolPortalController::class, 'leaves'])
        ->name('student.leaves');
    Route::post('/student/leaves', [SchoolPortalController::class, 'storeLeave'])
        ->name('student.leaves.store');
    Route::get('/student/reading-logs', [SchoolPortalController::class, 'readingLogs'])
        ->name('student.reading-logs');
    Route::post('/student/reading-logs', [SchoolPortalController::class, 'storeReadingLog'])
        ->name('student.reading-logs.store');
    Route::get('/student/payments', [SchoolPortalController::class, 'payments'])
        ->name('student.payments');
    Route::post('/student/payments', [SchoolPortalController::class, 'submitPayment'])
        ->name('student.payments.submit');
});

Route::middleware(['auth:teacher', 'teacher'])->group(function () {
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
    Route::get('/teacher/student-progress', [TeacherStudentProgressController::class, 'index'])
        ->name('teacher.progress.index');
    Route::get('/teacher/student-progress/{student}/edit', [TeacherStudentProgressController::class, 'edit'])
        ->name('teacher.progress.edit');
    Route::put('/teacher/student-progress/{student}', [TeacherStudentProgressController::class, 'update'])
        ->name('teacher.progress.update');
    Route::delete('/teacher/student-progress/{student}', [TeacherStudentProgressController::class, 'destroy'])
        ->name('teacher.progress.destroy');
    Route::post('/teacher/student-progress/{student}/exam-results', [TeacherStudentProgressController::class, 'storeExamResult'])
        ->name('teacher.progress.results.store');
    Route::delete('/teacher/student-progress/{student}/exam-results/{result}', [TeacherStudentProgressController::class, 'destroyExamResult'])
        ->name('teacher.progress.results.destroy');
    Route::get('/teacher/messages', [SchoolPortalController::class, 'messages'])
        ->name('teacher.messages');
    Route::post('/teacher/messages', [SchoolPortalController::class, 'sendMessage'])
        ->name('teacher.messages.send');
    Route::patch('/teacher/messages/{message}', [SchoolPortalController::class, 'updateMessage'])
        ->name('teacher.messages.update');
    Route::delete('/teacher/messages/{message}', [SchoolPortalController::class, 'deleteMessage'])
        ->name('teacher.messages.delete');
    Route::get('/teacher/complaints', [SchoolPortalController::class, 'complaints'])
        ->name('teacher.complaints');
    Route::post('/teacher/complaints', [SchoolPortalController::class, 'storeComplaint'])
        ->name('teacher.complaints.store');
    Route::get('/teacher/leaves', [SchoolPortalController::class, 'leaves'])
        ->name('teacher.leaves');
    Route::post('/teacher/leaves', [SchoolPortalController::class, 'storeLeave'])
        ->name('teacher.leaves.store');
    Route::get('/teacher/payments', [SchoolPortalController::class, 'payments'])
        ->name('teacher.payments');
    Route::patch('/teacher/payments/{payment}/received', [SchoolPortalController::class, 'confirmReceived'])
        ->name('teacher.payments.received');
});

Route::get('/parent-portal/{code}', [ParentPortalController::class, 'show'])
    ->name('parent.portal');

Route::middleware(['auth:student,teacher,teacher_admin,admin', 'portal_guard'])->group(function () {
    Route::get('/leave-documents/{leave}', [SchoolPortalController::class, 'downloadLeaveDocument'])
        ->name('leave-documents.download');
});

Route::middleware(['auth:teacher_admin', 'teacher_admin'])->group(function () {
    Route::get('/teacher-admin/dashboard', [TeacherAdminDashboardController::class, 'index'])
        ->name('teacher-admin.dashboard');

    Route::get('/teacher-admin/students', [TeacherAdminStudentController::class, 'index'])
        ->name('teacher-admin.students.index');
    Route::get('/teacher-admin/students/create', [TeacherAdminStudentController::class, 'create'])
        ->name('teacher-admin.students.create');
    Route::post('/teacher-admin/students', [TeacherAdminStudentController::class, 'store'])
        ->name('teacher-admin.students.store');
    Route::post('/teacher-admin/students/{student}/fees', [TeacherAdminStudentController::class, 'storeFee'])
        ->name('teacher-admin.students.fees.store');
    Route::patch('/teacher-admin/students/{student}/fees/{payment}', [TeacherAdminStudentController::class, 'updateFee'])
        ->name('teacher-admin.students.fees.update');
    Route::post('/teacher-admin/students/{student}/notices', [TeacherAdminStudentController::class, 'storeNotice'])
        ->name('teacher-admin.students.notices.store');
    Route::post('/teacher-admin/students/{student}/tasks', [TeacherAdminStudentController::class, 'storeTask'])
        ->name('teacher-admin.students.tasks.store');
    Route::get('/teacher-admin/students/{student}', [TeacherAdminStudentController::class, 'show'])
        ->name('teacher-admin.students.show');
    Route::get('/teacher-admin/students/{student}/edit', [TeacherAdminStudentController::class, 'edit'])
        ->name('teacher-admin.students.edit');
    Route::put('/teacher-admin/students/{student}', [TeacherAdminStudentController::class, 'update'])
        ->name('teacher-admin.students.update');
    Route::delete('/teacher-admin/students/{student}', [TeacherAdminStudentController::class, 'destroy'])
        ->name('teacher-admin.students.destroy');
    Route::get('/teacher-admin/teachers', [TeacherAdminTeacherController::class, 'index'])
        ->name('teacher-admin.teachers.index');
    Route::get('/teacher-admin/teachers/create', [TeacherAdminTeacherController::class, 'create'])
        ->name('teacher-admin.teachers.create');
    Route::post('/teacher-admin/teachers', [TeacherAdminTeacherController::class, 'store'])
        ->name('teacher-admin.teachers.store');
    Route::get('/teacher-admin/teachers/{teacher}', [TeacherAdminTeacherController::class, 'show'])
        ->name('teacher-admin.teachers.show');
    Route::get('/teacher-admin/teachers/{teacher}/edit', [TeacherAdminTeacherController::class, 'edit'])
        ->name('teacher-admin.teachers.edit');
    Route::put('/teacher-admin/teachers/{teacher}', [TeacherAdminTeacherController::class, 'update'])
        ->name('teacher-admin.teachers.update');
    Route::delete('/teacher-admin/teachers/{teacher}', [TeacherAdminTeacherController::class, 'destroy'])
        ->name('teacher-admin.teachers.destroy');

    Route::get('/teacher-admin/school-database', [TeacherAdminDashboardController::class, 'database'])
        ->name('teacher-admin.database');
    Route::get('/teacher-admin/attendance', [TeacherAttendanceController::class, 'index'])
        ->name('teacher-admin.attendance.index');
    Route::post('/teacher-admin/attendance', [TeacherAttendanceController::class, 'store'])
        ->name('teacher-admin.attendance.store');
    Route::get('/teacher-admin/attendance/{attendance}/edit', [TeacherAttendanceController::class, 'edit'])
        ->name('teacher-admin.attendance.edit');
    Route::put('/teacher-admin/attendance/{attendance}', [TeacherAttendanceController::class, 'update'])
        ->name('teacher-admin.attendance.update');
    Route::delete('/teacher-admin/attendance/{attendance}', [TeacherAttendanceController::class, 'destroy'])
        ->name('teacher-admin.attendance.destroy');
    Route::get('/teacher-admin/notices', [TeacherNoticeController::class, 'index'])
        ->name('teacher-admin.notices.index');
    Route::post('/teacher-admin/notices', [TeacherNoticeController::class, 'store'])
        ->name('teacher-admin.notices.store');
    Route::get('/teacher-admin/notices/{notice}/edit', [TeacherNoticeController::class, 'edit'])
        ->name('teacher-admin.notices.edit');
    Route::put('/teacher-admin/notices/{notice}', [TeacherNoticeController::class, 'update'])
        ->name('teacher-admin.notices.update');
    Route::delete('/teacher-admin/notices/{notice}', [TeacherNoticeController::class, 'destroy'])
        ->name('teacher-admin.notices.destroy');
    Route::get('/teacher-admin/student-progress', [TeacherStudentProgressController::class, 'index'])
        ->name('teacher-admin.progress.index');
    Route::get('/teacher-admin/student-progress/{student}/edit', [TeacherStudentProgressController::class, 'edit'])
        ->name('teacher-admin.progress.edit');
    Route::put('/teacher-admin/student-progress/{student}', [TeacherStudentProgressController::class, 'update'])
        ->name('teacher-admin.progress.update');
    Route::delete('/teacher-admin/student-progress/{student}', [TeacherStudentProgressController::class, 'destroy'])
        ->name('teacher-admin.progress.destroy');
    Route::post('/teacher-admin/student-progress/{student}/exam-results', [TeacherStudentProgressController::class, 'storeExamResult'])
        ->name('teacher-admin.progress.results.store');
    Route::delete('/teacher-admin/student-progress/{student}/exam-results/{result}', [TeacherStudentProgressController::class, 'destroyExamResult'])
        ->name('teacher-admin.progress.results.destroy');
    Route::get('/teacher-admin/reading-logs', [SchoolPortalController::class, 'readingLogs'])
        ->name('teacher-admin.reading-logs');
    Route::get('/teacher-admin/messages', [SchoolPortalController::class, 'messages'])
        ->name('teacher-admin.messages');
    Route::post('/teacher-admin/messages', [SchoolPortalController::class, 'sendMessage'])
        ->name('teacher-admin.messages.send');
    Route::patch('/teacher-admin/messages/{message}', [SchoolPortalController::class, 'updateMessage'])
        ->name('teacher-admin.messages.update');
    Route::delete('/teacher-admin/messages/{message}', [SchoolPortalController::class, 'deleteMessage'])
        ->name('teacher-admin.messages.delete');
    Route::get('/teacher-admin/complaints', [SchoolPortalController::class, 'complaints'])
        ->name('teacher-admin.complaints');
    Route::patch('/teacher-admin/complaints/{complaint}', [SchoolPortalController::class, 'updateComplaint'])
        ->name('teacher-admin.complaints.update');
    Route::get('/teacher-admin/leaves', [SchoolPortalController::class, 'leaves'])
        ->name('teacher-admin.leaves');
    Route::patch('/teacher-admin/leaves/{leave}', [SchoolPortalController::class, 'updateLeave'])
        ->name('teacher-admin.leaves.update');
    Route::get('/teacher-admin/payments', [SchoolPortalController::class, 'payments'])
        ->name('teacher-admin.payments');
    Route::post('/teacher-admin/payments', [SchoolPortalController::class, 'storePayment'])
        ->name('teacher-admin.payments.store');
    Route::patch('/teacher-admin/payments/{payment}/approve', [SchoolPortalController::class, 'approvePayment'])
        ->name('teacher-admin.payments.approve');
    Route::get('/teacher-admin/search', [SchoolPortalController::class, 'headSearch'])
        ->name('teacher-admin.search');
    Route::get('/teacher-admin/new-logins', [SchoolPortalController::class, 'loginReviews'])
        ->name('teacher-admin.login-reviews');
    Route::patch('/teacher-admin/new-logins/{review}/block', [SchoolPortalController::class, 'blockLogin'])
        ->name('teacher-admin.login-reviews.block');
    Route::patch('/teacher-admin/new-logins/{review}/unblock', [SchoolPortalController::class, 'unblockLogin'])
        ->name('teacher-admin.login-reviews.unblock');
    Route::delete('/teacher-admin/new-logins/{review}', [SchoolPortalController::class, 'deleteLogin'])
        ->name('teacher-admin.login-reviews.delete');
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
Route::middleware(['auth:student,teacher,teacher_admin,admin,web', 'portal_guard'])->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
});

Route::middleware(['auth:admin', 'admin'])->group(function () {
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

    Route::get('/admin/head-teachers', [ManageTeacherController::class, 'index'])->name('admin.head-teachers.index');

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
    Route::get('/admin/new-logins', [SchoolPortalController::class, 'loginReviews'])->name('admin.login-reviews');
    Route::patch('/admin/new-logins/{review}/block', [SchoolPortalController::class, 'blockLogin'])->name('admin.login-reviews.block');
    Route::patch('/admin/new-logins/{review}/unblock', [SchoolPortalController::class, 'unblockLogin'])->name('admin.login-reviews.unblock');
    Route::delete('/admin/new-logins/{review}', [SchoolPortalController::class, 'deleteLogin'])->name('admin.login-reviews.delete');
});

Route::middleware(['auth:student,teacher,teacher_admin,admin,web', 'portal_guard'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
