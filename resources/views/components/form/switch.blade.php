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
    <div class="flex items-center justify-between">
        <div class="flex-1">
            @if($label)
                <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
                    {{ $label }}
                </label>
            @endif
            @if($hint && !$error)
                <p class="text-sm text-gray-500">{{ $hint }}</p>
            @endif
        </div>

        <div class="ml-4">
            <label class="relative inline-flex items-center cursor-pointer">
                <input
                    type="checkbox"
                    id="{{ $name }}"
                    name="{{ $name }}"
                    value="{{ $value }}"
                    class="sr-only peer"
                    {{ $attributes }}
                    @if($checked) checked @endif
                    @if($disabled) disabled @endif
                />
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed"></div>
            </label>
        </div>
    </div>

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
