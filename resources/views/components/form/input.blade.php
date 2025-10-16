@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'hint' => null,
    'error' => null,
    'icon' => null,
    'required' => false,
    'disabled' => false,
])

@php
$hasPrepend = isset($prepend);
$hasAppend = isset($append);
$hasGroup = $hasPrepend || $hasAppend;

$inputClasses = 'block w-full border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm';

if ($hasGroup) {
    if ($hasPrepend && !$hasAppend) {
        $inputClasses .= ' rounded-r-md';
    } elseif (!$hasPrepend && $hasAppend) {
        $inputClasses .= ' rounded-l-md';
    }
} else {
    $inputClasses .= ' rounded-md';
}

if ($icon && !$hasGroup) {
    $inputClasses .= ' pl-10';
}

if ($error) {
    $inputClasses .= ' border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500';
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

            <input
                type="{{ $type }}"
                id="{{ $name }}"
                name="{{ $name }}"
                value="{{ $value }}"
                placeholder="{{ $placeholder }}"
                {{ $attributes->merge(['class' => $inputClasses]) }}
                @if($required) required @endif
                @if($disabled) disabled @endif
            />

            @isset($append)
                <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                    {{ $append }}
                </span>
            @endisset
        </div>
    @else
        <div class="relative">
            @if($icon)
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-icon :name="$icon" class="h-5 w-5 text-gray-400" />
                </div>
            @endif

            <input
                type="{{ $type }}"
                id="{{ $name }}"
                name="{{ $name }}"
                value="{{ $value }}"
                placeholder="{{ $placeholder }}"
                {{ $attributes->merge(['class' => $inputClasses]) }}
                @if($required) required @endif
                @if($disabled) disabled @endif
            />
        </div>
    @endif

    @if($hint && !$error)
        <p class="mt-1 text-sm text-gray-500">{{ $hint }}</p>
    @endif

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
