# Admin Features Guide

## Overview

This guide covers all administrative features in the NSRC AMS.

## Admin Dashboard

### Accessing Admin Dashboard
1. Login with admin account
2. You will be automatically redirected to `/admin/dashboard`
3. Or click "Admin Dashboard" in navigation

### Dashboard Components

**Key Metrics**
- Total Members: Active member count
- Total Sessions: Lifetime sessions
- Total Hours: Total volunteer hours
- Active Sessions: Currently ongoing sessions
- Pending Accounts: Awaiting approval
- Suspended Accounts: Temporarily disabled

**Recent Activity**
- Latest user registrations
- Recent sessions
- System activities
- Announcements

**Quick Actions**
- Approve Accounts
- Create Announcement
- View Reports
- Manage Sessions

## User Account Management

### Accessing Account Management
1. Go to Admin > Accounts
2. View all user accounts

### Account List Features
- Search by name or email
- Filter by status (active, pending, suspended, rejected)
- Sort by registration date, name, or status
- Bulk actions on multiple accounts

### Account Actions

**Approve Account**
1. Find pending account
2. Click "Approve"
3. Account status changes to 'active'
4. User can now login

**Reject Account**
1. Find pending account
2. Click "Reject"
3. Account status changes to 'rejected'
4. User cannot login

**Suspend Account**
1. Find active account
2. Click "Suspend"
3. Account status changes to 'suspended'
4. User cannot login
5. Can be reactivated later

**Bulk Actions**
1. Select multiple accounts
2. Choose action (approve, reject, suspend)
3. Click "Apply"
4. Action applied to all selected accounts

### User Impersonation
1. Find user account
2. Click "Impersonate"
3. You are now logged in as that user
4. Can see what user sees
5. Click "Stop Impersonating" to return to admin account

### Force Logout
1. Find user account
2. Click "Force Logout"
3. User's current session is terminated
4. User must login again

### Login History
1. Find user account
2. Click "View History"
3. See all login attempts
4. View date, time, and IP address

## Personnel Management

### Accessing Personnel
1. Go to Admin > Personnel
2. View all personnel records

### Personnel List Features
- Search by name
- Filter by status
- Sort by various fields
- View detailed records

### Creating Personnel Record
1. Click "Add Personnel"
2. Fill in required information:
   - Full Name
   - Email
   - Contact Number
   - School/College
   - Major
   - Year Level
   - Competency
3. Click "Save"

### Updating Personnel Record
1. Find personnel record
2. Click "Edit"
3. Update information
4. Click "Save"

### Deleting Personnel Record
1. Find personnel record
2. Click "Delete"
3. Confirm deletion
4. Record is removed

### Personnel Information
- Full name and contact details
- Educational background
- Competency and skills
- Address information
- Emergency contact

## Session Management

### Accessing Sessions
1. Go to Admin > Sessions
2. View all duty sessions

### Session List Features
- Search by volunteer name
- Filter by date range
- Filter by status
- Sort by various fields
- View session details

### Session Details
- Volunteer name
- Session date and time
- Duration (hours and minutes)
- Time in and time out
- Session status

### Editing Sessions
1. Find session
2. Click "Edit"
3. Update time in/out or duration
4. Click "Save"

### Deleting Sessions
1. Find session
2. Click "Delete"
3. Confirm deletion
4. Session is removed

### Restoring Sessions
1. Go to Admin > Sessions
2. Find deleted session
3. Click "Restore"
4. Session is restored

### Session Sync
1. Go to Admin > Attendance
2. Click "Sync"
3. System synchronizes attendance data
4. Updates are applied

## Announcements

### Accessing Announcements
1. Go to Admin > Announcements
2. View all announcements

### Creating Announcement
1. Click "Create Announcement"
2. Fill in details:
   - Title
   - Content
   - Publish Date
   - Visibility (members, admins, all)
3. Click "Publish"

### Editing Announcement
1. Find announcement
2. Click "Edit"
3. Update content
4. Click "Save"

### Publishing/Unpublishing
1. Find announcement
2. Toggle "Published" status
3. Changes take effect immediately

### Deleting Announcement
1. Find announcement
2. Click "Delete"
3. Confirm deletion
4. Announcement is removed

## Reports & Analytics

### Accessing Reports
1. Go to Admin > Reports
2. Or go to Reports page

