@props([
    'label' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
])

<div class="mb-4">
    @if($label)
        <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div {{ $attributes->merge(['class' => 'flex rounded-md shadow-sm']) }}>
        {{ $slot }}
    </div>

    @if($hint && !$error)
        <p class="mt-1 text-sm text-gray-500">{{ $hint }}</p>
    @endif

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
