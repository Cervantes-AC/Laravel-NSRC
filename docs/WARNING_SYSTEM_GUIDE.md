# Warning System & Failure Notifications Guide

## Overview

The Warning System is a comprehensive failure tracking and notification mechanism that automatically detects, logs, and notifies users about unsuccessful actions, errors, and critical failures throughout the NSRC AMS application.

## Architecture

### Core Components

1. **WarningService** (`app/Services/WarningService.php`)
   - Central service for logging failures and warnings
   - Handles different types of failures (validation, external services, database, etc.)
   - Manages admin notifications for critical issues
   - Provides specialized methods for different failure scenarios

2. **NotificationService** (Enhanced)
   - Extended with failure notification methods
   - Tracks failure context and details
   - Supports batch failure notifications
   - Provides query methods for retrieving failures

3. **Notification Model** (`app/Models/Notification.php`)
   - Represents individual notifications in the database
   - Includes failure tracking fields (severity, category, reason, context)
   - Provides scopes for querying failures, warnings, and critical alerts
   - Supports acknowledgment tracking

4. **Database Migration**
   - Adds failure tracking columns to notifications table
   - Includes indexes for performance optimization
   - Supports severity levels and categorization

## Failure Categories

The system supports the following failure categories:

| Category | Purpose | Severity |
|----------|---------|----------|
| `system` | General system failures | Variable |
| `security` | Authentication/authorization failures | Critical |
| `validation` | Data validation failures | Warning |
| `external_service` | Third-party service failures | Error |
| `database` | Database operation failures | Critical |
| `file` | File operation failures | Error |
| `import_export` | Import/export operation failures | Warning |
| `backup` | Backup operation failures | Critical |
| `scheduled_task` | Scheduled task failures | Error |
| `authorization` | Permission/authorization failures | Warning |

## Severity Levels

| Level | Description | Action |
|-------|-------------|--------|
| `info` | Informational message | Logged only |
| `warning` | Non-critical issue | User notified |
| `error` | Significant failure | User notified, logged |
| `critical` | Critical system failure | User notified, audit logged, admin notified |

## Usage Examples

### 1. Log a Simple Action Failure

```php
use App\Services\WarningService;

$warningService = app(WarningService::class);

$warningService->logActionFailure(
    action: 'User Import',
    reason: 'Invalid CSV format detected',
    user: $user,
    severity: 'warning'
);
```

### 2. Log a Batch Operation Failure

```php
$warningService->logBatchOperationFailure(
    operationName: 'Bulk Duty Session Update',
    totalItems: 100,
    failedItems: 15,
    failedIds: [1, 5, 12, 23, 45],
    user: $user
);
```

### 3. Log a Validation Failure

```php
$warningService->logValidationFailure(
    dataType: 'Attendance Record',
    errors: [
        'time_in is required',
        'volunteer_id must be numeric',
        'date must be a valid date'
    ],
    user: $user
);
```

### 4. Log an External Service Failure

```php
$warningService->logExternalServiceFailure(
    serviceName: 'Google Sheets API',
    reason: 'API rate limit exceeded',
    user: $user,
    context: ['retry_after' => 3600]
);
```

### 5. Log a Database Failure

```php
$warningService->logDatabaseFailure(
    operation: 'Bulk Insert',
    reason: 'Duplicate entry for volunteer_id: 123',
    user: $user,
    context: ['table' => 'duty_sessions']
);
```

### 6. Log a Security Failure

```php
$warningService->logSecurityFailure(
    failureType: 'Unauthorized Access Attempt',
    reason: 'User attempted to access admin panel without permission',
    user: $user,
    context: ['attempted_route' => '/admin/settings']
);
```

### 7. Log a File Operation Failure

```php
$warningService->logFileOperationFailure(
    operation: 'Upload',
    filename: 'attendance_report.pdf',
    reason: 'File size exceeds maximum allowed (50MB)',
    user: $user
);
```

### 8. Log an Import/Export Failure

```php
$warningService->logImportExportFailure(
    type: 'import',
    reason: 'Column mismatch: expected 5 columns, got 4',
    processedCount: 50,
    totalCount: 100,
    user: $user
);
```

### 9. Log a Backup Failure

```php
$warningService->logBackupFailure(
    backupType: 'database',
    reason: 'Insufficient disk space available',
    user: $user,
    context: ['required_space' => '2GB', 'available_space' => '500MB']
);
```

### 10. Log a Scheduled Task Failure

```php
$warningService->logScheduledTaskFailure(
    taskName: 'Google Sheets Sync',
    reason: 'Connection timeout after 30 seconds',
    context: ['attempt' => 1, 'max_retries' => 3]
);
```

