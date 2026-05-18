# Data Management Guide

## Overview

This guide covers data import, export, and management features in the NSRC AMS.

## Data Import

### Accessing Import
1. Go to Admin > Import
2. Select CSV file to import

### Import Process

**Step 1: Upload File**
- Click "Choose File"
- Select CSV file from your computer
- Click "Upload"

**Step 2: Preview Data**
- Review data preview
- Check for any obvious errors
- Verify column mappings

**Step 3: Validate Data**
- System validates data format
- Checks for required fields
- Identifies duplicate entries
- Reports any errors

**Step 4: Confirm Import**
- Review validation results
- Click "Import" to proceed
- Data is imported into system

### Import Template

Download the import template to ensure correct format:

1. Go to Admin > Import
2. Click "Download Template"
3. Use template as guide for your data

### CSV Format

**Required Columns:**
- name (Full Name)
- email (Email Address)
- password (Initial Password)

**Optional Columns:**
- full_name
- school_id
- nsrc_serial_number
- birthdate
- gender
- college
- major
- year_level
- primary_competency
- personal_contact_number
- current_address
- home_address
- emergency_contact_person
- emergency_contact_number

### Example CSV

```csv
name,email,password,school_id,college,major
John Doe,john@example.com,password123,S001,Engineering,Computer Science
Jane Smith,jane@example.com,password456,S002,Science,Biology
```

### Import Validation

The system validates:
- Email format and uniqueness
- Required fields presence
- Data type correctness
- Duplicate entries
- Password strength

### Handling Import Errors

If import fails:
1. Review error messages
2. Correct data in CSV file
3. Re-upload corrected file
4. Try import again

## Data Export

### Accessing Export
1. Go to Admin > Export
2. Select data type to export

### Export Options

**Export Accounts**
- All user accounts
- Includes: name, email, role, status, created date
- Formats: CSV, Excel

**Export Sessions**
- All duty sessions
- Includes: volunteer name, date, time in/out, duration
- Formats: CSV, Excel

**Export Personnel**
- All personnel records
- Includes: name, contact, education, competency
- Formats: CSV, Excel

**Export Attendance**
- All attendance records
- Includes: name, date, time, duration
- Formats: CSV, Excel

### Export Formats

**CSV (Comma-Separated Values)**
- Universal format
- Compatible with Excel, Google Sheets
- Plain text file

**Excel (.xlsx)**
- Microsoft Excel format
- Formatted with headers
- Multiple sheets support

**PDF**
- Formatted report
- Print-friendly
- Read-only format

### Exporting Data

1. Go to Admin > Export
2. Select data type
3. Choose format (CSV, Excel, PDF)
4. Click "Export"
5. File downloads to your computer

### Filtering Exports

Some exports support filtering:
- Date range
- Status
- User/volunteer
- Department/college

## Data Backup

### Automatic Backups

The system performs automatic daily backups:
- Scheduled time: 2:00 AM
- Retention: 30 days
- Location: Secure server storage

### Manual Backups

To create manual backup:

1. Go to Admin > Backup
2. Click "Run Backup"
3. System creates backup
4. Backup appears in list

### Backup Information

Each backup includes:
- Database dump
- File uploads
- Configuration files
- Timestamp

### Downloading Backups

1. Go to Admin > Backup
2. Find backup in list
3. Click "Download"
4. Backup file downloads

### Backup Restoration

To restore from backup:

1. Contact system administrator
2. Provide backup date/ID
3. Admin restores from backup
4. System is restored to backup point

**Warning**: Restoration will overwrite current data.

## Data Synchronization

### Attendance Sync

To synchronize attendance data:

1. Go to Admin > Attendance
2. Click "Sync"
3. System synchronizes data
4. Updates are applied

### Session Sync

To synchronize session data:

1. Go to Admin > Sessions
2. Click "Sync"
3. System synchronizes data
4. Updates are applied

## Data Privacy

### Data Retention

- Active user data: Retained indefinitely
- Deleted user data: Retained 90 days
- Audit logs: Retained 1 year
- Backups: Retained 30 days

### Data Deletion

To delete user data:

1. Go to Admin > Accounts
2. Find user
3. Click "Delete"
4. Confirm deletion
5. User data is marked for deletion
6. Data is permanently deleted after 90 days

### GDPR Compliance

The system supports GDPR requirements:
- Data export functionality
- Data deletion requests
- Audit logging
- Consent management

## Data Integrity

### Validation Rules

The system enforces:
- Email uniqueness
- Required field validation
- Data type checking
- Referential integrity

### Duplicate Detection

The system detects:
- Duplicate emails
- Duplicate names
- Duplicate records
- Conflicting data

### Data Consistency

The system maintains:
- Referential integrity
- Transaction consistency
- Data synchronization
- Backup consistency

## Best Practices

1. **Regular Backups**: Run backups regularly
2. **Validate Data**: Always validate before import
3. **Test Imports**: Test with sample data first
4. **Document Changes**: Keep records of data changes
5. **Secure Exports**: Protect exported files
6. **Monitor Integrity**: Check data consistency regularly
7. **Archive Old Data**: Archive old data periodically
8. **Verify Restores**: Test backup restoration process

## Troubleshooting

### Import Fails
- Check CSV format
- Verify required fields
- Check for duplicate emails
- Review error messages

### Export Takes Too Long
- Try smaller date range
- Export one data type at a time
- Check system resources
- Try again later

### Backup Fails
- Check disk space
- Verify database connection
- Check file permissions
- Review error logs

For more help, see [Troubleshooting Guide](./TROUBLESHOOTING.md).
