<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MultiGuardSessionTest extends TestCase
{
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