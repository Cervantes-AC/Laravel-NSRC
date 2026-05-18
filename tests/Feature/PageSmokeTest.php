<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pages_render_without_errors(): void
    {
        $admin = User::factory()->admin()->create();

        $routes = [
            'dashboard',
            'analytics',
            'ranking',
            'notifications',
            'reports',
            'reports/insights',
            'profile',
            'admin/dashboard',
            'admin/personnel',
            'admin/personnel/create',
            'admin/announcements',
            'admin/announcements/create',
            'admin/sessions',
            'admin/sessions/create',
            'admin/accounts',
            'admin/settings',
            'admin/audit-logs',
            'admin/import',
            'admin/backup',
        ];

        foreach ($routes as $route) {
            $this->actingAs($admin)
                ->followingRedirects()
                ->get($route)
                ->assertOk();
        }
    }

    public function test_member_pages_render_without_errors(): void
    {
        $member = User::factory()->member()->create();

        $routes = [
            'dashboard',
            'analytics',
            'ranking',
            'notifications',
            'profile',
            'member/dashboard',
            'member/attendance',
            'member/performance',
            'member/how-to-log',
            'member/rules',
        ];

        foreach ($routes as $route) {
            $this->actingAs($member)
                ->followingRedirects()
                ->get($route)
                ->assertOk();
        }
    }

    public function test_members_cannot_access_reports_pages(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get('/reports')
            ->assertRedirect(route('member.dashboard', absolute: false));

        $this->actingAs($member)
            ->get('/reports/insights')
            ->assertRedirect(route('member.dashboard', absolute: false));
    }

    public function test_members_cannot_access_reports_api(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->postJson('/api/reports/generate')
            ->assertForbidden();
    }
}
