# Warning System Implementation Examples

This document provides practical examples of how to integrate the Warning System into your existing code.

## Quick Start

### 1. Inject WarningService into Your Class

```php
use App\Services\WarningService;

class YourService
{
    public function __construct(
        private readonly WarningService $warningService,
    ) {}
}
```

### 2. Log Failures When They Occur

```php
try {
    // Your operation
    $result = performSomeOperation();
} catch (Exception $e) {
    $this->warningService->logActionFailure(
        action: 'Perform Operation',
        reason: $e->getMessage(),
        user: auth()->user(),
        severity: 'error'
    );
}
```

## Real-World Examples

### Example 1: Import Service with Batch Failure Tracking

**Before (without warning system):**
```php
class ImportService
{
    public function import(array $data)
    {
        $imported = 0;
        foreach ($data as $item) {
            try {
                DutySession::create($item);
                $imported++;
            } catch (Exception $e) {
                // Silently fail
            }
        }
        return $imported;
    }
}
```

**After (with warning system):**
```php
use App\Services\WarningService;

class ImportService
{
    public function __construct(
        private readonly WarningService $warningService,
    ) {}

    public function import(array $data, User $user)
    {
        $imported = 0;
        $failed = [];
        $failedIds = [];

        foreach ($data as $item) {
            try {
                DutySession::create($item);
                $imported++;
            } catch (Exception $e) {
                $failed[] = $item;
                $failedIds[] = $item['id'] ?? 'unknown';
            }
        }

        // Log batch failure if any items failed
        if (!empty($failed)) {
            $this->warningService->logBatchOperationFailure(
                operationName: 'Duty Session Import',
                totalItems: count($data),
                failedItems: count($failed),
                failedIds: $failedIds,
                user: $user
            );
        }

        return ['imported' => $imported, 'failed' => count($failed)];
    }
}
```

### Example 2: Google Sheets Sync with External Service Failure Tracking

**Before:**
```php
class GoogleSheetsService
{
    public function sync()
    {
        try {
            $sheets = $this->getSheets();
            // Process sheets
        } catch (Exception $e) {
            Log::error('Google Sheets sync failed: ' . $e->getMessage());
        }
    }
}
```

**After:**
```php
use App\Services\WarningService;

class GoogleSheetsService
{
    public function __construct(
        private readonly WarningService $warningService,
    ) {}

    public function sync()
    {
        try {
            $sheets = $this->getSheets();
            // Process sheets
        } catch (Exception $e) {
            $this->warningService->logExternalServiceFailure(
                serviceName: 'Google Sheets API',
                reason: $e->getMessage(),
                context: [
                    'error_code' => $e->getCode(),
                    'timestamp' => now()->toIso8601String(),
                ]
            );
        }
    }
}
```

### Example 3: Backup Service with Critical Failure Notification

**Before:**
```php
class BackupService
{
    public function backup($type)
    {
        try {
            // Backup logic
        } catch (Exception $e) {
            Log::error("Backup failed: {$e->getMessage()}");
        }
    }
}
```

**After:**
```php
use App\Services\WarningService;

class BackupService
{
    public function __construct(
        private readonly WarningService $warningService,
    ) {}

    public function backup($type, ?User $user = null)
    {
        try {
            // Backup logic
        } catch (Exception $e) {
            $this->warningService->logBackupFailure(
                backupType: $type,
                reason: $e->getMessage(),
                user: $user,
                context: [
                    'backup_type' => $type,
                    'error_code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );
        }
    }
}
```

### Example 4: Controller with Validation Failure Tracking

**Before:**
```php
class DutySessionController extends Controller
{
    public function store(StoreDutySessionRequest $request)
    {
        try {
            $session = DutySession::create($request->validated());
            return redirect()->route('duty-sessions.show', $session);
        } catch (Exception $e) {
            return back()->withError('Failed to create session');
        }
    }
}
```

