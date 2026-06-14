<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Notice;
use App\Models\PaymentConfirmation;
use App\Models\Student;
use App\Models\StudentAssignment;
use App\Models\StudentExamResult;
use App\Models\StudentGoal;
use App\Models\StudentProgress;
use App\Models\StudentTask;
use App\Models\Teacher;
use App\Models\User;
use Tests\TestCase;

class StudentPortalLinksTest extends TestCase
{
    public function test_student_sidebar_pages_render_successfully(): void
    {
        $school = 'Khulna Model School';
        $studentUser = User::factory()->create([
            'role' => 'student',
            'school' => $school,
            'area' => 'Sonadanga',
        ]);
        $teacherUser = User::factory()->create([
            'role' => 'teacher',
            'school' => $school,
            'name' => 'Teacher Alpha',
        ]);

        Student::query()->create([
            'user_id' => (string) $studentUser->getKey(),
            'class' => 'Nine',
            'group' => 'Science',
            'school' => $school,
            'subject' => 'Math, Physics',
            'subjects' => ['Math', 'Physics'],
            'area' => 'Sonadanga',
            'phone' => '01700000000',
            'bio' => 'Focused student.',
        ]);
        Teacher::query()->create([
            'user_id' => (string) $teacherUser->getKey(),
            'name' => $teacherUser->name,
            'qualification' => 'MSc',
            'subject' => 'Math',
            'subjects' => ['Math'],
            'institution' => $school,
            'area' => 'Sonadanga',
            'rating' => 4.7,
        ]);
        Attendance::query()->create([
            'institute' => $school,
            'student_user_id' => (string) $studentUser->getKey(),
            'teacher_user_id' => (string) $teacherUser->getKey(),
            'date' => now()->format('Y-m-d'),
            'status' => 'present',
        ]);
        Notice::query()->create([
            'institute' => $school,
            'teacher_user_id' => (string) $teacherUser->getKey(),
            'title' => 'Exam event notice',
            'body' => 'Class test next week.',
            'published_at' => now(),
        ]);
        PaymentConfirmation::query()->create([
            'school' => $school,
            'user_id' => (string) $studentUser->getKey(),
            'role' => 'student',
            'type' => 'tuition_fee',
            'month' => now()->format('Y-m'),
            'amount' => 1200,
            'confirmed_at' => now(),
        ]);
        StudentTask::query()->create([
            'user_id' => (string) $studentUser->getKey(),
            'title' => 'Read chapter',
            'due_date' => now()->addDay()->format('Y-m-d'),
            'priority' => 'medium',
            'is_completed' => false,
        ]);
        StudentAssignment::query()->create([
            'user_id' => (string) $studentUser->getKey(),
            'title' => 'Math worksheet',
            'subject' => 'Math',
            'deadline' => now()->addWeek()->format('Y-m-d'),
            'status' => 'pending',
        ]);
        StudentGoal::query()->create([
            'user_id' => (string) $studentUser->getKey(),
            'title' => 'Improve math',
            'target' => 100,
            'current' => 60,
            'unit' => '%',
        ]);
        StudentProgress::query()->create([
            'student_user_id' => (string) $studentUser->getKey(),
            'overall_score' => 82,
            'attendance_score' => 95,
            'behavior_score' => 90,
            'subjects' => [
                ['name' => 'Math', 'score' => 82, 'comment' => 'Good'],
            ],
        ]);
        StudentExamResult::query()->create([
            'student_user_id' => (string) $studentUser->getKey(),
            'subject' => 'Math',
            'exam_name' => 'Half Yearly',
            'term_name' => 'Term 1',
            'marks' => 82,
            'out_of' => 100,
            'exam_date' => now()->addMonth(),
        ]);

        $this->actingAs($studentUser, 'student');

        foreach ([
            route('student.dashboard'),
            route('student.school-members'),
            route('student.messages'),
            route('student.complaints'),
            route('student.leaves'),
            route('student.reading-logs'),
            route('student.payments'),
            route('teachers.index'),
            route('student.progress-hub.index'),
            route('student.attendance.index'),
            route('student.institute-teachers.index'),
            route('student.notices.index'),
            route('student.profile.create'),
            route('profile.edit', ['portal' => 'student']),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_dashboard_teacher_match_count_and_list_use_same_filters(): void
    {
        $school = 'Khulna Model School';
        $studentUser = User::factory()->create([
            'role' => 'student',
            'school' => $school,
            'area' => 'Sonadanga',
        ]);

        Student::query()->create([
            'user_id' => (string) $studentUser->getKey(),
            'school' => $school,
            'area' => 'Sonadanga',
            'subject' => 'Math',
            'subjects' => ['Math'],
        ]);
        Teacher::query()->create([
            'name' => 'Matched Teacher',
            'institution' => $school,
            'area' => 'Sonadanga',
            'subject' => 'Math',
            'subjects' => ['Math'],
            'rating' => 4.8,
        ]);
        Teacher::query()->create([
            'name' => 'Wrong Subject Teacher',
            'institution' => $school,
            'area' => 'Sonadanga',
            'subject' => 'English',
            'subjects' => ['English'],
            'rating' => 5,
        ]);

        $this->actingAs($studentUser, 'student');

        $this->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Matched teachers')
            ->assertSee('Matched Teacher')
            ->assertDontSee('Wrong Subject Teacher');

        $this->get(route('student.institute-teachers.index'))
            ->assertOk()
            ->assertSee('Matched Teacher')
            ->assertDontSee('Wrong Subject Teacher');
    }
}
