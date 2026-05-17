<div class="space-y-4" aria-label="{{ __('Account management') }}">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex flex-col sm:flex-row gap-4 flex-1">
            <div class="flex-1">
                <label for="accounts-search" class="sr-only">{{ __('Search accounts') }}</label>
                <input id="accounts-search" wire:model.debounce.300ms="search" type="text" placeholder="{{ __('Search by name or email...') }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" aria-label="{{ __('Search accounts') }}" />
            </div>
            <div>
                <label for="accounts-status" class="sr-only">{{ __('Filter by status') }}</label>
                <select id="accounts-status" wire:model="statusFilter" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" aria-label="{{ __('Filter by status') }}">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="suspended">{{ __('Suspended') }}</option>
                    <option value="rejected">{{ __('Rejected') }}</option>
                </select>
            </div>
        </div>
        <div>
            <select wire:model="bulkAction" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" aria-label="{{ __('Bulk actions') }}">
                <option value="">{{ __('Bulk Actions') }}</option>
                <option value="approve">{{ __('Approve Selected') }}</option>
                <option value="suspend">{{ __('Suspend Selected') }}</option>
                <option value="reject">{{ __('Reject Selected') }}</option>
            </select>
            <button wire:click="executeBulkAction" class="ml-2 inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition" aria-label="{{ __('Apply bulk action') }}">
                {{ __('Apply') }}
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" aria-label="{{ __('Accounts table') }}">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left">
                        <input type="checkbox" wire:model="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" aria-label="{{ __('Select all accounts') }}" />
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Name') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Email') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Role') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Registered') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($accounts ?? [] as $account)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" wire:model="selectedAccounts" value="{{ $account->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" aria-label="{{ __('Select account') }} {{ $account->full_name }}" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $account->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $account->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($account->role) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $account->status === 'active' ? 'bg-green-100 text-green-800' : ($account->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($account->status === 'suspended' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800')) }}">
                                {{ ucfirst($account->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $account->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                            <button wire:click="approve({{ $account->id }})" class="text-green-600 hover:text-green-900" aria-label="{{ __('Approve') }} {{ $account->full_name }}">{{ __('Approve') }}</button>
                            <button wire:click="suspend({{ $account->id }})" class="text-orange-600 hover:text-orange-900" aria-label="{{ __('Suspend') }} {{ $account->full_name }}">{{ __('Suspend') }}</button>
                            <button wire:click="reject({{ $account->id }})" class="text-red-600 hover:text-red-900" aria-label="{{ __('Reject') }} {{ $account->full_name }}">{{ __('Reject') }}</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('No accounts found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($accounts ?? [], 'links'))
        <div class="mt-4">
            {{ $accounts->links() }}
        </div>
    @endif
</div>