### 11. Log an Authorization Failure

```php
$warningService->logAuthorizationFailure(
    action: 'Delete User Account',
    user: $user,
    context: ['target_user_id' => 456]
);
```

### 12. Create a Warning Notification

```php
$warningService->createWarningNotification(
    user: $user,
    title: 'Storage Warning',
    message: 'Your storage is 85% full',
    severity: 'warning'
);
```

### 13. Create a System-Wide Warning

```php
$warningService->createSystemWarning(
    title: 'Maintenance Alert',
    message: 'System maintenance scheduled for tonight at 2 AM',
    severity: 'warning'
);
```

## Retrieving Failure Notifications

### Get Unread Failures for a User

```php
use App\Services\NotificationService;

$notificationService = app(NotificationService::class);
$unreadFailures = $notificationService->getUnreadFailureNotifications($user);

foreach ($unreadFailures as $notification) {
    echo $notification->data['message'];
    echo $notification->getSeverityLabel();
    echo $notification->getCategoryLabel();
}
```

### Get All Failures (Paginated)

```php
$failures = $notificationService->getFailureNotifications($user, perPage: 20);

foreach ($failures as $notification) {
    // Process notification
}
```

### Query Failures Using Scopes

```php
use App\Models\Notification;

// Get all critical notifications
$critical = Notification::critical()->get();

// Get all warnings
$warnings = Notification::warnings()->get();

// Get unread failures
$unread = Notification::failures()->unread()->get();

// Get failures by category
$dbFailures = Notification::byCategory('database')->get();

// Get unacknowledged critical alerts
$unacknowledged = Notification::critical()->unacknowledged()->get();
```

## Notification Model Methods

### Mark as Read

```php
$notification->markAsRead();
```

### Acknowledge Notification

```php
$notification->acknowledge(acknowledgedBy: 'admin@example.com');
```

### Check Notification Type

```php
if ($notification->isFailure()) {
    // Handle failure notification
}

if ($notification->isCritical()) {
    // Handle critical notification
}
```

### Get Labels

```php
$severityLabel = $notification->getSeverityLabel(); // "Critical", "Warning", etc.
$categoryLabel = $notification->getCategoryLabel();  // "Database", "Security", etc.
```

## Integration with Existing Services

### In Controllers

```php
use App\Services\WarningService;

class DutySessionController extends Controller
{
    public function __construct(
        private readonly WarningService $warningService,
    ) {}

    public function store(StoreDutySessionRequest $request)
    {
        try {
            $session = DutySession::create($request->validated());
            return redirect()->route('duty-sessions.show', $session);
        } catch (Exception $e) {
            $this->warningService->logActionFailure(
                action: 'Create Duty Session',
                reason: $e->getMessage(),
                user: auth()->user(),
                severity: 'error'
            );
            return back()->withError('Failed to create duty session');
        }
    }
}
```

### In Services

```php
use App\Services\WarningService;

class ImportService
{
    public function __construct(
        private readonly WarningService $warningService,
    ) {}

    public function import(array $data, User $user)
    {
        $failed = [];
        $processed = 0;

        foreach ($data as $item) {
            try {
                // Process item
                $processed++;
            } catch (Exception $e) {
                $failed[] = $item['id'];
            }
        }

        if (!empty($failed)) {
            $this->warningService->logBatchOperationFailure(
                operationName: 'CSV Import',
                totalItems: count($data),
                failedItems: count($failed),
                failedIds: $failed,
                user: $user
            );
        }
    }
}
```

### In Jobs

```php
use App\Services\WarningService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncGoogleSheetsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly WarningService $warningService,
    ) {}

    public function handle()
    {
        try {
            // Sync logic
        } catch (Exception $e) {
            $this->warningService->logScheduledTaskFailure(
                taskName: 'Google Sheets Sync',
                reason: $e->getMessage()
            );
        }
    }
}
```

## Frontend Display

### Show Unread Failures in Dashboard

```blade
@php
    $unreadFailures = auth()->user()->notifications()
        ->failures()
        ->unread()
        ->orderByDesc('created_at')
        ->limit(5)
        ->get();
@endphp

@if($unreadFailures->count() > 0)
    <div class="alert-section">
        <h3>Recent Failures</h3>
        @foreach($unreadFailures as $notification)
            <div class="alert alert-{{ $notification->severity }}">
                <strong>{{ $notification->getSeverityLabel() }}</strong>
                <span class="category">{{ $notification->getCategoryLabel() }}</span>
                <p>{{ $notification->data['message'] }}</p>
                <small>{{ $notification->created_at->diffForHumans() }}</small>
            </div>
        @endforeach
    </div>
@endif
```

### Notification Center

