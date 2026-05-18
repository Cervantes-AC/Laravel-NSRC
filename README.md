# PageTurner Online Bookstore Management System

## Student Information
- **Names:** Aaron Clyde C. Cervantes, Gabriel Araneta
- **Course:** Bachelor of Science in Information Technology
- **University:** Central Mindanao University
- **Activity:** Laboratory Activity 7 - Mass Data Seeding, Performance Optimization, and Scalability Engineering
- **Type:** Group Activity

## Project Description

PageTurner is a comprehensive, enterprise-grade online bookstore management system built with Laravel 12. This full-featured web application demonstrates advanced Laravel concepts including routing, controllers, views, Blade templating, database operations, user authentication, and modern enterprise features. The system serves both administrators and customers with distinct functionalities for managing books, categories, orders, reviews, auditing, backups, and analytics. Laboratory Activity 7 focuses on scaling the catalog to 1M+ records, database performance tuning, caching architectures, and horizontal scaling strategies.

## Learning Objectives Achieved

### Core Laravel Concepts
- ✅ Advanced routing with grouping, middleware, and resource controllers
- ✅ Complex controller architecture with admin and customer separation
- ✅ Blade templating with components, inheritance, and slots
- ✅ Database migrations with relationships and constraints
- ✅ Eloquent ORM with complex queries and relationships
- ✅ Database seeders and factories for test data

### Advanced Features Implemented
- ✅ Two-factor authentication (2FA) with recovery codes
- ✅ Email verification system
- ✅ Role-based access control (RBAC) with admin/customer/premium tiers
- ✅ API rate limiting with tiered access
- ✅ Comprehensive audit logging system
- ✅ Automated backup management with monitoring
- ✅ Data import/export functionality (Excel)
- ✅ Full-text search with Laravel Scout
- ✅ Queue system for background jobs
- ✅ Redis caching and session management
- ✅ Scheduled tasks and maintenance commands

### Lab 7: Scalability & Performance Engineering
- ✅ Mass data seeding of 1M+ book records in under 10 minutes (< 512 MB RAM)
- ✅ Valid ISBN-13 generation with modulo-10 checksum
- ✅ Chunked batch insert strategy (5K records per batch)
- ✅ Database table partitioning by publication year (range partitioning)
- ✅ Composite indexes and covering indexes for index-only scans
- ✅ MySQL FULLTEXT index for high-performance search
- ✅ Redis tag-based query result caching with targeted invalidation
- ✅ Cursor pagination (O(1)) vs offset pagination (O(n))
- ✅ Read/write splitting with sticky connections
- ✅ Materialized views for bestseller reporting
- ✅ Read replica integration for reporting offloading
- ✅ N+1 query prevention with whenLoaded() and selective eager loading
- ✅ Model observers for smart cache invalidation
- ✅ Asynchronous cache warming via background jobs
- ✅ Performance benchmarking automation with CI/CD exit codes
- ✅ Load testing with 50 concurrent request simulation

## Features

### User Management & Authentication
- 👤 User registration and login via Laravel Breeze
- 🔐 Two-factor authentication (2FA) with recovery codes
- ✉️ Email verification system
- 🎯 Role-based access control (Admin/Customer/Premium)
- 📝 User profile management with password changes
- 🛡️ Protected routes with middleware
- 📊 User management dashboard (Admin)

### Book Management (Admin)
- 📚 Complete CRUD operations for books
- 🏷️ Category assignment and management
- 📊 Stock quantity tracking with low-stock alerts
- 🖼️ Cover image upload support
- 📖 Detailed book information (ISBN, author, description, pricing)
- 🔍 Full-text search with Laravel Scout
- 📈 Book performance analytics
- 📋 Batch operations and bulk imports

### Category Management (Admin)
- 📂 Create, read, update, delete categories
- 📋 Category descriptions and metadata
- 📊 Book count per category
- 🎨 Category organization and hierarchy

### Customer Features
- 🔍 Browse books with advanced search and filtering
- 📖 View detailed book information with ratings
- ⭐ Read and write book reviews with 5-star ratings
- 🛒 Shopping cart functionality with persistent storage
- 📦 Order management and history
- 💬 Review management (edit/delete own reviews)
- 🎁 Wishlist functionality (future enhancement)

### Review System
- ⭐ 5-star rating system with averages
- 💬 Written reviews with detailed comments
- 👤 User-specific review management
- 📊 Average rating calculations
- 🔒 Review ownership validation

