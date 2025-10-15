<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobSeeker extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'bio',
        'current_job_title',
        'current_company',
        'years_of_experience',
        'current_location',
        'preferred_locations',
        'expected_salary_min',
        'expected_salary_max',
        'salary_currency',
        'employment_type_preference',
        'open_to_remote',
        'willing_to_relocate',
        'resume_path',
        'portfolio_url',
        'linkedin_url',
        'github_url',
        'profile_completion_percentage',
        'is_profile_public',
        'is_available',
        'available_from',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'expected_salary_min' => 'decimal:2',
            'expected_salary_max' => 'decimal:2',
            'open_to_remote' => 'boolean',
            'willing_to_relocate' => 'boolean',
            'is_profile_public' => 'boolean',
            'is_available' => 'boolean',
            'available_from' => 'date',
        ];
    }

    /**
     * User relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
    public function savedJobs(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    /**
     * Scope for public profiles.
     */
    public function scopePublic($query)
    {
        return $query->where('is_profile_public', true);
    }

    /**
     * Scope for available job seekers.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Get formatted salary expectation.
     */
    public function getFormattedSalaryExpectationAttribute(): string
    {
        if ($this->expected_salary_min && $this->expected_salary_max) {
            return "{$this->salary_currency} {$this->expected_salary_min} - {$this->expected_salary_max}";
        } elseif ($this->expected_salary_min) {
            return "{$this->salary_currency} {$this->expected_salary_min}+";
        }

        return 'Not specified';
    }
}
