<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkExperience extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'job_seeker_id',
        'company_name',
        'job_title',
        'employment_type',
        'location',
        'start_date',
        'end_date',
        'is_current',
        'description',
        'achievements',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    /**
     * Job seeker relationship.
     */
    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(JobSeeker::class);
    }

    /**
     * Get formatted work period.
     */
    public function getFormattedPeriodAttribute(): string
    {
        $start = $this->start_date?->format('M Y') ?? 'Unknown';
        $end = $this->is_current ? 'Present' : ($this->end_date?->format('M Y') ?? 'Unknown');

        return "{$start} - {$end}";
    }

    /**
     * Get duration in months.
     */
    public function getDurationInMonthsAttribute(): int
    {
        $start = $this->start_date;
        $end = $this->is_current ? now() : $this->end_date;

        if (!$start || !$end) {
            return 0;
        }

        return $start->diffInMonths($end);
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        $months = $this->duration_in_months;

        if ($months < 1) {
            return 'Less than a month';
        }

        $years = floor($months / 12);
        $remainingMonths = $months % 12;

        $parts = [];
        if ($years > 0) {
            $parts[] = $years . ' ' . ($years === 1 ? 'year' : 'years');
        }
        if ($remainingMonths > 0) {
            $parts[] = $remainingMonths . ' ' . ($remainingMonths === 1 ? 'month' : 'months');
        }

        return implode(' ', $parts);
    }
}