### Admin Dashboard & Analytics
- 📊 Comprehensive dashboard with key metrics
- 📈 Sales analytics and trends
- 👥 User activity monitoring
- 📚 Book performance metrics
- 🎯 Order statistics and insights

### Audit & Compliance
- 📋 Complete audit logging of all user actions
- 🔍 Audit trail with timestamps and user information
- 📊 Audit log export functionality
- 🔐 Security event tracking
- 📝 Compliance reporting

### Backup & Data Management
- 💾 Automated backup system with monitoring
- 📅 Scheduled backup tasks
- 🔄 Backup restoration capabilities
- 📊 Backup monitoring dashboard
- 🗑️ Cleanup of old backups

### Import/Export Features
- 📤 Export books, users, orders, and audit logs to Excel
- 📥 Import books from Excel files
- 📊 Batch operations with progress tracking
- 🔍 Import validation and error reporting
- 📋 Export history and logging

### API Rate Limiting
- 🚦 Tiered rate limiting (Standard/Premium/Admin)
- 📊 Rate limit monitoring dashboard
- 🔧 Configurable rate limits per tier
- 📈 Usage analytics and tracking
- ⚠️ Rate limit alerts and notifications

### Queue & Background Jobs
- ⚙️ Redis-based queue system
- 📧 Email notifications
- 🔄 Asynchronous task processing
- 📊 Job monitoring and logging
- 🔧 Queue management commands

### Caching & Performance
- ⚡ Redis caching layer
- 🔄 Session management with Redis
- 📊 Cache statistics and monitoring
- 🎯 Query optimization with eager loading
- 📈 Performance monitoring

### Lab 7: Mass Data Seeding (1M Books)
- ✅ Memory-safe chunked batch insert via `DB::table('books')->insert()` (5K per chunk)
- ✅ Manual garbage collection every 10 chunks to stay under 512 MB
- ✅ Faker-free static data generation (mt_rand + static arrays) for zero-overhead
- ✅ Valid ISBN-13 with proper prefix and checksum digit
- ✅ Format-aware realistic pricing (Paperback: $7.99-$24.99, Hardcover: $16.99-$45.00, etc.)
- ✅ 85% active / 15% inactive distribution
- ✅ 15 real-world publisher names
- ✅ 33 first names x 34 last names for varied author generation
- ✅ 12 title prefixes x 18 adjectives x 27 nouns x 22 themes for 128K+ unique titles
- ✅ Cursor pagination for stable iteration without OFFSET degradation

### Lab 7: Query Performance Optimization
- **ISBN Lookup:** `< 50ms` — unique index + Redis cache
- **Catalog Listing:** `< 100ms` — cursor pagination + covering index
- **Category Filter:** `< 150ms` — composite index (`category_id`, `published_at`, `is_active`) + query cache
- **Full-Text Search:** `< 300ms` — Scout + MySQL FULLTEXT index
- **Cached Requests:** `< 10ms` — Redis tag-based caching
- **Export (50K):** `< 30s` — queue + lazy collection streaming

### Lab 7: Database Architecture
- **Table Partitioning:** Range partitioning by `YEAR(published_at)` — 7 partitions (p_old through p_future)
- **Materialized View:** `mv_bestseller_stats` — pre-computed category aggregates refreshed hourly
- **Covering Index:** `idx_books_price_stock` on (`price`, `stock_quantity`, `id`) — index-only scans
- **Composite Index:** `idx_books_catalog_filter` on (`category_id`, `published_at`, `is_active`)
- **Full-Text Index:** `idx_books_fulltext` on (`title`, `description`)
- **Query Performance Logs:** `query_performance_logs` table for slow query monitoring

### Lab 7: Redis Cache Architecture
```
Database 0: General purpose
Database 1: Query result caching (redis-tags store)
├── tag: "catalog" → catalog listings, price range queries
└── tag: "category:{id}" → category-specific catalogs
Database 2: Session storage
Database 3: Queue jobs
```

### Lab 7: Scalability Features
- **Read/Write Splitting:** Automatic read traffic to replica hosts, sticky sessions
- **Database Sharding:** `Shardable` trait with modulo-4 routing for horizontal scaling
- **Async Cache Warmup:** `WarmCategoryCache` job pre-loads top 1000 books per category
- **Smart Cache Invalidation:** `BookObserver` flushes relevant tags on save/delete
- **Query Streaming:** `FromQuery` + `WithChunkReading` (chunk 2000) for memory-safe exports
- **Rate Limiting:** Tiered Redis-backed limits (public: 30/min, standard: 60, premium: 300, admin: 1000)
- **Connection Pooling:** PDO persistent connections for Swoole/RoadRunner compatibility

