# Import/Export Data Synchronization Fix

## Problem Statement
The import and export data structures were misaligned, causing data loss when exporting and re-importing attendance records:

1. **Export included `id` field** - Not needed for re-import and caused confusion
2. **Export excluded `source_signature` and `source_payload`** - These fields are part of the Attendance model but weren't being exported
3. **Import didn't handle `source_signature` and `source_payload`** - When re-importing exported data, these fields were ignored
4. **Inconsistent column sets** - Export and import had different column expectations

## Solution Implemented

### 1. **Updated `ExportController.php` - `attendance()` Method**

**Changes:**
- Removed `id` field from export (not needed for re-import)
- Added `source_signature` field to export
- Added `source_payload` field to export (converted to JSON string for CSV compatibility)

**Before:**
```php
$data = $records->map(function ($record) {
    return [
        'id' => $record->id,
        'full_name' => $record->full_name,
        'attendance' => $record->attendance,
        'date_time' => $record->date_time,
        'location' => $record->location,
        'shift_type' => $record->shift_type,
    ];
});
```

**After:**
```php
$data = $records->map(function ($record) {
    return [
        'full_name' => $record->full_name,
        'attendance' => $record->attendance,
        'date_time' => $record->date_time,
        'location' => $record->location,
        'shift_type' => $record->shift_type,
        'source_signature' => $record->source_signature,
        'source_payload' => is_array($record->source_payload) ? json_encode($record->source_payload) : $record->source_payload,
    ];
});
```

### 2. **Updated `ImportService.php` - `normalizeSheetRow()` Method**

**Changes:**
- Added support for `source_signature` field
- Added support for `source_payload` field with JSON validation
- Validates JSON format before storing

**Before:**
```php
private function normalizeSheetRow(array $row): array
{
    $timestamp = $row['timestamp'] ?? $row['date_time'] ?? now();

    return [
        'full_name' => trim((string) ($row['full_name'] ?? '')),
        'attendance' => trim((string) ($row['attendance'] ?? '')),
        'date_time' => $timestamp,
        'location' => isset($row['location']) ? trim((string) $row['location']) : null,
        'shift_type' => isset($row['shift_type']) ? trim((string) $row['shift_type']) : null,
    ];
}
```

**After:**
```php
private function normalizeSheetRow(array $row): array
{
    $timestamp = $row['timestamp'] ?? $row['date_time'] ?? now();
    $sourcePayload = $row['source_payload'] ?? null;

    // If source_payload is a JSON string, keep it as is for later parsing
    if (is_string($sourcePayload) && !empty($sourcePayload)) {
        try {
            // Validate it's valid JSON
            json_decode($sourcePayload, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $sourcePayload = null;
        }
    }

    return [
        'full_name' => trim((string) ($row['full_name'] ?? '')),
        'attendance' => trim((string) ($row['attendance'] ?? '')),
        'date_time' => $timestamp,
        'location' => isset($row['location']) ? trim((string) $row['location']) : null,
        'shift_type' => isset($row['shift_type']) ? trim((string) $row['shift_type']) : null,
        'source_signature' => isset($row['source_signature']) ? trim((string) $row['source_signature']) : null,
        'source_payload' => $sourcePayload,
    ];
}
```

### 3. **Updated `ImportService.php` - `processImport()` Method**

**Changes:**
- Added `source_signature` to Attendance::create()
- Added `source_payload` to Attendance::create() with JSON decoding

**Before:**
```php
Attendance::create([
    'full_name' => $normalized['full_name'],
    'attendance' => $normalized['attendance'],
    'date_time' => $normalized['date_time'],
    'location' => $normalized['location'],
    'shift_type' => $normalized['shift_type'],
]);
```

**After:**
```php
Attendance::create([
    'full_name' => $normalized['full_name'],
    'attendance' => $normalized['attendance'],
    'date_time' => $normalized['date_time'],
    'location' => $normalized['location'],
    'shift_type' => $normalized['shift_type'],
    'source_signature' => $normalized['source_signature'],
    'source_payload' => $normalized['source_payload'] ? json_decode($normalized['source_payload'], true) : null,
]);
```

