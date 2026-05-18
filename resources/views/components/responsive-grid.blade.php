@props([
    'cols' => 1,
    'gap' => 4,
])

@php
    $colsClasses = match($cols) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 sm:grid-cols-2',
        3 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
        4 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
        6 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6',
        default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    };
    
    $gapClasses = match($gap) {
        2 => 'gap-2',
        3 => 'gap-3',
        4 => 'gap-4',
        6 => 'gap-6',
        8 => 'gap-8',
        default => 'gap-4',
    };
@endphp

<div class="grid {{ $colsClasses }} {{ $gapClasses }}">
    {{ $slot }}
</div>
