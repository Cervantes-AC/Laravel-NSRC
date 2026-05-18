# User Roles & Permissions

## Role Overview

The NSRC AMS uses a two-tier role-based access control system:

### 1. Member Role
Members are volunteers who use the system to log their attendance and view their performance.

**Permissions:**
- View personal dashboard
- Log time in/out
- View personal attendance records
- View personal performance metrics
- View rankings
- View announcements
- Update personal profile
- Enable/disable 2FA
- View system rules and guidelines

**Access Level:** Limited to personal data and public information

### 2. Admin Role
Administrators manage the system, users, and generate reports.

**Permissions:**
- All member permissions
- View admin dashboard
- Manage user accounts (approve, reject, suspend)
- Manage personnel records
- Manage duty sessions
- Create and manage announcements
- Generate and export reports
- Import and export data
- View audit logs
- Manage backups
- Configure system settings
- Impersonate users
- Force logout users
- View user login history
- Switch AI providers
- Access analytics

**Access Level:** Full system access

## Account Status

### Active
- User can login and access the system
- Full permissions based on role

### Pending
- User cannot access the system
- Awaiting admin approval
- Created during registration

### Suspended
- User cannot login
- Account is temporarily disabled
- Can be reactivated by admin

### Rejected
- User cannot login
- Registration was rejected
- Cannot be reactivated

### Inactive
- User cannot login
- Account is disabled
- Can be reactivated by admin

## Permission Hierarchy

```
System
├── Public Routes
│   ├── Landing Page
│   ├── Login
│   └── Registration
├── Authenticated Routes
│   ├── Dashboard (role-based redirect)
│   ├── Profile Management
│   ├── Notifications
│   ├── Analytics
│   └── Rankings
├── Member Routes
│   ├── Member Dashboard
│   ├── Attendance
│   ├── Performance
│   ├── How-to Guide
│   └── Rules
└── Admin Routes
    ├── Admin Dashboard
    ├── Personnel Management
    ├── Session Management
    ├── Account Management
    ├── Announcements
    ├── Reports
    ├── Import/Export
    ├── Audit Logs
    ├── Backups
    └── Settings
```

## Middleware Protection

### Authentication Middleware (`auth`)
- Requires user to be logged in
- Redirects to login if not authenticated

### Role Middleware (`role:admin|member`)
- Checks user role
- Returns 403 Unauthorized if role doesn't match
- Requires user status to be 'active'

### Throttle Middleware (`throttle.custom`)
- Rate limits requests
- Prevents abuse and brute force attacks
- Configurable per route

## Permission Checks

### User Status Check
```php
if ($user->status !== 'active') {
    abort(403, 'Unauthorized action.');
}
```

### Role Check
```php
if (!in_array($user->role, $allowedRoles)) {
    abort(403, 'Unauthorized action.');
}
```

## API Permissions

### Public API Routes
- None (all API routes require authentication)

### Authenticated API Routes
- Dashboard data
- Sessions
- Notifications
- Analytics
- Rankings
- Personnel
- Accounts
- Audit logs

### Admin-Only API Routes
- Report generation
- Report export
- AI provider switching
- API key management

### Member-Only API Routes
- Member attendance
- Time in/out
- Member-specific data

## Best Practices

1. **Always verify user role and status** before granting access
2. **Use middleware** for route protection
3. **Log all admin actions** for audit purposes
4. **Regularly review permissions** for security
5. **Implement principle of least privilege** - grant minimum necessary permissions
6. **Use 2FA** for admin accounts
7. **Monitor login history** for suspicious activity
8. **Suspend accounts** that show signs of compromise

## Changing User Roles

Only administrators can change user roles. To change a user's role:

1. Go to Admin > Accounts
2. Find the user
3. Click on the user to view details
4. Update the role
5. Save changes

**Note:** Role changes are logged in the audit log.

## Troubleshooting Permissions

### "403 Unauthorized" Error
- Check user status is 'active'
- Verify user role matches route requirements
- Check if account is suspended or rejected

### Cannot Access Admin Panel
- Verify user role is 'admin'
- Check user status is 'active'
- Clear browser cache and cookies

### API Access Denied
- Verify authentication token is valid
- Check user role for API endpoint
- Verify user status is 'active'

For more help, see [Troubleshooting Guide](./TROUBLESHOOTING.md).
