<?php

namespace Tests\Feature;

use App\Mail\AccountApproved;
use App\Mail\AccountRejected;
use App\Mail\BackupEmailNotification;
use App\Mail\DutySessionAlert;
use App\Mail\ImportNotification;
use App\Mail\MfaCode;
use App\Mail\NewAnnouncement;
use App\Mail\WelcomeEmail;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTestAll extends TestCase
{
    protected User $user;
    protected NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email' => 'test@example.com']);
        $this->notificationService = app(NotificationService::class);
    }

    public function test_database_notifications_system_notification()
    {
        $this->notificationService->sendSystemNotification($this->user, 'Test system notification');
        
        $notification = $this->user->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertEquals('system_notification', $notification->type);
        $this->assertEquals('Test system notification', $notification->data['message']);
    }

    public function test_database_notifications_warning_alert()
    {
        $this->notificationService->sendWarningAlert($this->user, 'Test warning alert');
        
        $notification = $this->user->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertEquals('warning_alert', $notification->type);
        $this->assertEquals('warning', $notification->data['severity']);
    }

    public function test_database_notifications_critical_alert()
    {
        $this->notificationService->sendCriticalAlert($this->user, 'Test critical alert');
        
        $notification = $this->user->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertEquals('critical_alert', $notification->type);
        $this->assertEquals('critical', $notification->data['severity']);
    }

    public function test_database_notifications_backup_success()
    {
        $this->notificationService->sendBackupNotification($this->user, 'database', 'completed', 'Backup completed successfully');
        
        $notification = $this->user->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertStringContainsString('backup_completed', $notification->type);
    }

    public function test_database_notifications_backup_failed()
    {
        $this->notificationService->sendBackupNotification($this->user, 'database', 'failed', 'Connection timeout');
        
        $notification = $this->user->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertStringContainsString('backup_failed', $notification->type);
    }

    public function test_database_notifications_export_success()
    {
        $this->notificationService->sendExportNotification($this->user, 'users', 'completed', 'Export ready');
        
        $notification = $this->user->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertStringContainsString('export_completed', $notification->type);
    }

    public function test_database_notifications_import_success()
    {
        $this->notificationService->sendImportNotification($this->user, 'completed', 100, 5, 'Import finished');
        
        $notification = $this->user->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertStringContainsString('import_completed', $notification->type);
    }

    public function test_database_notifications_validation_error()
    {
        $this->notificationService->sendValidationNotification($this->user, 'import', 'error', 'Invalid file format');
        
        $notification = $this->user->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertStringContainsString('validation_error', $notification->type);
    }

    public function test_database_notifications_failure_notification()
    {
        $this->notificationService->sendFailureNotification(
            $this->user,
            'data_sync',
            'Database connection failed',
            ['error_code' => 'DB_001'],
            'critical'
        );
        
        $notification = $this->user->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertEquals('failure_notification', $notification->type);
    }

    public function test_database_notifications_batch_failure()
    {
        $this->notificationService->sendBatchFailureNotification(
            $this->user,
            'bulk_import',
            100,
            25,
            [1, 2, 3]
        );
        
        $notification = $this->user->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertEquals('batch_failure_notification', $notification->type);
    }

    public function test_notify_all_admins()
    {
        User::factory()->create(['role' => 'admin']);
        
        $this->notificationService->notifyAdmins(
            'system',
            'System Alert',
            'Test alert for all admins'
        );
        
        $adminNotifications = User::where('role', 'admin')->first()->notifications;
        $this->assertGreaterThan(0, $adminNotifications->count());
    }

    public function test_notify_all_users()
    {
        User::factory()->create(['status' => 'active']);
        
        $this->notificationService->notifyAll(
            'announcement',
            'New Announcement',
            'Test announcement for all users'
        );
        
        $userNotifications = User::where('status', 'active')->first()->notifications;
        $this->assertGreaterThan(0, $userNotifications->count());
    }

    // Email Notification Tests
    public function test_mail_welcome_email()
    {
        Mail::fake();
        
        Mail::to($this->user->email)->send(new WelcomeEmail($this->user));
        
        Mail::assertSent(WelcomeEmail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    public function test_mail_account_approved()
    {
        Mail::fake();
        
        Mail::to($this->user->email)->send(new AccountApproved($this->user));
        
        Mail::assertSent(AccountApproved::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    public function test_mail_account_rejected()
    {
        Mail::fake();
        
        Mail::to($this->user->email)->send(new AccountRejected($this->user, 'Insufficient credentials'));
        
        Mail::assertSent(AccountRejected::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    public function test_mail_mfa_code()
    {
        Mail::fake();
        
        Mail::to($this->user->email)->send(new MfaCode($this->user, '123456'));
        
        Mail::assertSent(MfaCode::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    public function test_mail_backup_notification()
    {
        Mail::fake();
        
        Mail::to($this->user->email)->send(new BackupEmailNotification(
            'database',
            true,
            'backup_2024_01_01.sql',
            '256MB',
            'Backup completed successfully',
            ['total_tables' => 15, 'total_records' => 50000]
        ));
        
        Mail::assertSent(BackupEmailNotification::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    public function test_mail_import_notification()
    {
        Mail::fake();
        
        Mail::to($this->user->email)->send(new ImportNotification(
            'users_import.csv',
            'completed',
            100,
            5,
            2
        ));
        
        Mail::assertSent(ImportNotification::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    public function test_mail_duty_session_alert()
    {
        Mail::fake();
        
        Mail::to($this->user->email)->send(new DutySessionAlert($this->user, 'Session timeout warning'));
        
        Mail::assertSent(DutySessionAlert::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    public function test_mail_new_announcement()
    {
        Mail::fake();
        
        $announcement = Announcement::factory()->create();
        
        Mail::to($this->user->email)->send(new NewAnnouncement($this->user, $announcement));
        
        Mail::assertSent(NewAnnouncement::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    public function test_notification_reset_password()
    {
        Notification::fake();
        
        Notification::assertNothingSent();
        
        $this->user->notify(new ResetPasswordNotification('test-token-123'));
        
        Notification::assertSentTo($this->user, ResetPasswordNotification::class);
    }

    public function test_notification_mark_as_read()
    {
        $this->notificationService->sendSystemNotification($this->user, 'Test notification');
        
        $notification = $this->user->notifications()->first();
        $this->assertNull($notification->read_at);
        
        $notification->markAsRead();
        
        $this->assertNotNull($notification->read_at);
    }

    public function test_notification_acknowledge()
    {
        $this->notificationService->sendCriticalAlert($this->user, 'Critical alert');
        
        $notification = $this->user->notifications()->first();
        $this->assertNull($notification->acknowledged_at);
        
        $notification->acknowledge('admin_user');
        
        $this->assertNotNull($notification->acknowledged_at);
        $this->assertEquals('admin_user', $notification->acknowledged_by);
    }

    public function test_notification_scopes()
    {
        $this->notificationService->sendSystemNotification($this->user, 'System notification');
        $this->notificationService->sendCriticalAlert($this->user, 'Critical alert');
        
        $unread = $this->user->notifications()->unread()->count();
        $this->assertEquals(2, $unread);
        
        $critical = $this->user->notifications()->critical()->count();
        $this->assertEquals(1, $critical);
    }
}
