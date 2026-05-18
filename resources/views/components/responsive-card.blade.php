@props([
    'title' => '',
    'icon' => null,
    'hoverable' => true,
    'padding' => 'p-4 sm:p-6',
])

<div class="bg-white border border-slate-200 rounded-lg sm:rounded-xl shadow-sm {{ $hoverable ? 'hover:shadow-md transition-shadow' : '' }} {{ $padding }}">
    {{-- Header --}}
    @if($title || $icon)
        <div class="flex items-center gap-3 mb-4">
            @if($icon)
                <div class="flex-shrink-0">
                    {{ $icon }}
                </div>
            @endif
            @if($title)
                <h3 class="text-base sm:text-lg font-semibold text-slate-900">{{ $title }}</h3>
            @endif
        </div>
    @endif

    {{-- Content --}}
    <div class="text-slate-700">
        {{ $slot }}
    </div>
</div>
