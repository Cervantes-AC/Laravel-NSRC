<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Admin Dashboard') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(!empty($storageWarning))
                <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800" role="alert">
                    {{ $storageWarning }}
                </div>
            @endif
            @livewire('dashboard')
        </div>
    </div>
</x-app-layout>
