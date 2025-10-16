# Component Library - Quick Start Guide

## Search Select Component

### 1. Basic Setup (5 minutes)

**Step 1: Create a Livewire Component**

```bash
php artisan make:livewire MyForm
```

**Step 2: Add Search Method**

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class MyForm extends Component
{
    public $selectedUserId;

    public function searchUsers($query)
    {
        return User::where('name', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'email'])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.my-form');
    }
}
```

**Step 3: Use in View**

```blade
<div>
    <x-form.search-select
        name="user_id"
        label="Select User"
        placeholder="Type to search users..."
        wire:model="selectedUserId"
        searchMethod="searchUsers"
        displayKey="name"
        valueKey="id"
    />

    @if($selectedUserId)
        <p>Selected User ID: {{ $selectedUserId }}</p>
    @endif
</div>
```

**Step 4: Add Route**

```php
Route::get('/my-form', MyForm::class);
```

**Done!** Visit `/my-form` to see it in action.

---

## Input Groups

### Quick Examples

**Prepend Text:**
```blade
<x-form.input name="website" label="Website">
    <x-slot:prepend>https://</x-slot:prepend>
</x-form.input>
```

**Append Text:**
```blade
<x-form.input name="price" label="Price" type="number">
    <x-slot:append>USD</x-slot:append>
</x-form.input>
```

**Prepend Icon:**
```blade
<x-form.input name="email" label="Email">
    <x-slot:prepend>
        <x-icon name="envelope" size="sm" />
    </x-slot:prepend>
</x-form.input>
```

**Both:**
```blade
<x-form.input name="discount" label="Discount" type="number">
    <x-slot:prepend>$</x-slot:prepend>
    <x-slot:append>.00</x-slot:append>
</x-form.input>
```

---

## All Form Components

### Input
```blade
<x-form.input
    name="email"
    label="Email"
    type="email"
    placeholder="Enter email"
    wire:model="email"
    required
/>
```

### Select
```blade
<x-form.select
    name="role"
    label="Role"
    :options="['admin' => 'Admin', 'user' => 'User']"
    wire:model="role"
/>
```

### Checkbox
```blade
<x-form.checkbox
    name="terms"
    label="I agree to terms"
    wire:model="acceptedTerms"
/>
```

### Switch
```blade
<x-form.switch
    name="notifications"
    label="Email Notifications"
    wire:model="emailNotifications"
/>
```

### Textarea
```blade
<x-form.textarea
    name="bio"
    label="Biography"
    rows="4"
    wire:model="bio"
/>
```

### Search Select
```blade
<x-form.search-select
    name="user_id"
    label="Select User"
    wire:model="userId"
    searchMethod="searchUsers"
/>
```

---

## UI Components

### Button
```blade
<x-ui.button variant="primary" wire:click="save">
    Save
</x-ui.button>
```

### Card
```blade
<x-ui.card>
    <x-slot:header>Title</x-slot:header>
    Content here
</x-ui.card>
```

### Badge
```blade
<x-ui.badge variant="success">Active</x-ui.badge>
```

### Alert
```blade
<x-ui.alert type="success" title="Success!">
    Operation completed successfully.
</x-ui.alert>
```

### Modal
```blade
<x-ui.modal wire:model="showModal" title="Edit">
    Modal content
    <x-slot:footer>
        <x-ui.button wire:click="save">Save</x-ui.button>
    </x-slot:footer>
</x-ui.modal>
```

### Icon
```blade
<x-icon name="user" size="md" />
```

---

## Testing Examples

### View Live Examples

```bash
php artisan serve
```

Visit: `http://localhost:8000/examples/search-select`

### Test API Endpoint

```bash
curl "http://localhost:8000/api/search?q=test"
```

---

## Common Patterns

### Form with Validation

```blade
<form wire:submit.prevent="submit">
    <x-form.input
        name="name"
        label="Name"
        wire:model="name"
        :error="$errors->first('name')"
        required
    />

    <x-form.search-select
        name="user_id"
        label="Assign To"
        wire:model="userId"
        searchMethod="searchUsers"
        :error="$errors->first('user_id')"
        required
    />

    <x-ui.button type="submit" :loading="$isSubmitting">
        Submit
    </x-ui.button>
</form>
```

### Search with Filters

```blade
<div class="space-y-4">
    <x-form.search-select
        name="job_id"
        label="Search Jobs"
        wire:model="jobId"
        searchMethod="searchJobs"
    />

    <x-form.select
        name="location"
        label="Location"
        :options="$locations"
        wire:model="location"
    />

    <x-ui.button wire:click="applyFilters">
        Apply Filters
    </x-ui.button>
</div>
```

---

## Documentation

- **Search Select**: `resources/views/components/SEARCH-SELECT.md`
- **Input Groups**: `resources/views/components/INPUT-GROUPS.md`
- **All Components**: `docs/components/README.md`

---

## Need Help?

1. Check the documentation files
2. View live examples at `/examples/search-select`
3. Review example Livewire component at `app/Livewire/Examples/SearchSelectExample.php`
