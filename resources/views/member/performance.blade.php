<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Performance') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Total Hours') }}</dt>
                        <dd class="mt-2 text-3xl font-bold text-indigo-600">{{ $metrics['total_hours'] ?? 0 }}</dd>
                        <dd class="text-xs text-gray-500">{{ __('Lifetime volunteer hours') }}</dd>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Total Sessions') }}</dt>
                        <dd class="mt-2 text-3xl font-bold text-green-600">{{ $metrics['total_sessions'] ?? 0 }}</dd>
                        <dd class="text-xs text-gray-500">{{ __('Completed duty sessions') }}</dd>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Avg Duration') }}</dt>
                        <dd class="mt-2 text-3xl font-bold text-blue-600">{{ $metrics['avg_duration'] ?? 0 }} <span class="text-lg">{{ __('min') }}</span></dd>
                        <dd class="text-xs text-gray-500">{{ __('Average session length') }}</dd>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Detailed Breakdown') }}</h3>

                    @if(isset($metrics['monthly']) && count($metrics['monthly']) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" aria-label="{{ __('Monthly breakdown table') }}">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Month') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Sessions') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Total Hours') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Avg Duration') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($metrics['monthly'] as $month)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $month['label'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $month['sessions'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $month['total_hours'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $month['avg_duration'] }} {{ __('min') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">{{ __('No performance data available yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
