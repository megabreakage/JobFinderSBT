<?php

namespace App\Traits;

use Livewire\WithPagination;

trait WithDataTable
{
    use WithPagination;

    /**
     * Number of items to display per page.
     *
     * @var int
     */
    public int $perPage = 20;

    /**
     * Search query string.
     *
     * @var string
     */
    public string $search = '';

    /**
     * Field to sort by.
     *
     * @var string
     */
    public string $sortField = 'created_at';

    /**
     * Sort direction (asc or desc).
     *
     * @var string
     */
    public string $sortDirection = 'desc';

    /**
     * Reset pagination when search is updated.
     *
     * @return void
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when perPage is updated.
     *
     * @return void
     */
    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Sort by a given field. Toggle direction if already sorting by this field.
     *
     * @param string $field The field to sort by
     * @return void
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            // Toggle sort direction if already sorting by this field
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Set new sort field and default to ascending
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    /**
     * Get the sort icon for a given field.
     *
     * @param string $field The field to check
     * @return string The icon class or empty string
     */
    public function getSortIcon(string $field): string
    {
        if ($this->sortField !== $field) {
            return '';
        }

        return $this->sortDirection === 'asc' ? '↑' : '↓';
    }

    /**
     * Reset all data table properties to defaults.
     *
     * @return void
     */
    public function resetDataTable(): void
    {
        $this->search = '';
        $this->perPage = 20;
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    /**
     * Get available per page options.
     *
     * @return array
     */
    public function getPerPageOptions(): array
    {
        return [10, 20, 50, 100];
    }
}
