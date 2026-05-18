<p align="center">
    <img src="https://img.shields.io/badge/Laravel-12-red?logo=laravel" alt="Laravel 12">
    <img src="https://img.shields.io/badge/Livewire-4-purple?logo=livewire" alt="Livewire 4">
    <img src="https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss" alt="Tailwind CSS 4">
    <img src="https://img.shields.io/badge/PHP-8.2-777BB4?logo=php" alt="PHP 8.2">
    <img src="https://img.shields.io/badge/license-Apache%202.0-blue" alt="License">
</p>

# NSRC Attendance Management System

A production-ready Laravel web application for tracking volunteer attendance, managing duty sessions, and analyzing performance for the National Service Reserve Corps (NSRC).

## Features

- **Spreadsheet-Based Attendance Tracking** — Google Spreadsheet rows are mirrored into attendance logs, then paired into attendance summaries with duration calculation and integrity scoring
- **Role-Based Access** — Admin and Member roles with distinct permissions and data visibility via Laravel Policies
- **Dashboard & Analytics** — Livewire-powered dashboards with stats, charts, and real-time activity feeds
- **Reporting** — User activity, transaction summary, audit trail, system usage, and custom report builder
- **PDF Generation** — Professional PDF output via DomPDF with branding and signature placeholders
- **Import/Export** — CSV/Excel bulk import with validation, CSV/Excel/PDF/JSON export
- **Audit Logging** — Complete event-driven audit trail with SECURITY, REGISTRY, OPERATIONS, SYSTEM categories
- **Notifications** — In-app notification center with system, warning, critical, and reminder alerts
- **Backup System** — Automated database, file, and full system backups with integrity verification
- **User Management** — Full admin user management with impersonation, force logout, login history
- **Site Settings** — Configurable branding, email, security, backup, and notification settings
- **Warning & Alert System** — Failed login monitoring, storage capacity warnings, deletion confirmations
- **Advanced Data Controls** — Pagination, column sorting, filtering, bulk actions, global search
- **Form Validation** — Client + server validation with inline feedback and accessible error messages
- **Security** — Rate limiting, SQL injection prevention (Eloquent), XSS/CSRF protection, security headers
- **Name Merging** — Levenshtein-distance fuzzy matching (85% threshold) for duplicate name resolution
- **Google Sheets Sync** — Automatically import attendance logs from Google Sheets

## Architecture

```
Blade + Livewire 4 (Frontend)
    |
Laravel 12 (Backend)
    |-- Controllers -> Services -> Models
    |-- Middleware -> Policies -> Events/Listeners
    |
Database (MySQL/SQLite/PostgreSQL) + Google Sheets API
```

### Key Services

| Service | Purpose |
|---------|---------|
| `DutyEngine` | Core processing: log parsing, time pairing, duration/status calculation |
| `MetricsService` | Volunteer metrics: regular/overtime/undertime/invalid/session counts |
| `MySQLAttendanceService` | Fetches attendance data from MySQL source table |
| `NameNormalizationService` | Levenshtein-based fuzzy name matching and merging |
| `BackupService` | Database dump, file archive, full system backup with integrity check |
| `NotificationService` | Multi-channel notification creation and delivery |
| `AlertService` | Failed login checks, storage capacity monitoring, deletion confirmations |
| `UserManagementService` | User impersonation, force logout, activity analytics |
| `ImportService` | CSV/Excel validation, preview, processing, duplicate detection |
| `ExportService` | CSV, Excel, PDF, JSON data export |
| `PDFService` | DomPDF-based PDF generation with branding |
| `ReportService` | 5 report types with filtering, scheduling, and export |

## Quick Start

```bash
# Requirements: PHP 8.2+, Composer, Node.js 18+, MySQL/SQLite

git clone <repository-url>
cd nsrc-ams
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

**Default test accounts:**

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@gmail.com` | `Admin@123` |
| Member | `member@gmail.com` | `Member@123` |

## Environment

Key `.env` variables — see `.env.example` for all options:

```
APP_NAME=NSRC-AMS
DB_CONNECTION=sqlite  # or mysql
GOOGLE_SHEETS_API_KEY=your_key
MAIL_MAILER=log       # configure for production
```

## Role Permissions

| Feature | Admin | Member |
|---------|-------|--------|
| Dashboard | System-wide stats | Personal stats only |
| Personnel | Full CRUD | View own profile |
| Duty Sessions | Full CRUD + restore | View own sessions |
| Accounts | Manage all | N/A |
| Reports | All types + insights | Personal reports |
| Analytics | Team analytics | Personal analytics |
| Audit Logs | Full access | N/A |
| Settings | System settings | Personal preferences |
| Backup | Run + download | N/A |

## Commands

```bash
# Sync Google Sheets attendance.
# A full sync replaces the local attendance mirror so the app reflects the spreadsheet.
php artisan attendance:sync-google-sheets

# Run backup (database/files/full)
php artisan backup:run --type=database
php artisan backup:run --type=files
php artisan backup:run --type=full
```

Scheduled tasks in `routes/console.php`:
- Google Sheets sync: hourly
- Database backup: weekly (Mon 2:00 AM)
- File backup: weekly (Sun 3:00 AM)
- Full backup: monthly (1st, 4:00 AM)

## Tests

```bash
php artisan test
```

## Database

15 migrations covering: users, duty_sessions, attendance, audit_logs, volunteer_metrics, notifications, user_preferences, backup_logs, settings, conversation_history, name_merging_log.

## License

Apache 2.0