### Report Types

**Attendance Report**
- Volunteer attendance records
- Date range filtering
- Export as CSV or PDF

**Session Report**
- Duty session details
- Duration analysis
- Volunteer breakdown

**Performance Report**
- Volunteer rankings
- Performance metrics
- Comparative analysis

**Personnel Report**
- Volunteer information
- Contact details
- Educational background

### Generating Reports
1. Select report type
2. Set date range
3. Choose filters (optional)
4. Click "Generate"
5. View report

### Exporting Reports
1. Generate report
2. Click "Export as CSV" or "Export as PDF"
3. File downloads to your computer

### AI-Powered Insights
1. Go to Reports > Insights
2. Select data range
3. Click "Generate Insights"
4. AI analyzes data and provides recommendations

### Switching AI Provider
1. Go to Reports
2. Click "Switch Provider"
3. Select provider (Google Gemini, OpenAI, Anthropic)
4. Enter API key
5. Click "Save"

## Data Management

### Import Data

**Accessing Import**
1. Go to Admin > Import
2. Click "Choose File"
3. Select CSV file

**Import Process**
1. Upload CSV file
2. Preview data
3. Validate data
4. Confirm import
5. Data is imported

**Import Template**
1. Go to Admin > Import
2. Click "Download Template"
3. Use template for data format
4. Fill in your data
5. Upload file

**Supported Formats**
- CSV (Comma-separated values)
- Excel (.xlsx)

### Export Data

**Accessing Export**
1. Go to Admin > Export
2. Select data type

**Export Options**
- Export Accounts: All user accounts
- Export Sessions: All duty sessions
- Export Personnel: All personnel records
- Export Attendance: All attendance records

**Export Formats**
- CSV
- Excel (.xlsx)
- PDF (for reports)

## Audit Logging

### Accessing Audit Logs
1. Go to Admin > Audit Logs
2. View all system activities

### Audit Log Information
- User who performed action
- Action type
- Resource affected
- Date and time
- IP address
- Changes made

### Filtering Audit Logs
- Filter by user
- Filter by action type
- Filter by date range
- Search by resource

### Exporting Audit Logs
1. Go to Audit Logs
2. Click "Export"
3. Choose format (CSV or PDF)
4. File downloads

## Backup Management

### Accessing Backups
1. Go to Admin > Backup
2. View backup history

### Running Backup
1. Click "Run Backup"
2. System creates backup
3. Backup is stored securely
4. Notification sent

### Backup Information
- Backup date and time
- File size
- Status (completed, failed)
- Download link

### Downloading Backup
1. Find backup
2. Click "Download"
3. Backup file downloads

### Email Notifications
1. Go to Backup
2. Toggle "Email Notifications"
3. Receive email when backup completes

### Backup Schedule
- Automatic daily backups
- Configurable in settings
- Retention policy applied

## Settings & Configuration

### Accessing Settings
1. Go to Admin > Settings
2. View configuration options

### Site Settings
- Organization Name
- Organization Logo
- Contact Email
- Contact Phone
- Address
- Website URL

### Security Settings
- Password Policy
- Session Timeout
- Failed Login Attempts
- Account Lockout Duration
- 2FA Requirements

### Email Settings
- SMTP Server
- Email Address
- Email Password
- Email Templates

### Backup Settings
- Backup Frequency
- Retention Days
- Email Notifications
- Backup Location

### AI Provider Settings
- Default Provider
- API Keys
- Model Selection

## Best Practices

1. **Regular Backups**: Run backups regularly
2. **Monitor Audit Logs**: Review activities regularly
3. **Approve Accounts Promptly**: Don't leave pending accounts
4. **Update Announcements**: Keep members informed
5. **Review Reports**: Monitor system usage
6. **Secure Credentials**: Protect API keys and passwords
7. **Use 2FA**: Enable 2FA for admin accounts
8. **Document Changes**: Keep records of configuration changes

## Troubleshooting

### Cannot Approve Account
- Verify account status is 'pending'
- Check if you have admin permissions
- Try refreshing page

### Import Fails
- Check CSV format
- Verify all required fields
- Check for duplicate emails
- See import template for correct format

### Report Generation Slow
- Try smaller date range
- Check system resources
- Try again later
- Contact support if issue persists

For more help, see [Troubleshooting Guide](./TROUBLESHOOTING.md).
