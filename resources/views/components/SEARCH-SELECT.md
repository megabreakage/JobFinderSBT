# Search Select Component

A dynamic, reusable autocomplete/search-select component that allows users to type, search, and select from a dropdown list of results. Built with Livewire and Alpine.js for real-time search functionality.

## Features

- ✅ **Real-time Search** - Debounced input for efficient searching
- ✅ **Keyboard Navigation** - Arrow keys, Enter, and Escape support
- ✅ **Loading States** - Visual feedback during search operations
- ✅ **Clear Selection** - Easy-to-use clear button
- ✅ **Livewire Integration** - Full wire:model support
- ✅ **API Support** - Search via Livewire methods or REST endpoints
- ✅ **Customizable** - Configure display keys, min characters, debounce, etc.
- ✅ **Accessible** - Proper keyboard support and visual feedback
- ✅ **Responsive** - Works on all screen sizes

## Basic Usage

### With Livewire Method

```blade
<x-form.search-select
    name="user_id"
    label="Select User"
    placeholder="Type to search users..."
    wire:model="selectedUserId"
    searchMethod="searchUsers"
    displayKey="name"
    valueKey="id"
/>
```

**Livewire Component:**

```php
public $selectedUserId;

public function searchUsers($query)
{
    return User::where('name', 'like', "%{$query}%")
        ->limit(10)
        ->get()
        ->toArray();
}
```

### With API Endpoint

```blade
<x-form.search-select
    name="user_id"
    label="Select User"
    placeholder="Type to search users..."
    searchUrl="/api/users/search"
    displayKey="name"
    valueKey="id"
/>
```

**API Endpoint Response:**

```json
{
    "results": [
        {"id": 1, "name": "John Doe", "email": "john@example.com"},
        {"id": 2, "name": "Jane Smith", "email": "jane@example.com"}
    ]
}
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | null | Form input name |
| `label` | string | null | Label text |
| `placeholder` | string | 'Search...' | Input placeholder |
| `hint` | string | null | Helper text below input |
| `error` | string | null | Error message |
| `required` | boolean | false | Mark field as required |
| `disabled` | boolean | false | Disable the input |
| `searchUrl` | string | null | API endpoint for search |
| `searchMethod` | string | null | Livewire method name for search |
| `minChars` | integer | 2 | Minimum characters before search |
| `debounce` | integer | 300 | Debounce delay in milliseconds |
| `displayKey` | string | 'name' | Key to display in results |
| `valueKey` | string | 'id' | Key to use as value |
| `noResultsText` | string | 'No results found' | Text when no results |
| `loadingText` | string | 'Searching...' | Text during search |

## Examples

### 1. Search Users

```blade
<x-form.search-select
    name="user_id"
    label="Assign to User"
    placeholder="Search by name or email..."
    hint="Start typing to search users"
    wire:model="assignedUserId"
    searchMethod="searchUsers"
    displayKey="name"
    valueKey="id"
    :minChars="2"
    :debounce="300"
/>
```

### 2. Search Companies

```blade
<x-form.search-select
    name="company_id"
    label="Select Company"
    placeholder="Type company name..."
    wire:model="companyId"
    searchMethod="searchCompanies"
    displayKey="name"
    valueKey="id"
    :minChars="1"
/>
```

### 3. Search Jobs

```blade
<x-form.search-select
    name="job_id"
    label="Related Job"
    placeholder="Search jobs by title or location..."
    wire:model="jobId"
    searchMethod="searchJobs"
    displayKey="title"
    valueKey="id"
    noResultsText="No jobs found"
    loadingText="Searching jobs..."
/>
```

### 4. Search with API

```blade
<x-form.search-select
    name="location"
    label="Location"
    placeholder="Search cities..."
    searchUrl="/api/locations/search"
    displayKey="city"
    valueKey="id"
    :minChars="3"
/>
```

### 5. With Error Handling

```blade
<x-form.search-select
    name="category_id"
    label="Category"
    placeholder="Search categories..."
    wire:model="categoryId"
    searchMethod="searchCategories"
    :error="$errors->first('category_id')"
    required
/>
```

## Livewire Component Implementation

### Basic Search Method

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class MyComponent extends Component
{
    public $selectedUserId;

    public function searchUsers($query)
    {
        return User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'email'])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.my-component');
    }
}
```

