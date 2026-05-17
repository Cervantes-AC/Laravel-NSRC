<div class="space-y-6" aria-label="{{ __('Dashboard') }}">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h3 class="text-lg font-medium text-gray-900">{{ __('Overview') }}</h3>
        <div class="flex items-center gap-2">
            <select wire:model.live="dateFilter" class="rounded-lg border-gray-300 text-sm shadow-sm">
                <option value="today">{{ __('Today') }}</option>
                <option value="all">{{ __('All Time') }}</option>
            </select>
            <button wire:click="refresh" class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition" aria-label="{{ __('Refresh dashboard') }}">
                <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                {{ __('Refresh') }}
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <dt class="text-sm font-medium text-gray-500">{{ __('Total Records') }}</dt>
            <dd class="mt-2 text-3xl font-bold text-gray-900">{{ $totalRecords }}</dd>
            <dd class="text-xs text-gray-500">{{ __('All time sessions') }}</dd>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <dt class="text-sm font-medium text-gray-500">{{ __('Today') }}</dt>
            <dd class="mt-2 text-3xl font-bold text-indigo-600">{{ $todayCount }}</dd>
            <dd class="text-xs text-gray-500">{{ __('Sessions today') }}</dd>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <dt class="text-sm font-medium text-gray-500">{{ __('Active Now') }}</dt>
            <dd class="mt-2 text-3xl font-bold text-green-600">{{ $activeNow }}</dd>
            <dd class="text-xs text-gray-500">{{ __('Currently on duty') }}</dd>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <dt class="text-sm font-medium text-gray-500">{{ __('Avg Duration') }}</dt>
            <dd class="mt-2 text-3xl font-bold text-blue-600">{{ number_format($avgDuration, 0) }} <span class="text-lg">{{ __('min') }}</span></dd>
            <dd class="text-xs text-gray-500">{{ __('Per session') }}</dd>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-medium text-gray-900">{{ __('Recent Sessions') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" aria-label="{{ __('Recent sessions table') }}">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Name') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Date') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Time In') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Time Out') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Duration') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentSessions ?? [] as $session)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $session->volunteer?->full_name ?? $session->full_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->time_in ? $session->time_in->format('h:i A') : __('N/A') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->time_out ? $session->time_out->format('h:i A') : __('---') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->duration_minutes ? $session->duration_minutes . ' ' . __('mins') : __('Ongoing') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <x-session-status-badge :status="$session->status" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('No recent sessions.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