```blade
@php
    $failures = auth()->user()->notifications()
        ->failures()
        ->orderByDesc('created_at')
        ->paginate(15);
@endphp

<div class="notification-center">
    @forelse($failures as $notification)
        <div class="notification-item {{ $notification->read_at ? 'read' : 'unread' }}">
            <div class="notification-header">
                <span class="severity-badge badge-{{ $notification->severity }}">
                    {{ $notification->getSeverityLabel() }}
                </span>
                <span class="category-badge">{{ $notification->getCategoryLabel() }}</span>
                <time>{{ $notification->created_at->format('M d, Y H:i') }}</time>
            </div>
            <div class="notification-body">
                <p>{{ $notification->data['message'] }}</p>
                @if($notification->failure_reason)
                    <p class="reason">{{ $notification->failure_reason }}</p>
                @endif
            </div>
            <div class="notification-actions">
                @if(!$notification->read_at)
                    <button onclick="markAsRead({{ $notification->id }})">Mark as Read</button>
                @endif
                @if(!$notification->acknowledged_at)
                    <button onclick="acknowledge({{ $notification->id }})">Acknowledge</button>
                @endif
            </div>
        </div>
    @empty
        <p>No failure notifications</p>
    @endforelse

    {{ $failures->links() }}
</div>
```

## Database Schema

### Notifications Table (Enhanced)

```sql
CREATE TABLE notifications (
    id UUID PRIMARY KEY,
    type VARCHAR(255),
    notifiable_type VARCHAR(255),
    notifiable_id BIGINT UNSIGNED,
    data JSON,
    severity VARCHAR(50) DEFAULT 'info',
    category VARCHAR(50) NULLABLE,
    failure_reason TEXT NULLABLE,
    failure_context JSON NULLABLE,
    read_at TIMESTAMP NULLABLE,
    acknowledged_at TIMESTAMP NULLABLE,
    acknowledged_by VARCHAR(255) NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX (severity, created_at),
    INDEX (category, created_at),
    INDEX (notifiable_id, notifiable_type, severity)
);
```

## Best Practices

1. **Always Provide Context**: Include relevant details in the context array for debugging
2. **Use Appropriate Severity**: Match severity to the actual impact of the failure
3. **Categorize Failures**: Use the correct category for better organization
4. **Notify Admins for Critical Issues**: Critical failures automatically notify all active admins
5. **Log to Application Logs**: All failures are logged to Laravel logs for audit trails
6. **Acknowledge Important Notifications**: Use the acknowledge feature for critical alerts
7. **Clean Up Old Notifications**: Implement a scheduled task to archive old notifications
8. **Test Failure Scenarios**: Test your failure handling in development before production

## Scheduled Maintenance

### Archive Old Notifications

Add to `routes/console.php`:

```php
Schedule::call(function () {
    Notification::where('created_at', '<', now()->subMonths(3))
        ->delete();
})->monthly();
```

### Clean Up Acknowledged Notifications

```php
Schedule::call(function () {
    Notification::where('acknowledged_at', '<', now()->subMonths(6))
        ->delete();
})->monthly();
```

## Troubleshooting

### Notifications Not Appearing

1. Check if the user has the `Notifiable` trait
2. Verify the notification is being created with correct `notifiable_type`
3. Check database for notification records
4. Verify user's notification preferences

### Missing Failure Context

1. Ensure context array is passed to logging methods
2. Check that `failure_context` column exists in database
3. Run migration: `php artisan migrate`

### Admin Notifications Not Sent

1. Verify admin users exist with `role = 'admin'` and `status = 'active'`
2. Check that `WarningService` is properly injected
3. Review application logs for errors

## Testing

```php
use App\Services\WarningService;
use App\Models\User;

public function test_warning_service_logs_action_failure()
{
    $user = User::factory()->create();
    $warningService = app(WarningService::class);

    $warningService->logActionFailure(
        action: 'Test Action',
        reason: 'Test Reason',
        user: $user,
        severity: 'warning'
    );

    $this->assertDatabaseHas('notifications', [
        'notifiable_id' => $user->id,
        'notifiable_type' => User::class,
        'type' => 'warning_alert',
    ]);
}
```

## Performance Considerations

- Notifications are indexed by severity and category for fast queries
- Use pagination when retrieving large notification sets
- Consider archiving old notifications regularly
- Monitor notification table size in production

## Security

- All failure notifications are logged to audit logs
- Critical failures trigger admin notifications
- Unauthorized access attempts are tracked
- Failed login attempts are monitored
- All actions are timestamped and attributed to users

---

For more information, see the source code in:
- `app/Services/WarningService.php`
- `app/Services/NotificationService.php`
- `app/Models/Notification.php`
