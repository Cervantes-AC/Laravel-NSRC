<div class="space-y-4" aria-label="{{ __('Audit logs') }}">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex flex-col sm:flex-row gap-4 flex-1">
            <div class="flex-1">
                <label for="audit-search" class="sr-only">{{ __('Search audit logs') }}</label>
                <input id="audit-search" wire:model.debounce.300ms="search" type="text" placeholder="{{ __('Search by user or details...') }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" aria-label="{{ __('Search audit logs') }}" />
            </div>
            <div>
                <label for="audit-type" class="sr-only">{{ __('Filter by type') }}</label>
                <select id="audit-type" wire:model="typeFilter" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" aria-label="{{ __('Filter by audit type') }}">
                    <option value="">{{ __('All Types') }}</option>
                    <option value="login">{{ __('Login') }}</option>
                    <option value="logout">{{ __('Logout') }}</option>
                    <option value="create">{{ __('Create') }}</option>
                    <option value="update">{{ __('Update') }}</option>
                    <option value="delete">{{ __('Delete') }}</option>
                    <option value="export">{{ __('Export') }}</option>
                    <option value="import">{{ __('Import') }}</option>
                </select>
            </div>
            <div>
                <label for="audit-date-from" class="sr-only">{{ __('Date from') }}</label>
                <input id="audit-date-from" wire:model="dateFrom" type="date" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" aria-label="{{ __('Date from') }}" />
            </div>
            <div>
                <label for="audit-date-to" class="sr-only">{{ __('Date to') }}</label>
                <input id="audit-date-to" wire:model="dateTo" type="date" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" aria-label="{{ __('Date to') }}" />
            </div>
            <div>
                <label for="audit-per-page" class="sr-only">{{ __('Per page') }}</label>
                <select id="audit-per-page" wire:model="perPage" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" aria-label="{{ __('Records per page') }}">
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
        <div class="flex gap-2">
            <button wire:click="clearFilters" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition text-sm" aria-label="{{ __('Clear filters') }}">
                {{ __('Clear Filters') }}
            </button>
            <button wire:click="export" class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition text-sm" aria-label="{{ __('Export audit logs') }}">
                {{ __('Export') }}
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" aria-label="{{ __('Audit log table') }}">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Timestamp') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('User') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Type') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Action') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Details') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('IP Address') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($logs ?? [] as $log)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->user->full_name ?? __('System') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $log->type === 'login' || $log->type === 'logout' ? 'bg-blue-100 text-blue-800' : ($log->type === 'create' ? 'bg-green-100 text-green-800' : ($log->type === 'update' ? 'bg-yellow-100 text-yellow-800' : ($log->type === 'delete' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                                {{ ucfirst($log->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->action }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">{{ $log->details ?? __('N/A') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">{{ $log->ip_address ?? __('N/A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('No audit logs found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(is_object($logs) && method_exists($logs, 'links'))
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    @endif
</div>
