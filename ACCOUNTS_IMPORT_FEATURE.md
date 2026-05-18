# Accounts Import Feature

## Overview
Added support for importing user accounts directly into the system. This complements the existing "Personnel" and "Duty Sessions" import types.

## Changes Made

### 1. **Updated Import View** (`resources/views/admin/import/index.blade.php`)
- Added "Accounts" option to the import type dropdown
- Positioned between "Personnel" and "Duty Sessions" for logical flow
- Users can now select "Accounts" when uploading a file

### 2. **Updated Import Request Validation** (`app/Http/Requests/ImportRequest.php`)
- Modified validation rules to accept "accounts" as a valid import type
- Updated rule: `'import_type' => ['required', 'in:personnel,accounts,sessions']`

### 3. **Updated Import Controller** (`app/Http/Controllers/ImportController.php`)

#### Modified `export()` method:
- Added "accounts" export type
- Exports all users with fields: `id, name, full_name, email, role, status, two_factor_enabled, created_at`
- Route: `admin.export.accounts`

#### Modified `process()` method:
- Added routing for accounts import type
- Calls new `processAccountsImport()` method when import_type is "accounts"

#### New `processAccountsImport()` method:
- Processes CSV/XLSX files containing account data
- **Required fields**: `email`, `full_name`
- **Optional fields**: `name`, `role` (defaults to 'member'), `status` (defaults to 'active')
- **Behavior**:
  - Creates new accounts if email doesn't exist
  - Updates existing accounts if email matches (updates name, full_name, role, status)
  - Generates random secure password for new accounts
  - Sets email_verified_at to current timestamp
  - Tracks success, skipped (updated), and failed counts
  - Returns detailed feedback message

## Import File Format

### CSV/XLSX Headers Required:
```
email, full_name, name, role, status
```

### Example Data:
```
john.doe@example.com, John Doe, John, admin, active
jane.smith@example.com, Jane Smith, Jane, member, active
bob.wilson@example.com, Bob Wilson, Bob, member, inactive
```

### Field Descriptions:
- **email** (required): User's email address (must be unique)
- **full_name** (required): User's full name
- **name** (optional): Short name (defaults to full_name if not provided)
- **role** (optional): User role - 'admin' or 'member' (defaults to 'member')
- **status** (optional): Account status - 'active', 'inactive', 'suspended', 'pending', 'rejected' (defaults to 'active')

## Usage

### To Import Accounts:
1. Navigate to Admin → Import Data
2. Select "Accounts" from the Import Type dropdown
3. Upload a CSV or XLSX file with account data
4. Click "Import"
5. System will create new accounts and update existing ones
6. View results with success/skipped/failed counts

### To Export Accounts:
1. Navigate to Admin → Import Data
2. In the "Export Data" section, click "Export Accounts"
3. CSV file downloads with all user accounts

## Features

✅ **Duplicate Handling**: Existing accounts (by email) are updated instead of skipped
✅ **Flexible Fields**: Only email and full_name are required
✅ **Role Management**: Can set admin or member roles during import
✅ **Status Control**: Can set account status (active, inactive, suspended, etc.)
✅ **Secure Passwords**: Random 32-character passwords generated for new accounts
✅ **Email Verification**: All imported accounts are marked as email_verified_at
✅ **Error Tracking**: Detailed error messages for each failed row
✅ **Feedback**: Clear success/skipped/failed counts in response message

## Example Workflow

### Scenario: Bulk Account Creation
1. Export existing accounts to get the format
2. Add new account rows to the CSV
3. Import the file
4. New accounts are created, existing ones are updated
5. All accounts are ready to use immediately

### Scenario: Update Account Roles
1. Export accounts
2. Modify the role column for specific users
3. Import the file
4. Roles are updated for matching email addresses

## Error Handling

- **Missing email**: Row fails with "Missing email" error
- **Missing full_name**: Row fails with "Missing full_name" error
- **Invalid role/status**: Uses defaults if invalid values provided
- **Database errors**: Caught and reported with row number and error message

## Database Impact

No schema changes required. Uses existing `users` table with columns:
- `id`, `name`, `full_name`, `email`, `password`, `role`, `status`, `email_verified_at`, `two_factor_enabled`, etc.

## Testing Recommendations

1. **Test Case 1: Create New Accounts**
   - Upload file with 3 new email addresses
   - Verify all 3 accounts created successfully

2. **Test Case 2: Update Existing Accounts**
   - Upload file with existing email but different role/status
   - Verify accounts updated, not duplicated

3. **Test Case 3: Mixed Create and Update**
   - Upload file with 2 new and 2 existing emails
   - Verify 2 created, 2 updated

4. **Test Case 4: Error Handling**
   - Upload file with missing email/full_name
   - Verify errors reported with row numbers

5. **Test Case 5: Export and Re-import**
   - Export accounts
   - Re-import the same file
   - Verify all accounts updated (no duplicates)

## Files Modified

1. `resources/views/admin/import/index.blade.php` - Added "Accounts" dropdown option
2. `app/Http/Requests/ImportRequest.php` - Updated validation rules
3. `app/Http/Controllers/ImportController.php` - Added export and import logic

## Backward Compatibility

✅ All existing import types (personnel, sessions) remain unchanged
✅ No breaking changes to existing functionality
✅ New feature is purely additive
