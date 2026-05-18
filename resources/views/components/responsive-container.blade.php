@props([
    'size' => 'lg',
])

@php
    $sizeClasses = match($size) {
        'sm' => 'max-w-2xl',
        'md' => 'max-w-4xl',
        'lg' => 'max-w-6xl',
        'xl' => 'max-w-7xl',
        'full' => 'max-w-full',
        default => 'max-w-6xl',
    };
@endphp

<div class="mx-auto px-4 sm:px-6 lg:px-8 {{ $sizeClasses }}">
    {{ $slot }}
</div>