### Lab 7: Performance Deliverables

| File | Purpose |
|------|---------|
| `database/factories/BookFactory.php` | Optimized factory with ISBN-13 generation, cached category IDs, format pricing |
| `database/seeders/MassBookSeeder.php` | Chunked batch insert (5K) for 1M records with garbage collection |
| `database/seeders/CategorySeeder.php` | Prerequisite seeder for users and categories |
| `app/Repositories/BookRepository.php` | Optimized data access with cursor pagination + Redis tag caching |
| `app/Services/BookCacheService.php` | Redis tag-based caching abstraction with targeted invalidation |
| `app/Observers/BookObserver.php` | Cache invalidation on model saved/deleted events |
| `app/Console/Commands/BenchmarkBookQueries.php` | Automated benchmarking against sub-second targets |
| `app/Console/Commands/IndexBooksBatch.php` | Chunked Scout search index import |
| `app/Console/Commands/RefreshMaterializedViews.php` | Hourly materialized view refresh |
| `app/Jobs/WarmCategoryCache.php` | Background cache warming per category |
| `app/Traits/Shardable.php` | Modulo-4 shard routing for horizontal scaling |
| `config/scout.php` | Scout search engine configuration |
| `tests/Performance/BookCatalogLoadTest.php` | Load test: 50 concurrent requests + response time validation |
| `database/migrations/*_optimize_books_*` | Composite, covering, full-text, and lookup indexes |
| `database/migrations/*_partition_books_by_year*` | Range partitioning by publication year |

## Technologies Used

- **Laravel:** 12.x
- **PHP:** 8.2+
- **Database:** MySQL with read replicas support
- **Cache/Session:** Redis
- **Queue System:** Redis-based queues
- **Search:** Laravel Scout with database driver (Meilisearch optional)
- **Authentication:** Laravel Breeze with 2FA
- **File Storage:** Laravel Storage with S3 support
- **Export:** Maatwebsite Excel
- **Backup:** Spatie Laravel Backup
- **PDF Generation:** Laravel DomPDF
- **Templating:** Blade Template Engine
- **Frontend:** HTML5, CSS3, Tailwind CSS
- **Version Control:** Git & GitHub

## Database Schema

### Tables Overview
- **users** - User accounts with roles and 2FA support
- **categories** - Book categories/genres
- **books** - Book inventory with category relationships (partitioned by year)
- **orders** - Customer orders with status tracking
- **order_items** - Individual items in orders
- **reviews** - Customer book reviews with ratings
- **audit_logs** - Complete audit trail of system actions
- **api_rate_limits** - Rate limiting configuration and tracking
- **backup_monitoring** - Backup status and monitoring
- **export_logs** - Export operation history
- **import_logs** - Import operation history
- **scheduled_tasks** - Scheduled task tracking
- **mv_bestseller_stats** - Materialized view for bestseller/inventory reports
- **book_yearly_partitions** - Partitioned book data by publication year
- **query_performance_logs** - Slow query monitoring and logging
- **notifications** - Notification and alert preferences
- **sessions** - User session data (Redis)
- **cache** - Application cache (Redis)
- **jobs** - Queue job tracking (Redis)

### Key Relationships
- User hasMany Orders, Reviews, AuditLogs
- Category hasMany Books
- Book belongsTo Category, hasMany Reviews, OrderItems
- Order belongsTo User, hasMany OrderItems
- Review belongsTo User, Book
- AuditLog belongsTo User
- OrderItem belongsTo Order, Book

## Installation Instructions

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL 8.0+ or compatible database
- Redis (for caching, sessions, and queues)
- Git

### 1. Clone the Repository
```bash
git clone [your-repository-url]
cd pageturner-bookstore
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Configure Environment
```bash
cp .env.example .env
```

Edit `.env` file with your configuration:
```env
APP_NAME="PageTurner Bookstore"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pageturner_bookstore
DB_USERNAME=root
DB_PASSWORD=your_password

# Read Replica Configuration (optional)
DB_READ_HOST_1=127.0.0.1
DB_READ_HOST_2=127.0.0.1
DB_WRITE_HOST=127.0.0.1

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
REDIS_SESSION_DB=2
REDIS_QUEUE_DB=3

# Session & Cache
SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis

# Mail Configuration
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"

