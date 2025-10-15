<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class JobPosting extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'company_id',
        'posted_by_user_id',
        'title',
        'slug',
        'description',
        'requirements',
        'responsibilities',
        'benefits',
        'industry_id',
        'type',
        'experience_level',
        'location',
        'is_remote',
        'salary_min',
        'salary_max',
        'salary_currency',
        'salary_period',
        'salary_negotiable',
        'positions_available',
        'application_deadline',
        'status',
        'is_featured',
        'is_urgent',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_remote' => 'boolean',
            'salary_negotiable' => 'boolean',
            'is_featured' => 'boolean',
            'is_urgent' => 'boolean',
            'application_deadline' => 'date',
            'expires_at' => 'date',
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($job) {
            $job->uuid = \Illuminate\Support\Str::uuid();
            if (!$job->slug) {
                $job->slug = \Illuminate\Support\Str::slug($job->title);
            }
        });
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'salary_min', 'salary_max'])
            ->logOnlyDirty();
    }

    /**
     * Company relationship.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Posted by user relationship.
     */
    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by_user_id');
    }

    /**
     * Industry relationship.
     */
    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    /**
     * Job applications relationship.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Saved jobs relationship.
     */
    public function savedBy(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    /**
     * Scope for active jobs.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope for featured jobs.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for urgent jobs.
     */
    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    /**
     * Scope for remote jobs.
     */
    public function scopeRemote($query)
    {
        return $query->where('is_remote', true);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get formatted salary range.
     */
    public function getFormattedSalaryAttribute(): string
    {
        if ($this->salary_negotiable) {
            return 'Negotiable';
        }

        if ($this->salary_min && $this->salary_max) {
            return "{$this->salary_currency} {$this->salary_min} - {$this->salary_max} per {$this->salary_period}";
        } elseif ($this->salary_min) {
            return "{$this->salary_currency} {$this->salary_min}+ per {$this->salary_period}";
        }

        return 'Not specified';
    }
}
