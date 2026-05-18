# Getting Started with NSRC AMS

## System Requirements

- PHP 8.2 or higher
- Laravel 11.x
- MySQL 8.0 or higher
- Modern web browser (Chrome, Firefox, Safari, Edge)

## Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd nsrc_ams
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Configuration
Update your `.env` file with database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nsrc_ams
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations
```bash
php artisan migrate
php artisan db:seed
```

### 6. Build Assets
```bash
npm run build
```

### 7. Start the Application
```bash
php artisan serve
```

The application will be available at `http://127.0.0.1:8000`

## First Login

### Default Admin Account
- **Email**: admin@example.com
- **Password**: password

**Important**: Change the default password immediately after first login.

## Initial Setup

1. **Update Site Settings**: Go to Admin > Settings to configure your organization details
2. **Create Announcements**: Add important announcements for members
3. **Import Personnel**: Use the import feature to add volunteer data
4. **Configure Security**: Set up security policies and backup schedules

## Accessing the System

### For Members
1. Navigate to the login page
2. Register a new account or use provided credentials
3. Access the member dashboard at `/member/dashboard`

### For Administrators
1. Login with admin credentials
2. Access the admin dashboard at `/admin/dashboard`
3. Manage users, sessions, and settings

## Common Tasks

### Time In/Out
Members can log their attendance by:
1. Going to Member Dashboard
2. Clicking "Time In" to start a session
3. Clicking "Time Out" to end a session

### View Performance
Members can view their performance metrics:
1. Go to Member > Performance
2. View attendance records and statistics
3. Check rankings and comparisons

### Generate Reports
Administrators can generate reports:
1. Go to Admin > Reports
2. Select report type and date range
3. Export as CSV or PDF

## Troubleshooting

If you encounter issues during setup, refer to the [Troubleshooting Guide](./TROUBLESHOOTING.md).

## Next Steps

- Read [Features Overview](./FEATURES.md) to understand all capabilities
- Check [User Roles & Permissions](./ROLES_AND_PERMISSIONS.md) for access control
- Review [Security & Compliance](./SECURITY.md) for best practices