### Advanced Search with Relations

```php
public function searchJobs($query)
{
    return Job::with('company')
        ->where('title', 'like', "%{$query}%")
        ->orWhere('location', 'like', "%{$query}%")
        ->limit(10)
        ->get()
        ->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'company' => $job->company->name,
                'location' => $job->location,
            ];
        })
        ->toArray();
}
```

### Search with Custom Display

```php
public function searchUsers($query)
{
    return User::where('name', 'like', "%{$query}%")
        ->limit(10)
        ->get()
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name . ' (' . $user->email . ')',
                'email' => $user->email,
            ];
        })
        ->toArray();
}
```

## API Endpoint Implementation

### Laravel Route

```php
Route::get('/api/users/search', function (Request $request) {
    $query = $request->input('q');
    
    $users = User::where('name', 'like', "%{$query}%")
        ->limit(10)
        ->get(['id', 'name', 'email']);
    
    return response()->json([
        'results' => $users
    ]);
});
```

### With Authentication

```php
Route::middleware('auth:sanctum')->get('/api/users/search', function (Request $request) {
    $query = $request->input('q');
    
    $users = User::where('name', 'like', "%{$query}%")
        ->where('company_id', auth()->user()->company_id)
        ->limit(10)
        ->get(['id', 'name', 'email']);
    
    return response()->json([
        'results' => $users
    ]);
});
```

## Keyboard Navigation

- **Arrow Down** - Move to next result
- **Arrow Up** - Move to previous result
- **Enter** - Select highlighted result
- **Escape** - Close dropdown
- **Type** - Search and filter results

## Events

The component dispatches a custom event when an item is selected:

```javascript
// Listen for item selection
document.addEventListener('item-selected', (event) => {
    console.log('Selected item:', event.detail.item);
});
```

In Alpine.js:

```blade
<div @item-selected="handleSelection($event.detail.item)">
    <x-form.search-select ... />
</div>
```

## Styling

The component uses Tailwind CSS classes and can be customized by:

1. **Modifying the component** - Edit `resources/views/components/form/search-select.blade.php`
2. **Adding custom classes** - Use the `class` attribute
3. **Overriding styles** - Add custom CSS

```blade
<x-form.search-select
    class="custom-search-select"
    ...
/>
```

## Performance Tips

1. **Limit Results** - Return only 10-20 results for better performance
2. **Use Indexes** - Ensure database columns are indexed
3. **Debounce** - Adjust debounce time based on your needs (default: 300ms)
4. **Min Characters** - Set appropriate minimum characters (default: 2)
5. **Cache Results** - Consider caching frequent searches

## Common Use Cases

### Job Search Platform

```blade
<x-form.search-select
    name="job_id"
    label="Search Jobs"
    placeholder="Job title, keywords, or location..."
    wire:model="selectedJobId"
    searchMethod="searchJobs"
    displayKey="title"
    valueKey="id"
/>
```

### User Assignment

```blade
<x-form.search-select
    name="assigned_to"
    label="Assign To"
    placeholder="Search team members..."
    wire:model="assignedTo"
    searchMethod="searchTeamMembers"
    displayKey="name"
    valueKey="id"
/>
```

### Company Selection

```blade
<x-form.search-select
    name="company_id"
    label="Company"
    placeholder="Search companies..."
    wire:model="companyId"
    searchMethod="searchCompanies"
    displayKey="name"
    valueKey="id"
/>
```

### Location Autocomplete

```blade
<x-form.search-select
    name="location"
    label="Location"
    placeholder="City, state, or country..."
    searchUrl="/api/locations/search"
    displayKey="full_name"
    valueKey="id"
    :minChars="3"
/>
```

## Troubleshooting

### Search not working

- Ensure `searchMethod` or `searchUrl` is provided
- Check Livewire method returns an array
- Verify API endpoint returns correct JSON format

### Results not displaying

- Check `displayKey` matches your data structure
- Ensure minimum characters requirement is met
- Verify results array is not empty

### Selection not updating

- Confirm `wire:model` is set correctly
- Check `valueKey` matches your data structure
- Ensure Livewire property exists in component

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Dependencies

- Laravel 12.x
- Livewire 3.x
- Alpine.js (included with Livewire)
- Tailwind CSS 4.x
