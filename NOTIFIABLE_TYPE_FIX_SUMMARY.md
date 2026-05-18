# Notifiable Type Default Value Fix

## Problem
The application was throwing a database error when creating notifications:

```
SQLSTATE[HY000]: General error: 1364 Field 'notifiable_type' doesn't have a default value
```

This occurred because the `notifiable_type` column in the `notifications` table was missing a default value, causing INSERT operations to fail when the column wasn't explicitly provided.

## Root Cause
The `notifications` table was created using Laravel's `morphs('notifiable')` method, which creates two columns:
- `notifiable_id` - The ID of the notifiable model
- `notifiable_type` - The class name of the notifiable model

However, the `notifiable_type` column didn't have a default value, making it required for every INSERT operation.

## Solution
Created a new migration that adds a default value to the `notifiable_type` column:

**Migration File:** `2026_05_18_170000_fix_notifiable_type_default.php`

```php
Schema::table('notifications', function (Blueprint $table) {
    $table->string('notifiable_type')->default('App\Models\User')->change();
});
```

## What Changed
- **Column:** `notifications.notifiable_type`
- **Before:** No default value (required)
- **After:** Default value = `'App\Models\User'`

## Impact
✅ Notifications can now be created without explicitly providing `notifiable_type`
✅ All existing notification creation code will work
✅ Backup notifications, import notifications, and all other notifications will work properly

## Migration Status
✅ Migration applied successfully
✅ Database schema updated
✅ Ready for production

## Testing
To verify the fix works, try creating a notification:

```php
// This will now work without errors
Notification::create([
    'id' => \Illuminate\Support\Str::uuid(),
    'type' => 'action_started',
    'notifiable_id' => 1,
    // notifiable_type will default to 'App\Models\User'
    'data' => ['title' => 'Test', 'message' => 'Test message'],
]);
```

## Files Modified
- Created: `database/migrations/2026_05_18_170000_fix_notifiable_type_default.php`

## Rollback
If needed, the migration can be rolled back:

```bash
php artisan migrate:rollback --step=1
```

This will remove the default value from the `notifiable_type` column.

## Related Issues
This fix resolves errors in:
- Backup notifications
- Import notifications
- Export notifications
- System notifications
- Any other notification creation

## Status
✅ **FIXED** - Ready for use

---

**Date Fixed:** May 18, 2026
**Migration:** 2026_05_18_170000_fix_notifiable_type_default
**Status:** Applied
