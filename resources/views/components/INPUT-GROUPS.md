# Input Grouping Feature

Input grouping allows you to prepend or append text, icons, buttons, or other form elements to inputs and selects, providing better context and user experience.

## Features Added

### 1. Enhanced Components
- **`<x-form.input>`** - Now supports `prepend` and `append` slots
- **`<x-form.select>`** - Now supports `prepend` and `append` slots

### 2. New Components
- **`<x-form.input-group>`** - Flexible container for complex input groups
- **`<x-form.input-addon>`** - Reusable addon component for prepend/append elements

## Usage Approaches

### Approach 1: Slot-based (Recommended for Simple Cases)

Use named slots directly on input/select components:

```blade
{{-- Prepend text --}}
<x-form.input name="website" label="Website">
    <x-slot:prepend>https://</x-slot:prepend>
</x-form.input>

{{-- Append text --}}
<x-form.input name="price" label="Price" type="number">
    <x-slot:append>USD</x-slot:append>
</x-form.input>

{{-- Both prepend and append --}}
<x-form.input name="discount" label="Discount" type="number">
    <x-slot:prepend>$</x-slot:prepend>
    <x-slot:append>.00</x-slot:append>
</x-form.input>

{{-- Prepend icon --}}
<x-form.input name="email" label="Email">
    <x-slot:prepend>
        <x-icon name="envelope" size="sm" />
    </x-slot:prepend>
</x-form.input>

{{-- Append button --}}
<x-form.input name="search" label="Search">
    <x-slot:append>
        <x-ui.button size="sm" wire:click="search">Go</x-ui.button>
    </x-slot:append>
</x-form.input>
```

### Approach 2: Component-based (Recommended for Complex Cases)

Use the input-group component for advanced scenarios:

```blade
{{-- Multiple inputs in one group --}}
<x-form.input-group label="Price Range">
    <x-form.input-addon position="prepend">$</x-form.input-addon>
    <input type="number" name="min" class="..." />
    <x-form.input-addon position="append">to</x-form.input-addon>
    <input type="number" name="max" class="..." />
    <x-form.input-addon position="append">USD</x-form.input-addon>
</x-form.input-group>

{{-- Input with icon and button --}}
<x-form.input-group label="Search">
    <x-form.input-addon position="prepend">
        <x-icon name="magnifying-glass" size="sm" />
    </x-form.input-addon>
    <input type="text" name="query" class="..." />
    <x-ui.button class="rounded-l-none">Search</x-ui.button>
</x-form.input-group>

{{-- Phone number with country code --}}
<x-form.input-group label="Phone">
    <x-form.input-addon position="prepend">
        <x-icon name="phone" size="sm" />
    </x-form.input-addon>
    <select name="country_code" class="...">
        <option>+1</option>
        <option>+44</option>
    </select>
    <input type="tel" name="phone" class="..." />
</x-form.input-group>
```

## Common Use Cases

### 1. URLs and Domains
```blade
<x-form.input name="website">
    <x-slot:prepend>https://</x-slot:prepend>
    <x-slot:append>.com</x-slot:append>
</x-form.input>
```

### 2. Email Addresses
```blade
<x-form.input name="username">
    <x-slot:append>@company.com</x-slot:append>
</x-form.input>
```

### 3. Currency and Prices
```blade
<x-form.input name="price" type="number">
    <x-slot:prepend>$</x-slot:prepend>
    <x-slot:append>USD</x-slot:append>
</x-form.input>
```

### 4. Percentages
```blade
<x-form.input name="discount" type="number">
    <x-slot:append>%</x-slot:append>
</x-form.input>
```

### 5. Units of Measurement
```blade
<x-form.input name="weight" type="number">
    <x-slot:append>kg</x-slot:append>
</x-form.input>
```

### 6. Search with Icon
```blade
<x-form.input name="search">
    <x-slot:prepend>
        <x-icon name="magnifying-glass" size="sm" />
    </x-slot:prepend>
</x-form.input>
```

### 7. Location Selection
```blade
<x-form.select name="country" :options="$countries">
    <x-slot:prepend>
        <x-icon name="map-pin" size="sm" />
    </x-slot:prepend>
</x-form.select>
```

### 8. Action Buttons
```blade
<x-form.input name="code">
    <x-slot:append>
        <x-ui.button size="sm" wire:click="generate">Generate</x-ui.button>
    </x-slot:append>
</x-form.input>
```

## Styling Notes

- Addons automatically adjust border radius to connect seamlessly with inputs
- Prepend elements have `rounded-l-md` and `border-r-0`
- Append elements have `rounded-r-md` and `border-l-0`
- Middle elements in complex groups should use `rounded-none`
- All elements maintain consistent height and alignment
- Background color for text addons is `bg-gray-50` for visual distinction

## Accessibility

- Labels are properly associated with form inputs
- Error messages are displayed below the input group
- Hint text provides additional context
- Icons include appropriate sizing for readability
- All interactive elements maintain proper focus states

## Examples

See `resources/views/components/examples/input-groups.blade.php` for comprehensive examples of all input grouping patterns.
