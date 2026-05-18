@props([
    'variant' => 'primary',
    'size' => 'md',
    'fullWidth' => false,
    'icon' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 active:scale-95';
    
    $sizeClasses = match($size) {
        'sm' => 'px-3 py-2 text-xs min-h-[36px] min-w-[36px]',
        'md' => 'px-4 py-2.5 text-sm min-h-[44px] min-w-[44px]',
        'lg' => 'px-6 py-3 text-base min-h-[48px] min-w-[48px]',
        default => 'px-4 py-2.5 text-sm min-h-[44px] min-w-[44px]',
    };
    
    $variantClasses = match($variant) {
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 shadow-sm hover:shadow-md',
        'secondary' => 'bg-slate-200 text-slate-900 hover:bg-slate-300 focus:ring-slate-500 shadow-sm hover:shadow-md',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 shadow-sm hover:shadow-md',
        'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500 shadow-sm hover:shadow-md',
        'outline' => 'border-2 border-slate-300 text-slate-700 hover:bg-slate-50 focus:ring-slate-500',
        'ghost' => 'text-slate-700 hover:bg-slate-100 focus:ring-slate-500',
        default => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 shadow-sm hover:shadow-md',
    };
    
    $widthClasses = $fullWidth ? 'w-full' : '';
@endphp

<button {{ $attributes->merge(['class' => "$baseClasses $sizeClasses $variantClasses $widthClasses"]) }}>
    @if($icon)
        <span class="mr-2">{{ $icon }}</span>
    @endif
    {{ $slot }}
</button>