# Scout Search Configuration
SCOUT_DRIVER=database
# SCOUT_DRIVER=meilisearch (optional)
# MEILISEARCH_HOST=http://localhost:7700
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Run Migrations and Seeders
```bash
php artisan migrate:fresh --seed
```

### 6. Create Storage Link (for file uploads)
```bash
php artisan storage:link
```

### 7. Build Frontend Assets
```bash
npm run build
```

### 8. Start Development Server
```bash
php artisan serve
```

### 9. Start Queue Worker (in another terminal)
```bash
php artisan queue:listen
```

### 10. Start Redis Server
Ensure Redis is running on your system (typically on port 6379)

### 11. Access the Application
Visit: `http://localhost:8000`

## Default Login Credentials

### Admin Account
- **Email:** admin@gmail.com
- **Password:** password

### Test Customer Accounts
- Multiple customer accounts created via seeders
- **Password:** password (for all seeded accounts)

## Usage Guide

### For Administrators
1. **Login** with admin credentials
2. **Manage Categories:**
   - Navigate to Categories section
   - Create, edit, or delete book categories
3. **Manage Books:**
   - Add new books with category assignment
   - Upload cover images
   - Update stock quantities and pricing
   - Edit book details and descriptions
4. **View Orders:** Monitor customer orders and update status

### For Customers
1. **Register/Login** as a customer
2. **Browse Books:**
   - View all books or filter by category
   - Search by title or author
   - View detailed book information
3. **Write Reviews:**
   - Rate books (1-5 stars)
   - Leave written comments
   - Edit or delete your reviews
4. **Place Orders:** Add books to cart and checkout

## MVC Architecture Implementation

### Models
- **User:** Authentication and user data
- **Category:** Book categorization
- **Book:** Book inventory with relationships
- **Order:** Customer order management
- **OrderItem:** Individual order line items
- **Review:** Customer feedback system

### Views (Blade Templates)
- **Layout System:** `layouts/app.blade.php` with partials
- **Authentication:** Login, register, profile pages
- **Book Management:** Index, show, create, edit views
- **Category Management:** CRUD operation views
- **Components:** Reusable book cards, alerts, navigation

### Controllers
- **HomeController:** Homepage with featured content
- **CategoryController:** Category CRUD operations
- **BookController:** Book management with search/filter
- **OrderController:** Order processing and history
- **ReviewController:** Review submission and management

## Route Structure

### Public Routes
- `GET /` - Homepage
- `GET /books` - Book listing
- `GET /books/{book}` - Book details
- `GET /categories` - Category listing

### Authenticated Routes
- `POST /books/{book}/reviews` - Submit review
- `GET /orders` - Order history
- `POST /orders` - Create order

### Admin Routes (Prefix: /admin)
- Category management routes
- Book management routes
- Protected by authentication middleware

## Blade Features Demonstrated

### Directives Used
- `@extends` - Template inheritance
- `@section/@yield` - Content sections
- `@include` - Partial views
- `@foreach/@forelse` - Data iteration
- `@if/@auth/@guest` - Conditional rendering
- `@csrf/@method` - Form security
- `@error` - Validation error display

### Components
- Book card component with slots
- Alert component with type variants
- Reusable navigation and footer partials

## Security Features

✅ **CSRF Protection:** All forms protected with @csrf tokens  
✅ **Authentication:** Laravel Breeze integration  
✅ **Authorization:** Role-based access control  
✅ **Validation:** Comprehensive form validation  
✅ **SQL Injection Prevention:** Eloquent ORM usage  
✅ **File Upload Security:** Proper image validation  

## What I Learned

### Technical Skills
- Advanced Laravel routing with grouping and middleware
- Resource controllers and RESTful conventions
- Complex Eloquent relationships and queries
- Blade templating with components and inheritance
- Database migrations with foreign key constraints
- Factory and seeder implementation for test data
- File upload handling with Laravel Storage
- Form validation and error handling

### Conceptual Understanding
- MVC architecture in complex applications
- Authentication and authorization patterns
- Database design with normalized relationships
- RESTful API design principles
- Component-based UI development
- Security best practices in web applications

## Project Structure

