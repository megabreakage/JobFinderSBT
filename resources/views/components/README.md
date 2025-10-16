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
