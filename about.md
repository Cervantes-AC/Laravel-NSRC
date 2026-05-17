# NSRC Attendance Management System (AMS)

An attendance management system for the National Service Reserve Corps (NSRC), built with Laravel.

**Framework:** Laravel 12  
**Auth:** Laravel Breeze (Blade with Alpine.js)  
**Status:** Under Development  

---

## Current State

This project is a **fresh Laravel 12 installation** with Breeze authentication scaffolding. Development is ongoing to build out the full attendance management features.

### What's Implemented

- Laravel 12 with Breeze auth (Blade + Alpine.js)
- User registration, login, password reset, email verification
- Dashboard stub (basic "You're logged in!" view)
- User profile editing
- Default migrations (users, cache, jobs tables)

### What's Planned

- Attendance tracking and duty session management
- Role-based access control (Admin/Member)
- Dashboard with analytics and charts
- Personnel management
- Reporting and data export
- And more features as development progresses

---

## Tech Stack

- **Backend:** Laravel 12
- **Frontend:** Blade + Alpine.js + Tailwind CSS
- **Auth:** Laravel Breeze
- **Database:** SQLite / MySQL (configurable)

---

## Getting Started

```bash
composer install
npm install
npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
npm run dev
```
│   └── Listeners/              # Event listeners
│       ├── LogUserLogin.php
│       └── ...
├── resources/
│   ├── views/
│   │   ├── layouts/            # Layout templates
│   │   ├── components/         # Blade components
│   │   ├── dashboard.blade.php
│   │   ├── reports.blade.php
│   │   └── ...
│   └── css/                    # Tailwind CSS
├── routes/
│   ├── web.php                 # Web routes
│   ├── api.php                 # API routes
│   └── ...
├── database/
│   ├── migrations/             # Database migrations
│   ├── seeders/                # Database seeders
│   └── factories/              # Model factories
├── config/
│   ├── attendance.php          # App configuration
│   ├── database.php
│   └── ...
├── tests/
│   ├── Unit/                   # Unit tests
│   ├── Feature/                # Feature tests
│   └── Browser/                # Browser tests
├── public/                     # Public assets
├── storage/                    # File storage
├── bootstrap/                  # Bootstrap files
├── .env.example                # Environment template
├── artisan                     # Laravel CLI
├── composer.json               # PHP dependencies
└── package.json                # Node dependencies
```

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.2+
- Composer
- MySQL/PostgreSQL
- Node.js 18+ (for Tailwind CSS)
- Google Sheets API credentials
- LLM API keys (Groq or OpenRouter)

### Installation
```bash
# Clone the repository
git clone <repository-url>
cd nsrc-ams

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed

# Build Tailwind CSS
npm run build

# Start development server
php artisan serve

# In another terminal, watch for CSS changes
npm run watch
```

### Environment Variables
```
APP_NAME=NSRC-AMS
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nsrc_ams
DB_USERNAME=root
DB_PASSWORD=

GOOGLE_SHEETS_API_KEY=...
GROQ_API_KEY=...
OPENROUTER_API_KEY=...
GOOGLE_GENAI_API_KEY=...
```

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/DutyEngineTest.php

# Run with coverage
php artisan test --coverage
```

---

## 📖 Documentation

- **Design System**: See `DESIGN_SYSTEM.md`
- **Name Merging**: See `NAME_MERGING.md`
- **Member Mode**: See `MEMBER_MODE_UPDATES.md`
- **Implementation**: See `IMPLEMENTATION_COMPLETE.md`

---

## 🤝 Contributing

When contributing to this project:
1. Follow Laravel conventions and PSR-12 coding standards
2. Maintain type safety with PHP 8.2+ features
3. Add accessibility features to Blade components
4. Test on multiple screen sizes
5. Write tests for new features
6. Update documentation
7. Use meaningful commit messages

### Development Workflow
```bash
# Create a feature branch
git checkout -b feature/your-feature

# Make changes and commit
git add .
git commit -m "Add your feature"

# Push to remote
git push origin feature/your-feature

# Create a pull request
```

---

## 📄 License

SPDX-License-Identifier: Apache-2.0

---

## 📞 Support

For issues, questions, or feature requests, please refer to the project documentation or contact the development team.

---

**Last Updated:** May 2026  
**Maintained By:** NSRC Development Team  
**Framework:** Laravel 11 with Livewire 3
