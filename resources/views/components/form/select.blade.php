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

@php
$hasPrepend = isset($prepend);
$hasAppend = isset($append);
$hasGroup = $hasPrepend || $hasAppend;

$selectClasses = 'block w-full border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm';

if ($hasGroup) {
    if ($hasPrepend && !$hasAppend) {
        $selectClasses .= ' rounded-r-md';
    } elseif (!$hasPrepend && $hasAppend) {
        $selectClasses .= ' rounded-l-md';
    }
} else {
    $selectClasses .= ' rounded-md';
}

if ($error) {
    $selectClasses .= ' border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500';
}
@endphp

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    @if($hasGroup)
        <div class="flex rounded-md shadow-sm">
            @isset($prepend)
                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                    {{ $prepend }}
                </span>
            @endisset

            <select
                id="{{ $name }}"
                name="{{ $name }}{{ $multiple ? '[]' : '' }}"
                {{ $attributes->merge(['class' => $selectClasses]) }}
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

            @isset($append)
                <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                    {{ $append }}
                </span>
            @endisset
        </div>
    @else
        <select
            id="{{ $name }}"
            name="{{ $name }}{{ $multiple ? '[]' : '' }}"
            {{ $attributes->merge(['class' => $selectClasses]) }}
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
    @endif

    @if($hint && !$error)
        <p class="mt-1 text-sm text-gray-500">{{ $hint }}</p>
    @endif

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
