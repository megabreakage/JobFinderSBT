@props([
    'striped' => true,
    'hover' => true,
])

@php
$tableClasses = 'min-w-full divide-y divide-gray-200';
$tbodyClasses = 'bg-white divide-y divide-gray-200';
$rowClasses = '';

if ($striped) {
    $tbodyClasses = 'bg-white divide-y divide-gray-200';
}

if ($hover) {
    $rowClasses .= ' hover:bg-gray-50';
}
@endphp

<div class="overflow-x-auto">
    <div class="inline-block min-w-full align-middle">
        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
            <table {{ $attributes->merge(['class' => $tableClasses]) }}>
                @isset($header)
                    <thead class="bg-gray-50">
                        {{ $header }}
                    </thead>
                @endisset

                <tbody class="{{ $tbodyClasses }}">
                    {{ $slot }}
                </tbody>

                @isset($footer)
                    <tfoot class="bg-gray-50">
                        {{ $footer }}
                    </tfoot>
                @endisset
            </table>
        </div>
    </div>
</div>

@pushOnce('styles')
<style>
    tbody tr.striped:nth-child(even) {
        background-color: #f9fafb;
    }
    tbody tr.hover:hover {
        background-color: #f3f4f6;
    }
</style>
@endPushOnce
