# AI Prompt for HR Outsourcing & Talent Management Website Development

## Project Overview

Build a comprehensive Human Resource Outsourcing Services and Talent Management website that serves three main user types: job seekers, employers, and administrators. The platform should facilitate job posting, application management, and HR service subscriptions using Laravel backend, Livewire frontend with Bootstrap components, MySQL database, and Vonage for SMS/OTP functionality.

## Technical Stack Requirements

- **Backend**: Laravel (PHP) with RESTful API architecture
- **Frontend**: Livewire with Bootstrap components
- **Database**: MySQL with proper indexing and relationships
- **Authentication**: JWT tokens with email verification and Vonage OTP
- **File Storage**: Secure cloud storage for resumes (AWS S3 or similar)
- **Communication**: Vonage API for SMS notifications and OTP
- **Payment**: Integration with payment gateway for HR service subscriptions
- **Security**: HTTPS enforcement, rate limiting, CORS configuration

## Core Features to Implement

### 1. Authentication System (Epic A)

Create a dual-registration system that supports both job seekers and employers with:

- Email and phone number registration with validation
- Two-factor verification using email confirmation and Vonage OTP
- Secure login with JWT token management
- Password reset functionality with email verification
- Role-based access control (job_seeker, employer, admin)
- Session management with refresh tokens

### 2. Job Seeker Portal (Epic B)

Develop comprehensive job seeker features including:

- **Public Job Browse**: Implement filterable job listings with search functionality
  - Filters: industry, location, job type, salary range, experience level
  - Advanced search with keyword matching
  - Pagination and sorting options
- **Job Details Page**: Display complete job information with apply CTA
- **Application System**:
  - Resume upload with file type validation (PDF, DOC, DOCX)
  - Cover letter text editor
  - Auto-populate from saved profile option
  - Application tracking and status updates
- **Save Jobs Feature**: Wishlist functionality with saved jobs dashboard
- **Profile Management**:
  - Comprehensive profile builder (bio, education, work experience, skills)
  - Resume management with version control
  - Portfolio/project showcase section
  - Privacy settings for profile visibility

### 3. Employer Portal (Epic C)

Build employer-specific features:

- **Company Registration**: Multi-step onboarding with company verification
- **Job Posting System**:
  - Rich text editor for job descriptions
  - Required fields: title, description, requirements, type, location, industry
  - Job posting templates
  - Draft saving functionality
  - Job expiration date settings
- **Application Management**:
  - Dashboard with application metrics
  - Candidate filtering and sorting
  - Resume viewing and downloading
  - Application status management (new, reviewed, shortlisted, rejected)
  - Bulk actions for applications
  - Communication tools with candidates

### 4. HR Services Subscription (Epic D)

Implement tiered subscription model:

- **Service Packages Display**:
  - Starter Package: Basic HR support
  - Economy Package: Enhanced HR services
  - Business Package: Full HR outsourcing
  - Feature comparison table
  - Pricing calculator
- **Subscription Flow**:
  - Package selection interface
  - Checkout process with order summary
  - Payment gateway integration
  - Invoice generation
  - Subscription management dashboard
  - Auto-renewal settings
  - Upgrade/downgrade functionality

### 5. Admin Panel (Epic E)

Create comprehensive admin dashboard with:

- **User Management**: CRUD operations for all user types with search and filters
- **Job Management**: Approve/reject job postings, edit listings, manage categories
- **Industry & Category Management**: Dynamic category system for jobs
- **Application Oversight**: Monitor application flow, resolve disputes
- **Company Verification**: Verify employer accounts and company details
- **HR Package Configuration**: Manage service tiers, pricing, features
- **Order Management**: Track subscriptions, payments, invoices
- **Content Management System**:
  - Edit static pages (About Us, Contact, Privacy Policy, Terms)
  - FAQ management
  - Blog/news section
  - Email template editor

### 6. Security & Compliance (Epic F)

Implement robust security measures:

