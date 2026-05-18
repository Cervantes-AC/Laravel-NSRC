# NSRC Attendance Management System (AMS)

Laravel-based attendance management system for NSRC volunteer duty tracking, user administration, auditability, reports, and operational analytics.

## Current Implementation Coverage

| Rubric Area | Implementation |
| --- | --- |
| User Role Management | Admin and Member roles, role middleware, permission map in `config/permissions.php`, active/inactive/suspended/pending/rejected status support, admin account controls, profile avatar upload. |
| Authentication | Laravel Breeze auth, encrypted passwords, remember-me sessions, token password reset, failed-login tracking, 15-minute lockout after 5 failed attempts, enforced password policy: 8+ chars, mixed case, number, symbol. |
| Audit Logging | Event/listener audit trail for login/logout, account changes, role changes, data modification, reports, errors, page visits, suspicious activity metadata, searchable/exportable admin log views, and scheduled 90-day archiving. |
| Dashboard | Admin/member dashboards, metric services, chart-ready API endpoints, activity summaries, responsive Blade/Tailwind layouts. |
| Notifications and Alerts | Admin announcement CRUD for member announcements, notification service, notification center, unread/read/delete actions, warning/critical/reminder/system categories, Server-Sent Events notification refresh, failed-login security checks, CAPTCHA challenge after repeated failures, and storage warning helpers. |
| Backup | Scheduled database, file, and full-system backups, manual backup controller, backup logs, integrity verification hooks, retention display. |
| Import/Export | CSV/Excel-oriented import preview/process/template flow, validation service, duplicate handling support, report and audit exports. |
| Reporting and PDF | Report service, PDF service, report filters, PDF export view with professional layout support, print/download workflows. |
| CRUD Standards | Admin CRUD for duty sessions/personnel/accounts, soft-delete/restore for sessions, audit logging, confirmation-oriented delete flows, policy-based access. |
| Forms and Data Controls | Form requests for server validation, client-friendly Blade errors, pagination/search/filter/sort/bulk-action surfaces across admin tables. |
| Admin User Management | Account approval/rejection/suspension, impersonation, force logout, login history, user activity analytics, bulk actions. |
| Site Settings | Branding, email, security, backup, notification, maintenance/API-style settings via settings service and admin settings UI. |
| Security and Performance | Eloquent/parameterized data access, CSRF protection, custom rate limiting, security headers, secure password hashing, session timeout middleware, and 5-minute session expiration warning UI. |
| UI/UX | Responsive Tailwind UI, role-aware navigation, notification badge area, empty/loading state patterns in views and components. |

## Default Seed Accounts

| Role | Email | Password |
| --- | --- | --- |
| Administrator | `admin@gmail.com` | `Admin@123` |
| Standard User | `member@gmail.com` | `Member@123` |
| Suspended User | `suspended@gmail.com` | `Member@123` |

## Development Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

## Verification

```bash
php artisan test
```

Latest verified result: 76 tests passing and production assets build successfully.

## Remaining Integration Notes

- Email OTP MFA is implemented; SMS delivery requires a configured SMS gateway.
- Server-Sent Events are implemented for notification refresh; production hosting must allow `text/event-stream`.
- Backup cloud/external-drive/email attachment destinations depend on production mail/storage configuration.