**After:**
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
                context: [
                    'input' => $request->validated(),
                    'error_code' => $e->getCode(),
                ],
                severity: 'error'
            );
            return back()->withError('Failed to create session');
        }
    }
}
```

### Example 5: Authorization Failure Tracking

**Before:**
```php
class DutySessionPolicy
{
    public function update(User $user, DutySession $session)
    {
        return $user->id === $session->volunteer_id || $user->isAdmin();
    }
}
```

**After:**
```php
use App\Services\WarningService;

class DutySessionPolicy
{
    public function __construct(
        private readonly WarningService $warningService,
    ) {}

    public function update(User $user, DutySession $session)
    {
        $authorized = $user->id === $session->volunteer_id || $user->isAdmin();

        if (!$authorized) {
            $this->warningService->logAuthorizationFailure(
                action: 'Update Duty Session',
                user: $user,
                context: [
                    'session_id' => $session->id,
                    'session_owner_id' => $session->volunteer_id,
                ]
            );
        }

        return $authorized;
    }
}
```

### Example 6: File Upload with Failure Tracking

**Before:**
```php
class FileUploadService
{
    public function upload($file)
    {
        try {
            $path = $file->store('uploads');
            return $path;
        } catch (Exception $e) {
            return null;
        }
    }
}
```

**After:**
```php
use App\Services\WarningService;

class FileUploadService
{
    public function __construct(
        private readonly WarningService $warningService,
    ) {}

    public function upload($file, ?User $user = null)
    {
        try {
            $path = $file->store('uploads');
            return $path;
        } catch (Exception $e) {
            $this->warningService->logFileOperationFailure(
                operation: 'Upload',
                filename: $file->getClientOriginalName(),
                reason: $e->getMessage(),
                user: $user,
                context: [
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]
            );
            return null;
        }
    }
}
```

### Example 7: Scheduled Task with Failure Notification

**Before:**
```php
// routes/console.php
Schedule::call(function () {
    try {
        // Sync logic
    } catch (Exception $e) {
        Log::error('Sync failed: ' . $e->getMessage());
    }
})->hourly();
```

**After:**
```php
// routes/console.php
use App\Services\WarningService;

Schedule::call(function () {
    $warningService = app(WarningService::class);
    
    try {
        // Sync logic
    } catch (Exception $e) {
        $warningService->logScheduledTaskFailure(
            taskName: 'Google Sheets Sync',
            reason: $e->getMessage(),
            context: [
                'scheduled_time' => now()->toIso8601String(),
                'error_code' => $e->getCode(),
            ]
        );
    }
})->hourly();
```

### Example 8: Database Operation with Failure Tracking

**Before:**
```php
class VolunteerMetricsService
{
    public function calculateMetrics($volunteerId)
    {
        try {
            $metrics = VolunteerMetrics::updateOrCreate(
                ['volunteer_id' => $volunteerId],
                ['total_hours' => $this->calculateHours($volunteerId)]
            );
            return $metrics;
        } catch (Exception $e) {
            return null;
        }
    }
}
```

**After:**
```php
use App\Services\WarningService;

class VolunteerMetricsService
{
    public function __construct(
        private readonly WarningService $warningService,
    ) {}

    public function calculateMetrics($volunteerId, ?User $user = null)
    {
        try {
            $metrics = VolunteerMetrics::updateOrCreate(
                ['volunteer_id' => $volunteerId],
                ['total_hours' => $this->calculateHours($volunteerId)]
            );
            return $metrics;
        } catch (Exception $e) {
            $this->warningService->logDatabaseFailure(
                operation: 'Update Volunteer Metrics',
                reason: $e->getMessage(),
                user: $user,
                context: [
                    'volunteer_id' => $volunteerId,
                    'table' => 'volunteer_metrics',
                    'error_code' => $e->getCode(),
                ]
            );
            return null;
        }
    }
}
```

### Example 9: Data Export with Failure Tracking

**Before:**
```php
class ExportService
{
    public function exportToCSV($data)
    {
        try {
            // Export logic
        } catch (Exception $e) {
            return false;
        }
    }
}
```

**After:**
```php
use App\Services\WarningService;

