# Project Structure & Organization

## Directory Layout

### Application Core (`app/`)
```
app/
├── Http/
│   └── Controllers/
│       ├── Api/           # API controllers for mobile/SPA
│       │   ├── AuthController.php
│       │   ├── JobController.php
│       │   └── Admin/     # Admin-specific API controllers
│       └── Controller.php # Base controller
├── Models/               # Eloquent models
│   ├── User.php         # Main user model with roles
│   ├── JobPosting.php   # Job listings
│   ├── Company.php      # Company profiles
│   ├── JobSeeker.php    # Job seeker profiles
│   └── ...
└── Providers/           # Service providers
    └── AppServiceProvider.php
```

### Database (`database/`)
```
database/
├── migrations/          # Database schema migrations
├── seeders/            # Database seeders
├── factories/          # Model factories for testing
└── database.sqlite     # SQLite database (development)
```

### Frontend Resources (`resources/`)
```
resources/
├── css/
│   └── app.css         # Tailwind CSS entry point
├── js/
│   ├── app.js          # Main JavaScript entry
│   └── bootstrap.js    # Bootstrap configuration
└── views/
    └── welcome.blade.php # Blade templates
```

### Configuration (`config/`)
- Standard Laravel configuration files
- Key configs: `app.php`, `auth.php`, `database.php`, `jwt.php`

## Naming Conventions

### Models
- **Singular PascalCase**: `User`, `JobPosting`, `Company`
- **Relationships**: Use descriptive method names (`jobSeeker()`, `postedJobs()`)
- **Scopes**: Prefix with `scope` (`scopeActive()`, `scopeVerified()`)

### Controllers
- **PascalCase with Controller suffix**: `AuthController`, `JobController`
- **API Controllers**: Namespace under `Api/` directory
- **Admin Controllers**: Namespace under `Api/Admin/`

### Routes
- **API Routes**: RESTful structure with clear resource grouping
- **Route Groups**: Organized by functionality (`auth`, `jobs`, `employer`, `admin`)
- **Middleware**: Role-based protection (`role:job-seeker`, `role:employer`)

### Database
- **Tables**: Snake_case plural (`users`, `job_postings`, `user_company_roles`)
- **Columns**: Snake_case (`first_name`, `posted_by_user_id`, `is_active`)
- **Foreign Keys**: `{model}_id` format (`company_id`, `user_id`)

## Code Organization Patterns

### Model Structure
- **Fillable attributes** clearly defined
- **Relationships** grouped together
- **Scopes** for common queries
- **Accessors/Mutators** for data formatting
- **Traits**: `SoftDeletes`, `LogsActivity`, `HasRoles`, `HasUuids`

### API Controller Structure
- **Resource-based** endpoints following REST conventions
- **Role-based middleware** for access control
- **Consistent response format** for API endpoints
- **Validation** handled in form requests or controller methods

### Authentication & Authorization
- **JWT-based API authentication**
- **Role-based access control** via Spatie Permission
- **Three main roles**: `job-seeker`, `employer`, `admin`/`super-admin`
- **Route protection** via middleware groups

## File Naming Standards
- **Models**: Singular PascalCase (`JobPosting.php`)
- **Controllers**: PascalCase with suffix (`JobController.php`)
- **Migrations**: Timestamp + descriptive name (`2025_10_15_210936_create_job_postings_table.php`)
- **Config files**: Lowercase with hyphens (`jwt.php`, `permission.php`)

## Key Architectural Decisions
- **API-first approach** with separate web and API routes
- **Role-based access control** throughout the application
- **Soft deletes** for data preservation
- **UUID support** for public-facing identifiers
- **Activity logging** for audit trails
- **Livewire components** for interactive frontend elements