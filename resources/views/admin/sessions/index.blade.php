<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Duty Sessions') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div>
                                <label for="date-filter" class="sr-only">{{ __('Filter by date') }}</label>
                                <input id="date-filter" type="date" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" aria-label="{{ __('Filter by date') }}" />
                            </div>
                            <div>
                                <label for="status-filter" class="sr-only">{{ __('Filter by status') }}</label>
                                <select id="status-filter" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" aria-label="{{ __('Filter by status') }}">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    <option value="completed">{{ __('Completed') }}</option>
                                    <option value="ongoing">{{ __('Ongoing') }}</option>
                                    <option value="cancelled">{{ __('Cancelled') }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="search" class="sr-only">{{ __('Search personnel') }}</label>
                                <input id="search" type="text" placeholder="{{ __('Search personnel...') }}" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" aria-label="{{ __('Search personnel') }}" />
                            </div>
                        </div>
                        <a href="{{ route('admin.sessions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition" aria-label="{{ __('Create duty session') }}">
                            + {{ __('New Session') }}
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" aria-label="{{ __('Duty sessions table') }}">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Name') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Date') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Time In') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Time Out') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Duration') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($sessions ?? [] as $session)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $session->user->full_name ?? __('N/A') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->date->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->time_in ? $session->time_in->format('h:i A') : __('N/A') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->time_out ? $session->time_out->format('h:i A') : __('N/A') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->duration ? $session->duration . ' ' . __('mins') : __('Ongoing') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $session->status === 'completed' ? 'bg-green-100 text-green-800' : ($session->status === 'ongoing' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                                {{ ucfirst($session->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('admin.sessions.show', $session) }}" class="text-indigo-600 hover:text-indigo-900" aria-label="{{ __('View session') }}">{{ __('View') }}</a>
                                            <a href="{{ route('admin.sessions.edit', $session) }}" class="ml-3 text-yellow-600 hover:text-yellow-900" aria-label="{{ __('Edit session') }}">{{ __('Edit') }}</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('No duty sessions found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
