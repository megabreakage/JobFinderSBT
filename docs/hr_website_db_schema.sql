-- ==============================================
-- COMPLETE HR TALENT MANAGEMENT DATABASE SCHEMA
-- ==============================================
-- Features:
-- - Laravel Spatie Role/Permission System with Enhanced Authorization
-- - HR Outsourcing Services and Talent Management
-- - Comprehensive Audit Logging with Activity Tracking
-- - Soft Deletes with Super Admin Hard Delete
-- - Strict Foreign Key Constraints and Performance Optimization
-- - Job Posting, Application Management, and Company Subscriptions
-- - Communication, Analytics, and CMS Management
-- ==============================================

CREATE DATABASE IF NOT EXISTS hr_talent_management;
USE hr_talent_management;

-- Enable foreign key constraints
SET foreign_key_checks = 1;

-- ==============================================
-- CORE USER AUTHENTICATION TABLES
-- ==============================================

-- Users table with comprehensive authentication features
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20) NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    avatar_url VARCHAR(500) NULL,
    email_verified_at TIMESTAMP NULL,
    phone_verified_at TIMESTAMP NULL,
    email_verification_token VARCHAR(255) NULL,
    phone_verification_otp VARCHAR(6) NULL,
    phone_otp_expires_at TIMESTAMP NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    last_login_at TIMESTAMP NULL,
    failed_login_attempts INT NOT NULL DEFAULT 0,
    locked_until TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idx_uuid (uuid),
    INDEX idx_is_active (is_active),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_last_login (last_login_at),
    UNIQUE KEY unique_active_email (email, deleted_at),
    UNIQUE KEY unique_active_phone (phone, deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password reset tokens
CREATE TABLE password_resets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_token (token),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Personal access tokens for API authentication
CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_tokenable (tokenable_type, tokenable_id),
    INDEX idx_token (token),
    INDEX idx_last_used (last_used_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- ENHANCED SPATIE ROLE & PERMISSION SYSTEM
-- ==============================================

-- Permissions table with enhanced metadata
CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(125) NOT NULL,
    guard_name VARCHAR(125) NOT NULL DEFAULT 'web',
    display_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    category VARCHAR(100) NOT NULL,
    is_system BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_name (name),
    INDEX idx_guard_name (guard_name),
    INDEX idx_category (category),
    INDEX idx_is_system (is_system),
    INDEX idx_deleted_at (deleted_at),
    UNIQUE KEY unique_permission_guard (name, guard_name, deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles table with enhanced metadata
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(125) NOT NULL,
    guard_name VARCHAR(125) NOT NULL DEFAULT 'web',
    display_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    level INT NOT NULL DEFAULT 1,
    is_system BOOLEAN NOT NULL DEFAULT FALSE,
    color VARCHAR(7) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_name (name),
    INDEX idx_guard_name (guard_name),
    INDEX idx_level (level),
    INDEX idx_is_system (is_system),
    INDEX idx_deleted_at (deleted_at),
    UNIQUE KEY unique_role_guard (name, guard_name, deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Many-to-many: Users to Roles with enhanced tracking
CREATE TABLE model_has_roles (
    role_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(125) NOT NULL DEFAULT 'App\\Models\\User',
    model_id BIGINT UNSIGNED NOT NULL,
    assigned_by BIGINT UNSIGNED NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    
    PRIMARY KEY (role_id, model_type, model_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (model_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    
    INDEX idx_model (model_type, model_id),
    INDEX idx_role_id (role_id),
    INDEX idx_assigned_by (assigned_by),
    INDEX idx_expires_at (expires_at),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Many-to-many: Users to Permissions (direct assignments)
CREATE TABLE model_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(125) NOT NULL DEFAULT 'App\\Models\\User',
    model_id BIGINT UNSIGNED NOT NULL,
    assigned_by BIGINT UNSIGNED NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    
    PRIMARY KEY (permission_id, model_type, model_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (model_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    
    INDEX idx_model (model_type, model_id),
    INDEX idx_permission_id (permission_id),
    INDEX idx_assigned_by (assigned_by),
    INDEX idx_expires_at (expires_at),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Many-to-many: Roles to Permissions
CREATE TABLE role_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    assigned_by BIGINT UNSIGNED NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (permission_id, role_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    
    INDEX idx_role_id (role_id),
    INDEX idx_permission_id (permission_id),
    INDEX idx_assigned_by (assigned_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- INDUSTRIES AND SKILLS MASTER DATA
-- ==============================================

-- Industries/Categories with hierarchical structure
CREATE TABLE industries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    icon VARCHAR(50) NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (parent_id) REFERENCES industries(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_slug (slug),
    INDEX idx_parent (parent_id),
    INDEX idx_is_active (is_active),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Skills master table
CREATE TABLE skills (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    category VARCHAR(100) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_name (name),
    INDEX idx_slug (slug),
    INDEX idx_category (category),
    INDEX idx_is_active (is_active),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- COMPANY MANAGEMENT TABLES
-- ==============================================

-- Companies with comprehensive profile data
CREATE TABLE companies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    website VARCHAR(500) NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(20) NULL,
    logo_url VARCHAR(500) NULL,
    cover_image_url VARCHAR(500) NULL,
    description TEXT NULL,
    industry_id BIGINT UNSIGNED NULL,
    company_size ENUM('1-10', '11-50', '51-200', '201-500', '501-1000', '1000+') NULL,
    founded_year INT NULL,
    headquarters_location VARCHAR(255) NULL,
    locations JSON NULL COMMENT 'Array of office locations',
    social_links JSON NULL COMMENT 'Social media links',
    is_verified BOOLEAN DEFAULT FALSE,
    verified_at TIMESTAMP NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (industry_id) REFERENCES industries(id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    
    INDEX idx_slug (slug),
    INDEX idx_uuid (uuid),
    INDEX idx_industry (industry_id),
    INDEX idx_is_verified (is_verified),
    INDEX idx_is_featured (is_featured),
    INDEX idx_is_active (is_active),
    INDEX idx_created_by (created_by),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_company_size (company_size),
    FULLTEXT idx_search (name, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User-Company relationships with role-based access
CREATE TABLE user_company_roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    company_id BIGINT UNSIGNED NOT NULL,
    role_type ENUM('owner', 'admin', 'hr_manager', 'recruiter', 'member') NOT NULL,
    job_title VARCHAR(255) NULL,
    department VARCHAR(100) NULL,
    is_primary_contact BOOLEAN NOT NULL DEFAULT FALSE,
    can_post_jobs BOOLEAN DEFAULT TRUE,
    can_manage_applications BOOLEAN DEFAULT TRUE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    left_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    INDEX idx_user_id (user_id),
    INDEX idx_company_id (company_id),
    INDEX idx_role_type (role_type),
    INDEX idx_is_primary (is_primary_contact),
    INDEX idx_is_active (is_active),
    INDEX idx_deleted_at (deleted_at),
    UNIQUE KEY unique_primary_company (user_id, company_id, is_primary_contact, deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- JOB SEEKER PROFILE TABLES
-- ==============================================

-- Job seeker comprehensive profiles
CREATE TABLE job_seekers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED UNIQUE NOT NULL,
    bio TEXT NULL,
    date_of_birth DATE NULL,
    gender ENUM('male', 'female', 'other', 'prefer_not_to_say') NULL,
    nationality VARCHAR(100) NULL,
    current_location VARCHAR(255) NULL,
    preferred_locations JSON NULL COMMENT 'Array of preferred work locations',
    current_job_title VARCHAR(255) NULL,
    years_of_experience INT NULL,
    expected_salary_min DECIMAL(12,2) NULL,
    expected_salary_max DECIMAL(12,2) NULL,
    salary_currency VARCHAR(3) DEFAULT 'USD',
    notice_period_days INT NULL,
    available_from DATE NULL,
    languages JSON NULL COMMENT 'Array of languages with proficiency levels',
    resume_url VARCHAR(500) NULL,
    resume_uploaded_at TIMESTAMP NULL,
    linkedin_url VARCHAR(500) NULL,
    portfolio_url VARCHAR(500) NULL,
    github_url VARCHAR(500) NULL,
    profile_completion_percentage INT DEFAULT 0,
    is_profile_public BOOLEAN DEFAULT TRUE,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_location (current_location),
    INDEX idx_available (is_available),
    INDEX idx_public (is_profile_public),
    INDEX idx_experience (years_of_experience),
    INDEX idx_salary_range (expected_salary_min, expected_salary_max),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Education records
CREATE TABLE education (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_seeker_id BIGINT UNSIGNED NOT NULL,
    institution_name VARCHAR(255) NOT NULL,
    degree_type VARCHAR(100) NOT NULL,
    field_of_study VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    is_current BOOLEAN DEFAULT FALSE,
    grade VARCHAR(50) NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (job_seeker_id) REFERENCES job_seekers(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_job_seeker (job_seeker_id),
    INDEX idx_degree_type (degree_type),
    INDEX idx_is_current (is_current),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Work experience records
CREATE TABLE work_experiences (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_seeker_id BIGINT UNSIGNED NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    job_title VARCHAR(255) NOT NULL,
    employment_type ENUM('full_time', 'part_time', 'contract', 'freelance', 'internship') NOT NULL,
    location VARCHAR(255) NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    is_current BOOLEAN DEFAULT FALSE,
    description TEXT NULL,
    achievements TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (job_seeker_id) REFERENCES job_seekers(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_job_seeker (job_seeker_id),
    INDEX idx_current (is_current),
    INDEX idx_employment_type (employment_type),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Job seeker skills with proficiency tracking
CREATE TABLE job_seeker_skills (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_seeker_id BIGINT UNSIGNED NOT NULL,
    skill_id BIGINT UNSIGNED NOT NULL,
    proficiency_level ENUM('beginner', 'intermediate', 'advanced', 'expert') DEFAULT 'intermediate',
    years_of_experience INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (job_seeker_id) REFERENCES job_seekers(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    UNIQUE KEY unique_job_seeker_skill (job_seeker_id, skill_id, deleted_at),
    INDEX idx_job_seeker (job_seeker_id),
    INDEX idx_skill (skill_id),
    INDEX idx_proficiency (proficiency_level),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- JOB POSTING TABLES
-- ==============================================

-- Comprehensive job postings
CREATE TABLE jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    company_id BIGINT UNSIGNED NOT NULL,
    posted_by_user_id BIGINT UNSIGNED NOT NULL,
    industry_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NOT NULL,
    requirements TEXT NULL,
    responsibilities TEXT NULL,
    benefits TEXT NULL,
    job_type ENUM('full_time', 'part_time', 'contract', 'temporary', 'internship', 'freelance') NOT NULL,
    experience_level ENUM('entry', 'junior', 'mid', 'senior', 'lead', 'executive') NOT NULL,
    location_type ENUM('onsite', 'remote', 'hybrid') NOT NULL DEFAULT 'onsite',
    location VARCHAR(255) NULL,
    salary_min DECIMAL(12,2) NULL,
    salary_max DECIMAL(12,2) NULL,
    salary_currency VARCHAR(3) DEFAULT 'USD',
    salary_period ENUM('hourly', 'daily', 'weekly', 'monthly', 'yearly') DEFAULT 'yearly',
    is_salary_visible BOOLEAN DEFAULT TRUE,
    positions_available INT DEFAULT 1,
    application_deadline DATE NULL,
    status ENUM('draft', 'pending', 'active', 'paused', 'closed', 'expired') DEFAULT 'draft',
    views_count INT DEFAULT 0,
    applications_count INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    featured_until TIMESTAMP NULL,
    published_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (posted_by_user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (industry_id) REFERENCES industries(id) ON DELETE SET NULL ON UPDATE CASCADE,
    
    INDEX idx_uuid (uuid),
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_company (company_id),
    INDEX idx_posted_by (posted_by_user_id),
    INDEX idx_industry (industry_id),
    INDEX idx_job_type (job_type),
    INDEX idx_experience_level (experience_level),
    INDEX idx_location_type (location_type),
    INDEX idx_is_featured (is_featured),
    INDEX idx_application_deadline (application_deadline),
    INDEX idx_expires_at (expires_at),
    INDEX idx_published_at (published_at),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_salary_range (salary_min, salary_max),
    INDEX idx_location (location),
    FULLTEXT idx_search (title, description, requirements)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Job skills requirements with importance levels
CREATE TABLE job_skills (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_id BIGINT UNSIGNED NOT NULL,
    skill_id BIGINT UNSIGNED NOT NULL,
    is_required BOOLEAN DEFAULT TRUE,
    importance_level ENUM('nice_to_have', 'important', 'critical') DEFAULT 'important',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    UNIQUE KEY unique_job_skill (job_id, skill_id, deleted_at),
    INDEX idx_job (job_id),
    INDEX idx_skill (skill_id),
    INDEX idx_is_required (is_required),
    INDEX idx_importance (importance_level),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- APPLICATION MANAGEMENT TABLES
-- ==============================================

-- Comprehensive job applications
CREATE TABLE applications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    job_id BIGINT UNSIGNED NOT NULL,
    job_seeker_id BIGINT UNSIGNED NOT NULL,
    resume_url VARCHAR(500) NULL,
    cover_letter TEXT NULL,
    expected_salary DECIMAL(12,2) NULL,
    availability_date DATE NULL,
    status ENUM('submitted', 'reviewing', 'shortlisted', 'interview', 'offered', 'rejected', 'withdrawn') DEFAULT 'submitted',
    rejection_reason TEXT NULL,
    notes TEXT NULL COMMENT 'Internal notes by employer',
    reviewed_by BIGINT UNSIGNED NULL,
    viewed_at TIMESTAMP NULL,
    shortlisted_at TIMESTAMP NULL,
    interviewed_at TIMESTAMP NULL,
    offered_at TIMESTAMP NULL,
    responded_at TIMESTAMP NULL,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (job_seeker_id) REFERENCES job_seekers(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    
    UNIQUE KEY unique_application (job_id, job_seeker_id, deleted_at),
    INDEX idx_uuid (uuid),
    INDEX idx_job (job_id),
    INDEX idx_job_seeker (job_seeker_id),
    INDEX idx_status (status),
    INDEX idx_reviewed_by (reviewed_by),
    INDEX idx_applied_at (applied_at),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Application status change history
CREATE TABLE application_status_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NOT NULL,
    from_status VARCHAR(50) NULL,
    to_status VARCHAR(50) NOT NULL,
    changed_by_user_id BIGINT UNSIGNED NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (changed_by_user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_application (application_id),
    INDEX idx_changed_by (changed_by_user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Saved jobs functionality
CREATE TABLE saved_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_seeker_id BIGINT UNSIGNED NOT NULL,
    job_id BIGINT UNSIGNED NOT NULL,
    notes TEXT NULL,
    saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (job_seeker_id) REFERENCES job_seekers(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    UNIQUE KEY unique_saved_job (job_seeker_id, job_id, deleted_at),
    INDEX idx_job_seeker (job_seeker_id),
    INDEX idx_job (job_id),
    INDEX idx_saved_at (saved_at),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- HR SERVICES & SUBSCRIPTION MANAGEMENT
-- ==============================================

-- HR service packages
CREATE TABLE hr_packages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    tier ENUM('starter', 'economy', 'business', 'enterprise') NOT NULL,
    price_monthly DECIMAL(10,2) NOT NULL,
    price_yearly DECIMAL(10,2) NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    features JSON NOT NULL COMMENT 'Array of feature strings',
    max_job_posts INT NULL COMMENT 'NULL for unlimited',
    max_active_jobs INT NULL COMMENT 'NULL for unlimited',
    max_users INT NULL COMMENT 'NULL for unlimited',
    resume_database_access BOOLEAN DEFAULT FALSE,
    priority_support BOOLEAN DEFAULT FALSE,
    dedicated_account_manager BOOLEAN DEFAULT FALSE,
    custom_branding BOOLEAN DEFAULT FALSE,
    api_access BOOLEAN DEFAULT FALSE,
    analytics_access BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    is_popular BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_slug (slug),
    INDEX idx_tier (tier),
    INDEX idx_is_active (is_active),
    INDEX idx_is_popular (is_popular),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Company subscriptions
CREATE TABLE subscriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    company_id BIGINT UNSIGNED NOT NULL,
    hr_package_id BIGINT UNSIGNED NOT NULL,
    billing_period ENUM('monthly', 'yearly') NOT NULL,
    status ENUM('trial', 'active', 'past_due', 'cancelled', 'expired') NOT NULL DEFAULT 'active',
    trial_ends_at TIMESTAMP NULL,
    starts_at TIMESTAMP NOT NULL,
    ends_at TIMESTAMP NOT NULL,
    cancelled_at TIMESTAMP NULL,
    auto_renew BOOLEAN DEFAULT TRUE,
    next_billing_date DATE NULL,
    job_posts_used INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (hr_package_id) REFERENCES hr_packages(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    INDEX idx_uuid (uuid),
    INDEX idx_company (company_id),
    INDEX idx_package (hr_package_id),
    INDEX idx_status (status),
    INDEX idx_ends_at (ends_at),
    INDEX idx_next_billing (next_billing_date),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payment transactions
CREATE TABLE transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    subscription_id BIGINT UNSIGNED NULL,
    company_id BIGINT UNSIGNED NOT NULL,
    transaction_id VARCHAR(255) UNIQUE NOT NULL COMMENT 'Payment gateway transaction ID',
    type ENUM('subscription', 'addon', 'refund') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    payment_method VARCHAR(50) NULL,
    gateway ENUM('stripe', 'paypal', 'mpesa', 'bank_transfer') NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'failed', 'refunded') NOT NULL,
    gateway_response JSON NULL,
    invoice_number VARCHAR(50) NULL,
    invoice_url VARCHAR(500) NULL,
    paid_at TIMESTAMP NULL,
    failed_at TIMESTAMP NULL,
    refunded_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    INDEX idx_uuid (uuid),
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_company (company_id),
    INDEX idx_subscription (subscription_id),
    INDEX idx_status (status),
    INDEX idx_invoice (invoice_number),
    INDEX idx_paid_at (paid_at),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- COMPREHENSIVE AUDIT LOGGING SYSTEM
-- ==============================================

-- Enhanced audit logs for all database changes
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL, -- 'created', 'updated', 'deleted', 'restored', 'hard_deleted'
    table_name VARCHAR(100) NOT NULL,
    record_id BIGINT UNSIGNED NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    changed_fields JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    session_id VARCHAR(255) NULL,
    request_id VARCHAR(36) NULL,
    tags JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    
    INDEX idx_user_id (user_id),
    INDEX idx_table_name (table_name),
    INDEX idx_record_id (record_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at),
    INDEX idx_table_record (table_name, record_id),
    INDEX idx_session_id (session_id),
    INDEX idx_request_id (request_id),
    INDEX idx_user_table (user_id, table_name),
    INDEX idx_user_action (user_id, action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity logs for user actions
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    model_type VARCHAR(100) NULL,
    model_id BIGINT UNSIGNED NULL,
    description TEXT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_model (model_type, model_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Authentication logs for security tracking
CREATE TABLE authentication_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    email VARCHAR(255) NOT NULL,
    event_type ENUM('login', 'logout', 'failed_login', 'password_reset', 'email_verified', 'phone_verified') NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    session_id VARCHAR(255) NULL,
    success BOOLEAN NOT NULL DEFAULT TRUE,
    failure_reason VARCHAR(255) NULL,
    location_data JSON NULL,
    device_info JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    
    INDEX idx_user_id (user_id),
    INDEX idx_email (email),
    INDEX idx_event_type (event_type),
    INDEX idx_success (success),
    INDEX idx_created_at (created_at),
    INDEX idx_ip_address (ip_address),
    INDEX idx_user_event (user_id, event_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- COMMUNICATION & NOTIFICATION TABLES
-- ==============================================

-- In-app notifications
CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    user_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSON NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_uuid (uuid),
    INDEX idx_user (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_type (type),
    INDEX idx_created_at (created_at),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email communication logs
CREATE TABLE email_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    to_email VARCHAR(255) NOT NULL,
    subject VARCHAR(500) NOT NULL,
    template VARCHAR(100) NULL,
    status ENUM('queued', 'sent', 'failed', 'bounced') NOT NULL DEFAULT 'queued',
    sent_at TIMESTAMP NULL,
    failed_at TIMESTAMP NULL,
    error_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_to_email (to_email),
    INDEX idx_status (status),
    INDEX idx_template (template),
    INDEX idx_sent_at (sent_at),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SMS communication logs
CREATE TABLE sms_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    to_phone VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('otp', 'notification', 'marketing') NOT NULL,
    status ENUM('queued', 'sent', 'delivered', 'failed') NOT NULL DEFAULT 'queued',
    gateway_message_id VARCHAR(255) NULL,
    gateway_response JSON NULL,
    sent_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    failed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_to_phone (to_phone),
    INDEX idx_status (status),
    INDEX idx_type (type),
    INDEX idx_sent_at (sent_at),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- CMS & CONTENT MANAGEMENT TABLES
-- ==============================================

-- CMS pages for website content
CREATE TABLE cms_pages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    excerpt TEXT NULL,
    meta_title VARCHAR(255) NULL,
    meta_description TEXT NULL,
    meta_keywords TEXT NULL,
    is_published BOOLEAN DEFAULT TRUE,
    published_at TIMESTAMP NULL,
    featured_image_url VARCHAR(500) NULL,
    template VARCHAR(100) NULL,
    created_by_user_id BIGINT UNSIGNED NULL,
    updated_by_user_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (updated_by_user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    
    INDEX idx_slug (slug),
    INDEX idx_is_published (is_published),
    INDEX idx_published_at (published_at),
    INDEX idx_created_by (created_by_user_id),
    INDEX idx_deleted_at (deleted_at),
    FULLTEXT idx_content_search (title, content, excerpt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FAQ management
CREATE TABLE faqs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    is_published BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_category (category),
    INDEX idx_is_published (is_published),
    INDEX idx_sort_order (sort_order),
    INDEX idx_deleted_at (deleted_at),
    FULLTEXT idx_faq_search (question, answer)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- System settings
CREATE TABLE site_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NULL,
    setting_type ENUM('string', 'number', 'boolean', 'json', 'text') DEFAULT 'string',
    category VARCHAR(100) NOT NULL DEFAULT 'general',
    description TEXT NULL,
    is_public BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_setting_key (setting_key),
    INDEX idx_category (category),
    INDEX idx_is_public (is_public)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- ANALYTICS & TRACKING TABLES
-- ==============================================

-- Job view tracking for analytics
CREATE TABLE job_views (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    referrer VARCHAR(500) NULL,
    session_id VARCHAR(255) NULL,
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    
    INDEX idx_job (job_id),
    INDEX idx_user (user_id),
    INDEX idx_viewed_at (viewed_at),
    INDEX idx_ip_address (ip_address),
    INDEX idx_session_id (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Search query analytics
CREATE TABLE search_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    search_query VARCHAR(500) NOT NULL,
    filters JSON NULL,
    results_count INT DEFAULT 0,
    clicked_result_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    session_id VARCHAR(255) NULL,
    searched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_searched_at (searched_at),
    INDEX idx_results_count (results_count),
    INDEX idx_session_id (session_id),
    FULLTEXT idx_search_query (search_query)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Media files management
CREATE TABLE media_files (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    user_id BIGINT UNSIGNED NULL,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    mime_type VARCHAR(255) NOT NULL,
    size BIGINT NOT NULL,
    path VARCHAR(500) NOT NULL,
    disk VARCHAR(50) NOT NULL DEFAULT 'local',
    is_public BOOLEAN NOT NULL DEFAULT FALSE,
    metadata JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    
    INDEX idx_uuid (uuid),
    INDEX idx_user_id (user_id),
    INDEX idx_mime_type (mime_type),
    INDEX idx_is_public (is_public),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_filename (filename)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- PERFORMANCE OPTIMIZATION VIEWS
-- ==============================================

-- Active users with their roles
CREATE VIEW v_active_users_with_roles AS
SELECT 
    u.id,
    u.uuid,
    u.email,
    u.first_name,
    u.last_name,
    u.is_active,
    u.last_login_at,
    GROUP_CONCAT(r.name ORDER BY r.level DESC SEPARATOR ', ') as roles,
    GROUP_CONCAT(r.display_name ORDER BY r.level DESC SEPARATOR ', ') as role_display_names,
    MAX(r.level) as highest_role_level
FROM users u
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id AND mhr.is_active = TRUE
LEFT JOIN roles r ON mhr.role_id = r.id AND r.deleted_at IS NULL
WHERE u.deleted_at IS NULL AND u.is_active = TRUE
GROUP BY u.id, u.uuid, u.email, u.first_name, u.last_name, u.is_active, u.last_login_at;

-- Active jobs with company info
CREATE VIEW v_active_jobs_with_company AS
SELECT 
    j.id,
    j.uuid,
    j.title,
    j.slug,
    j.job_type,
    j.experience_level,
    j.salary_min,
    j.salary_max,
    j.location_type,
    j.location,
    j.is_featured,
    j.views_count,
    j.applications_count,
    j.published_at,
    j.expires_at,
    c.name as company_name,
    c.slug as company_slug,
    c.logo_url as company_logo,
    c.is_verified as company_verified,
    CONCAT(u.first_name, ' ', u.last_name) as posted_by_name,
    i.name as industry_name
FROM jobs j
JOIN companies c ON j.company_id = c.id
JOIN users u ON j.posted_by_user_id = u.id
LEFT JOIN industries i ON j.industry_id = i.id
WHERE j.deleted_at IS NULL 
    AND j.status = 'active'
    AND c.deleted_at IS NULL 
    AND c.is_active = TRUE
    AND u.deleted_at IS NULL;

-- Job seeker profiles with completion stats
CREATE VIEW v_job_seeker_profiles AS
SELECT 
    js.id,
    js.user_id,
    CONCAT(u.first_name, ' ', u.last_name) as full_name,
    u.email,
    js.current_job_title,
    js.years_of_experience,
    js.current_location,
    js.expected_salary_min,
    js.expected_salary_max,
    js.profile_completion_percentage,
    js.is_available,
    COUNT(DISTINCT e.id) as education_count,
    COUNT(DISTINCT we.id) as experience_count,
    COUNT(DISTINCT jss.id) as skills_count
FROM job_seekers js
JOIN users u ON js.user_id = u.id
LEFT JOIN education e ON js.id = e.job_seeker_id AND e.deleted_at IS NULL
LEFT JOIN work_experiences we ON js.id = we.job_seeker_id AND we.deleted_at IS NULL
LEFT JOIN job_seeker_skills jss ON js.id = jss.job_seeker_id AND jss.deleted_at IS NULL
WHERE js.deleted_at IS NULL 
    AND u.deleted_at IS NULL 
    AND js.is_profile_public = TRUE
GROUP BY js.id, js.user_id, u.first_name, u.last_name, u.email, 
         js.current_job_title, js.years_of_experience, js.current_location,
         js.expected_salary_min, js.expected_salary_max, 
         js.profile_completion_percentage, js.is_available;

-- ==============================================
-- STORED PROCEDURES FOR BUSINESS LOGIC
-- ==============================================

-- Procedure to assign role with validation
DELIMITER //
CREATE PROCEDURE AssignRoleToUser(
    IN p_user_id BIGINT UNSIGNED,
    IN p_role_name VARCHAR(125),
    IN p_assigned_by BIGINT UNSIGNED,
    IN p_expires_at TIMESTAMP
)
BEGIN
    DECLARE v_role_id BIGINT UNSIGNED;
    DECLARE v_exists INT DEFAULT 0;
    
    -- Get role ID
    SELECT id INTO v_role_id FROM roles WHERE name = p_role_name AND deleted_at IS NULL LIMIT 1;
    
    IF v_role_id IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Role not found or is deleted';
    END IF;
    
    -- Check if assignment already exists
    SELECT COUNT(*) INTO v_exists 
    FROM model_has_roles 
    WHERE role_id = v_role_id AND model_id = p_user_id AND is_active = TRUE;
    
    IF v_exists = 0 THEN
        INSERT INTO model_has_roles (role_id, model_id, assigned_by, expires_at)
        VALUES (v_role_id, p_user_id, p_assigned_by, p_expires_at);
    ELSE
        UPDATE model_has_roles 
        SET expires_at = p_expires_at, assigned_by = p_assigned_by, assigned_at = NOW()
        WHERE role_id = v_role_id AND model_id = p_user_id;
    END IF;
END//

-- Procedure to get user permissions
CREATE PROCEDURE GetUserPermissions(IN p_user_id BIGINT UNSIGNED)
BEGIN
    SELECT DISTINCT p.name, p.display_name, p.category,
           CASE 
               WHEN mhp.permission_id IS NOT NULL THEN 'direct'
               ELSE 'role-based'
           END as assignment_type
    FROM permissions p
    LEFT JOIN model_has_permissions mhp ON p.id = mhp.permission_id 
        AND mhp.model_id = p_user_id 
        AND mhp.is_active = TRUE
        AND (mhp.expires_at IS NULL OR mhp.expires_at > NOW())
    LEFT JOIN role_has_permissions rhp ON p.id = rhp.permission_id
    LEFT JOIN model_has_roles mhr ON rhp.role_id = mhr.role_id 
        AND mhr.model_id = p_user_id 
        AND mhr.is_active = TRUE
        AND (mhr.expires_at IS NULL OR mhr.expires_at > NOW())
    WHERE (mhp.permission_id IS NOT NULL OR mhr.role_id IS NOT NULL)
        AND p.deleted_at IS NULL
    ORDER BY p.category, p.name;
END//

-- Procedure for safe hard delete (Super Admin only)
CREATE PROCEDURE HardDeleteRecord(
    IN p_table_name VARCHAR(100),
    IN p_record_id BIGINT UNSIGNED,
    IN p_user_id BIGINT UNSIGNED
)
BEGIN
    DECLARE v_is_super_admin INT DEFAULT 0;
    
    -- Verify user is super admin
    SELECT COUNT(*) INTO v_is_super_admin
    FROM model_has_roles mhr
    JOIN roles r ON mhr.role_id = r.id
    WHERE mhr.model_id = p_user_id 
        AND r.name = 'super-admin' 
        AND mhr.is_active = TRUE
        AND (mhr.expires_at IS NULL OR mhr.expires_at > NOW());
    
    IF v_is_super_admin = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Only Super Administrators can perform hard deletes';
    END IF;
    
    -- Set audit context
    SET @audit_user_id = p_user_id;
    
    -- Perform hard delete based on table
    CASE p_table_name
        WHEN 'users' THEN
            DELETE FROM users WHERE id = p_record_id;
        WHEN 'companies' THEN
            DELETE FROM companies WHERE id = p_record_id;
        WHEN 'jobs' THEN
            DELETE FROM jobs WHERE id = p_record_id;
        WHEN 'applications' THEN
            DELETE FROM applications WHERE id = p_record_id;
        WHEN 'job_seekers' THEN
            DELETE FROM job_seekers WHERE id = p_record_id;
        ELSE
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Table not allowed for hard delete';
    END CASE;
END//

-- Update job application count when application status changes
CREATE PROCEDURE UpdateJobApplicationCount(IN p_job_id BIGINT UNSIGNED)
BEGIN
    UPDATE jobs 
    SET applications_count = (
        SELECT COUNT(*) 
        FROM applications 
        WHERE job_id = p_job_id AND deleted_at IS NULL
    )
    WHERE id = p_job_id;
END//

-- Update job views count
CREATE PROCEDURE IncrementJobViewCount(IN p_job_id BIGINT UNSIGNED)
BEGIN
    UPDATE jobs 
    SET views_count = views_count + 1 
    WHERE id = p_job_id AND deleted_at IS NULL;
END//
DELIMITER ;

-- ==============================================
-- DATABASE TRIGGERS FOR AUDIT LOGGING
-- ==============================================

-- User audit triggers
DELIMITER //
CREATE TRIGGER users_audit_insert AFTER INSERT ON users
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (user_id, action, table_name, record_id, new_values)
    VALUES (NEW.id, 'created', 'users', NEW.id, JSON_OBJECT(
        'id', NEW.id,
        'email', NEW.email,
        'first_name', NEW.first_name,
        'last_name', NEW.last_name,
        'is_active', NEW.is_active
    ));
END//

CREATE TRIGGER users_audit_update AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    DECLARE changed_fields JSON DEFAULT JSON_ARRAY();
    
    IF OLD.email != NEW.email THEN
        SET changed_fields = JSON_ARRAY_APPEND(changed_fields, ', 'email');
    END IF;
    IF OLD.first_name != NEW.first_name THEN
        SET changed_fields = JSON_ARRAY_APPEND(changed_fields, ', 'first_name');
    END IF;
    IF OLD.last_name != NEW.last_name THEN
        SET changed_fields = JSON_ARRAY_APPEND(changed_fields, ', 'last_name');
    END IF;
    IF OLD.is_active != NEW.is_active THEN
        SET changed_fields = JSON_ARRAY_APPEND(changed_fields, ', 'is_active');
    END IF;
    IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
        SET changed_fields = JSON_ARRAY_APPEND(changed_fields, ', 'deleted_at');
    END IF;
    
    IF JSON_LENGTH(changed_fields) > 0 THEN
        INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values, changed_fields)
        VALUES (@audit_user_id, 
                CASE 
                    WHEN OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN 'deleted'
                    WHEN OLD.deleted_at IS NOT NULL AND NEW.deleted_at IS NULL THEN 'restored'
                    ELSE 'updated'
                END, 
                'users', NEW.id,
                JSON_OBJECT(
                    'email', OLD.email,
                    'first_name', OLD.first_name,
                    'last_name', OLD.last_name,
                    'is_active', OLD.is_active
                ),
                JSON_OBJECT(
                    'email', NEW.email,
                    'first_name', NEW.first_name,
                    'last_name', NEW.last_name,
                    'is_active', NEW.is_active
                ),
                changed_fields);
    END IF;
END//

-- Application count update trigger
CREATE TRIGGER applications_after_insert AFTER INSERT ON applications
FOR EACH ROW
BEGIN
    CALL UpdateJobApplicationCount(NEW.job_id);
END//

CREATE TRIGGER applications_after_delete AFTER DELETE ON applications
FOR EACH ROW
BEGIN
    CALL UpdateJobApplicationCount(OLD.job_id);
END//

-- Job view trigger
CREATE TRIGGER job_views_after_insert AFTER INSERT ON job_views
FOR EACH ROW
BEGIN
    CALL IncrementJobViewCount(NEW.job_id);
END//
DELIMITER ;

-- ==============================================
-- INITIAL DATA INSERTIONS
-- ==============================================

-- Insert system roles
INSERT INTO roles (name, guard_name, display_name, description, level, is_system, color) VALUES
('super-admin', 'web', 'Super Administrator', 'Full system access with all permissions', 100, TRUE, '#dc2626'),
('admin', 'web', 'Administrator', 'Administrative access with some restrictions', 90, TRUE, '#ea580c'),
('hr-manager', 'web', 'HR Manager', 'Extended employer permissions with HR features', 70, TRUE, '#7c3aed'),
('employer', 'web', 'Employer', 'Can post jobs and manage applications', 60, TRUE, '#059669'),
('job-seeker', 'web', 'Job Seeker', 'Can apply for jobs and manage profile', 30, TRUE, '#0ea5e9'),
('guest', 'web', 'Guest', 'Minimal read-only access', 10, TRUE, '#6b7280');

-- Insert comprehensive permissions
INSERT INTO permissions (name, guard_name, display_name, description, category, is_system) VALUES
-- User Management
('users.view', 'web', 'View Users', 'View user profiles and listings', 'users', TRUE),
('users.create', 'web', 'Create Users', 'Create new user accounts', 'users', TRUE),
('users.edit', 'web', 'Edit Users', 'Edit user profiles and information', 'users', TRUE),
('users.delete', 'web', 'Delete Users', 'Soft delete user accounts', 'users', TRUE),
('users.force-delete', 'web', 'Permanently Delete Users', 'Permanently delete user accounts', 'users', TRUE),
('users.restore', 'web', 'Restore Users', 'Restore soft-deleted users', 'users', TRUE),
('users.impersonate', 'web', 'Impersonate Users', 'Login as another user', 'users', TRUE),

-- Role & Permission Management
('roles.view', 'web', 'View Roles', 'View roles and permissions', 'authorization', TRUE),
('roles.create', 'web', 'Create Roles', 'Create new roles', 'authorization', TRUE),
('roles.edit', 'web', 'Edit Roles', 'Edit existing roles', 'authorization', TRUE),
('roles.delete', 'web', 'Delete Roles', 'Delete roles', 'authorization', TRUE),
('roles.assign', 'web', 'Assign Roles', 'Assign roles to users', 'authorization', TRUE),
('permissions.view', 'web', 'View Permissions', 'View permissions', 'authorization', TRUE),
('permissions.create', 'web', 'Create Permissions', 'Create new permissions', 'authorization', TRUE),
('permissions.edit', 'web', 'Edit Permissions', 'Edit existing permissions', 'authorization', TRUE),
('permissions.delete', 'web', 'Delete Permissions', 'Delete permissions', 'authorization', TRUE),
('permissions.assign', 'web', 'Assign Permissions', 'Assign permissions to users/roles', 'authorization', TRUE),

-- Company Management
('companies.view', 'web', 'View Companies', 'View company profiles', 'companies', TRUE),
('companies.create', 'web', 'Create Companies', 'Create new company profiles', 'companies', TRUE),
('companies.edit', 'web', 'Edit Companies', 'Edit company information', 'companies', TRUE),
('companies.delete', 'web', 'Delete Companies', 'Soft delete companies', 'companies', TRUE),
('companies.verify', 'web', 'Verify Companies', 'Verify company authenticity', 'companies', TRUE),
('companies.manage-own', 'web', 'Manage Own Company', 'Manage own company profile', 'companies', TRUE),

-- Job Management
('jobs.view', 'web', 'View Jobs', 'View all job listings', 'jobs', TRUE),
('jobs.view-own', 'web', 'View Own Jobs', 'View own job listings', 'jobs', TRUE),
('jobs.create', 'web', 'Create Jobs', 'Create new job listings', 'jobs', TRUE),
('jobs.edit', 'web', 'Edit Jobs', 'Edit all job listings', 'jobs', TRUE),
('jobs.edit-own', 'web', 'Edit Own Jobs', 'Edit own job listings', 'jobs', TRUE),
('jobs.delete', 'web', 'Delete Jobs', 'Soft delete job listings', 'jobs', TRUE),
('jobs.delete-own', 'web', 'Delete Own Jobs', 'Delete own job listings', 'jobs', TRUE),
('jobs.feature', 'web', 'Feature Jobs', 'Mark jobs as featured', 'jobs', TRUE),
('jobs.publish', 'web', 'Publish Jobs', 'Publish job listings', 'jobs', TRUE),

-- Application Management
('applications.view', 'web', 'View Applications', 'View all job applications', 'applications', TRUE),
('applications.view-own', 'web', 'View Own Applications', 'View own job applications', 'applications', TRUE),
('applications.create', 'web', 'Apply for Jobs', 'Submit job applications', 'applications', TRUE),
('applications.edit', 'web', 'Edit Applications', 'Edit job applications', 'applications', TRUE),
('applications.edit-own', 'web', 'Edit Own Applications', 'Edit own applications', 'applications', TRUE),
('applications.delete', 'web', 'Delete Applications', 'Delete applications', 'applications', TRUE),
('applications.review', 'web', 'Review Applications', 'Review and manage applications', 'applications', TRUE),
('applications.shortlist', 'web', 'Shortlist Applications', 'Shortlist candidates', 'applications', TRUE),
('applications.reject', 'web', 'Reject Applications', 'Reject candidate applications', 'applications', TRUE),
('applications.download-resumes', 'web', 'Download Resumes', 'Download candidate resumes', 'applications', TRUE),

-- Job Seeker Profiles
('profiles.view', 'web', 'View Profiles', 'View job seeker profiles', 'profiles', TRUE),
('profiles.edit-own', 'web', 'Edit Own Profile', 'Edit own profile', 'profiles', TRUE),
('profiles.delete-own', 'web', 'Delete Own Profile', 'Delete own profile', 'profiles', TRUE),
('profiles.export', 'web', 'Export Profiles', 'Export job seeker profiles', 'profiles', TRUE),

-- HR Services & Subscriptions
('hr-packages.view', 'web', 'View HR Packages', 'View HR service packages', 'hr-services', TRUE),
('hr-packages.create', 'web', 'Create HR Packages', 'Create HR packages', 'hr-services', TRUE),
('hr-packages.edit', 'web', 'Edit HR Packages', 'Edit HR packages', 'hr-services', TRUE),
('hr-packages.delete', 'web', 'Delete HR Packages', 'Delete HR packages', 'hr-services', TRUE),
('subscriptions.view', 'web', 'View Subscriptions', 'View all subscriptions', 'hr-services', TRUE),
('subscriptions.create', 'web', 'Subscribe to Services', 'Subscribe to HR services', 'hr-services', TRUE),
('subscriptions.manage', 'web', 'Manage Subscriptions', 'Manage subscription settings', 'hr-services', TRUE),
('subscriptions.cancel', 'web', 'Cancel Subscriptions', 'Cancel subscriptions', 'hr-services', TRUE),

-- Financial Management
('transactions.view', 'web', 'View Transactions', 'View payment transactions', 'financial', TRUE),
('transactions.manage', 'web', 'Manage Payments', 'Process payments and refunds', 'financial', TRUE),
('invoices.view', 'web', 'View Invoices', 'View invoices', 'financial', TRUE),
('invoices.generate', 'web', 'Generate Invoices', 'Generate invoices', 'financial', TRUE),
('reports.financial', 'web', 'Financial Reports', 'Generate financial reports', 'financial', TRUE),

-- CMS Management
('cms.pages.view', 'web', 'View CMS Pages', 'View CMS pages', 'cms', TRUE),
('cms.pages.create', 'web', 'Create CMS Pages', 'Create new CMS pages', 'cms', TRUE),
('cms.pages.edit', 'web', 'Edit CMS Pages', 'Edit CMS pages', 'cms', TRUE),
('cms.pages.delete', 'web', 'Delete CMS Pages', 'Delete CMS pages', 'cms', TRUE),
('cms.faqs.manage', 'web', 'Manage FAQs', 'Manage FAQ content', 'cms', TRUE),

-- System Administration
('system.settings', 'web', 'System Settings', 'Manage system configuration', 'system', TRUE),
('system.maintenance', 'web', 'System Maintenance', 'Put system in maintenance mode', 'system', TRUE),
('system.logs', 'web', 'View System Logs', 'View system and error logs', 'system', TRUE),
('audit.view', 'web', 'View Audit Logs', 'View audit trail and logs', 'audit', TRUE),
('audit.export', 'web', 'Export Audit Logs', 'Export audit logs', 'audit', TRUE),

-- Analytics & Reporting
('analytics.view', 'web', 'View Analytics', 'View system analytics', 'analytics', TRUE),
('analytics.company', 'web', 'View Company Analytics', 'View company-specific analytics', 'analytics', TRUE),
('analytics.export', 'web', 'Export Analytics', 'Export analytics data', 'analytics', TRUE),
('reports.generate', 'web', 'Generate Reports', 'Generate system reports', 'analytics', TRUE),

-- Media Management
('media.view', 'web', 'View Media', 'View uploaded files and media', 'media', TRUE),
('media.upload', 'web', 'Upload Media', 'Upload files and media', 'media', TRUE),
('media.delete', 'web', 'Delete Media', 'Delete uploaded media', 'media', TRUE),

-- Communication
('notifications.send', 'web', 'Send Notifications', 'Send notifications to users', 'communication', TRUE),
('emails.send', 'web', 'Send Emails', 'Send email communications', 'communication', TRUE),
('sms.send', 'web', 'Send SMS', 'Send SMS messages', 'communication', TRUE);

-- Assign permissions to roles
-- Super Admin gets ALL permissions
INSERT INTO role_has_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p WHERE r.name = 'super-admin';

-- Admin role permissions
INSERT INTO role_has_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p 
WHERE r.name = 'admin' AND p.name IN (
    'users.view', 'users.create', 'users.edit', 'users.delete',
    'companies.view', 'companies.edit', 'companies.verify',
    'jobs.view', 'jobs.edit', 'jobs.publish', 'jobs.feature',
    'applications.view', 'applications.review',
    'profiles.view', 'profiles.export',
    'hr-packages.view', 'hr-packages.edit',
    'subscriptions.view', 'subscriptions.manage',
    'transactions.view', 'invoices.view', 'reports.financial',
    'cms.pages.view', 'cms.pages.edit', 'cms.faqs.manage',
    'system.logs', 'audit.view', 'audit.export',
    'analytics.view', 'analytics.export', 'reports.generate',
    'media.view', 'media.upload'
);

-- HR Manager role permissions
INSERT INTO role_has_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p 
WHERE r.name = 'hr-manager' AND p.name IN (
    'users.view',
    'companies.view', 'companies.manage-own',
    'jobs.view', 'jobs.create', 'jobs.edit-own', 'jobs.delete-own', 'jobs.publish',
    'applications.view', 'applications.review', 'applications.shortlist', 'applications.reject', 'applications.download-resumes',
    'profiles.view', 'profiles.edit-own', 'profiles.export',
    'subscriptions.view', 'subscriptions.manage',
    'analytics.company', 'reports.generate',
    'media.view', 'media.upload', 'media.delete',
    'notifications.send', 'emails.send'
);

-- Employer role permissions
INSERT INTO role_has_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p 
WHERE r.name = 'employer' AND p.name IN (
    'companies.view', 'companies.manage-own',
    'jobs.view-own', 'jobs.create', 'jobs.edit-own', 'jobs.delete-own', 'jobs.publish',
    'applications.view', 'applications.review', 'applications.shortlist', 'applications.reject', 'applications.download-resumes',
    'profiles.view', 'profiles.edit-own',
    'subscriptions.create', 'subscriptions.manage',
    'invoices.view',
    'analytics.company',
    'media.view', 'media.upload'
);

-- Job Seeker role permissions
INSERT INTO role_has_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p 
WHERE r.name = 'job-seeker' AND p.name IN (
    'jobs.view',
    'companies.view',
    'applications.create', 'applications.view-own', 'applications.edit-own',
    'profiles.edit-own', 'profiles.delete-own',
    'media.view', 'media.upload'
);

-- Guest role permissions
INSERT INTO role_has_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p 
WHERE r.name = 'guest' AND p.name IN (
    'jobs.view',
    'companies.view'
);

-- Insert default industries
INSERT INTO industries (name, slug, parent_id, sort_order) VALUES
('Technology', 'technology', NULL, 1),
('Healthcare', 'healthcare', NULL, 2),
('Finance & Banking', 'finance-banking', NULL, 3),
('Education', 'education', NULL, 4),
('Manufacturing', 'manufacturing', NULL, 5),
('Retail & E-commerce', 'retail-ecommerce', NULL, 6),
('Hospitality & Tourism', 'hospitality-tourism', NULL, 7),
('Construction & Real Estate', 'construction-real-estate', NULL, 8),
('Transportation & Logistics', 'transportation-logistics', NULL, 9),
('Agriculture & Food', 'agriculture-food', NULL, 10),
('Media & Communications', 'media-communications', NULL, 11),
('Legal Services', 'legal-services', NULL, 12),
('Consulting', 'consulting', NULL, 13),
('Non-Profit', 'non-profit', NULL, 14),
('Government', 'government', NULL, 15);

-- Insert technology sub-industries
INSERT INTO industries (name, slug, parent_id, sort_order) VALUES
('Software Development', 'software-development', 1, 1),
('Artificial Intelligence', 'artificial-intelligence', 1, 2),
('Cybersecurity', 'cybersecurity', 1, 3),
('Data Science', 'data-science', 1, 4),
('DevOps', 'devops', 1, 5);

-- Insert default skills
INSERT INTO skills (name, slug, category) VALUES
-- Programming Languages
('JavaScript', 'javascript', 'Programming'),
('Python', 'python', 'Programming'),
('Java', 'java', 'Programming'),
('PHP', 'php', 'Programming'),
('C#', 'csharp', 'Programming'),
('Ruby', 'ruby', 'Programming'),
('Go', 'golang', 'Programming'),
('Swift', 'swift', 'Programming'),
('Kotlin', 'kotlin', 'Programming'),
('TypeScript', 'typescript', 'Programming'),

-- Frameworks & Libraries
('React', 'react', 'Frontend Framework'),
('Vue.js', 'vuejs', 'Frontend Framework'),
('Angular', 'angular', 'Frontend Framework'),
('Laravel', 'laravel', 'Backend Framework'),
('Django', 'django', 'Backend Framework'),
('Node.js', 'nodejs', 'Backend Framework'),
('Express.js', 'expressjs', 'Backend Framework'),
('Spring Boot', 'spring-boot', 'Backend Framework'),

-- Databases
('MySQL', 'mysql', 'Database'),
('PostgreSQL', 'postgresql', 'Database'),
('MongoDB', 'mongodb', 'Database'),
('Redis', 'redis', 'Database'),
('SQLite', 'sqlite', 'Database'),

-- Cloud & DevOps
('AWS', 'aws', 'Cloud Platform'),
('Google Cloud', 'google-cloud', 'Cloud Platform'),
('Azure', 'azure', 'Cloud Platform'),
('Docker', 'docker', 'DevOps'),
('Kubernetes', 'kubernetes', 'DevOps'),
('Jenkins', 'jenkins', 'DevOps'),

-- Soft Skills
('Project Management', 'project-management', 'Management'),
('Leadership', 'leadership', 'Management'),
('Communication', 'communication', 'Soft Skills'),
('Problem Solving', 'problem-solving', 'Soft Skills'),
('Team Collaboration', 'team-collaboration', 'Soft Skills'),
('Critical Thinking', 'critical-thinking', 'Soft Skills'),

-- Design
('UI/UX Design', 'ui-ux-design', 'Design'),
('Graphic Design', 'graphic-design', 'Design'),
('Adobe Photoshop', 'photoshop', 'Design Tools'),
('Figma', 'figma', 'Design Tools');

-- Insert HR packages
INSERT INTO hr_packages (name, slug, tier, price_monthly, price_yearly, features, max_job_posts, max_active_jobs, max_users) VALUES
('Starter Package', 'starter', 'starter', 99.00, 990.00, 
JSON_ARRAY('5 job posts per month', 'Basic applicant tracking', 'Email support', 'Standard job visibility', 'Basic analytics'), 
5, 3, 1),

('Economy Package', 'economy', 'economy', 299.00, 2990.00,
JSON_ARRAY('20 job posts per month', 'Advanced applicant tracking', 'Priority email support', 'Featured job posts', 'Advanced analytics', 'Resume database access'),
20, 10, 3),

('Business Package', 'business', 'business', 799.00, 7990.00,
JSON_ARRAY('Unlimited job posts', 'Full applicant tracking', 'Phone & email support', 'Premium featured posts', 'Advanced analytics', 'API access', 'Custom branding', 'Priority support'),
NULL, NULL, 10),

('Enterprise Package', 'enterprise', 'enterprise', 1999.00, 19990.00,
JSON_ARRAY('Unlimited job posts', 'Enterprise applicant tracking', '24/7 dedicated support', 'Premium featured posts', 'Advanced analytics & reporting', 'Full API access', 'Custom branding', 'Dedicated account manager', 'Custom integrations'),
NULL, NULL, NULL);

-- Insert default CMS pages
INSERT INTO cms_pages (slug, title, content, meta_title, meta_description) VALUES
('privacy-policy', 'Privacy Policy', 
'<h1>Privacy Policy</h1><p>This privacy policy explains how we collect, use, and protect your personal information...</p>', 
'Privacy Policy - HR Talent Management', 
'Learn about our privacy practices and how we protect your personal information.'),

('terms-of-service', 'Terms of Service', 
'<h1>Terms of Service</h1><p>These terms of service govern your use of our platform...</p>', 
'Terms of Service - HR Talent Management', 
'Terms and conditions for using our HR talent management platform.'),

('about-us', 'About Us', 
'<h1>About Us</h1><p>We are a leading HR outsourcing and talent management company...</p>', 
'About Us - HR Talent Management', 
'Learn more about our company, mission, and values.'),

('contact', 'Contact Us', 
'<h1>Contact Us</h1><p>Get in touch with our team for support or inquiries...</p>', 
'Contact Us - HR Talent Management', 
'Contact our team for support, inquiries, or partnership opportunities.');

-- Insert FAQs
INSERT INTO faqs (category, question, answer, sort_order) VALUES
('General', 'How do I create an account?', 'You can create an account by clicking the "Sign Up" button and filling out the registration form with your basic information.', 1),
('General', 'Is the platform free to use?', 'Job seekers can use the platform for free. Employers need to subscribe to one of our HR packages to post jobs and access advanced features.', 2),
('Job Seekers', 'How do I apply for a job?', 'Browse available jobs, click on the job you are interested in, and click the "Apply" button. You will need to submit your resume and cover letter.', 1),
('Job Seekers', 'Can I save jobs for later?', 'Yes, you can save jobs by clicking the "Save" button on any job listing. You can view your saved jobs in your profile dashboard.', 2),
('Employers', 'How do I post a job?', 'Subscribe to one of our HR packages, then go to your employer dashboard and click "Post New Job". Fill out the job details and publish.', 1),
('Employers', 'How can I manage applications?', 'Go to your employer dashboard where you can view all applications for your jobs, shortlist candidates, and manage the hiring process.', 2),
('Billing', 'What payment methods do you accept?', 'We accept major credit cards, PayPal, M-Pesa, and bank transfers depending on your location.', 1),
('Billing', 'Can I cancel my subscription anytime?', 'Yes, you can cancel your subscription at any time from your account settings. Your access will continue until the end of your billing period.', 2);

-- Insert system settings
INSERT INTO site_settings (setting_key, setting_value, setting_type, category, description, is_public) VALUES
('app_name', 'HR Talent Management', 'string', 'general', 'Application name', TRUE),
('app_tagline', 'Your Premier HR Outsourcing Partner', 'string', 'general', 'Application tagline', TRUE),
('app_timezone', 'UTC', 'string', 'general', 'Default application timezone', FALSE),
('contact_email', 'contact@hrtalent.com', 'string', 'contact', 'Primary contact email', TRUE),
('support_email', 'support@hrtalent.com', 'string', 'contact', 'Support email address', TRUE),
('contact_phone', '+1-555-0123', 'string', 'contact', 'Primary contact phone', TRUE),
('max_login_attempts', '5', 'number', 'security', 'Maximum login attempts before lockout', FALSE),
('lockout_duration', '900', 'number', 'security', 'Lockout duration in seconds (15 minutes)', FALSE),
('jobs_auto_expire_days', '30', 'number', 'jobs', 'Days after which jobs auto-expire', FALSE),
('max_applications_per_job', '1', 'number', 'applications', 'Maximum applications per job per user', TRUE),
('max_file_upload_size', '10485760', 'number', 'media', 'Maximum file upload size in bytes (10MB)', FALSE),
('allowed_file_types', '["pdf","doc","docx","jpg","jpeg","png"]', 'json', 'media', 'Allowed file types for uploads', FALSE),
('audit_retention_days', '365', 'number', 'audit', 'Days to retain audit logs', FALSE),
('email_verification_required', 'true', 'boolean', 'authentication', 'Require email verification for new accounts', FALSE),
('phone_verification_required', 'false', 'boolean', 'authentication', 'Require phone verification for new accounts', FALSE),
('featured_job_duration_days', '30', 'number', 'jobs', 'Default duration for featured jobs in days', FALSE),
('company_verification_required', 'true', 'boolean', 'companies', 'Require admin verification for new companies', FALSE),
('default_currency', 'USD', 'string', 'financial', 'Default currency for the platform', TRUE),
('maintenance_mode', 'false', 'boolean', 'system', 'Enable maintenance mode', FALSE),
('registration_enabled', 'true', 'boolean', 'authentication', 'Allow new user registrations', TRUE);

-- Create sample users for testing
INSERT INTO users (email, phone, password, first_name, last_name, email_verified_at, is_active) VALUES
('admin@hrtalent.com', '+1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Administrator', NOW(), TRUE),
('hr@techcorp.com', '+1234567891', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Johnson', NOW(), TRUE),
('john@techcorp.com', '+1234567892', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Smith', NOW(), TRUE),
('jane.seeker@email.com', '+1234567893', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Doe', NOW(), TRUE),
('mike.both@email.com', '+1234567894', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike', 'Wilson', NOW(), TRUE);

-- Assign roles to sample users
INSERT INTO model_has_roles (role_id, model_id, assigned_by) VALUES
((SELECT id FROM roles WHERE name = 'super-admin'), 1, 1),
((SELECT id FROM roles WHERE name = 'hr-manager'), 2, 1),
((SELECT id FROM roles WHERE name = 'employer'), 3, 1),
((SELECT id FROM roles WHERE name = 'job-seeker'), 4, 1),
-- User 5 has both employer and job-seeker roles (mixed roles example)
((SELECT id FROM roles WHERE name = 'employer'), 5, 1),
((SELECT id FROM roles WHERE name = 'job-seeker'), 5, 1);

-- Create sample companies
INSERT INTO companies (name, slug, website, email, phone, description, industry_id, company_size, founded_year, headquarters_location, is_verified, created_by) VALUES
('TechCorp Solutions', 'techcorp-solutions', 'https://techcorp.com', 'contact@techcorp.com', '+1555123456', 
'Leading technology solutions provider specializing in enterprise software development and digital transformation.', 
1, '201-500', 2015, 'San Francisco, CA', TRUE, 2),

('StartupX Innovation', 'startupx-innovation', 'https://startupx.com', 'hello@startupx.com', '+1555123457', 
'Innovative startup focused on AI-powered solutions for modern businesses.', 
2, '11-50', 2020, 'Austin, TX', FALSE, 5);

-- Link users to companies
INSERT INTO user_company_roles (user_id, company_id, role_type, job_title, is_primary_contact, can_post_jobs, can_manage_applications) VALUES
(2, 1, 'hr_manager', 'Senior HR Manager', TRUE, TRUE, TRUE),
(3, 1, 'recruiter', 'Technical Recruiter', FALSE, TRUE, TRUE),
(5, 2, 'owner', 'Founder & CEO', TRUE, TRUE, TRUE);

-- Create job seeker profiles
INSERT INTO job_seekers (user_id, bio, current_job_title, years_of_experience, expected_salary_min, expected_salary_max, current_location, is_available, profile_completion_percentage) VALUES
(4, 'Passionate software developer with expertise in full-stack development. Love working with modern technologies and solving complex problems.', 
'Software Developer', 3, 70000.00, 90000.00, 'New York, NY', TRUE, 85),

(5, 'Experienced entrepreneur and technical leader looking for new opportunities in the tech industry.', 
'Founder & CTO', 8, 150000.00, 200000.00, 'Austin, TX', TRUE, 90);

-- Add education records
INSERT INTO education (job_seeker_id, institution_name, degree_type, field_of_study, start_date, end_date, grade) VALUES
(1, 'University of California, Berkeley', 'Bachelor of Science', 'Computer Science', '2018-09-01', '2022-05-15', '3.7 GPA'),
(2, 'Stanford University', 'Master of Science', 'Computer Science', '2012-09-01', '2014-05-15', '3.9 GPA');

-- Add work experiences
INSERT INTO work_experiences (job_seeker_id, company_name, job_title, employment_type, location, start_date, end_date, is_current, description) VALUES
(1, 'WebDev Inc', 'Junior Developer', 'full_time', 'San Francisco, CA', '2022-06-01', '2024-01-15', FALSE, 'Developed responsive web applications using React and Node.js. Collaborated with cross-functional teams to deliver high-quality software solutions.'),
(1, 'TechStart Solutions', 'Software Developer', 'full_time', 'New York, NY', '2024-02-01', NULL, TRUE, 'Full-stack development using modern technologies. Leading development of customer-facing applications.'),
(2, 'InnovateLab', 'Co-founder & CTO', 'full_time', 'Austin, TX', '2016-01-01', '2023-12-31', FALSE, 'Built and led engineering team of 15+ developers. Architected scalable systems serving millions of users.');

-- Add skills to job seekers
INSERT INTO job_seeker_skills (job_seeker_id, skill_id, proficiency_level, years_of_experience) VALUES
-- Job seeker 1 skills
(1, (SELECT id FROM skills WHERE name = 'JavaScript'), 'advanced', 3),
(1, (SELECT id FROM skills WHERE name = 'React'), 'advanced', 2),
(1, (SELECT id FROM skills WHERE name = 'Node.js'), 'intermediate', 2),
(1, (SELECT id FROM skills WHERE name = 'Python'), 'intermediate', 1),
(1, (SELECT id FROM skills WHERE name = 'MySQL'), 'intermediate', 2),

-- Job seeker 2 skills
(2, (SELECT id FROM skills WHERE name = 'JavaScript'), 'expert', 8),
(2, (SELECT id FROM skills WHERE name = 'Python'), 'expert', 6),
(2, (SELECT id FROM skills WHERE name = 'AWS'), 'advanced', 5),
(2, (SELECT id FROM skills WHERE name = 'Leadership'), 'expert', 7),
(2, (SELECT id FROM skills WHERE name = 'Project Management'), 'advanced', 6);

-- Create sample job postings
INSERT INTO jobs (company_id, posted_by_user_id, industry_id, title, slug, description, requirements, responsibilities, benefits, job_type, experience_level, location_type, location, salary_min, salary_max, positions_available, status, published_at) VALUES
(1, 2, 1, 'Senior Full-Stack Developer', 'senior-full-stack-developer-techcorp', 
'We are seeking a talented Senior Full-Stack Developer to join our growing engineering team. You will be responsible for developing and maintaining web applications that serve millions of users worldwide.',
'• Bachelor''s degree in Computer Science or equivalent experience
• 5+ years of experience in full-stack development
• Proficiency in React, Node.js, and modern JavaScript
• Experience with cloud platforms (AWS, Azure, or GCP)
• Strong problem-solving and communication skills',
'• Develop and maintain scalable web applications
• Collaborate with product managers and designers
• Write clean, maintainable, and well-tested code
• Participate in code reviews and technical discussions
• Mentor junior developers and contribute to team growth',
'• Competitive salary and equity package
• Health, dental, and vision insurance
• Flexible work arrangements and remote options
• Professional development budget
• Generous PTO and parental leave',
'full_time', 'senior', 'hybrid', 'San Francisco, CA', 120000.00, 180000.00, 2, 'active', NOW()),

(2, 5, 2, 'AI Research Scientist', 'ai-research-scientist-startupx', 
'Join our innovative team working on cutting-edge AI solutions. We''re looking for a passionate researcher who can contribute to our machine learning initiatives.',
'• PhD in Machine Learning, AI, or related field
• 3+ years of research experience in AI/ML
• Strong background in deep learning frameworks
• Published research papers preferred
• Experience with Python, TensorFlow, or PyTorch',
'• Conduct research in machine learning and AI
• Develop and implement novel algorithms
• Collaborate with engineering teams to productize research
• Publish research findings in top-tier conferences
• Stay current with latest developments in AI',
'• Competitive salary and significant equity
• Research budget for conferences and publications
• State-of-the-art computing resources
• Flexible schedule and remote work options
• Health and wellness benefits',
'full_time', 'senior', 'remote', NULL, 140000.00, 200000.00, 1, 'active', NOW());

-- Add required skills to jobs
INSERT INTO job_skills (job_id, skill_id, is_required, importance_level) VALUES
-- Senior Full-Stack Developer skills
(1, (SELECT id FROM skills WHERE name = 'JavaScript'), TRUE, 'critical'),
(1, (SELECT id FROM skills WHERE name = 'React'), TRUE, 'critical'),
(1, (SELECT id FROM skills WHERE name = 'Node.js'), TRUE, 'critical'),
(1, (SELECT id FROM skills WHERE name = 'AWS'), FALSE, 'important'),
(1, (SELECT id FROM skills WHERE name = 'MySQL'), TRUE, 'important'),

-- AI Research Scientist skills
(2, (SELECT id FROM skills WHERE name = 'Python'), TRUE, 'critical'),
(2, (SELECT id FROM skills WHERE name = 'Leadership'), FALSE, 'nice_to_have'),
(2, (SELECT id FROM skills WHERE name = 'Problem Solving'), TRUE, 'important');

-- Create sample applications
INSERT INTO applications (job_id, job_seeker_id, cover_letter, expected_salary, status, applied_at) VALUES
(1, 1, 'I am excited to apply for the Senior Full-Stack Developer position at TechCorp Solutions. With my experience in React and Node.js, I believe I would be a great fit for your team. I am passionate about building scalable web applications and would love to contribute to your mission of delivering innovative technology solutions.', 
85000.00, 'submitted', NOW()),

(2, 1, 'I am writing to express my interest in the AI Research Scientist position. While my background is primarily in full-stack development, I have been actively learning machine learning and have completed several online courses in AI. I am eager to transition into AI research and believe this role would be perfect for my career growth.',
120000.00, 'submitted', NOW()),

-- User 5 (who has both employer and job-seeker roles) applying to job 1
(1, 2, 'As an entrepreneur with technical leadership experience, I am interested in this senior developer role to gain hands-on experience with your technology stack while potentially exploring partnership opportunities. My background in scaling engineering teams would bring valuable perspective to your development processes.',
140000.00, 'reviewing', DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Create sample subscriptions
INSERT INTO subscriptions (company_id, hr_package_id, billing_period, status, starts_at, ends_at, next_billing_date, job_posts_used) VALUES
(1, 3, 'yearly', 'active', DATE_SUB(NOW(), INTERVAL 60 DAY), DATE_ADD(NOW(), INTERVAL 305 DAY), DATE_ADD(NOW(), INTERVAL 305 DAY), 5),
(2, 1, 'monthly', 'trial', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_ADD(NOW(), INTERVAL 20 DAY), DATE_ADD(NOW(), INTERVAL 20 DAY), 2);

-- Create sample transactions
INSERT INTO transactions (subscription_id, company_id, transaction_id, type, amount, currency, payment_method, gateway, status, paid_at, invoice_number) VALUES
(1, 1, 'txn_1234567890', 'subscription', 7990.00, 'USD', 'credit_card', 'stripe', 'completed', DATE_SUB(NOW(), INTERVAL 60 DAY), 'INV-2024-001'),
(2, 2, 'txn_0987654321', 'subscription', 0.00, 'USD', 'trial', 'stripe', 'completed', DATE_SUB(NOW(), INTERVAL 10 DAY), 'INV-2024-002');

-- Create sample saved jobs
INSERT INTO saved_jobs (job_seeker_id, job_id, notes, saved_at) VALUES
(1, 2, 'Interesting AI role - need to improve Python skills first', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(2, 1, 'Good full-stack role at established company', DATE_SUB(NOW(), INTERVAL 2 DAY));

-- Create sample job views for analytics
INSERT INTO job_views (job_id, user_id, ip_address, viewed_at) VALUES
(1, 1, '192.168.1.100', NOW()),
(1, 2, '192.168.1.101', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(1, NULL, '192.168.1.102', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 1, '192.168.1.100', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(2, 4, '192.168.1.103', DATE_SUB(NOW(), INTERVAL 3 HOUR));

-- Create sample search logs
INSERT INTO search_logs (user_id, search_query, results_count, searched_at) VALUES
(1, 'full stack developer', 15, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(1, 'remote javascript jobs', 8, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 'entry level developer', 25, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(NULL, 'senior developer san francisco', 12, DATE_SUB(NOW(), INTERVAL 1 HOUR));

-- Create sample notifications
INSERT INTO notifications (user_id, type, title, message, data) VALUES
(1, 'application_status', 'Application Status Update', 'Your application for Senior Full-Stack Developer has been reviewed', JSON_OBJECT('application_id', 1, 'job_id', 1, 'status', 'reviewed')),
(4, 'new_job_match', 'New Job Match', 'A new job matching your profile has been posted', JSON_OBJECT('job_id', 1, 'match_score', 85)),
(2, 'new_application', 'New Application Received', 'You have received a new application for Senior Full-Stack Developer', JSON_OBJECT('application_id', 1, 'job_id', 1)),
(5, 'subscription_reminder', 'Trial Ending Soon', 'Your trial subscription will end in 5 days', JSON_OBJECT('subscription_id', 2, 'days_remaining', 5));

-- ==============================================
-- ADDITIONAL PERFORMANCE INDEXES
-- ==============================================

-- Composite indexes for common queries
CREATE INDEX idx_jobs_company_status_published ON jobs(company_id, status, published_at);
CREATE INDEX idx_applications_job_status ON applications(job_id, status);
CREATE INDEX idx_applications_seeker_status ON applications(job_seeker_id, status);
CREATE INDEX idx_users_email_active_deleted ON users(email, is_active, deleted_at);
CREATE INDEX idx_companies_verified_active ON companies(is_verified, is_active, deleted_at);
CREATE INDEX idx_job_views_job_date ON job_views(job_id, viewed_at);
CREATE INDEX idx_notifications_user_read ON notifications(user_id, is_read, created_at);
CREATE INDEX idx_subscriptions_status_expires ON subscriptions(status, ends_at);
CREATE INDEX idx_transactions_company_status ON transactions(company_id, status);

-- ==============================================
-- FINAL NOTES AND DEPLOYMENT CHECKLIST
-- ==============================================

/*
COMPREHENSIVE HR TALENT MANAGEMENT DATABASE SCHEMA - FINAL VERSION

FEATURES IMPLEMENTED:
✅ Enhanced Laravel Spatie Role/Permission System
✅ Comprehensive User Authentication with Phone/Email Verification
✅ Complete Job Seeker Profile Management
✅ Advanced Company and Job Posting Management
✅ Sophisticated Application Tracking System
✅ HR Services and Subscription Management
✅ Payment and Transaction Processing
✅ Full Audit Logging and Activity Tracking
✅ CMS and Content Management
✅ Analytics and Reporting Infrastructure
✅ Communication and Notification System
✅ Media File Management
✅ Soft Deletes with Super Admin Hard Delete Override
✅ Performance Optimization with Views and Indexes
✅ Comprehensive Sample Data for Testing

ROLE HIERARCHY:
1. Super Admin (Level 100) - Full system access
2. Admin (Level 90) - Administrative access with restrictions
3. HR Manager (Level 70) - Extended employer permissions + HR features
4. Employer (Level 60) - Job posting and application management
5. Job Seeker (Level 30) - Profile and application management
6. Guest (Level 10) - Read-only access

KEY SECURITY FEATURES:
- Comprehensive audit trails for all data changes
- Failed login attempt tracking and account lockout
- Role-based access control with permission inheritance
- Soft deletes with hard delete restricted to Super Admin
- Foreign key constraints preventing orphaned records
- Session and request tracking for security monitoring

BUSINESS FEATURES:
- Multi-role users (e.g., User 5 has both employer and job-seeker roles)
- Hierarchical industry categorization
- Skills-based job matching potential
- Subscription-based HR services with usage tracking
- Comprehensive application workflow management
- Advanced search and analytics capabilities
- CMS for dynamic content management
- Multi-currency support for global operations

DEPLOYMENT CHECKLIST:
□ Configure database connection and charset
□ Set up proper database user permissions
□ Configure SSL/TLS for database connections
□ Set up automated backup strategy
□ Configure log rotation for audit_logs table
□ Set up monitoring for failed login attempts
□ Implement rate limiting for API endpoints
□ Configure file upload security and limits
□ Set up email and SMS service providers
□ Configure payment gateway integration
□ Set up cron jobs for:
  - Expired job cleanup
  - Subscription billing processing
  - Notification sending
  - Audit log cleanup
  - Analytics data aggregation
□ Test all stored procedures and triggers
□ Verify all foreign key constraints
□ Configure search engine optimization
□ Set up application monitoring and alerting
□ Perform security penetration testing
□ Load test with sample data
□ Configure CDN for media files
□ Set up development/staging environments

MAINTENANCE PROCEDURES:
- Regular audit log cleanup (retention: 365 days)
- Monthly subscription billing processing
- Weekly job expiration cleanup
- Daily notification processing
- Periodic index optimization
- Regular backup verification
- Security audit reviews
- Performance monitoring and optimization

SAMPLE DATA INCLUDED:
- 6 predefined roles with appropriate permissions
- 15 industries with technology sub-categories
- 35+ skills across multiple categories
- 4 HR packages with different tiers
- 5 sample users with mixed role assignments
- 2 companies with different verification states
- 2 job postings with skill requirements
- 3 job applications in different states
- 2 active subscriptions
- Sample analytics and tracking data
- CMS pages and FAQ content
- System configuration settings

This schema provides a production-ready foundation for a comprehensive HR talent management platform with enterprise-level features, security, and scalability.
*/