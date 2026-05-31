<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_admin_dashboard_shows_menu_button_and_profile_avatar(): void
    {
        Storage::disk('public')->deleteDirectory('admin-images');

        $user = User::factory()->create([
            'role' => 'admin',
            'image' => 'admin-images/admin-avatar.png',
        ]);

        Storage::disk('public')->put($user->image, 'avatar');

        $response = $this
            ->actingAs($user, 'admin')
            ->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee('aria-label="Open menu"', false);
        $response->assertSee('aria-label="Open profile settings"', false);
        $response->assertSee('src="/storage/'.$user->image.'"', false);
        $response->assertSee(route('profile.edit'), false);
    }
}