class ExportService
{
    public function __construct(
        private readonly WarningService $warningService,
    ) {}

    public function exportToCSV($data, User $user)
    {
        try {
            // Export logic
            return true;
        } catch (Exception $e) {
            $this->warningService->logImportExportFailure(
                type: 'export',
                reason: $e->getMessage(),
                processedCount: 0,
                totalCount: count($data),
                user: $user,
                context: [
                    'format' => 'csv',
                    'data_type' => 'duty_sessions',
                ]
            );
            return false;
        }
    }
}
```

### Example 10: Displaying Failures in Dashboard

**Blade Template:**
```blade
@php
    $recentFailures = auth()->user()->notifications()
        ->failures()
        ->unread()
        ->orderByDesc('created_at')
        ->limit(5)
        ->get();
@endphp

@if($recentFailures->count() > 0)
    <div class="card border-danger">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">
                <i class="fas fa-exclamation-triangle"></i>
                Recent Failures ({{ $recentFailures->count() }})
            </h5>
        </div>
        <div class="card-body">
            @foreach($recentFailures as $notification)
                <div class="alert alert-{{ $notification->severity }} mb-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>{{ $notification->data['action'] ?? 'Action Failed' }}</strong>
                            <span class="badge badge-{{ $notification->severity }}">
                                {{ $notification->getSeverityLabel() }}
                            </span>
                        </div>
                        <small class="text-muted">
                            {{ $notification->created_at->diffForHumans() }}
                        </small>
                    </div>
                    <p class="mb-0 mt-2">{{ $notification->data['reason'] ?? $notification->data['message'] }}</p>
                    @if($notification->failure_context)
                        <small class="text-muted d-block mt-1">
                            Context: {{ json_encode($notification->failure_context) }}
                        </small>
                    @endif
                </div>
            @endforeach
            <a href="{{ route('notifications.failures') }}" class="btn btn-sm btn-outline-danger">
                View All Failures
            </a>
        </div>
    </div>
@endif
```

## Testing the Warning System

```php
use App\Services\WarningService;
use App\Models\User;
use Tests\TestCase;

class WarningSystemTest extends TestCase
{
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

    public function test_warning_service_logs_batch_failure()
    {
        $user = User::factory()->create();
        $warningService = app(WarningService::class);

        $warningService->logBatchOperationFailure(
            operationName: 'Test Batch',
            totalItems: 100,
            failedItems: 25,
            failedIds: [1, 2, 3],
            user: $user
        );

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $user->id,
            'type' => 'warning_alert',
        ]);
    }

    public function test_critical_failures_notify_admins()
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $user = User::factory()->create();
        $warningService = app(WarningService::class);

        $warningService->logDatabaseFailure(
            operation: 'Test Operation',
            reason: 'Test Failure',
            user: $user
        );

        // Admin should receive notification
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'severity' => 'critical',
        ]);
    }
}
```

## Integration Checklist

- [ ] Inject `WarningService` into your service/controller
- [ ] Wrap operations in try-catch blocks
- [ ] Call appropriate `logXxxFailure()` method on exception
- [ ] Pass user context when available
- [ ] Include relevant context data
- [ ] Test failure scenarios
- [ ] Verify notifications appear in dashboard
- [ ] Check audit logs for critical failures
- [ ] Monitor admin notifications for critical issues

## Performance Tips

1. **Use Batch Operations**: When logging multiple failures, batch them together
2. **Limit Context Data**: Don't store massive objects in context
3. **Archive Old Notifications**: Implement scheduled cleanup
4. **Index Queries**: Use the provided scopes for efficient queries
5. **Paginate Results**: Always paginate when displaying notifications

## Security Considerations

1. **Sensitive Data**: Don't log passwords or tokens in context
2. **User Privacy**: Be careful with personal information in failure reasons
3. **Authorization**: Always check user permissions before showing notifications
4. **Audit Trail**: Critical failures are automatically logged to audit logs
5. **Rate Limiting**: Consider rate limiting failure notifications to prevent spam

---

For more information, see `WARNING_SYSTEM_GUIDE.md`
