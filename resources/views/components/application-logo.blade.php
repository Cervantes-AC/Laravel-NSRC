@props([
    'alt' => config('app.name', 'NSRC'),
])

@php
    $src = config('app.logo', 'images/nsrc-logo.png');

    if (! str_starts_with($src, 'http://') && ! str_starts_with($src, 'https://')) {
        $src = asset(ltrim($src, '/'));
    }
@endphp

<img src="{{ $src }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => 'object-contain']) }} />
