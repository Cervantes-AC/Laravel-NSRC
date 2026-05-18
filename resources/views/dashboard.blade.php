<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="dashboard" class="space-y-6">
                <div x-show="loading" class="text-center py-4 text-gray-500">Loading...</div>
                <template x-if="data">
                    <div class="space-y-6">
                        <div class="text-center py-12 text-gray-500">Redirecting to your dashboard...</div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</x-app-layout>
