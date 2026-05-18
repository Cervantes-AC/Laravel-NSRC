# Quick Start: Warning System

## 5-Minute Setup

### Step 1: Inject the Service
```php
use App\Services\WarningService;

class YourService
{
    public function __construct(
        private readonly WarningService $warningService,
    ) {}
}
```

### Step 2: Log Failures
```php
try {
    // Your operation
    $result = doSomething();
} catch (Exception $e) {
    $this->warningService->logActionFailure(
        action: 'Do Something',
        reason: $e->getMessage(),
        user: auth()->user(),
        severity: 'error'
    );
}
```

### Step 3: View Notifications
Users can view their notifications at:
- `/notifications` - All notifications
- `/notifications/failures` - Failure notifications only

## Common Scenarios

### Import/Export Failure
```php
$this->warningService->logImportExportFailure(
    type: 'import',
    reason: 'Invalid CSV format',
    processedCount: 50,
    totalCount: 100,
    user: $user
);
```

### External Service Failure
```php
$this->warningService->logExternalServiceFailure(
    serviceName: 'Google Sheets API',
    reason: 'Connection timeout',
    user: $user
);
```

### Database Failure
```php
$this->warningService->logDatabaseFailure(
    operation: 'Bulk Insert',
    reason: 'Duplicate entry',
    user: $user
);
```

### Batch Operation Failure
```php
$this->warningService->logBatchOperationFailure(
    operationName: 'Update Sessions',
    totalItems: 100,
    failedItems: 15,
    failedIds: [1, 5, 12],
    user: $user
);
```

### Scheduled Task Failure
```php
$this->warningService->logScheduledTaskFailure(
    taskName: 'Google Sheets Sync',
    reason: 'API rate limit exceeded'
);
```

### Validation Failure
```php
$this->warningService->logValidationFailure(
    dataType: 'Attendance Record',
    errors: ['time_in is required', 'date must be valid'],
    user: $user
);
```

### File Operation Failure
```php
$this->warningService->logFileOperationFailure(
    operation: 'Upload',
    filename: 'report.pdf',
    reason: 'File size exceeds limit',
    user: $user
);
```

### Authorization Failure
```php
$this->warningService->logAuthorizationFailure(
    action: 'Delete User',
    user: $user
);
```

### Security Failure
```php
$this->warningService->logSecurityFailure(
    failureType: 'Unauthorized Access',
    reason: 'User attempted admin access',
    user: $user
);
```

### Backup Failure
```php
$this->warningService->logBackupFailure(
    backupType: 'database',
    reason: 'Insufficient disk space',
    user: $user
);
```

## Severity Levels

| Level | Use When |
|-------|----------|
| `warning` | Non-critical issue, user should be aware |
| `error` | Significant failure, operation didn't complete |
| `critical` | System failure, admins must be notified |

## Querying Notifications

### Get Unread Failures
```php
$failures = auth()->user()->notifications()
    ->failures()
    ->unread()
    ->get();
```

### Get Critical Alerts
```php
$critical = auth()->user()->notifications()
    ->critical()
    ->get();
```

### Get by Category
```php
$dbFailures = auth()->user()->notifications()
    ->byCategory('database')
    ->get();
```

### Get Unacknowledged
```php
$unacknowledged = auth()->user()->notifications()
    ->unacknowledged()
    ->get();
```

## Display in Blade

### Show Recent Failures
```blade
@php
    $failures = auth()->user()->notifications()
        ->failures()
        ->unread()
        ->limit(5)
        ->get();
@endphp

@foreach($failures as $notification)
    <div class="alert alert-{{ $notification->severity }}">
        <strong>{{ $notification->getSeverityLabel() }}</strong>
        <p>{{ $notification->data['message'] }}</p>
        <small>{{ $notification->created_at->diffForHumans() }}</small>
    </div>
@endforeach
```

### Show Failure Count
```blade
@php
    $failureCount = auth()->user()->notifications()
        ->failures()
        ->unread()
        ->count();
@endphp

@if($failureCount > 0)
    <span class="badge badge-danger">{{ $failureCount }} failures</span>
@endif
```

## Routes

| Route | Method | Purpose |
|-------|--------|---------|
| `/notifications` | GET | View all notifications |
| `/notifications/failures` | GET | View failures only |
| `/notifications/{id}` | GET | View single notification |
| `/notifications/{id}/read` | POST | Mark as read |
| `/notifications/read-all` | POST | Mark all as read |
| `/notifications/{id}/acknowledge` | POST | Acknowledge notification |
| `/notifications/{id}` | DELETE | Delete notification |
| `/notifications/export` | GET | Export to CSV |

## API Endpoints

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/notifications/api/unread-count` | GET | Get unread count (JSON) |
| `/notifications/api/recent-failures` | GET | Get recent failures (JSON) |
| `/notifications/api/critical-alerts` | GET | Get critical alerts (JSON) |

## Notification Methods

### Mark as Read
```php
$notification->markAsRead();
```

### Acknowledge
```php
$notification->acknowledge(auth()->user()->email);
```

### Check Type
```php
if ($notification->isFailure()) { }
if ($notification->isCritical()) { }
```

### Get Labels
```php
$notification->getSeverityLabel();  // "Critical", "Warning", etc.
$notification->getCategoryLabel();  // "Database", "Security", etc.
```

## Testing

```php
public function test_failure_notification()
{
    $user = User::factory()->create();
    $warningService = app(WarningService::class);

    $warningService->logActionFailure(
        action: 'Test',
        reason: 'Test Reason',
        user: $user
    );

    $this->assertDatabaseHas('notifications', [
        'notifiable_id' => $user->id,
    ]);
}
```

## Best Practices

1. **Always provide context** - Include relevant details for debugging
2. **Use appropriate severity** - Match severity to actual impact
3. **Categorize failures** - Use correct category for organization
4. **Notify admins for critical** - Critical failures auto-notify admins
5. **Test failure scenarios** - Verify notifications appear correctly
6. **Clean up old notifications** - Archive old notifications regularly
7. **Don't log sensitive data** - Avoid passwords, tokens, PII

## Troubleshooting

### Notifications not appearing?
1. Check user has `Notifiable` trait
2. Verify notification is created with correct `notifiable_type`
3. Check database for notification records
4. Review application logs

### Admin not receiving critical alerts?
1. Verify admin exists with `role = 'admin'` and `status = 'active'`
2. Check `WarningService` is properly injected
3. Review application logs for errors

### Missing failure context?
1. Ensure context array is passed to logging method
2. Check `failure_context` column exists in database
3. Run migration: `php artisan migrate`

## Files to Review

- `WARNING_SYSTEM_GUIDE.md` - Comprehensive documentation
- `IMPLEMENTATION_EXAMPLES.md` - Real-world code examples
- `app/Services/WarningService.php` - Service implementation
- `app/Models/Notification.php` - Notification model
- `app/Http/Controllers/NotificationController.php` - Controller

## Next Steps

1. ✅ Review this quick start guide
2. ✅ Read `WARNING_SYSTEM_GUIDE.md` for details
3. ✅ Check `IMPLEMENTATION_EXAMPLES.md` for patterns
4. ✅ Integrate into your services
5. ✅ Test failure scenarios
6. ✅ Create notification views
7. ✅ Monitor admin notifications

---

**Ready to use!** Start logging failures in your code today.
