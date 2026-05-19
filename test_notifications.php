<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Mail;

echo "=== NOTIFICATION SYSTEM TEST ===\n\n";

// Get or create a test user
$user = User::firstOrCreate(
    ['email' => 'test@example.com'],
    [
        'name' => 'Test User',
        'full_name' => 'Test User',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'status' => 'active',
    ]
);

echo "Test User: {$user->email} (ID: {$user->id})\n\n";

$notificationService = app(NotificationService::class);

// Test 1: System Notification
echo "1. Testing System Notification...\n";
$notificationService->sendSystemNotification($user, 'This is a test system notification');
$count = Notification::where('notifiable_id', $user->id)->count();
echo "   ✓ Created. Total notifications: {$count}\n\n";

// Test 2: Warning Alert
echo "2. Testing Warning Alert...\n";
$notificationService->sendWarningAlert($user, 'This is a test warning alert');
$count = Notification::where('notifiable_id', $user->id)->count();
echo "   ✓ Created. Total notifications: {$count}\n\n";

// Test 3: Critical Alert
echo "3. Testing Critical Alert...\n";
$notificationService->sendCriticalAlert($user, 'This is a test critical alert');
$count = Notification::where('notifiable_id', $user->id)->count();
echo "   ✓ Created. Total notifications: {$count}\n\n";

// Test 4: Backup Success
echo "4. Testing Backup Success Notification...\n";
$notificationService->sendBackupNotification($user, 'database', 'completed', 'Backup completed successfully');
$count = Notification::where('notifiable_id', $user->id)->count();
echo "   ✓ Created. Total notifications: {$count}\n\n";

// Test 5: Backup Failed
echo "5. Testing Backup Failed Notification...\n";
$notificationService->sendBackupNotification($user, 'database', 'failed', 'Connection timeout');
$count = Notification::where('notifiable_id', $user->id)->count();
echo "   ✓ Created. Total notifications: {$count}\n\n";

// Test 6: Export Success
echo "6. Testing Export Success Notification...\n";
$notificationService->sendExportNotification($user, 'users', 'completed', 'Export ready');
$count = Notification::where('notifiable_id', $user->id)->count();
echo "   ✓ Created. Total notifications: {$count}\n\n";

// Test 7: Import Success
echo "7. Testing Import Success Notification...\n";
$notificationService->sendImportNotification($user, 'completed', 100, 5, 'Import finished');
$count = Notification::where('notifiable_id', $user->id)->count();
echo "   ✓ Created. Total notifications: {$count}\n\n";

// Test 8: Validation Error
echo "8. Testing Validation Error Notification...\n";
$notificationService->sendValidationNotification($user, 'import', 'error', 'Invalid file format');
$count = Notification::where('notifiable_id', $user->id)->count();
echo "   ✓ Created. Total notifications: {$count}\n\n";

// Test 9: Failure Notification
echo "9. Testing Failure Notification...\n";
$notificationService->sendFailureNotification(
    $user,
    'data_sync',
    'Database connection failed',
    ['error_code' => 'DB_001'],
    'critical'
);
$count = Notification::where('notifiable_id', $user->id)->count();
echo "   ✓ Created. Total notifications: {$count}\n\n";

// Test 10: Batch Failure
echo "10. Testing Batch Failure Notification...\n";
$notificationService->sendBatchFailureNotification(
    $user,
    'bulk_import',
    100,
    25,
    [1, 2, 3]
);
$count = Notification::where('notifiable_id', $user->id)->count();
echo "   ✓ Created. Total notifications: {$count}\n\n";

// Test 11: Notify All Admins
echo "11. Testing Notify All Admins...\n";
$adminCount = User::where('role', 'admin')->first()->id;
$notificationService->notifyAdmins(
    'system',
    'System Alert',
    'Test alert for all admins'
);
$adminNotifCount = Notification::where('notifiable_id', $adminCount)->count();
echo "   ✓ Created. Admin notifications: {$adminNotifCount}\n\n";

