<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AlertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertServiceTest extends TestCase
{
    use RefreshDatabase;

    private AlertService $alertService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->alertService = app(AlertService::class);
    }

    public function test_check_failed_login_attempts_below_threshold(): void
    {
        $user = User::factory()->create();

        $this->alertService->checkFailedLoginAttempts($user, 2);

        $this->assertCount(0, $user->notifications()->get());
    }

    public function test_check_failed_login_attempts_at_threshold(): void
    {
        $user = User::factory()->create();

        $this->alertService->checkFailedLoginAttempts($user, 3);

        $this->assertCount(1, $user->notifications()->get());
    }

    public function test_check_failed_login_attempts_notifies_admins(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $member = User::factory()->create(['role' => 'member']);

        $this->alertService->checkFailedLoginAttempts($member, 5);

        $this->assertCount(1, $admin->notifications()->get());
    }

    public function test_check_failed_login_attempts_only_active_admins(): void
    {
        $inactiveAdmin = User::factory()->create(['role' => 'admin', 'status' => 'inactive']);
        $member = User::factory()->create(['role' => 'member']);

        $this->alertService->checkFailedLoginAttempts($member, 3);

        $this->assertCount(0, $inactiveAdmin->notifications()->get());
    }

    public function test_confirm_record_deletion_returns_message(): void
    {
        $result = $this->alertService->confirmRecordDeletion('Duty Session');
        $this->assertStringContainsString('delete', strtolower($result));
        $this->assertStringContainsString('Duty Session', $result);
    }

    public function test_storage_capacity_below_threshold_returns_null(): void
    {
        $result = $this->alertService->checkStorageCapacity();
        $this->assertNull($result);
    }
}
