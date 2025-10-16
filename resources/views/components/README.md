# Blade Component Library

This directory contains reusable Blade components for the HR Talent Management platform. All components are designed to work seamlessly with Livewire 3 and Tailwind CSS 4.

## Form Components

### Input (`<x-form.input>`)
Text input field with label, error, hint, and icon support.

```blade
<x-form.input
    name="email"
    label="Email Address"
    type="email"
    placeholder="Enter your email"
    hint="We'll never share your email"
    :error="$errors->first('email')"
    icon="envelope"
    wire:model="email"
    required
/>
```

**Input Groups with Prepend/Append:**

```blade
{{-- Prepend text --}}
<x-form.input
    name="website"
    label="Website"
    placeholder="example.com"
    wire:model="website"
>
    <x-slot:prepend>https://</x-slot:prepend>
</x-form.input>

{{-- Append text --}}
<x-form.input
    name="price"
    label="Price"
    type="number"
    wire:model="price"
>
    <x-slot:append>USD</x-slot:append>
</x-form.input>

{{-- Prepend icon --}}
<x-form.input
    name="username"
    label="Username"
    wire:model="username"
>
    <x-slot:prepend>
        <x-icon name="user" size="sm" />
    </x-slot:prepend>
</x-form.input>

{{-- Both prepend and append --}}
<x-form.input
    name="discount"
    label="Discount"
    type="number"
    wire:model="discount"
>
    <x-slot:prepend>$</x-slot:prepend>
    <x-slot:append>.00</x-slot:append>
</x-form.input>

{{-- Append button --}}
<x-form.input
    name="search"
    label="Search"
    wire:model="searchTerm"
>
    <x-slot:append>
        <x-ui.button size="sm" wire:click="search">Search</x-ui.button>
    </x-slot:append>
</x-form.input>
```

### Select (`<x-form.select>`)
Dropdown select with options, placeholder, and multiple selection support.

```blade
<x-form.select
    name="role"
    label="User Role"
    :options="['job-seeker' => 'Job Seeker', 'employer' => 'Employer']"
    placeholder="Select a role"
    wire:model="role"
    required
/>
```

**Select with Input Groups:**

```blade
{{-- Prepend icon --}}
<x-form.select
    name="country"
    label="Country"
    :options="$countries"
    wire:model="country"
>
    <x-slot:prepend>
        <x-icon name="map-pin" size="sm" />
    </x-slot:prepend>
</x-form.select>

{{-- Append text --}}
<x-form.select
    name="currency"
    label="Currency"
    :options="['usd' => 'US Dollar', 'eur' => 'Euro']"
    wire:model="currency"
>
    <x-slot:append>Currency</x-slot:append>
</x-form.select>

{{-- Prepend and append --}}
<x-form.select
    name="timezone"
    label="Timezone"
    :options="$timezones"
    wire:model="timezone"
>
    <x-slot:prepend>
        <x-icon name="clock" size="sm" />
    </x-slot:prepend>
    <x-slot:append>GMT</x-slot:append>
</x-form.select>
```

### Checkbox (`<x-form.checkbox>`)
Checkbox input with label and hint.

```blade
<x-form.checkbox
    name="terms"
    label="I agree to the terms and conditions"
    hint="You must agree to continue"
    wire:model="acceptedTerms"
/>
```

### Switch (`<x-form.switch>`)
Toggle switch with label and hint.

```blade
<x-form.switch
    name="notifications"
    label="Email Notifications"
    hint="Receive email updates about your applications"
    wire:model="emailNotifications"
/>
```

### Textarea (`<x-form.textarea>`)
Multi-line text input with configurable rows.

```blade
<x-form.textarea
    name="bio"
    label="Biography"
    rows="6"
    placeholder="Tell us about yourself"
    wire:model="bio"
/>
```

### Search Select (`<x-form.search-select>`)
Dynamic autocomplete/search-select component with real-time search and dropdown selection.

```blade
<x-form.search-select
    name="user_id"
    label="Select User"
    placeholder="Type to search users..."
    wire:model="selectedUserId"
    searchMethod="searchUsers"
    displayKey="name"
    valueKey="id"
    :minChars="2"
/>
```

**Features:**
- Real-time search with debouncing
- Keyboard navigation (arrows, enter, escape)
- Loading states and visual feedback
- Clear selection button
- Works with Livewire methods or API endpoints

**See [SEARCH-SELECT.md](./SEARCH-SELECT.md) for detailed documentation.**

### Input Group (`<x-form.input-group>`)
Flexible container for grouping form elements with addons. Use this for complex input groups with multiple elements.

