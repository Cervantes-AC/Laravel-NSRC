<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Account Management') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div x-data="accountsApp" class="space-y-4" aria-label="{{ __('Account management') }}">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex flex-col sm:flex-row gap-4 flex-1">
                                <div class="flex-1">
                                    <input type="text" x-model="search" @input.debounce.300ms="loadAccounts()" placeholder="{{ __('Search by name or email...') }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                                <div>
                                    <select x-model="statusFilter" @change="loadAccounts()" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">{{ __('All Statuses') }}</option>
                                        <option value="active">{{ __('Active') }}</option>
                                        <option value="pending">{{ __('Pending') }}</option>
                                        <option value="suspended">{{ __('Suspended') }}</option>
                                        <option value="rejected">{{ __('Rejected') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <select x-model="bulkAction" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Bulk Actions') }}</option>
                                    <option value="approve">{{ __('Approve Selected') }}</option>
                                    <option value="suspend">{{ __('Suspend Selected') }}</option>
                                    <option value="reject">{{ __('Reject Selected') }}</option>
                                </select>
                                <button @click="executeBulkAction()" class="ml-2 inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">{{ __('Apply') }}</button>
                            </div>
                        </div>

                        <div x-show="loading" class="text-center py-4 text-gray-500">{{ __('Loading...') }}</div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left">
                                            <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Name') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Email') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Role') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Registered') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="a in accounts" :key="a.id">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" :checked="selectedAccounts.includes(a.id)" @change="toggleAccount(a.id)" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="a.full_name"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="a.email"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="a.role.charAt(0).toUpperCase() + a.role.slice(1)"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 py-1 rounded-full text-xs font-medium" :class="a.status === 'active' ? 'bg-green-100 text-green-800' : a.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : a.status === 'suspended' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800'" x-text="a.status.charAt(0).toUpperCase() + a.status.slice(1)"></span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="a.created_at"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                                <button @click="approve(a.id)" class="text-green-600 hover:text-green-900">{{ __('Approve') }}</button>
                                                <button @click="suspend(a.id)" class="text-orange-600 hover:text-orange-900">{{ __('Suspend') }}</button>
                                                <button @click="reject(a.id)" class="text-red-600 hover:text-red-900">{{ __('Reject') }}</button>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="accounts.length === 0">
                                        <tr><td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('No accounts found.') }}</td></tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <template x-if="totalPages > 1">
                            <div class="flex items-center justify-between mt-4">
                                <div class="text-xs font-bold text-slate-500">Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span></div>
                                <div class="flex items-center gap-1.5">
                                    <button @click="currentPage = Math.max(1, currentPage - 1); loadAccounts()" :disabled="currentPage === 1" class="px-3 py-1 bg-white border rounded disabled:opacity-30">Prev</button>
                                    <template x-for="p in totalPages" :key="p">
                                        <template x-if="totalPages <= 7 || p === 1 || p === totalPages || (p >= currentPage - 1 && p <= currentPage + 1)">
                                            <button @click="currentPage = p; loadAccounts()" class="px-3 py-1 rounded text-xs font-black" :class="currentPage === p ? 'bg-indigo-600 text-white' : 'bg-white border'"><span x-text="p"></span></button>
                                        </template>
                                    </template>
                                    <button @click="currentPage = Math.min(totalPages, currentPage + 1); loadAccounts()" :disabled="currentPage === totalPages" class="px-3 py-1 bg-white border rounded disabled:opacity-30">Next</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
