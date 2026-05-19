# NSRC Attendance Management System (AMS)

A Laravel + React attendance management system for NSRC volunteer duty tracking, user administration, audit logging, reporting, and operational analytics.

## Features

### Authentication & Authorization
- User registration and secure login
- Two-factor authentication (2FA) with recovery codes
- Email verification
- Role-based access control (Admin/Member)
- Account status: Active, Pending, Suspended, Rejected

### Attendance Management
- Real-time time-in/time-out logging
- Duty session tracking with automatic duration calculation
- Historical attendance records
- Attendance data sync

### Member Dashboard
- Personal attendance tracking and session history
- Performance metrics and volunteer rankings
- How-to guide and system rules

### Admin Dashboard
- System overview with key metrics and statistics
- User and session analytics
- Recent activity monitoring

### User Management
- Account approval/rejection/suspension
- Bulk actions on accounts
- User impersonation and force logout
- Login history tracking

### Personnel Management
- Volunteer information records
- Profile and contact management
- Educational background and competency tracking

### Announcements
- Create, publish/unpublish announcements
- Member notification on new announcements

### Reporting & Analytics
- Custom report generation (CSV/PDF export)
- AI-powered insights with multi-provider support
- Date range filtering and data visualization

### Data Management
- Bulk import with preview, validation, and templates
- Export accounts, sessions, personnel, attendance
- Automated backup system

### Audit & Compliance
- Complete audit trail of system activities
- Searchable/exportable audit logs
- 90-day scheduled archiving

### Security
- Strong password policy and account lockout
- Rate limiting and CSRF protection
- Session management with timeout warnings

### AI Chatbot
- Multi-provider AI chatbot (Groq, OpenRouter)
- Conversation history
- Streaming responses

## Technologies

- **Backend:** Laravel 12.x, PHP 8.2+
- **Frontend:** React, Tailwind CSS, Vite
- **Database:** MySQL / SQLite
- **Cache/Session:** Redis
- **Authentication:** Laravel Breeze with 2FA
- **AI Providers:** Groq, OpenRouter
- **PDF:** Laravel DomPDF

## Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

## Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@gmail.com | Admin@123 |
| Member | member@gmail.com | Member@123 |

## Testing

```bash
php artisan test
```

## Database Schema

- **users** - User accounts with roles and 2FA
- **duty_sessions** - Volunteer work sessions
- **attendance** - Attendance records
- **audit_logs** - System activity trail
- **volunteer_metrics** - Performance statistics
- **notifications** - System notifications
- **announcements** - Admin announcements
- **settings** - System configuration
- **backup_logs** - Backup operation history
- **conversation_history** - Chatbot conversations
- **chatbot_messages** - AI chatbot messages
- **user_preferences** - Individual user settings
- **api_keys** - API authentication keys
- **name_merging_logs** - Name merge operation logs
