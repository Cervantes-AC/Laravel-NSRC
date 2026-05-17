<div class="space-y-4" aria-label="{{ __('Personnel management') }}">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex flex-col sm:flex-row gap-4 flex-1">
            <div class="flex-1">
                <label for="personnel-search" class="sr-only">{{ __('Search personnel') }}</label>
                <input id="personnel-search" wire:model.debounce.300ms="search" type="text" placeholder="{{ __('Search by name or email...') }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" aria-label="{{ __('Search personnel') }}" />
            </div>
            <div>
                <label for="personnel-status" class="sr-only">{{ __('Filter by status') }}</label>
                <select id="personnel-status" wire:model="statusFilter" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" aria-label="{{ __('Filter by status') }}">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
            </div>
            <div>
                <label for="personnel-per-page" class="sr-only">{{ __('Per page') }}</label>
                <select id="personnel-per-page" wire:model="perPage" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" aria-label="{{ __('Records per page') }}">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
        <a href="{{ route('admin.personnel.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition" aria-label="{{ __('Create new personnel') }}">
            + {{ __('New') }}
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" aria-label="{{ __('Personnel table') }}">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Full Name') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Email') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Role') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('School ID') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($personnel ?? [] as $person)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $person->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $person->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($person->role) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $person->status === 'active' ? 'bg-green-100 text-green-800' : ($person->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($person->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $person->school_id ?? __('N/A') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-3">
                            <a href="{{ route('admin.personnel.show', $person) }}" class="text-indigo-600 hover:text-indigo-900" aria-label="{{ __('View') }}">{{ __('View') }}</a>
                            <a href="{{ route('admin.personnel.edit', $person) }}" class="text-yellow-600 hover:text-yellow-900" aria-label="{{ __('Edit') }}">{{ __('Edit') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('No personnel found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(is_object($personnel) && method_exists($personnel, 'links'))
        <div class="mt-4">
            {{ $personnel->links() }}
        </div>
    @endif
</div>
