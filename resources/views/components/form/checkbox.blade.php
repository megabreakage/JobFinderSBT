@props([
    'label' => null,
    'name' => null,
    'value' => '1',
    'checked' => false,
    'hint' => null,
    'error' => null,
    'disabled' => false,
])

<div class="mb-4">
    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input
                type="checkbox"
                id="{{ $name }}"
                name="{{ $name }}"
                value="{{ $value }}"
                {{ $attributes->merge(['class' => 'h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500' . ($error ? ' border-red-300' : '')]) }}
                @if($checked) checked @endif
                @if($disabled) disabled @endif
            />
        </div>

        @if($label)
            <div class="ml-3 text-sm">
                <label for="{{ $name }}" class="font-medium text-gray-700">
                    {{ $label }}
                </label>
                @if($hint && !$error)
                    <p class="text-gray-500">{{ $hint }}</p>
                @endif
            </div>
        @endif
    </div>

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