### 4. **Updated `ImportService.php` - `validateRow()` Method**

**Changes:**
- Added validation rules for `source_signature` field
- Added validation rules for `source_payload` field

**Before:**
```php
$validator = Validator::make($normalized, [
    'full_name' => 'required|string|max:255',
    'attendance' => 'required|string',
    'date_time' => 'required',
    'location' => 'nullable|string|max:255',
]);
```

**After:**
```php
$validator = Validator::make($normalized, [
    'full_name' => 'required|string|max:255',
    'attendance' => 'required|string',
    'date_time' => 'required',
    'location' => 'nullable|string|max:255',
    'shift_type' => 'nullable|string|max:255',
    'source_signature' => 'nullable|string|max:255',
    'source_payload' => 'nullable|string',
]);
```

### 5. **Updated `ImportController.php` - `export()` Method**

**Changes:**
- Removed `id` field from sessions export
- Added attendance export support with proper field mapping
- Ensured consistency with ExportController

**Before:**
```php
'sessions' => DutySession::with('volunteer')->get()->map(fn ($s) => [
    'id' => $s->id,
    'full_name' => $s->full_name,
    // ... other fields
]),
```

**After:**
```php
'sessions' => DutySession::with('volunteer')->get()->map(fn ($s) => [
    'full_name' => $s->full_name,
    // ... other fields
]),
'attendance' => Attendance::all()->map(fn ($a) => [
    'full_name' => $a->full_name,
    'attendance' => $a->attendance,
    'date_time' => $a->date_time,
    'location' => $a->location,
    'shift_type' => $a->shift_type,
    'source_signature' => $a->source_signature,
    'source_payload' => is_array($a->source_payload) ? json_encode($a->source_payload) : $a->source_payload,
]),
```

## Export/Import Data Structure

### Attendance Export Columns (CSV)
```
full_name, attendance, date_time, location, shift_type, source_signature, source_payload
```

### Attendance Import Expected Columns (CSV)
```
full_name, attendance, date_time (or timestamp), location, shift_type, source_signature, source_payload
```

**Note:** All fields except `full_name` and `attendance` are optional.

## Benefits

1. **Data Integrity**: All Attendance model fields are now exported and can be re-imported
2. **Consistency**: Export and import use the same column structure
3. **No Data Loss**: `source_signature` and `source_payload` are preserved during export/import cycles
4. **Cleaner Exports**: Removed unnecessary `id` field that isn't needed for re-import
5. **JSON Handling**: Proper JSON encoding/decoding for `source_payload` field
6. **Validation**: Added validation for new fields to ensure data quality

## Testing Recommendations

1. **Test Case 1: Export and Re-import**
   - Export attendance records
   - Re-import the exported CSV
   - Verify all fields are preserved, including `source_signature` and `source_payload`

2. **Test Case 2: Partial Data**
   - Export records with some null `source_signature` and `source_payload` values
   - Re-import and verify null values are handled correctly

3. **Test Case 3: JSON Payload**
   - Export records with complex `source_payload` JSON
   - Re-import and verify JSON is correctly decoded and stored

4. **Test Case 4: Manual CSV Creation**
   - Create a CSV with only required fields (full_name, attendance, date_time)
   - Import and verify optional fields default to null

## Files Modified

- `app/Http/Controllers/ExportController.php` - Updated `attendance()` method
- `app/Http/Controllers/ImportController.php` - Updated `export()` method
- `app/Services/ImportService.php` - Updated `normalizeSheetRow()`, `processImport()`, and `validateRow()` methods

## Database Impact

No database schema changes required. The fix works with existing `Attendance` table structure:
- `full_name`: string
- `attendance`: string (nullable)
- `date_time`: datetime (nullable)
- `location`: string (nullable)
- `shift_type`: string (nullable)
- `source_signature`: string (nullable, unique)
- `source_payload`: json (nullable)

## Backward Compatibility

✅ **Fully backward compatible**
- Existing imports without `source_signature` and `source_payload` will continue to work
- These fields are optional and default to null
- No breaking changes to existing functionality