- Rate limiting on all authentication endpoints
- HTTPS enforcement across all pages
- Secure file upload with virus scanning
- Signed URLs for resume access with expiration
- GDPR compliance for data handling
- Regular security audits and logging
- Input sanitization and SQL injection prevention
- XSS protection
- CSRF token implementation

## Database Schema Design

### Core Tables Required

1. **users**: id, email, phone, password, role, email_verified_at, phone_verified_at
2. **job_seekers**: user_id, bio, resume_url, skills (JSON), education (JSON)
3. **companies**: id, name, website, logo, description, verified_at
4. **employers**: user_id, company_id, position, permissions
5. **jobs**: id, company_id, title, description, requirements, type, location, industry_id, status, expires_at
6. **applications**: id, job_id, job_seeker_id, resume_url, cover_letter, status, applied_at
7. **saved_jobs**: job_seeker_id, job_id, saved_at
8. **industries**: id, name, slug, parent_id
9. **hr_packages**: id, name, price, features (JSON), duration
10. **subscriptions**: id, company_id, package_id, status, starts_at, expires_at
11. **transactions**: id, subscription_id, amount, payment_method, status

## API Endpoints Structure

### Authentication

- POST /api/register (with role parameter)
- POST /api/login
- POST /api/logout
- POST /api/verify-email
- POST /api/verify-phone
- POST /api/password/reset

### Jobs

- GET /api/jobs (with filters)
- GET /api/jobs/{id}
- POST /api/jobs (employer only)
- PUT /api/jobs/{id} (employer only)
- DELETE /api/jobs/{id} (employer only)

### Applications

- POST /api/jobs/{id}/apply (job seeker only)
- GET /api/applications (contextual by user role)
- PUT /api/applications/{id}/status (employer only)

### Profile

- GET /api/profile
- PUT /api/profile
- POST /api/profile/resume

### HR Services

- GET /api/hr-packages
- POST /api/subscriptions
- GET /api/subscriptions/current

## Frontend Components Structure

### Shared Components

- Navigation (role-based menu items)
- Footer
- SearchBar
- FilterPanel
- JobCard
- Pagination
- LoadingSpinner
- ErrorBoundary
- NotificationToast

### Pages/Routes

- / (Homepage)
- /jobs (Job listings)
- /jobs/{id} (Job detail)
- /about
- /contact
- /hr-services
- /register
- /login
- /dashboard (role-based redirection)
- /profile
- /admin/* (protected admin routes)
- /employer/* (protected employer routes)

## Integration Requirements

### Vonage Integration

- SMS notification for:
  - Registration OTP
  - Application confirmation
  - Job posting approval
  - Subscription confirmation
- Configure webhooks for delivery status

### Email Integration

- Transactional emails for:
  - Welcome email
  - Email verification
  - Password reset
  - Application notifications
  - Subscription invoices

### Payment Gateway

- Integrate Stripe/PayPal for:
  - Subscription payments
  - Recurring billing
  - Invoice generation
  - Refund processing

## Performance Optimization

- Implement caching for job listings
- Lazy loading for images
- Database query optimization with proper indexing
- CDN integration for static assets
- Redis for session management
- Queue system for email/SMS notifications

## Testing Requirements

- Unit tests for critical business logic
- Integration tests for API endpoints
- Frontend component testing with Livewire
- E2E testing for critical user flows
- Performance testing for high-traffic scenarios

## Deployment Considerations

- CI/CD pipeline setup
- Environment-specific configurations
- Database migration strategy
- Zero-downtime deployment
- Backup and disaster recovery plan
- Monitoring and alerting setup
- SSL certificate configuration

## Success Metrics to Track

- User registration rate
- Job posting frequency
- Application submission rate
- Subscription conversion rate
- User engagement metrics
- Page load times
- API response times
- Error rates

Generate the complete implementation starting with the database migrations, then API controllers, followed by Livewire components, ensuring all features from the MVP plan are fully functional and production-ready.
