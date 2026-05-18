<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_opening_member_dashboard_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/member/dashboard')
            ->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_member_opening_admin_dashboard_is_redirected_to_member_dashboard(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get('/admin/dashboard')
            ->assertRedirect(route('member.dashboard', absolute: false));
    }
}
