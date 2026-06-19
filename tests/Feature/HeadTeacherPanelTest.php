<?php

namespace Tests\Feature;

use App\Models\Complaint;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\LoginReview;
use App\Models\Notice;
use App\Models\PaymentConfirmation;
use App\Models\Student;
use App\Models\StudentProgress;
use App\Models\StudentTask;
use App\Models\Teacher;
use App\Models\User;
use Tests\TestCase;

class HeadTeacherPanelTest extends TestCase
{
    public function test_head_teacher_sidebar_pages_render_successfully(): void
    {
        $school = 'Khulna Model School';
        $headTeacher = User::factory()->create([
            'role' => 'teacher_admin',
            'school' => $school,
        ]);
        $studentUser = User::factory()->create([
            'role' => 'student',
            'school' => $school,
            'name' => 'Student Alpha',
        ]);
        $teacherUser = User::factory()->create([
            'role' => 'teacher',
            'school' => $school,
            'name' => 'Teacher Beta',
        ]);

        Student::query()->create([
            'user_id' => (string) $studentUser->getKey(),
            'class' => 'Nine',
            'school' => $school,
            'area' => 'Khulna',
        ]);
        Teacher::query()->create([
            'user_id' => (string) $teacherUser->getKey(),
            'name' => $teacherUser->name,
            'qualification' => 'MSc',
            'subject' => 'Math',
            'institution' => $school,
            'area' => 'Khulna',
        ]);
        Complaint::query()->create([
            'school' => $school,
            'created_by' => (string) $studentUser->getKey(),
            'creator_role' => 'student',
            'against_name' => 'Teacher Beta',
            'title' => 'Class issue',
            'body' => 'Needs attention.',
            'status' => 'open',
        ]);
        LeaveApplication::query()->create([
            'school' => $school,
            'user_id' => (string) $teacherUser->getKey(),
            'role' => 'teacher',
            'leave_type' => 'advance',
            'from_date' => '2026-06-02',
            'to_date' => '2026-06-03',
            'reason' => 'Family event.',
            'document_path' => 'leave-documents/sample.pdf',
            'status' => 'pending',
        ]);
        PaymentConfirmation::query()->create([
            'school' => $school,
            'user_id' => (string) $teacherUser->getKey(),
            'role' => 'teacher',
            'type' => 'salary',
            'month' => '2026-06',
            'amount' => 12000,
            'confirmed_by' => (string) $headTeacher->getKey(),
            'confirmed_at' => now(),
        ]);
        LoginReview::query()->create([
            'user_id' => (string) $studentUser->getKey(),
            'name' => $studentUser->name,
            'email' => $studentUser->email,
            'role' => 'student',
            'school' => $school,
            'status' => 'allowed',
        ]);

        $this->actingAs($headTeacher, 'teacher_admin');

        foreach ([
            route('teacher-admin.dashboard'),
            route('teacher-admin.teachers.index'),
            route('teacher-admin.students.index'),
            route('teacher-admin.database'),
            route('teacher-admin.messages'),
            route('teacher-admin.complaints'),
            route('teacher-admin.leaves'),
            route('teacher-admin.payments'),
            route('teacher-admin.search', ['q' => 'Teacher']),
            route('teacher-admin.login-reviews'),
            route('profile.edit', ['portal' => 'teacher-admin']),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_head_teacher_can_update_teacher_and_student_details(): void
    {
        $school = 'Khulna Model School';
        $headTeacher = User::factory()->create([
            'role' => 'teacher_admin',
            'school' => $school,
        ]);
        $studentUser = User::factory()->create([
            'role' => 'student',
            'school' => $school,
            'name' => 'Old Student',
            'email' => 'old.student@example.test',
        ]);
        $teacherUser = User::factory()->create([
            'role' => 'teacher',
            'school' => $school,
            'name' => 'Old Teacher',
            'email' => 'old.teacher@example.test',
        ]);
        $student = Student::query()->create([
            'user_id' => (string) $studentUser->getKey(),
            'class' => 'Eight',
            'group' => 'Science',
            'school' => $school,
            'subject' => 'Math',
            'area' => 'Khulna',
            'phone' => '01000000000',
        ]);
        $teacher = Teacher::query()->create([
            'user_id' => (string) $teacherUser->getKey(),
            'name' => 'Old Teacher',
            'qualification' => 'BSc',
            'experience' => '2 years',
            'subject' => 'Math',
            'subjects' => ['Math'],
            'institution' => $school,
            'area' => 'Khulna',
            'salary' => 10000,
            'rating' => 4,
        ]);

        $this->actingAs($headTeacher, 'teacher_admin');

        $teacherResponse = $this->put(route('teacher-admin.teachers.update', $teacher), [
            'name' => 'Updated Teacher',
            'email' => 'updated.teacher@example.test',
            'qualification' => 'MSc',
            'experience' => '5 years',
            'subject' => 'Physics',
            'subjects_text' => 'Physics, ICT',
            'salary' => 15000,
            'area' => 'Sonadanga',
            'bio' => 'Updated teacher bio',
            'rating' => 4.5,
            'availability' => 'Morning',
            'class_level' => 'Class 9-10',
            'verification_status' => 'verified',
        ]);

        $studentResponse = $this->put(route('teacher-admin.students.update', $student), [
            'name' => 'Updated Student',
            'email' => 'updated.student@example.test',
            'class' => 'Nine',
            'group' => 'Business Studies',
            'subject' => 'Accounting, English',
            'preferred_teacher' => 'Updated Teacher',
            'area' => 'Boyra',
            'phone' => '01999999999',
            'bio' => 'Updated student bio',
        ]);

        $teacherResponse->assertRedirect(route('teacher-admin.teachers.index'));
        $studentResponse->assertRedirect(route('teacher-admin.students.index'));

        $teacher->refresh();
        $teacherUser->refresh();
        $student->refresh();
        $studentUser->refresh();

        $this->assertSame('Updated Teacher', $teacher->name);
        $this->assertSame('updated.teacher@example.test', $teacherUser->email);
        $this->assertSame('Physics', $teacher->subject);
        $this->assertSame(['Physics', 'ICT'], $teacher->subjects);
        $this->assertSame(15000, (int) $teacher->salary);

        $this->assertSame('Updated Student', $studentUser->name);
        $this->assertSame('updated.student@example.test', $studentUser->email);
        $this->assertSame('Nine', $student->class);
        $this->assertSame(['Accounting', 'English'], $student->subjects);
        $this->assertSame('01999999999', $student->phone);
    }

    public function test_head_teacher_can_update_school_records_even_when_login_user_is_missing(): void
    {
        $school = 'Khulna Model School';
        $headTeacher = User::factory()->create([
            'role' => 'teacher_admin',
            'school' => $school,
        ]);
        $teacher = Teacher::query()->create([
            'name' => 'Directory Teacher',
            'institution' => $school,
            'area' => 'Khulna',
        ]);
        $student = Student::query()->create([
            'class' => 'Seven',
            'school' => $school,
            'area' => 'Khulna',
        ]);

        $this->actingAs($headTeacher, 'teacher_admin');

        $this->put(route('teacher-admin.teachers.update', $teacher), [
            'name' => 'Directory Teacher Updated',
            'email' => '',
            'qualification' => '',
            'experience' => '3 years',
            'subject' => '',
            'subjects_text' => 'Bangla, English',
            'salary' => '',
            'area' => 'Rupsha',
            'bio' => 'Updated without login account.',
            'rating' => '',
            'availability' => '',
            'class_level' => '',
            'verification_status' => 'pending',
        ])->assertRedirect(route('teacher-admin.teachers.index'));

        $this->put(route('teacher-admin.students.update', $student), [
            'name' => 'Directory Student',
            'email' => '',
            'class' => 'Eight',
            'group' => '',
            'subject' => 'Bangla',
            'preferred_teacher' => '',
            'area' => 'Rupsha',
            'phone' => '',
            'bio' => 'Updated without login account.',
        ])->assertRedirect(route('teacher-admin.students.index'));

        $teacher = Teacher::find($teacher->getKey());
        $student = Student::find($student->getKey());

        $this->assertSame('Directory Teacher Updated', $teacher->name);
        $this->assertSame('Rupsha', $teacher->area);
        $this->assertSame(['Bangla', 'English'], $teacher->subjects);
        $this->assertNull($teacher->user_id);

        $this->assertSame('Eight', $student->class);
        $this->assertSame('Rupsha', $student->area);
        $this->assertSame(['Bangla'], $student->subjects);
        $this->assertNull($student->user_id);
    }

    public function test_head_teacher_can_update_only_own_school_student_progress(): void
    {
        $school = 'Khulna Model School';
        $headTeacher = User::factory()->create([
            'role' => 'teacher_admin',
            'school' => $school,
        ]);
        $studentUser = User::factory()->create([
            'role' => 'student',
            'school' => $school,
        ]);
        $student = Student::query()->create([
            'user_id' => (string) $studentUser->getKey(),
            'class' => 'Nine',
            'school' => $school,
            'area' => 'Khulna',
        ]);
        $otherStudent = Student::query()->create([
            'class' => 'Ten',
            'school' => 'Other School',
            'area' => 'Dhaka',
        ]);

        $this->actingAs($headTeacher, 'teacher_admin');

        $this->put(route('teacher-admin.progress.update', $student), [
            'attendance_score' => 90,
            'reading_score' => 80,
            'writing_score' => 70,
            'teacher_comment' => 'Improving well.',
            'subject_names' => ['Math'],
            'subject_scores' => [88],
            'subject_comments' => ['Strong'],
        ])->assertRedirect(route('teacher-admin.progress.index'));

        $this->assertNotNull(StudentProgress::query()->firstWhere('student_user_id', (string) $studentUser->getKey()));

        $this->put(route('teacher-admin.progress.update', $otherStudent), [
            'overall_score' => 95,
        ])->assertForbidden();
    }

    public function test_head_teacher_can_manage_single_student_operations(): void
    {
        $school = 'Khulna Model School';
        $headTeacher = User::factory()->create([
            'role' => 'teacher_admin',
            'school' => $school,
        ]);
        $studentUser = User::factory()->create([
            'role' => 'student',
            'school' => $school,
        ]);
        $student = Student::query()->create([
            'user_id' => (string) $studentUser->getKey(),
            'class' => 'Nine',
            'school' => $school,
            'area' => 'Khulna',
        ]);

        $this->actingAs($headTeacher, 'teacher_admin');

        $this->post(route('teacher-admin.attendance.store'), [
            'student_user_id' => (string) $studentUser->getKey(),
            'date' => '2026-06-19',
            'status' => 'present',
            'note' => 'Morning class attended.',
        ])->assertSessionHas('success');

        $this->post(route('teacher-admin.students.fees.store', $student), [
            'month' => '2026-06',
            'amount' => 1500,
            'status' => 'pending',
            'note' => 'Monthly tuition.',
        ])->assertSessionHas('success');

        $payment = PaymentConfirmation::query()->firstWhere('user_id', (string) $studentUser->getKey());

        $this->patch(route('teacher-admin.students.fees.update', [$student, $payment]), [
            'amount' => 1500,
            'status' => 'approved',
            'note' => 'Cleared by Head Sir.',
        ])->assertSessionHas('success');

        $this->post(route('teacher-admin.students.notices.store', $student), [
            'title' => 'Special notice',
            'body' => 'Bring guardian tomorrow.',
        ])->assertSessionHas('success');

        $this->post(route('teacher-admin.students.tasks.store', $student), [
            'title' => 'Complete chapter 3',
            'due_date' => '2026-06-25',
            'priority' => 'high',
        ])->assertSessionHas('success');

        $this->assertNotNull(Attendance::query()->firstWhere('student_user_id', (string) $studentUser->getKey()));
        $this->assertSame('approved', PaymentConfirmation::query()->firstWhere('user_id', (string) $studentUser->getKey())->status);
        $this->assertSame((string) $studentUser->getKey(), Notice::query()->firstWhere('title', 'Special notice')->target_user_id);
        $this->assertNotNull(StudentTask::query()->firstWhere('user_id', (string) $studentUser->getKey()));
    }

    public function test_teacher_can_manage_only_students_from_their_own_school(): void
    {
        $school = 'Khulna Model School';
        $teacherUser = User::factory()->create([
            'role' => 'teacher',
            'school' => $school,
        ]);
        Teacher::query()->create([
            'user_id' => (string) $teacherUser->getKey(),
            'name' => $teacherUser->name,
            'institution' => $school,
            'area' => 'Khulna',
        ]);
        $studentUser = User::factory()->create([
            'role' => 'student',
            'school' => $school,
            'name' => 'Old Student',
        ]);
        $student = Student::query()->create([
            'user_id' => (string) $studentUser->getKey(),
            'class' => 'Eight',
            'school' => $school,
            'area' => 'Khulna',
        ]);
        $otherStudent = Student::query()->create([
            'class' => 'Ten',
            'school' => 'Other School',
            'area' => 'Dhaka',
        ]);

        $this->actingAs($teacherUser, 'teacher');

        $this->put(route('teacher.students.update', $student), [
            'name' => 'Updated Student',
            'email' => 'updated.student.teacher@example.test',
            'class' => 'Nine',
            'group' => 'Science',
            'subject' => 'Math',
            'preferred_teacher' => '',
            'area' => 'Boyra',
            'phone' => '01999999999',
            'bio' => 'Updated by school teacher.',
        ])->assertRedirect(route('teacher.students.index'));

        $student->refresh();
        $studentUser->refresh();

        $this->assertSame('Nine', $student->class);
        $this->assertSame($school, $student->school);
        $this->assertSame($school, $studentUser->school);

        $this->put(route('teacher.students.update', $otherStudent), [
            'name' => 'Blocked Student',
            'email' => '',
            'class' => 'Nine',
            'area' => 'Dhaka',
        ])->assertForbidden();
    }
}
