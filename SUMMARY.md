# NSRC Attendance Management System (AMS) - Project Summary

## Overview

**NSRC AMS** is a production-ready Laravel web application designed to track volunteer attendance, manage duty sessions, and provide comprehensive analytics for the National Service Reserve Corps (NSRC). The system automates attendance logging from Google Sheets, calculates work durations, and generates detailed reports with audit trails.

**Tech Stack:**
- Backend: Laravel 12 (PHP 8.2+)
- Frontend: Livewire 4 + Blade + Tailwind CSS 4
- Database: MySQL/SQLite/PostgreSQL
- Build: Vite + Node.js
- External: Google Sheets API, Firebase, DomPDF

---

## Key Features

### 1. **Attendance Tracking**
- Automatic sync from Google Sheets to local database
- Spreadsheet rows mirrored into attendance logs
- Intelligent time pairing (time-in/time-out matching)
- Duration calculation with integrity scoring
- Status tracking: COMPLETE, MISSING_TIMEOUT, INVALID_LOG

### 2. **Role-Based Access Control**
- **Admin Role**: Full system access, user management, reports, settings
- **Member Role**: Personal dashboard, own duty sessions, personal reports
- Policy-based authorization with middleware protection
- User status support: active, inactive, suspended, pending, rejected

### 3. **Dashboard & Analytics**
- Admin dashboard: System-wide statistics and metrics
- Member dashboard: Personal performance and activity
- Real-time activity feeds
- Chart-ready API endpoints
- Volunteer metrics: regular/overtime/undertime/invalid/session counts

### 4. **Reporting System**
- 5 report types: User Activity, Transaction Summary, Audit Trail, System Usage, Custom Reports
- Advanced filtering and scheduling
- Multi-format export: CSV, Excel, PDF, JSON
- Professional PDF generation with branding and signatures

### 5. **Audit Logging**
- Event-driven audit trail for all critical actions
- Categories: SECURITY, REGISTRY, OPERATIONS, SYSTEM
- Login/logout tracking with failed-login monitoring
- 90-day automatic archiving
- Searchable and exportable logs

### 6. **User Management**
- Admin account approval/rejection/suspension workflow
- User impersonation for support
- Force logout capability
- Login history tracking
- User activity analytics
- Bulk actions support

### 7. **Backup System**
- Automated backups: Database, Files, Full System
- Scheduled tasks:
  - Database: Weekly (Monday 2:00 AM)
  - Files: Weekly (Sunday 3:00 AM)
  - Full: Monthly (1st, 4:00 AM)
- Integrity verification
- Manual backup triggers

### 8. **Notifications & Alerts**
- In-app notification center
- Categories: System, Warning, Critical, Reminder
- Admin announcements for members
- Failed login security checks
- Storage capacity warnings
- Server-Sent Events for real-time updates

### 9. **Import/Export**
- CSV/Excel bulk import with validation
- Duplicate detection and handling
- Import preview before processing
- Multi-format export (CSV, Excel, PDF, JSON)
- Template generation

### 10. **Security Features**
- Laravel Breeze authentication
- Encrypted password storage
- Rate limiting (15-minute lockout after 5 failed attempts)
- Password policy: 8+ chars, mixed case, number, symbol
- CSRF/XSS protection
- SQL injection prevention (Eloquent ORM)
- Security headers
- Session timeout (5-minute warning)
- CAPTCHA after repeated failures

### 11. **Data Processing**
- **Name Merging**: Levenshtein-distance fuzzy matching (85% threshold) for duplicate resolution
- **DutyEngine**: Core service for log parsing, time pairing, duration/status calculation
- **MetricsService**: Volunteer performance metrics
- **NameNormalizationService**: Fuzzy name matching and merging

### 12. **Site Settings**
- Configurable branding
- Email configuration
- Security settings
- Backup preferences
- Notification settings
- Maintenance mode

---

## Architecture

```
┌─────────────────────────────────────────┐
│   Frontend (Blade + Livewire 4)         │
│   Tailwind CSS 4 + Alpine.js            │
└──────────────┬──────────────────────────┘
               │
┌──────────────▼──────────────────────────┐
│   Laravel 12 Application                │
│  ┌────────────────────────────────────┐ │
│  │ Controllers → Services → Models    │ │
│  │ Middleware → Policies → Events     │ │
│  └────────────────────────────────────┘ │
└──────────────┬──────────────────────────┘
               │
┌──────────────▼──────────────────────────┐
│   Data Layer                            │
│  ┌────────────────────────────────────┐ │
│  │ MySQL/SQLite/PostgreSQL Database   │ │
│  │ Google Sheets API Integration      │ │
│  └────────────────────────────────────┘ │
└─────────────────────────────────────────┘
```

---

## Core Services

