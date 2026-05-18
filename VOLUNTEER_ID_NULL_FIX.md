# Volunteer ID Null Error Fix

## Problem

When syncing attendance data, the system was throwing a database error:

```
SQLSTATE[22007]: Invalid datetime format: 1366 Incorrect integer value: '' for column 
`nsrc_ams`.`volunteer_metrics`.`volunteer_id` at row 1
```

This occurred because:
1. `DutySession` records were being created with `volunteer_id = null` when no matching user was found
2. `MetricsService::calculateVolunteerMetrics()` was grouping sessions by `volunteer_id` without filtering nulls
3. The code attempted to insert a `VolunteerMetrics` record with `volunteer_id = null`, which violates the database constraint

## Root Cause

The volunteer ID resolution logic was inconsistent across the codebase:

- **SyncMySQLAttendance command**: Had proper resolution with exact match → fuzzy match → create new volunteer
- **AttendanceController**: Simple lookup that returned null if not found
- **ImportController**: Simple lookup that returned null if not found
- **SessionsController API**: Simple lookup that returned null if not found

When `volunteer_id` was null, `MetricsService` would try to insert a metric record with a null ID, causing the database error.

## Solution

### 1. Updated MetricsService (app/Services/MetricsService.php)

Added filtering to skip sessions with null `volunteer_id`:

```php
public function calculateVolunteerMetrics(Collection $sessions): Collection
{
    // Filter out sessions with null volunteer_id to prevent database errors
    $validSessions = $sessions->filter(fn ($s) => $s->volunteer_id !== null);
    $grouped = $validSessions->groupBy('volunteer_id');
    // ... rest of method
}
```

**Impact**: Prevents database errors by only processing sessions with valid volunteer IDs.

### 2. Updated AttendanceController (app/Http/Controllers/AttendanceController.php)

Added `resolveVolunteerId()` method with proper matching logic:

```php
private function resolveVolunteerId(string $fullName): ?int
{
    // Try exact match first
    $exact = User::query()
        ->where('full_name', $fullName)
        ->value('id');

    if ($exact) {
        return (int) $exact;
    }

    // Try fuzzy match with 85% similarity threshold
    $nameService = app(NameNormalizationService::class);
    foreach (User::query()->whereNotNull('full_name')->get(['id', 'full_name']) as $user) {
        if ($nameService->areNamesSimilar($fullName, $user->full_name, 85.0)) {
            return $user->id;
        }
    }

    // No match found - return null (will be handled by MetricsService filter)
    Log::warning('No volunteer found for attendance record', ['full_name' => $fullName]);
    return null;
}
```

**Impact**: Consistent volunteer ID resolution with proper logging of unmatched names.

### 3. Updated ImportController (app/Http/Controllers/ImportController.php)

Added the same `resolveVolunteerId()` method for consistency.

**Impact**: Ensures import operations use the same matching logic.

### 4. Updated SessionsController API (app/Http/Controllers/Api/SessionsController.php)

Added the same `resolveVolunteerId()` method for consistency.

**Impact**: Ensures API sync operations use the same matching logic.

## Files Modified

1. `app/Services/MetricsService.php` - Added null filtering
2. `app/Http/Controllers/AttendanceController.php` - Added resolveVolunteerId method
3. `app/Http/Controllers/ImportController.php` - Added resolveVolunteerId method
4. `app/Http/Controllers/Api/SessionsController.php` - Added resolveVolunteerId method

## Behavior Changes

### Before Fix
- Attendance records with unmatched names → `volunteer_id = null` → Database error when calculating metrics
- No logging of unmatched names
- Inconsistent matching logic across controllers

### After Fix
- Attendance records with unmatched names → `volunteer_id = null` → Skipped during metrics calculation
- Unmatched names logged for debugging
- Consistent matching logic across all controllers (exact → fuzzy → null)
- No database errors

## Testing

All existing tests pass:
```
Tests: 6 passed (22 assertions)
Duration: 1.67s
```

## Recommendations

1. **Monitor Logs**: Check application logs for "No volunteer found for attendance record" warnings to identify unmatched names
2. **Data Quality**: Review attendance records with unmatched names and either:
   - Create corresponding user accounts
   - Update user full_name fields to match attendance records
3. **Fuzzy Matching**: The 85% similarity threshold can be adjusted if needed (currently matches names like "Juan Dela Cruz" with "Juan De La Cruz")

## Migration Notes

No database migrations required. The fix works with existing schema and only changes application logic.
