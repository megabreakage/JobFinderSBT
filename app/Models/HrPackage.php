<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrPackage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'tier',
        'description',
        'price_monthly',
        'price_yearly',
        'features',
        'max_job_posts',
        'max_active_jobs',
        'max_users',
        'api_access',
        'priority_support',
        'custom_branding',
        'is_popular',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'features' => 'array',
            'price_monthly' => 'decimal:2',
            'price_yearly' => 'decimal:2',
            'api_access' => 'boolean',
            'priority_support' => 'boolean',
            'custom_branding' => 'boolean',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($package) {
            if (!$package->slug) {
                $package->slug = \Illuminate\Support\Str::slug($package->name);
            }
        });
    }

    /**
     * Subscriptions relationship.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Scope for active packages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get yearly savings percentage.
     */
    public function getYearlySavingsAttribute(): float
    {
        if ($this->price_monthly && $this->price_yearly) {
            $yearlyFromMonthly = $this->price_monthly * 12;
            return round((($yearlyFromMonthly - $this->price_yearly) / $yearlyFromMonthly) * 100);
        }

        return 0;
    }
}
