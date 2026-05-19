# Developer Integration Checklist

Use this checklist to integrate the Warning System into your code.

## Phase 1: Understanding (30 minutes)

- [ ] Read `QUICK_START_WARNING_SYSTEM.md`
- [ ] Review `WARNING_SYSTEM_GUIDE.md` architecture section
- [ ] Check `IMPLEMENTATION_EXAMPLES.md` for your use case
- [ ] Understand severity levels (info, warning, error, critical)
- [ ] Understand failure categories (system, security, validation, etc.)

## Phase 2: Setup (15 minutes)

- [ ] Verify migration ran: `php artisan migrate:status`
- [ ] Check notification table has new columns:
  ```sql
  DESCRIBE notifications;
  ```
- [ ] Verify `WarningService` loads: `php artisan tinker`
- [ ] Verify `Notification` model loads
- [ ] Verify routes registered: `/notifications`

## Phase 3: Service Integration (Per Service)

For each service that needs failure tracking:

### Step 1: Add Dependency
- [ ] Add `WarningService` to constructor
- [ ] Verify service loads without errors

### Step 2: Identify Failure Points
- [ ] List all try-catch blocks
- [ ] List all operations that could fail
- [ ] Identify which failures need logging
- [ ] Determine appropriate severity level

### Step 3: Add Failure Logging
- [ ] Wrap operations in try-catch
- [ ] Call appropriate `log*Failure()` method
- [ ] Pass user context when available
- [ ] Include relevant context data
- [ ] Test with actual failure scenario

### Step 4: Verify
- [ ] Check notification appears in database
- [ ] Verify notification displays in UI
- [ ] Check admin receives critical alerts
- [ ] Review audit logs for critical failures

## Phase 4: Controller Integration

For each controller that needs failure tracking:

- [ ] Add `WarningService` to constructor
- [ ] Identify operations that could fail
- [ ] Add try-catch blocks around operations
- [ ] Log failures with appropriate severity
- [ ] Test failure scenarios
- [ ] Verify user sees error message
- [ ] Verify notification is created

## Phase 5: Job/Scheduled Task Integration

For each background job or scheduled task:

- [ ] Add `WarningService` to constructor
- [ ] Wrap job logic in try-catch
- [ ] Call `logScheduledTaskFailure()` on error
- [ ] Verify admins are notified on failure
- [ ] Test with forced failure
- [ ] Check audit logs

## Phase 6: Policy Integration

For authorization policies:

- [ ] Add `WarningService` to constructor
- [ ] Log unauthorized access attempts
- [ ] Use `logAuthorizationFailure()`
- [ ] Include attempted action in context
- [ ] Test with unauthorized user
- [ ] Verify notification is created

## Phase 7: View Integration

For displaying notifications:

- [ ] Create notification display template
- [ ] Show unread failure count
- [ ] Display recent failures on dashboard
- [ ] Add link to full notification center
- [ ] Show severity badges
- [ ] Show category labels
- [ ] Add mark as read functionality
- [ ] Add acknowledge functionality
- [ ] Test with various notification types

## Phase 8: Testing

For each integrated component:

- [ ] Write unit test for failure logging
- [ ] Write integration test for notification creation
- [ ] Test with actual failure scenario
- [ ] Verify notification appears in database
- [ ] Verify notification displays in UI
- [ ] Test admin notification for critical failures
- [ ] Test batch failure notifications
- [ ] Test with different severity levels

## Phase 9: Documentation

For your code changes:

- [ ] Add comments explaining failure logging
- [ ] Document expected failure scenarios
- [ ] Document severity level chosen
- [ ] Document context data included
- [ ] Update service documentation
- [ ] Add examples to README if applicable

## Phase 10: Deployment

Before deploying to production:

- [ ] Run all tests: `php artisan test`
- [ ] Check for any PHP errors: `php artisan tinker`
- [ ] Verify routes: `php artisan route:list`
- [ ] Test failure scenarios in staging
- [ ] Verify admin notifications work
- [ ] Check audit logs for critical failures
- [ ] Monitor for any issues post-deployment

## Common Integration Patterns

### Pattern 1: Service Method Failure
```php
try {
    $result = $this->performOperation();
} catch (Exception $e) {
    $this->warningService->logActionFailure(
        action: 'Perform Operation',
        reason: $e->getMessage(),
        user: $user,
        severity: 'error'
    );
    throw $e; // Re-throw if needed
}
```

### Pattern 2: Batch Operation Failure
```php
$failed = [];
foreach ($items as $item) {
    try {
        $this->process($item);
    } catch (Exception $e) {
        $failed[] = $item->id;
    }
}

if (!empty($failed)) {
    $this->warningService->logBatchOperationFailure(
        operationName: 'Process Items',
        totalItems: count($items),
        failedItems: count($failed),
        failedIds: $failed,
        user: $user
    );
}
```

