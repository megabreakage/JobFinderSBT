# Technology Stack

## Backend Framework
- **Laravel 12.x** (PHP 8.3+) - Main application framework
- **JWT Authentication** via `tymon/jwt-auth` for API security
- **MySQL/SQLite** - Primary database (SQLite for development)
- **Eloquent ORM** - Database interactions with relationships and scopes

## Frontend Stack
- **Livewire 3.x** - Dynamic frontend components
- **Tailwind CSS 4.x** - Utility-first CSS framework
- **Vite** - Asset bundling and hot module replacement
- **Axios** - HTTP client for API requests

## Key Packages & Libraries
- **Spatie Laravel Permission** - Role-based access control (RBAC)
- **Spatie Laravel Activity Log** - User activity tracking
- **Spatie Laravel Backup** - Automated backups
- **Vonage Client** - SMS/OTP functionality
- **Intervention Image** - Image processing and manipulation
- **Laravel Pint** - Code style formatting
- **Laravel Sail** - Docker development environment

## Development Tools
- **Laravel Boost** - Enhanced development experience
- **Laravel Pail** - Real-time log monitoring
- **PHPUnit** - Testing framework
- **Faker** - Test data generation

## Common Commands

### Setup & Installation
```bash
composer run setup          # Full project setup
composer install           # Install PHP dependencies
npm install                # Install Node.js dependencies
php artisan key:generate    # Generate application key
php artisan migrate         # Run database migrations
```

### Development
```bash
composer run dev           # Start all development services (server, queue, logs, vite)
php artisan serve         # Start Laravel development server
npm run dev               # Start Vite development server
php artisan queue:work    # Process background jobs
php artisan pail          # Monitor application logs
```

### Testing & Quality
```bash
composer run test         # Run PHPUnit tests
php artisan test         # Alternative test command
./vendor/bin/pint        # Format code with Laravel Pint
```

### Database Operations
```bash
php artisan migrate              # Run migrations
php artisan migrate:rollback     # Rollback migrations
php artisan db:seed             # Run database seeders
php artisan migrate:fresh --seed # Fresh migration with seeding
```

## Architecture Patterns
- **API-First Design** - RESTful API with clear endpoint structure
- **Repository Pattern** - Used in models with Eloquent relationships
- **Service Layer** - Business logic separation (implied in controllers)
- **Role-Based Access Control** - Spatie Permission integration
- **Soft Deletes** - Data preservation with logical deletion
- **UUID Support** - Models use UUIDs for public identification
- **Activity Logging** - Comprehensive audit trails