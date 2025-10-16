@props([
    'position' => 'prepend', // prepend or append
])

@php
$classes = 'inline-flex items-center px-3 border border-gray-300 bg-gray-50 text-gray-500 sm:text-sm';

if ($position === 'prepend') {
    $classes .= ' rounded-l-md border-r-0';
} else {
    $classes .= ' rounded-r-md border-l-0';
}
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
