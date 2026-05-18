<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_published_announcement_for_members(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->member()->create();

        $response = $this->actingAs($admin)->post('/admin/announcements', [
            'title' => 'Duty Schedule Update',
            'body' => 'Members should check the updated duty schedule.',
            'priority' => 'important',
            'status' => 'published',
            'audience' => 'members',
            'published_at' => now()->format('Y-m-d H:i:s'),
            'expires_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(route('admin.announcements.index', absolute: false));

        $this->assertDatabaseHas('announcements', [
            'title' => 'Duty Schedule Update',
            'status' => 'published',
        ]);

        $this->assertSame(1, $member->fresh()->notifications()->where('type', 'announcement')->count());
    }

    public function test_members_can_see_published_announcements(): void
    {
        $member = User::factory()->member()->create();
        Announcement::factory()->published()->create([
            'title' => 'General Assembly',
            'body' => 'Assembly starts at 8 AM.',
            'audience' => 'members',
        ]);

        $this->actingAs($member)
            ->get('/notifications')
            ->assertOk()
            ->assertSee('General Assembly')
            ->assertSee('Assembly starts at 8 AM.');
    }

    public function test_draft_announcements_are_hidden_from_members(): void
    {
        $member = User::factory()->member()->create();
        Announcement::factory()->create([
            'title' => 'Draft Notice',
            'status' => 'draft',
            'audience' => 'members',
        ]);

        $this->actingAs($member)
            ->get('/notifications')
            ->assertOk()
            ->assertDontSee('Draft Notice');
    }
}
