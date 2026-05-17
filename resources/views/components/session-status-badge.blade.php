@props(['status'])

@php
    $normalized = strtoupper((string) $status);
    $classes = match ($normalized) {
        'COMPLETE' => 'bg-green-100 text-green-800',
        'ONGOING' => 'bg-blue-100 text-blue-800',
        'MISSING_TIMEOUT' => 'bg-amber-100 text-amber-800',
        'INVALID_LOG' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800',
    };
    $label = str_replace('_', ' ', $normalized);
@endphp

<span {{ $attributes->merge(['class' => "px-2 py-1 rounded-full text-xs font-medium {$classes}"]) }}>
    {{ ucwords(strtolower($label)) }}
</span>
