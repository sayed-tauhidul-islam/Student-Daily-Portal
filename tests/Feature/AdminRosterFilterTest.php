<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Tests\TestCase;

class AdminRosterFilterTest extends TestCase
{
    public function test_admin_rosters_only_show_the_expected_role(): void
    {
        School::create([
            'name' => 'Boyra Secondary School',
            'area' => 'Boyra',
            'type' => 'School',
            'rating' => 4.2,
            'students' => 920,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $studentUser = User::factory()->create(['name' => 'Student Alpha', 'role' => 'student']);
        $teacherUser = User::factory()->create(['name' => 'Teacher Bravo', 'role' => 'teacher']);
        $headTeacherUser = User::factory()->create(['name' => 'Head Charlie', 'role' => 'teacher_admin']);
        $superAdminUser = User::factory()->create(['name' => 'Super Delta', 'role' => 'super_admin']);

        Student::create([
            'user_id' => $studentUser->getKey(),
            'class' => '10',
            'group' => 'Science',
            'school' => 'Boyra Secondary School',
            'subject' => 'Math',
            'subjects' => ['Math'],
            'preferred_teacher' => null,
            'area' => 'Boyra',
            'phone' => null,
            'bio' => null,
        ]);

        Student::create([
            'user_id' => $teacherUser->getKey(),
            'class' => '11',
            'group' => 'Science',
            'school' => 'Boyra Secondary School',
            'subject' => 'Physics',
            'subjects' => ['Physics'],
            'preferred_teacher' => null,
            'area' => 'Boyra',
            'phone' => null,
            'bio' => null,
        ]);

        Student::create([
            'user_id' => $headTeacherUser->getKey(),
            'class' => '12',
            'group' => 'Commerce',
            'school' => 'Boyra Secondary School',
            'subject' => 'Accounting',
            'subjects' => ['Accounting'],
            'preferred_teacher' => null,
            'area' => 'Boyra',
            'phone' => null,
            'bio' => null,
        ]);

        Teacher::create([
            'user_id' => $teacherUser->getKey(),
            'name' => 'Teacher Bravo',
            'qualification' => 'BSc',
            'experience' => '5 years',
            'subject' => 'Physics',
            'subjects' => ['Physics'],
            'salary' => 20000,
            'area' => 'Boyra',
            'bio' => null,
            'rating' => 4.5,
            'image' => null,
            'availability' => null,
            'institution' => 'Boyra Secondary School',
            'gender' => null,
            'online' => false,
            'class_level' => null,
            'verification_status' => 'verified',
        ]);

        Teacher::create([
            'user_id' => $headTeacherUser->getKey(),
            'name' => 'Head Charlie',
            'qualification' => 'MEd',
            'experience' => '8 years',
            'subject' => 'Accounting',
            'subjects' => ['Accounting'],
            'salary' => 25000,
            'area' => 'Boyra',
            'bio' => null,
            'rating' => 4.7,
            'image' => null,
            'availability' => null,
            'institution' => 'Boyra Secondary School',
            'gender' => null,
            'online' => false,
            'class_level' => null,
            'verification_status' => 'verified',
        ]);

        Teacher::create([
            'user_id' => $superAdminUser->getKey(),
            'name' => 'Super Delta',
            'qualification' => 'MBA',
            'experience' => '10 years',
            'subject' => 'Management',
            'subjects' => ['Management'],
            'salary' => 30000,
            'area' => 'Boyra',
            'bio' => null,
            'rating' => 5,
            'image' => null,
            'availability' => null,
            'institution' => 'Boyra Secondary School',
            'gender' => null,
            'online' => false,
            'class_level' => null,
            'verification_status' => 'verified',
        ]);

        $this->actingAs($admin, 'admin');

        $this->get('/admin/students')
            ->assertOk()
            ->assertSee('Student Alpha', false)
            ->assertDontSee('Teacher Bravo', false)
            ->assertDontSee('Head Charlie', false)
            ->assertDontSee('Super Delta', false);

        $this->get('/admin/teachers')
            ->assertOk()
            ->assertSee('Teacher Bravo', false)
            ->assertDontSee('Student Alpha', false)
            ->assertDontSee('Head Charlie', false)
            ->assertDontSee('Super Delta', false);

        $this->get('/admin/head-teachers')
            ->assertOk()
            ->assertSee('Head Charlie', false)
            ->assertDontSee('Student Alpha', false)
            ->assertDontSee('Teacher Bravo', false)
            ->assertDontSee('Super Delta', false);
    }
}