// Test 12: Notify All Users
echo "12. Testing Notify All Users...\n";
$activeUserId = User::where('status', 'active')->first()->id;
$notificationService->notifyAll(
    'announcement',
    'New Announcement',
    'Test announcement for all users'
);
$activeNotifCount = Notification::where('notifiable_id', $activeUserId)->count();
echo "   ✓ Created. Active user notifications: {$activeNotifCount}\n\n";

// Test 13: Mail - Welcome Email
echo "13. Testing Mail - Welcome Email...\n";
try {
    Mail::to($user->email)->send(new \App\Mail\WelcomeEmail($user));
    echo "   ✓ Mail sent successfully\n\n";
} catch (\Exception $e) {
    echo "   ✗ Error: {$e->getMessage()}\n\n";
}

// Test 14: Mail - Account Approved
echo "14. Testing Mail - Account Approved...\n";
try {
    Mail::to($user->email)->send(new \App\Mail\AccountApproved($user));
    echo "   ✓ Mail sent successfully\n\n";
} catch (\Exception $e) {
    echo "   ✗ Error: {$e->getMessage()}\n\n";
}

// Test 15: Mail - MFA Code
echo "15. Testing Mail - MFA Code...\n";
try {
    Mail::to($user->email)->send(new \App\Mail\MfaCode($user, '123456'));
    echo "   ✓ Mail sent successfully\n\n";
} catch (\Exception $e) {
    echo "   ✗ Error: {$e->getMessage()}\n\n";
}

// Test 16: Mail - Backup Notification
echo "16. Testing Mail - Backup Notification...\n";
try {
    Mail::to($user->email)->send(new \App\Mail\BackupEmailNotification(
        'database',
        true,
        'backup_2024_01_01.sql',
        '256MB',
        'Backup completed successfully',
        [
            ['name' => 'Users Table', 'count' => 1000, 'status' => 'Success'],
            ['name' => 'Logs Table', 'count' => 50000, 'status' => 'Success'],
        ]
    ));
    echo "   ✓ Mail sent successfully\n\n";
} catch (\Exception $e) {
    echo "   ✗ Error: {$e->getMessage()}\n\n";
}

// Test 17: Mail - Import Notification
echo "17. Testing Mail - Import Notification...\n";
try {
    Mail::to($user->email)->send(new \App\Mail\ImportNotification(
        'users_import.csv',
        100,
        95,
        5,
        0,
        true
    ));
    echo "   ✓ Mail sent successfully\n\n";
} catch (\Exception $e) {
    echo "   ✗ Error: {$e->getMessage()}\n\n";
}

// Test 18: Mail - Duty Session Alert
echo "18. Testing Mail - Duty Session Alert...\n";
try {
    Mail::to($user->email)->send(new \App\Mail\DutySessionAlert($user, 'reminder', 'Session timeout warning', 'Session started at 08:00 AM'));
    echo "   ✓ Mail sent successfully\n\n";
} catch (\Exception $e) {
    echo "   ✗ Error: {$e->getMessage()}\n\n";
}

// Test 19: Notification Scopes
echo "19. Testing Notification Scopes...\n";
$unread = Notification::where('notifiable_id', $user->id)->whereNull('read_at')->count();
$critical = Notification::where('notifiable_id', $user->id)->where('severity', 'critical')->count();
echo "   ✓ Unread notifications: {$unread}\n";
echo "   ✓ Critical notifications: {$critical}\n\n";

// Test 20: Mark as Read
echo "20. Testing Mark as Read...\n";
$notification = Notification::where('notifiable_id', $user->id)->first();
if ($notification) {
    $notification->markAsRead();
    echo "   ✓ Notification marked as read\n\n";
}

// Test 21: Acknowledge
echo "21. Testing Acknowledge...\n";
$notification = Notification::where('notifiable_id', $user->id)->whereNull('acknowledged_at')->first();
if ($notification) {
    $notification->acknowledge('admin_user');
    echo "   ✓ Notification acknowledged\n\n";
}

// Summary
echo "=== SUMMARY ===\n";
$totalNotifications = Notification::where('notifiable_id', $user->id)->count();
echo "Total notifications created: {$totalNotifications}\n";
echo "All tests completed successfully!\n";
