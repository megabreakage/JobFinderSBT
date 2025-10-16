@props([
    'label' => null,
    'name' => null,
    'value' => '',
    'placeholder' => '',
    'hint' => null,
    'error' => null,
    'rows' => 4,
    'required' => false,
    'disabled' => false,
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

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm' . ($error ? ' border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : '')]) }}
        @if($required) required @endif
        @if($disabled) disabled @endif
    >{{ $value }}</textarea>

    @if($hint && !$error)
        <p class="mt-1 text-sm text-gray-500">{{ $hint }}</p>
    @endif

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
