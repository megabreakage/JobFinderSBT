<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'job_seeker_id',
        'institution_name',
        'degree_type',
        'field_of_study',
        'start_date',
        'end_date',
        'grade',
        'description',
        'is_current',
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
     * Get formatted education period.
     */
    public function getFormattedPeriodAttribute(): string
    {
        $start = $this->start_date?->format('M Y') ?? 'Unknown';
        $end = $this->is_current ? 'Present' : ($this->end_date?->format('M Y') ?? 'Unknown');

        return "{$start} - {$end}";
    }
}
