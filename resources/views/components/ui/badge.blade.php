@props([
    'variant' => 'default',
    'size' => 'md',
    'rounded' => true,
])

@php
$baseClasses = 'inline-flex items-center font-medium';

$variantClasses = [
    'default' => 'bg-gray-100 text-gray-800',
    'primary' => 'bg-indigo-100 text-indigo-800',
    'secondary' => 'bg-gray-100 text-gray-800',
    'success' => 'bg-green-100 text-green-800',
    'danger' => 'bg-red-100 text-red-800',
    'warning' => 'bg-yellow-100 text-yellow-800',
    'info' => 'bg-blue-100 text-blue-800',
];

$sizeClasses = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-0.5 text-sm',
    'lg' => 'px-3 py-1 text-base',
];

$roundedClass = $rounded ? 'rounded-full' : 'rounded';

$classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size] . ' ' . $roundedClass;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
