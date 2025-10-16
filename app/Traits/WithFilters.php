<?php

namespace App\Traits;

trait WithFilters
{
    /**
     * Filter by status.
     *
     * @var string|null
     */
    public ?string $filterStatus = null;

    /**
     * Filter by date range - start date.
     *
     * @var string|null
     */
    public ?string $filterDateFrom = null;

    /**
     * Filter by date range - end date.
     *
     * @var string|null
     */
    public ?string $filterDateTo = null;

    /**
     * Filter by category/type.
     *
     * @var string|null
     */
    public ?string $filterCategory = null;

    /**
     * Filter by user/owner.
     *
     * @var int|null
     */
    public ?int $filterUserId = null;

    /**
     * Filter by company.
     *
     * @var int|null
     */
    public ?int $filterCompanyId = null;

    /**
     * Filter by industry.
     *
     * @var int|null
     */
    public ?int $filterIndustryId = null;

    /**
     * Filter by location.
     *
     * @var string|null
     */
    public ?string $filterLocation = null;

    /**
     * Filter by job type.
     *
     * @var string|null
     */
    public ?string $filterJobType = null;

    /**
     * Filter by experience level.
     *
     * @var string|null
     */
    public ?string $filterExperienceLevel = null;

    /**
     * Filter by location type (remote, onsite, hybrid).
     *
     * @var string|null
     */
    public ?string $filterLocationType = null;

    /**
     * Filter by salary range - minimum.
     *
     * @var float|null
     */
    public ?float $filterSalaryMin = null;

    /**
     * Filter by salary range - maximum.
     *
     * @var float|null
     */
    public ?float $filterSalaryMax = null;

    /**
     * Filter by verification status.
     *
     * @var bool|null
     */
    public ?bool $filterIsVerified = null;

    /**
     * Filter by active status.
     *
     * @var bool|null
     */
    public ?bool $filterIsActive = null;

    /**
     * Filter by featured status.
     *
     * @var bool|null
     */
    public ?bool $filterIsFeatured = null;

    /**
     * Reset all filters to their default values.
     *
     * @return void
     */
    public function resetFilters(): void
    {
        $this->filterStatus = null;
        $this->filterDateFrom = null;
        $this->filterDateTo = null;
        $this->filterCategory = null;
        $this->filterUserId = null;
        $this->filterCompanyId = null;
        $this->filterIndustryId = null;
        $this->filterLocation = null;
        $this->filterJobType = null;
        $this->filterExperienceLevel = null;
        $this->filterLocationType = null;
        $this->filterSalaryMin = null;
        $this->filterSalaryMax = null;
        $this->filterIsVerified = null;
        $this->filterIsActive = null;
        $this->filterIsFeatured = null;

        // Reset pagination if the trait is used with WithDataTable
        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    /**
     * Reset specific filter by name.
     *
     * @param string $filterName The filter property name
     * @return void
     */
    public function resetFilter(string $filterName): void
    {
        if (property_exists($this, $filterName)) {
            $this->$filterName = null;

            // Reset pagination if the trait is used with WithDataTable
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }
        }
    }

    /**
     * Check if any filters are active.
     *
     * @return bool
     */
    public function hasActiveFilters(): bool
    {
        return $this->filterStatus !== null
            || $this->filterDateFrom !== null
            || $this->filterDateTo !== null
            || $this->filterCategory !== null
            || $this->filterUserId !== null
            || $this->filterCompanyId !== null
            || $this->filterIndustryId !== null
            || $this->filterLocation !== null
            || $this->filterJobType !== null
            || $this->filterExperienceLevel !== null
            || $this->filterLocationType !== null
            || $this->filterSalaryMin !== null
            || $this->filterSalaryMax !== null
            || $this->filterIsVerified !== null
            || $this->filterIsActive !== null
            || $this->filterIsFeatured !== null;
    }

    /**
     * Get count of active filters.
     *
     * @return int
     */
    public function getActiveFiltersCount(): int
    {
        $count = 0;

        if ($this->filterStatus !== null) $count++;
        if ($this->filterDateFrom !== null) $count++;
        if ($this->filterDateTo !== null) $count++;
        if ($this->filterCategory !== null) $count++;
        if ($this->filterUserId !== null) $count++;
        if ($this->filterCompanyId !== null) $count++;
        if ($this->filterIndustryId !== null) $count++;
        if ($this->filterLocation !== null) $count++;
        if ($this->filterJobType !== null) $count++;
        if ($this->filterExperienceLevel !== null) $count++;
        if ($this->filterLocationType !== null) $count++;
        if ($this->filterSalaryMin !== null) $count++;
        if ($this->filterSalaryMax !== null) $count++;
        if ($this->filterIsVerified !== null) $count++;
        if ($this->filterIsActive !== null) $count++;
        if ($this->filterIsFeatured !== null) $count++;

        return $count;
    }

    /**
     * Apply filters to a query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyFilters($query)
    {
        if ($this->filterStatus !== null) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterDateFrom !== null) {
            $query->whereDate('created_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo !== null) {
            $query->whereDate('created_at', '<=', $this->filterDateTo);
        }

        if ($this->filterCategory !== null) {
            $query->where('category', $this->filterCategory);
        }

        if ($this->filterUserId !== null) {
            $query->where('user_id', $this->filterUserId);
        }

        if ($this->filterCompanyId !== null) {
            $query->where('company_id', $this->filterCompanyId);
        }

        if ($this->filterIndustryId !== null) {
            $query->where('industry_id', $this->filterIndustryId);
        }

        if ($this->filterLocation !== null) {
            $query->where('location', 'like', '%' . $this->filterLocation . '%');
        }

        if ($this->filterJobType !== null) {
            $query->where('job_type', $this->filterJobType);
        }

        if ($this->filterExperienceLevel !== null) {
            $query->where('experience_level', $this->filterExperienceLevel);
        }

        if ($this->filterLocationType !== null) {
            $query->where('location_type', $this->filterLocationType);
        }

        if ($this->filterSalaryMin !== null) {
            $query->where('salary_max', '>=', $this->filterSalaryMin);
        }

        if ($this->filterSalaryMax !== null) {
            $query->where('salary_min', '<=', $this->filterSalaryMax);
        }

        if ($this->filterIsVerified !== null) {
            $query->where('is_verified', $this->filterIsVerified);
        }

        if ($this->filterIsActive !== null) {
            $query->where('is_active', $this->filterIsActive);
        }

        if ($this->filterIsFeatured !== null) {
            $query->where('is_featured', $this->filterIsFeatured);
        }

        return $query;
    }
}
