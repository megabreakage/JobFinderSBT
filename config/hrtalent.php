<?php

return [

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    |
    | Configure file upload limits and allowed file types for different
    | upload categories in the application.
    |
    */

    'uploads' => [
        'max_file_size' => env('MAX_FILE_SIZE', 10240), // KB (10MB default)
        'max_image_size' => env('MAX_IMAGE_SIZE', 5120), // KB (5MB default)
        
        'allowed_types' => [
            'resume' => explode(',', env('ALLOWED_RESUME_TYPES', 'pdf,doc,docx')),
            'image' => explode(',', env('ALLOWED_IMAGE_TYPES', 'jpg,jpeg,png')),
        ],
        
        'disks' => [
            'resume' => env('RESUME_DISK', 'resumes'),
            'company_media' => env('COMPANY_MEDIA_DISK', 'company_media'),
            'avatar' => env('AVATAR_DISK', 'avatars'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Settings
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for different endpoint categories to prevent
    | abuse and ensure fair usage of the platform.
    |
    */

    'rate_limits' => [
        'auth' => [
            'attempts' => env('RATE_LIMIT_AUTH', 5),
            'decay_minutes' => env('RATE_LIMIT_DECAY_MINUTES', 1),
        ],
        'api' => [
            'attempts' => env('RATE_LIMIT_API', 60),
            'decay_minutes' => env('RATE_LIMIT_DECAY_MINUTES', 1),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Configure security-related settings including login attempts,
    | lockout duration, and password reset expiry.
    |
    */

    'security' => [
        'max_login_attempts' => env('MAX_LOGIN_ATTEMPTS', 5),
        'lockout_duration' => env('LOCKOUT_DURATION', 900), // seconds (15 minutes)
        'password_reset_expiry' => env('PASSWORD_RESET_EXPIRY', 60), // minutes
        'otp_expiry' => env('OTP_EXPIRY', 10), // minutes
        'otp_length' => 6,
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Settings
    |--------------------------------------------------------------------------
    |
    | General application settings including pagination, profile completion,
    | and job expiry configurations.
    |
    */

    'pagination' => [
        'per_page' => env('PAGINATION_PER_PAGE', 20),
        'max_per_page' => 100,
    ],

    'profile' => [
        'completion_threshold' => env('PROFILE_COMPLETION_THRESHOLD', 80),
    ],

    'jobs' => [
        'expiry_days' => env('JOB_EXPIRY_DAYS', 30),
        'auto_close_after_expiry' => true,
    ],

    'subscriptions' => [
        'reminder_days' => env('SUBSCRIPTION_REMINDER_DAYS', 7),
        'grace_period_days' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Vonage SMS Settings
    |--------------------------------------------------------------------------
    |
    | Configure Vonage API credentials for SMS and OTP functionality.
    |
    */

    'vonage' => [
        'api_key' => env('VONAGE_API_KEY'),
        'api_secret' => env('VONAGE_API_SECRET'),
        'from' => env('VONAGE_FROM', 'HR_TALENT'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Settings
    |--------------------------------------------------------------------------
    |
    | Configure payment gateway credentials for Stripe and PayPal.
    |
    */

    'payment' => [
        'default_gateway' => env('PAYMENT_GATEWAY', 'stripe'),
        
        'stripe' => [
            'key' => env('STRIPE_KEY'),
            'secret' => env('STRIPE_SECRET'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        ],
        
        'paypal' => [
            'mode' => env('PAYPAL_MODE', 'sandbox'),
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret' => env('PAYPAL_SECRET'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configure notification channels and preferences.
    |
    */

    'notifications' => [
        'channels' => [
            'database' => true,
            'mail' => true,
            'sms' => false, // Enable when Vonage is configured
        ],
        'queue' => 'high', // Use high priority queue for notifications
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics Settings
    |--------------------------------------------------------------------------
    |
    | Configure analytics and tracking settings.
    |
    */

    'analytics' => [
        'track_job_views' => true,
        'track_searches' => true,
        'cache_duration' => 3600, // seconds (1 hour)
    ],

];
