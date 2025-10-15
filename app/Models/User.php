<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'avatar',
        'date_of_birth',
        'gender',
        'timezone',
        'locale',
        'is_active',
        'email_notifications',
        'sms_notifications',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->uuid = \Str::uuid();
        });
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'email', 'phone', 'is_active'])
            ->logOnlyDirty();
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Job seeker profile relationship.
     */
    public function jobSeeker(): HasOne
    {
        return $this->hasOne(JobSeeker::class);
    }

    /**
     * Company relationships through user_company_roles.
     */
    public function companyRoles(): HasMany
    {
        return $this->hasMany(UserCompanyRole::class);
    }

    /**
     * Companies relationship.
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'user_company_roles')
            ->withPivot(['role_type', 'job_title', 'is_active'])
            ->wherePivot('is_active', true);
    }

    /**
     * Posted jobs relationship.
     */
    public function postedJobs(): HasMany
    {
        return $this->hasMany(JobPosting::class, 'posted_by_user_id');
    }

    /**
     * Check if user is a job seeker.
     */
    public function isJobSeeker(): bool
    {
        return $this->hasRole('job-seeker');
    }

    /**
     * Check if user is an employer.
     */
    public function isEmployer(): bool
    {
        return $this->hasRole('employer');
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['admin', 'super-admin']);
    }

    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for verified users.
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }
}
