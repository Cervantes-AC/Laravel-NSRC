# Time In/Time Out Logic Fix

## Problem Statement
Previously, when a user had a "time in" record but no corresponding "time out" within the same day, the system would:
1. Create a record with `duration_minutes = 0`
2. Mark status as `MISSING_TIMEOUT`
3. Not properly record the session duration
4. Show incomplete data in reports and metrics

## Solution Implemented

### Changes to `DutyEngine.php`

#### 1. **Updated `calculateDuration()` Method**
**Before:**
```php
public function calculateDuration($timeIn, $timeOut): int
{
    if (!$timeIn || !$timeOut) {
        return 0;  // Returns 0 if either is missing
    }
    // ... calculate difference
}
```

**After:**
```php
public function calculateDuration($timeIn, $timeOut): int
{
    if (!$timeIn) {
        return 0;
    }

    // If no time_out, calculate from time_in to end of day (23:59:59)
    if (!$timeOut) {
        $start = $timeIn instanceof \DateTimeInterface ? $timeIn : now()->parse($timeIn);
        $endOfDay = $start->copy()->endOfDay();
        
        if ($endOfDay <= $start) {
            return 0;
        }
        
        return (int) $start->diffInMinutes($endOfDay);
    }
    // ... calculate difference between time_in and time_out
}
```

**Impact:** Now calculates duration from time_in to end of day (23:59:59) when time_out is missing, ensuring records are not lost.

#### 2. **Updated `generateIntegrityScore()` Method**
**Before:**
- Complete session (both times): 100.0
- Missing timeout: 60.0
- Invalid: 40.0

**After:**
- Complete session (both times): 100.0
- Missing timeout: 70.0 (increased from 60.0 to reflect partial data)
- Invalid: 40.0

**Impact:** Better reflects data quality when timeout is missing.

#### 3. **Updated `determineStatus()` Method**
**Before:**
```php
if ($timeIn && !$timeOut && $hasPair) {
    return 'ONGOING';  // Only if hasPair was true
}

if ($timeIn && !$timeOut) {
    return 'MISSING_TIMEOUT';
}
```

**After:**
```php
if ($timeIn && !$timeOut) {
    // Record exists with time_in but no time_out - mark as MISSING_TIMEOUT
    // Duration is calculated from time_in to end of day
    return 'MISSING_TIMEOUT';
}
```

**Impact:** Consistently marks incomplete sessions as `MISSING_TIMEOUT` with calculated duration.

## Results

### Before Fix
- Records with only time_in: Not properly recorded
- Duration: 0 minutes
- Status: MISSING_TIMEOUT (but with no duration data)
- Reports: Incomplete data

### After Fix
- Records with only time_in: Properly recorded
- Duration: Calculated from time_in to 23:59:59 of that day
- Status: MISSING_TIMEOUT (with valid duration)
- Reports: Complete data with proper duration calculations
- Metrics: Included in calculations with estimated duration

## Database Impact
No database schema changes required. The fix works with existing `DutySession` table structure:
- `time_in`: datetime (can be null)
- `time_out`: datetime (can be null)
- `duration_minutes`: integer (now calculated even when time_out is null)
- `status`: string (COMPLETE, MISSING_TIMEOUT, INVALID_LOG)

## Testing Recommendations

1. **Test Case 1: Complete Session**
   - Time In: 09:00 AM
   - Time Out: 05:00 PM
   - Expected Duration: 480 minutes (8 hours)
   - Expected Status: COMPLETE

2. **Test Case 2: Missing Timeout (Same Day)**
   - Time In: 02:00 PM
   - Time Out: null
   - Expected Duration: ~600 minutes (2 PM to 11:59 PM)
   - Expected Status: MISSING_TIMEOUT

3. **Test Case 3: Invalid Session**
   - Time In: null
   - Time Out: null
   - Expected Duration: 0 minutes
   - Expected Status: INVALID_LOG

## Files Modified
- `app/Services/DutyEngine.php`
  - `calculateDuration()` method
  - `generateIntegrityScore()` method
  - `determineStatus()` method
