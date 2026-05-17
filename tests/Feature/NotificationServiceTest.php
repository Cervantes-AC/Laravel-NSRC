<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationService = app(NotificationService::class);
    }

    public function test_send_system_notification_creates_notification(): void
    {
        $user = User::factory()->create();

        $this->notificationService->sendSystemNotification($user, 'Test system notification');

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $user->id,
            'type' => 'system_notification',
        ]);

        $notification = $user->notifications()->first();
        $this->assertEquals('Test system notification', $notification->data['message']);
    }

    public function test_send_warning_alert_creates_warning_notification(): void
    {
        $user = User::factory()->create();

        $this->notificationService->sendWarningAlert($user, 'Test warning');

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $user->id,
            'type' => 'warning_alert',
        ]);

        $notification = $user->notifications()->first();
        $this->assertEquals('warning', $notification->data['severity']);
    }

    public function test_send_critical_alert_creates_notification_and_audit_log(): void
    {
        $user = User::factory()->create();

        $this->notificationService->sendCriticalAlert($user, 'Critical error occurred');

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $user->id,
            'type' => 'critical_alert',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'CRITICAL_ALERT_SENT',
        ]);
    }

    public function test_send_reminder_notification_creates_reminder(): void
    {
        $user = User::factory()->create();

        $this->notificationService->sendReminderNotification($user, 'Upcoming duty');

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $user->id,
            'type' => 'reminder',
        ]);
    }

    public function test_send_security_alert_creates_critical_alert(): void
    {
        $user = User::factory()->create();

        $this->notificationService->sendSecurityAlert($user, 'Security breach detected');

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $user->id,
            'type' => 'critical_alert',
        ]);
    }

    public function test_multiple_notifications_to_same_user(): void
    {
        $user = User::factory()->create();

        $this->notificationService->sendSystemNotification($user, 'First');
        $this->notificationService->sendSystemNotification($user, 'Second');
        $this->notificationService->sendSystemNotification($user, 'Third');

        $this->assertCount(3, $user->notifications()->get());
    }
}
