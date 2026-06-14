<?php

namespace Tests\Feature;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MultiGuardSessionTest extends TestCase
{
    public function test_teacher_portal_logs_in_head_teacher_created_teachers_with_teacher_guard(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'email' => 'teacher@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->post('/login', [
            'portal' => 'teacher',
            'email' => $teacher->email,
            'password' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($teacher, 'teacher');
        $this->assertGuest('teacher_admin');
        $this->assertGuest('admin');
    }

    public function test_head_teacher_and_super_admin_log_in_with_their_matching_guards(): void
    {
        $headTeacher = User::factory()->create([
            'role' => 'teacher_admin',
            'email' => 'head@example.com',
            'password' => Hash::make('password123'),
        ]);

        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'email' => 'super@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->post('/login', [
            'portal' => 'teacher-admin',
            'email' => $headTeacher->email,
            'password' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($headTeacher, 'teacher_admin');
        $this->post('/logout?portal=teacher-admin');

        $this->post('/login', [
            'portal' => 'super-admin',
            'email' => $superAdmin->email,
            'password' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($superAdmin, 'admin');
    }

    public function test_head_teacher_created_teacher_can_only_enter_teacher_portal_dashboard(): void
    {
        $headTeacher = User::factory()->create([
            'role' => 'teacher_admin',
            'school' => 'Demo School',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($headTeacher, 'teacher_admin')
            ->post('/teacher-admin/teachers', [
                'name' => 'School Teacher',
                'email' => 'school.teacher@example.com',
                'password' => 'password123',
                'qualification' => 'BSc',
                'subject' => 'Math',
                'salary' => 5000,
                'area' => 'Dhaka',
                'verification_status' => 'verified',
            ])->assertRedirect(route('teacher-admin.teachers.index', absolute: false));

        $teacherUser = User::query()->where('email', 'school.teacher@example.com')->first();
        $this->assertNotNull($teacherUser);
        $this->assertSame('teacher', $teacherUser->role);
        $this->assertSame('Demo School', $teacherUser->school);
        $this->assertTrue(Teacher::query()->where('user_id', $teacherUser->getKey())->where('institution', 'Demo School')->exists());

        $this->post('/logout?portal=teacher-admin');

        $this->post('/login', [
            'portal' => 'teacher',
            'email' => 'school.teacher@example.com',
            'password' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($teacherUser, 'teacher');
        $this->get('/dashboard')->assertRedirect(route('teacher.dashboard', absolute: false));
        $this->get('/teacher-admin/dashboard')->assertRedirect(route('login', absolute: false));
        $this->get('/admin/dashboard')->assertRedirect(route('login', absolute: false));
    }

    public function test_admin_created_head_teacher_can_only_enter_head_teacher_control_panel(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($admin, 'admin')
            ->post('/admin/teachers', [
                'name' => 'Head Sir',
                'email' => 'head.sir@example.com',
                'password' => 'password123',
                'role' => 'teacher_admin',
                'school' => 'Demo College',
                'qualification' => 'MSc',
                'subject' => 'Physics',
                'salary' => 10000,
                'area' => 'Dhaka',
                'institution' => 'Demo College',
                'verification_status' => 'verified',
            ])->assertRedirect(route('admin.teachers.index', absolute: false));

        $headTeacherUser = User::query()->where('email', 'head.sir@example.com')->first();
        $this->assertNotNull($headTeacherUser);
        $this->assertSame('teacher_admin', $headTeacherUser->role);
        $this->assertSame('Demo College', $headTeacherUser->school);

        $this->post('/logout?portal=admin');

        $this->post('/login', [
            'portal' => 'teacher-admin',
            'email' => 'head.sir@example.com',
            'password' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($headTeacherUser, 'teacher_admin');
        $this->get('/dashboard')->assertRedirect(route('teacher-admin.dashboard', absolute: false));

        $this->post('/teacher-admin/teachers', [
            'name' => 'Controlled Teacher',
            'email' => 'controlled.teacher@example.com',
            'password' => 'password123',
            'qualification' => 'BEd',
            'subject' => 'English',
            'salary' => 6000,
            'area' => 'Dhaka',
            'verification_status' => 'verified',
        ])->assertRedirect(route('teacher-admin.teachers.index', absolute: false));

        $controlledTeacher = User::query()->where('email', 'controlled.teacher@example.com')->first();
        $this->assertNotNull($controlledTeacher);
        $this->assertSame('teacher', $controlledTeacher->role);
        $this->assertSame('Demo College', $controlledTeacher->school);

        $this->get('/teacher/dashboard')->assertRedirect(route('login', absolute: false));
        $this->get('/admin/dashboard')->assertRedirect(route('login', absolute: false));
    }

    public function test_student_and_admin_can_remain_signed_in_in_the_same_browser_session(): void
    {
        Storage::disk('public')->put('profile-images/student-avatar.png', 'student');
        Storage::disk('public')->put('admin-images/admin-avatar.png', 'admin');

        $student = User::factory()->create([
            'role' => 'student',
            'image' => 'profile-images/student-avatar.png',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'image' => 'admin-images/admin-avatar.png',
        ]);

        $this->actingAs($student, 'student');
        $this->actingAs($admin, 'admin');

        $this->get('/student/dashboard')
            ->assertOk()
            ->assertSee('Welcome back, '.$student->name, false)
            ->assertSee('src="/storage/'.$student->image.'"', false)
            ->assertDontSee('src="/storage/'.$admin->image.'"', false);

        $this->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('src="/storage/'.$admin->image.'"', false)
            ->assertDontSee('src="/storage/'.$student->image.'"', false);
    }

    public function test_profile_settings_resolve_to_the_selected_active_guard(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'name' => 'Student User',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Admin User',
        ]);

        $this->actingAs($student, 'student');
        $this->actingAs($admin, 'admin');

        $this->withSession(['active_guard' => 'admin'])
            ->get('/profile?portal=admin')
            ->assertOk()
            ->assertSee('Admin User')
            ->assertDontSee('Student User');

        $this->withSession(['active_guard' => 'student'])
            ->get('/profile?portal=student')
            ->assertOk()
            ->assertSee('Student User')
            ->assertDontSee('Admin User');
    }

    public function test_logout_only_signs_out_the_active_guard(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($student, 'student');
        $this->actingAs($admin, 'admin');

        $this->withSession(['active_guard' => 'student'])
            ->post('/logout?portal=student')
            ->assertRedirect('/');

        $this->assertGuest('student');
        $this->assertAuthenticated('admin');

        $this->get('/admin/dashboard')->assertOk();
    }
}
