@props([
    'id' => 'modal-' . uniqid(),
    'title' => '',
    'size' => 'md',
])

@php
    $sizeClasses = match($size) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        default => 'max-w-md',
    };
@endphp

<div x-data="{ open: false }" @keydown.escape.window="open = false" class="relative z-50">
    {{-- Backdrop --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm"
         style="display: none;">
    </div>

    {{-- Modal --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 sm:scale-100"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95 sm:scale-100"
         class="fixed inset-0 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 z-50 w-full sm:w-auto {{ $sizeClasses }} max-h-[90vh] overflow-y-auto"
         style="display: none;">
        
        <div class="bg-white rounded-lg shadow-xl sm:rounded-xl">
            {{-- Header --}}
            @if($title)
                <div class="flex items-center justify-between px-4 sm:px-6 py-4 border-b border-slate-200">
                    <h2 class="text-lg sm:text-xl font-semibold text-slate-900">{{ $title }}</h2>
                    <button @click="open = false" class="text-slate-400 hover:text-slate-600 transition p-1 hover:bg-slate-100 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Content --}}
            <div class="px-4 sm:px-6 py-4 sm:py-6">
                {{ $slot }}
            </div>
        </div>
    </div>

    {{-- Trigger Slot --}}
    @isset($trigger)
        <div @click="open = true">
            {{ $trigger }}
        </div>
    @endisset
</div>