```
pageturner-bookstore/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── BenchmarkBookQueries.php      # Performance benchmark
│   │       ├── IndexBooksBatch.php           # Scout chunked import
│   │       ├── RefreshMaterializedViews.php  # Hourly stats refresh
│   │       ├── WarmCategoryCache.php         # (moved to Jobs/)
│   │       └── ... (maintenance commands)
│   ├── Exports/
│   │   ├── BooksExport.php                  # FromQuery + WithChunkReading
│   │   └── QueuedBooksExport.php            # ShouldQueue export
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/ (Dashboard, Backup, Import/Export, Audit, etc.)
│   │   │   ├── Auth/ (Breeze controllers)
│   │   │   ├── BookController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── HomeController.php
│   │   │   ├── OrderController.php
│   │   │   └── ReviewController.php
│   │   └── Resources/
│   │       ├── BookResource.php             # whenLoaded() N+1 prevention
│   │       ├── CategoryResource.php
│   │       └── ReviewResource.php
│   ├── Jobs/
│   │   └── WarmCategoryCache.php            # Async cache pre-warming
│   ├── Models/
│   │   ├── User.php
│   │   ├── Category.php
│   │   ├── Book.php                         # Searchable trait, toSearchableArray()
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   └── Review.php
│   ├── Observers/
│   │   └── BookObserver.php                 # Tag-based cache invalidation
│   ├── Repositories/
│   │   └── BookRepository.php               # Cursor pagination + Redis tag cache
│   ├── Services/
│   │   ├── BookCacheService.php             # Redis tag-based caching
│   │   └── ... (BookFilterService, OrderService)
│   └── Traits/
│       └── Shardable.php                    # Modulo-4 shard routing
├── database/
│   ├── factories/
│   │   ├── BookFactory.php                  # ISBN-13, cached IDs, format pricing
│   │   └── ...
│   ├── migrations/
│   │   ├── *create_books_table.php
│   │   ├── *optimize_books_table_indexes.php # Composite, covering, fulltext indexes
│   │   ├── *partition_books_by_year.php      # Range partitioning
│   │   ├── *create_bestseller_stats_table.php # Materialized view table
│   │   ├── *create_query_performance_logs_table.php
│   │   └── ...
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── CategorySeeder.php               # Users + 5 categories
│       └── MassBookSeeder.php               # 1M records, chunked batch insert
├── resources/
│   └── views/ ...
├── routes/
│   ├── web.php
│   ├── api.php                              # Book API with cursor pagination
│   └── console.php                          # Schedule definitions
└── tests/
    └── Performance/
        └── BookCatalogLoadTest.php          # 50 concurrent request load test
```

## Challenges Faced and Solutions

1. **Challenge:** Complex Eloquent relationships
   - **Solution:** Carefully planned database schema and used proper foreign key constraints

2. **Challenge:** Role-based authorization
   - **Solution:** Implemented custom middleware and helper methods in User model

3. **Challenge:** File upload handling
   - **Solution:** Used Laravel Storage facade with proper validation

4. **Challenge:** Complex Blade component structure
   - **Solution:** Created reusable components with slots for flexibility

5. **Challenge:** Seeding 1M records without exhausting memory
   - **Solution:** Used `DB::table('books')->insert()` with 5K chunks instead of Eloquent models, manual `gc_collect_cycles()` every 10 chunks, and faker-free static data arrays

6. **Challenge:** Maintaining sub-100ms catalog response at 1M records
   - **Solution:** Cursor pagination (O(1) vs O(n) for OFFSET), covering indexes for index-only scans, and Redis tag-based caching with targeted invalidation

7. **Challenge:** Full-text search across 1M records
   - **Solution:** MySQL FULLTEXT index on title/description + Laravel Scout database engine with queued indexing

## Future Enhancements

- [ ] Advanced inventory management
- [ ] Email notifications for orders
- [ ] Wishlist functionality
- [ ] Book recommendations system
- [ ] Multi-language support
- [ ] Payment gateway integration
- [ ] Advanced reporting dashboard
- [ ] Meilisearch integration for faster full-text search
- [ ] Horizontal sharding with dedicated database servers
- [ ] ClickHouse integration for real-time analytics
- [ ] GraphQL API endpoint for flexible queries

## Academic Integrity Statement

This project was completed as part of Laboratory Activity 7. All code was written following Laravel best practices and the provided specifications. The implementation demonstrates original understanding of mass data seeding, performance optimization, and scalability engineering concepts taught in class.

## License

This project was created for educational purposes as part of coursework requirements.

## Acknowledgments

- **Laravel Documentation** - For comprehensive framework guidance
- **Laravel Breeze** - For authentication scaffolding
- **Tailwind CSS** - For responsive design components
- **Our Instructor** - For providing detailed specifications and support

---

**Created with ❤️ for Laboratory Activity 7**  
*Mass Data Seeding, Performance Optimization, and Scalability Engineering*  
*PageTurner Online Bookstore Management System*
