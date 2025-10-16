@props([
    'label' => null,
    'name' => null,
    'options' => [],
    'placeholder' => 'Select an option',
    'hint' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'multiple' => false,
    'selected' => null,
])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <select
        id="{{ $name }}"
        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm' . ($error ? ' border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500' : '')]) }}
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($multiple) multiple @endif
    >
        @if($placeholder && !$multiple)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $value => $label)
            <option 
                value="{{ $value }}"
                @if(is_array($selected) ? in_array($value, $selected) : $value == $selected) selected @endif
            >
                {{ $label }}
            </option>
        @endforeach
    </select>

    @if($hint && !$error)
        <p class="mt-1 text-sm text-gray-500">{{ $hint }}</p>
    @endif

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
