@props([
    'padding' => true,
])

@php
$cardClasses = 'bg-white overflow-hidden shadow rounded-lg';
$bodyClasses = $padding ? 'px-4 py-5 sm:p-6' : '';
@endphp

<div {{ $attributes->merge(['class' => $cardClasses]) }}>
    @isset($header)
        <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
            {{ $header }}
        </div>
    @endisset

    <div class="{{ $bodyClasses }}">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="border-t border-gray-200 bg-gray-50 px-4 py-4 sm:px-6">
            {{ $footer }}
        </div>
    @endisset
</div>
