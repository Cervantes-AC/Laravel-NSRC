<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Audit Logs') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div x-data="auditLogsApp" class="space-y-4" aria-label="{{ __('Audit logs') }}">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex flex-col sm:flex-row gap-4 flex-1">
                                <div class="flex-1">
                                    <input type="text" x-model="search" @input.debounce.300ms="loadLogs()" placeholder="{{ __('Search by user or details...') }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                                <div>
                                    <select x-model="type" @change="loadLogs()" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                                    <input type="date" x-model="dateFrom" @change="loadLogs()" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                                <div>
                                    <input type="date" x-model="dateTo" @change="loadLogs()" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                                <div>
                                    <select x-model="perPage" @change="loadLogs()" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="15">15</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button @click="clearFilters()" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition text-sm">{{ __('Clear') }}</button>
                                <button @click="exportLogs()" class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition text-sm">{{ __('Export') }}</button>
                            </div>
                        </div>

                        <div x-show="loading" class="text-center py-4 text-gray-500">{{ __('Loading...') }}</div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Timestamp') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('User') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Type') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Action') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Details') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('IP Address') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="l in logs" :key="l.id">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="l.created_at"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="l.user"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 py-1 rounded-full text-xs font-medium" :class="l.type === 'login' || l.type === 'logout' ? 'bg-blue-100 text-blue-800' : l.type === 'create' ? 'bg-green-100 text-green-800' : l.type === 'update' ? 'bg-yellow-100 text-yellow-800' : l.type === 'delete' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'" x-text="l.type.charAt(0).toUpperCase() + l.type.slice(1)"></span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="l.action"></td>
                                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate" x-text="l.details"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono" x-text="l.ip_address"></td>
                                        </tr>
                                    </template>
                                    <template x-if="logs.length === 0">
                                        <tr><td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('No audit logs found.') }}</td></tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <template x-if="totalPages > 1">
                            <div class="flex items-center justify-between mt-4">
                                <div class="text-xs font-bold text-slate-500">Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span></div>
                                <div class="flex items-center gap-1.5">
                                    <button @click="currentPage = Math.max(1, currentPage - 1); loadLogs()" :disabled="currentPage === 1" class="px-3 py-1 bg-white border rounded disabled:opacity-30">Prev</button>
                                    <template x-for="p in totalPages" :key="p">
                                        <template x-if="totalPages <= 7 || p === 1 || p === totalPages || (p >= currentPage - 1 && p <= currentPage + 1)">
                                            <button @click="currentPage = p; loadLogs()" class="px-3 py-1 rounded text-xs font-black" :class="currentPage === p ? 'bg-indigo-600 text-white' : 'bg-white border'" x-text="p"></button>
                                        </template>
                                    </template>
                                    <button @click="currentPage = Math.min(totalPages, currentPage + 1); loadLogs()" :disabled="currentPage === totalPages" class="px-3 py-1 bg-white border rounded disabled:opacity-30">Next</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
