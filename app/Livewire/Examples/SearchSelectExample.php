<?php

namespace App\Livewire\Examples;

use Livewire\Component;
use App\Traits\WithToastr;

class SearchSelectExample extends Component
{
    use WithToastr;
    
    public $selectedUserId;
    public $selectedCompanyId;
    public $selectedJobId;
    
    public function updatedSelectedUserId($value)
    {
        if ($value) {
            $this->toastSuccess('User selected successfully!', 'Selection Updated');
        }
    }
    
    public function updatedSelectedCompanyId($value)
    {
        if ($value) {
            $this->toastInfo('Company selected: ID ' . $value, 'Selection Updated');
        }
    }
    
    public function updatedSelectedJobId($value)
    {
        if ($value) {
            $this->toastSuccess('Job selected successfully!', 'Selection Updated');
        }
    }

    /**
     * Search users by name or email
     */
    public function searchUsers($query)
    {
        // Simulate database search
        // In real implementation, use: User::where('name', 'like', "%{$query}%")->limit(10)->get()
        
        $users = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
            ['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com'],
            ['id' => 4, 'name' => 'Alice Williams', 'email' => 'alice@example.com'],
            ['id' => 5, 'name' => 'Charlie Brown', 'email' => 'charlie@example.com'],
        ];

        return collect($users)
            ->filter(function ($user) use ($query) {
                return stripos($user['name'], $query) !== false || 
                       stripos($user['email'], $query) !== false;
            })
            ->values()
            ->take(10)
            ->toArray();
    }

    /**
     * Search companies by name
     */
    public function searchCompanies($query)
    {
        // Simulate database search
        $companies = [
            ['id' => 1, 'name' => 'Tech Corp', 'industry' => 'Technology'],
            ['id' => 2, 'name' => 'Finance Inc', 'industry' => 'Finance'],
            ['id' => 3, 'name' => 'Health Plus', 'industry' => 'Healthcare'],
            ['id' => 4, 'name' => 'Edu Solutions', 'industry' => 'Education'],
            ['id' => 5, 'name' => 'Retail Giant', 'industry' => 'Retail'],
        ];

        return collect($companies)
            ->filter(function ($company) use ($query) {
                return stripos($company['name'], $query) !== false;
            })
            ->values()
            ->take(10)
            ->toArray();
    }

    /**
     * Search jobs by title or location
     */
    public function searchJobs($query)
    {
        // Simulate database search
        $jobs = [
            ['id' => 1, 'title' => 'Software Engineer', 'location' => 'New York', 'company' => 'Tech Corp'],
            ['id' => 2, 'title' => 'Product Manager', 'location' => 'San Francisco', 'company' => 'Tech Corp'],
            ['id' => 3, 'title' => 'Data Analyst', 'location' => 'Chicago', 'company' => 'Finance Inc'],
            ['id' => 4, 'title' => 'UX Designer', 'location' => 'Austin', 'company' => 'Tech Corp'],
            ['id' => 5, 'title' => 'Marketing Manager', 'location' => 'Boston', 'company' => 'Retail Giant'],
        ];

        return collect($jobs)
            ->filter(function ($job) use ($query) {
                return stripos($job['title'], $query) !== false || 
                       stripos($job['location'], $query) !== false;
            })
            ->values()
            ->take(10)
            ->toArray();
    }

    public function render()
    {
        return view('livewire.examples.search-select-example');
    }
}