```blade
{{-- Basic input group with text addons --}}
<x-form.input-group label="Website URL" hint="Enter your website address">
    <x-form.input-addon position="prepend">https://</x-form.input-addon>
    <input
        type="text"
        name="url"
        wire:model="url"
        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
    />
    <x-form.input-addon position="append">.com</x-form.input-addon>
</x-form.input-group>

{{-- Input group with icon and button --}}
<x-form.input-group label="Search Jobs">
    <x-form.input-addon position="prepend">
        <x-icon name="magnifying-glass" size="sm" />
    </x-form.input-addon>
    <input
        type="text"
        name="search"
        wire:model="searchQuery"
        placeholder="Job title, keywords..."
        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
    />
    <x-ui.button wire:click="search" class="rounded-l-none">
        Search
    </x-ui.button>
</x-form.input-group>

{{-- Multiple inputs in a group --}}
<x-form.input-group label="Price Range">
    <x-form.input-addon position="prepend">$</x-form.input-addon>
    <input
        type="number"
        name="min_price"
        wire:model="minPrice"
        placeholder="Min"
        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm rounded-none"
    />
    <x-form.input-addon position="append">to</x-form.input-addon>
    <input
        type="number"
        name="max_price"
        wire:model="maxPrice"
        placeholder="Max"
        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm rounded-none"
    />
    <x-form.input-addon position="append">$</x-form.input-addon>
</x-form.input-group>
```

## UI Components

### Button (`<x-ui.button>`)
Button with variants, sizes, and loading state.

```blade
<x-ui.button
    variant="primary"
    size="md"
    type="submit"
    :loading="$isSubmitting"
    wire:click="submit"
>
    Save Changes
</x-ui.button>
```

Variants: `primary`, `secondary`, `success`, `danger`, `warning`, `outline`, `ghost`
Sizes: `xs`, `sm`, `md`, `lg`, `xl`

### Table (`<x-ui.table>`)
Data table with headers, striping, and hover effects.

```blade
<x-ui.table striped hover>
    <x-slot:header>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </x-slot:header>

    @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td><!-- actions --></td>
        </tr>
    @endforeach
</x-ui.table>
```

### Paginator (`<x-ui.paginator>`)
Pagination controls with per-page selection.

```blade
<x-ui.paginator
    :paginator="$users"
    :perPageOptions="[10, 25, 50, 100]"
    showPerPage
/>
```

### Card (`<x-ui.card>`)
Container card with optional header and footer.

```blade
<x-ui.card>
    <x-slot:header>
        <h3>Card Title</h3>
    </x-slot:header>

    Card content goes here

    <x-slot:footer>
        <x-ui.button>Action</x-ui.button>
    </x-slot:footer>
</x-ui.card>
```

### Badge (`<x-ui.badge>`)
Status badge with color variants.

```blade
<x-ui.badge variant="success" size="md">
    Active
</x-ui.badge>
```

Variants: `default`, `primary`, `secondary`, `success`, `danger`, `warning`, `info`
Sizes: `sm`, `md`, `lg`

### Alert (`<x-ui.alert>`)
Alert message with types and dismissible option.

```blade
<x-ui.alert
    type="success"
    title="Success!"
    dismissible
    wire:model="showAlert"
>
    Your profile has been updated successfully.
</x-ui.alert>
```

Types: `success`, `error`, `warning`, `info`

### Modal (`<x-ui.modal>`)
Modal dialog with size variants (Livewire-controlled, no Alpine.js).

```blade
<x-ui.modal
    wire:model="showModal"
    size="md"
    title="Edit Profile"
>
    Modal content goes here

    <x-slot:footer>
        <x-ui.button wire:click="$set('showModal', false)" variant="outline">
            Cancel
        </x-ui.button>
        <x-ui.button wire:click="save">
            Save
        </x-ui.button>
    </x-slot:footer>
</x-ui.modal>
```

Sizes: `sm`, `md`, `lg`, `xl`, `full`

### Icon (`<x-icon>`)
SVG icon component with Heroicons.

```blade
<x-icon name="user" size="md" class="text-gray-500" />
```

Available icons: `user`, `users`, `briefcase`, `building-office`, `envelope`, `phone`, `map-pin`, `calendar`, `clock`, `currency-dollar`, `document`, `document-text`, `pencil`, `trash`, `eye`, `eye-slash`, `magnifying-glass`, `funnel`, `arrow-up`, `arrow-down`, `chevron-left`, `chevron-right`, `chevron-up`, `chevron-down`, `x-mark`, `check`, `plus`, `minus`, `bell`, `cog`, `home`, `chart-bar`, `heart`, `star`, `information-circle`, `exclamation-circle`, `check-circle`, `x-circle`

Sizes: `xs`, `sm`, `md`, `lg`, `xl`, `2xl`

## Notes

- All form components support `wire:model` for Livewire binding
- All components use Tailwind CSS classes for styling
- Components are designed to be accessible and responsive
- The modal component uses Livewire properties instead of Alpine.js for state management

## Input Grouping

The form components support two approaches for input grouping:

### 1. Slot-based (Simple)
Use `prepend` and `append` slots directly on `<x-form.input>` and `<x-form.select>` components for simple text, icons, or single elements.

### 2. Component-based (Advanced)
Use `<x-form.input-group>` with `<x-form.input-addon>` for complex scenarios requiring:
- Multiple inputs in a single group
- Custom styling or layouts
- Buttons or other interactive elements
- Fine-grained control over element positioning

Both approaches provide flexible ways to enhance form inputs with contextual information, icons, units, or actions.