### Pattern 3: External Service Failure
```php
try {
    $response = $this->externalService->call();
} catch (Exception $e) {
    $this->warningService->logExternalServiceFailure(
        serviceName: 'External API',
        reason: $e->getMessage(),
        user: $user,
        context: ['retry_after' => 3600]
    );
}
```

### Pattern 4: Validation Failure
```php
$errors = $this->validate($data);
if (!empty($errors)) {
    $this->warningService->logValidationFailure(
        dataType: 'User Input',
        errors: $errors,
        user: $user
    );
}
```

## Severity Decision Tree

```
Is it a critical system failure?
├─ YES → severity: 'critical'
│        (Admins will be notified)
└─ NO
   │
   Is it a significant operation failure?
   ├─ YES → severity: 'error'
   │        (User will be notified)
   └─ NO
      │
      Is it a non-critical issue?
      ├─ YES → severity: 'warning'
      │        (User will be notified)
      └─ NO → severity: 'info'
             (Logged only)
```

## Category Decision Tree

```
What type of failure is it?
├─ Authentication/Authorization → 'security'
├─ Data Validation → 'validation'
├─ External Service (API, etc.) → 'external_service'
├─ Database Operation → 'database'
├─ File Operation → 'file'
├─ Import/Export → 'import_export'
├─ Backup Operation → 'backup'
├─ Scheduled Task → 'scheduled_task'
├─ Permission Check → 'authorization'
└─ Other → 'system'
```

## Testing Checklist

For each failure type:

- [ ] Create test user
- [ ] Trigger failure scenario
- [ ] Verify notification created in database
- [ ] Verify notification type is correct
- [ ] Verify severity is correct
- [ ] Verify category is correct
- [ ] Verify failure_reason is populated
- [ ] Verify failure_context is populated
- [ ] Verify user can view notification
- [ ] Verify admin receives critical alerts
- [ ] Verify audit log entry for critical failures
- [ ] Verify notification displays in UI

## Performance Checklist

- [ ] Notifications are indexed by severity and category
- [ ] Queries use pagination for large result sets
- [ ] Old notifications are archived regularly
- [ ] Batch operations are efficient
- [ ] No N+1 queries in notification retrieval
- [ ] Database indexes are being used

## Security Checklist

- [ ] No sensitive data in failure_reason
- [ ] No passwords/tokens in failure_context
- [ ] No PII in notification messages
- [ ] Authorization checks on notification access
- [ ] Users can only view their own notifications
- [ ] Critical failures logged to audit logs
- [ ] Failed login attempts tracked
- [ ] Unauthorized access attempts logged

## Documentation Checklist

- [ ] Code comments explain failure logging
- [ ] Expected failure scenarios documented
- [ ] Severity level choice documented
- [ ] Context data documented
- [ ] Service documentation updated
- [ ] Examples added to README
- [ ] Team trained on system
- [ ] Runbook created for admins

## Deployment Checklist

- [ ] All tests passing
- [ ] No PHP errors
- [ ] Routes registered
- [ ] Migration applied
- [ ] Staging tested
- [ ] Admin notifications working
- [ ] Audit logs working
- [ ] Performance acceptable
- [ ] No breaking changes
- [ ] Rollback plan ready

## Post-Deployment Checklist

- [ ] Monitor for errors in logs
- [ ] Check admin notifications
- [ ] Verify notification counts
- [ ] Monitor database size
- [ ] Check for any issues
- [ ] Gather user feedback
- [ ] Document any issues
- [ ] Plan improvements

## Quick Reference

### Inject Service
```php
use App\Services\WarningService;

public function __construct(
    private readonly WarningService $warningService,
) {}
```

### Log Failure
```php
$this->warningService->logActionFailure(
    action: 'Operation Name',
    reason: $e->getMessage(),
    user: auth()->user(),
    severity: 'error'
);
```

### Query Notifications
```php
$failures = auth()->user()->notifications()
    ->failures()
    ->unread()
    ->get();
```

### Display in Blade
```blade
@foreach($failures as $notification)
    <div class="alert alert-{{ $notification->severity }}">
        {{ $notification->data['message'] }}
    </div>
@endforeach
```

## Support Resources

- `QUICK_START_WARNING_SYSTEM.md` - Quick reference
- `WARNING_SYSTEM_GUIDE.md` - Comprehensive guide
- `IMPLEMENTATION_EXAMPLES.md` - Code examples
- `app/Services/WarningService.php` - Service code
- `app/Models/Notification.php` - Model code
- `app/Http/Controllers/NotificationController.php` - Controller code

## Getting Help

1. Check the documentation files
2. Review implementation examples
3. Check application logs
4. Review audit logs
5. Ask team members
6. Check Laravel documentation

---

**Status**: Ready for integration

Use this checklist to systematically integrate the Warning System into your application.