| Service | Purpose |
|---------|---------|
| **DutyEngine** | Log parsing, time pairing, duration calculation, status determination, integrity scoring |
| **MetricsService** | Volunteer metrics: regular/overtime/undertime/invalid/session counts |
| **MySQLAttendanceService** | Fetches attendance data from MySQL source table |
| **NameNormalizationService** | Levenshtein-based fuzzy name matching and merging |
| **BackupService** | Database dump, file archive, full system backup with integrity verification |
| **NotificationService** | Multi-channel notification creation and delivery |
| **AlertService** | Failed login checks, storage capacity monitoring, deletion confirmations |
| **UserManagementService** | User impersonation, force logout, activity analytics |
| **ImportService** | CSV/Excel validation, preview, processing, duplicate detection |
| **ExportService** | CSV, Excel, PDF, JSON data export |
| **PDFService** | DomPDF-based PDF generation with branding |
| **ReportService** | 5 report types with filtering, scheduling, and export |

---

## Database Schema

15 migrations covering:
- Users (authentication, roles, status)
- Duty Sessions (time tracking, duration, status)
- Attendance (raw logs from Google Sheets)
- Audit Logs (event tracking)
- Volunteer Metrics (performance data)
- Notifications (in-app alerts)
- User Preferences (personal settings)
- Backup Logs (backup history)
- Settings (system configuration)
- Conversation History (chat/support)
- Name Merging Log (duplicate resolution)

---

## Role Permissions Matrix

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

---

## Default Test Accounts

| Role | Email | Password |
|------|-------|----------|
| Administrator | `admin@gmail.com` | `Admin@123` |
| Standard User | `member@gmail.com` | `Member@123` |
| Suspended User | `suspended@gmail.com` | `Member@123` |

---

## Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL/SQLite/PostgreSQL

### Installation

```bash
# Clone repository
git clone <repository-url>
cd nsrc-ams

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed

# Build assets
npm run build

# Start development server
php artisan serve
```

### Development Commands

```bash
# Run tests
php artisan test

# Sync Google Sheets attendance
php artisan attendance:sync-google-sheets

# Run backups
php artisan backup:run --type=database
php artisan backup:run --type=files
php artisan backup:run --type=full

# Development server
npm run dev

# Build for production
npm run build
```

---

## Scheduled Tasks

Configured in `routes/console.php`:

| Task | Schedule | Purpose |
|------|----------|---------|
| Google Sheets Sync | Hourly | Mirror spreadsheet data to database |
| Database Backup | Weekly (Mon 2:00 AM) | Backup database |
| File Backup | Weekly (Sun 3:00 AM) | Backup uploaded files |
| Full Backup | Monthly (1st, 4:00 AM) | Complete system backup |

---

## Recent Fixes

### Time In/Time Out Logic Fix
**Problem**: Records with only time-in (no time-out) were not properly recorded with duration.

**Solution**: Updated `DutyEngine.php` to:
1. Calculate duration from time-in to end of day (23:59:59) when time-out is missing
2. Increased integrity score for MISSING_TIMEOUT from 60.0 to 70.0
3. Consistently mark incomplete sessions as MISSING_TIMEOUT with valid duration

**Impact**: Complete data recording with proper duration calculations for all sessions.

---

## Environment Configuration

Key `.env` variables (see `.env.example` for all options):

```env
APP_NAME=NSRC-AMS
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=nsrc_ams
# DB_USERNAME=root
# DB_PASSWORD=

GOOGLE_SHEETS_API_KEY=your_key

MAIL_MAILER=log
# Configure for production email delivery
```

---

## Project Structure

```
nsrc-ams/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   ├── Services/
│   ├── Policies/
│   └── Events/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php
│   ├── api.php
│   └── console.php
├── config/
├── tests/
├── public/
└── storage/
```

---

## Testing

The project includes comprehensive test coverage:

```bash
php artisan test
```

**Latest Status**: 76 tests passing, production assets build successfully.

---

## Security Considerations

- ✅ Eloquent ORM prevents SQL injection
- ✅ CSRF/XSS protection enabled
- ✅ Password hashing with bcrypt
- ✅ Rate limiting on authentication
- ✅ Session timeout with warnings
- ✅ Audit logging for all critical actions
- ✅ Role-based access control
- ✅ Security headers configured
- ✅ Failed login tracking and CAPTCHA

---

## Integration Notes

- **Email OTP MFA**: Implemented; SMS requires configured SMS gateway
- **Server-Sent Events**: Implemented for notifications; production hosting must allow `text/event-stream`
- **Cloud Backups**: Depend on production mail/storage configuration
- **Google Sheets**: Requires valid API key in `.env`

---

## Performance Features

- Pagination across all data tables
- Column sorting and filtering
- Global search functionality
- Bulk actions for efficiency
- Optimized database queries with Eloquent
- Caching strategies for metrics
- Lazy loading for large datasets

---

## Support & Maintenance

- Comprehensive audit logging for troubleshooting
- Admin impersonation for support scenarios
- Detailed error logging
- Backup and restore capabilities
- System health monitoring via alerts

---

## License

Apache 2.0

---

## Status

**Production Ready** ✅

- All core features implemented
- 76 tests passing
- Security hardened
- Performance optimized
- Documentation complete
