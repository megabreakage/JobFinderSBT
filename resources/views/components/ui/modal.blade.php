@props([
    'show' => false,
    'size' => 'md',
    'title' => null,
])

@php
$sizeClasses = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-lg',
    'lg' => 'sm:max-w-2xl',
    'xl' => 'sm:max-w-4xl',
    'full' => 'sm:max-w-full sm:m-4',
];

$maxWidth = $sizeClasses[$size];
@endphp

@if($show)
<div
    class="relative z-50"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    {{-- Background backdrop --}}
    <div
        wire:click="$set('{{ $attributes->wire('model')->value() }}', false)"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    ></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            {{-- Modal panel --}}
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full {{ $maxWidth }}">
                @if($title)
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                {{ $title }}
                            </h3>
                            <button
                                type="button"
                                wire:click="$set('{{ $attributes->wire('model')->value() }}', false)"
                                class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    {{ $slot }}
                </div>

                @isset($footer)
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-200">
                        {{ $footer }}
                    </div>
                @endisset
            </div>
        </div>
    </div>
</div>
@endif
