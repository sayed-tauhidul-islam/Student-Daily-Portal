<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_super_admin_portal_redirects_to_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'portal' => 'super admin',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->get('/dashboard')
            ->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_teacher_can_authenticate_when_stored_role_needs_normalization(): void
    {
        $user = User::factory()->create([
            'email' => 'sakibfoysal@gmail.com',
            'role' => 'Teacher ',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'portal' => 'teacher',
        ]);

        $this->assertAuthenticated('teacher');
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->get('/dashboard')
            ->assertRedirect(route('teacher.dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
