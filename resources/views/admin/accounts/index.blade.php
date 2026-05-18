<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Account Management') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div x-data="accountsApp" class="space-y-6 max-w-7xl mx-auto px-1" aria-label="{{ __('Account management') }}">

                        {{-- Header section --}}
                        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-50 to-blue-50 border border-slate-200 p-8 shadow-lg">
                            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-600/30 shrink-0">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z"></path></svg>
                                    </div>
                                    <div>
                                        <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900">Accounts</h1>
                                        <p class="text-sm text-slate-600 font-bold mt-1"><span x-text="stats.total"></span> total accounts</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Stats cards --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="bg-white border border-slate-200 rounded-xl px-5 py-4 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Total</p>
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <p class="text-2xl font-black text-slate-900 leading-none" x-text="stats.total"></p>
                                <p class="text-[10px] text-slate-500 font-medium mt-1">registered accounts</p>
                            </div>
                            <div class="bg-white border border-green-200 rounded-xl px-5 py-4 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Active</p>
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-2xl font-black text-green-600 leading-none" x-text="stats.active"></p>
                                <p class="text-[10px] text-slate-500 font-medium mt-1">active users</p>
                            </div>
                            <div class="bg-white border border-yellow-200 rounded-xl px-5 py-4 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Pending</p>
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-2xl font-black text-yellow-600 leading-none" x-text="stats.pending"></p>
                                <p class="text-[10px] text-slate-500 font-medium mt-1">awaiting approval</p>
                            </div>
                            <div class="bg-white border border-red-200 rounded-xl px-5 py-4 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Suspended</p>
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <p class="text-2xl font-black text-red-600 leading-none" x-text="stats.suspended"></p>
                                <p class="text-[10px] text-slate-500 font-medium mt-1">suspended accounts</p>
                            </div>
                        </div>

                        {{-- Error / Success banners --}}
                        <div x-show="errorMessage" x-text="errorMessage" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"></div>
                        <div x-show="successMessage" x-text="successMessage" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"></div>

                        {{-- Toolbar --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 bg-white border border-slate-200 rounded-xl px-5 py-3 shadow-sm">
                            <div class="flex items-center gap-2 flex-1">
                                <div class="relative group flex-1 max-w-sm">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    <input type="text" x-model="search" @input.debounce.300ms="applyFilters()"
                                        placeholder="{{ __('Search by name or email...') }}"
                                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 outline-none transition-all shadow-sm hover:border-slate-300" />
                                </div>
                                <div class="flex gap-1.5">
                                    <button @click="statusFilter = ''; applyFilters()" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all border" :class="statusFilter === '' ? 'bg-blue-100 text-blue-700 border-blue-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700'">All</button>
                                    <button @click="statusFilter = 'active'; applyFilters()" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all border" :class="statusFilter === 'active' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700'">Active</button>
                                    <button @click="statusFilter = 'pending'; applyFilters()" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all border" :class="statusFilter === 'pending' ? 'bg-yellow-100 text-yellow-700 border-yellow-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700'">Pending</button>
                                    <button @click="statusFilter = 'suspended'; applyFilters()" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all border" :class="statusFilter === 'suspended' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700'">Suspended</button>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sort:</span>
                                <button @click="toggleSort('full_name')" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all border" :class="sortBy === 'full_name' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700'">Name <span x-text="sortBy === 'full_name' ? (sortDirection === 'asc' ? '↑' : '↓') : ''"></span></button>
                                <button @click="toggleSort('created_at')" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all border" :class="sortBy === 'created_at' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700'">Date <span x-text="sortBy === 'created_at' ? (sortDirection === 'asc' ? '↑' : '↓') : ''"></span></button>
                                <button @click="toggleSort('last_login_at')" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all border" :class="sortBy === 'last_login_at' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700'">Last Login <span x-text="sortBy === 'last_login_at' ? (sortDirection === 'asc' ? '↑' : '↓') : ''"></span></button>
                            </div>
                        </div>

                        {{-- Bulk actions bar --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center gap-3 text-sm text-gray-500">
                                <select x-model="perPage" @change="applyFilters()"
                                    class="rounded-lg border-gray-300 text-sm shadow-sm">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span>per page</span>
                                <span x-show="selectedAccounts.length > 0"
                                    x-text="selectedAccounts.length + ' selected'" class="font-semibold text-indigo-600">
                                </span>
                            </div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <select x-model="bulkAction"
                                    class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Bulk Actions') }}</option>
                                    <option value="approve">{{ __('Approve Selected') }}</option>
                                    <option value="suspend">{{ __('Suspend Selected') }}</option>
                                    <option value="reject">{{ __('Reject Selected') }}</option>
                                </select>
                                <button @click="executeBulkAction()" :disabled="!bulkAction || selectedAccounts.length === 0"
                                    class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition disabled:opacity-40">
                                    {{ __('Apply') }}
                                </button>
                                <button @click="exportCSV()"
                                    class="inline-flex items-center px-3 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg transition">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    {{ __('Export CSV') }}
                                </button>
                            </div>
                        </div>

                        <div x-show="loading" class="text-center py-8 text-gray-500">{{ __('Loading...') }}</div>

                        <template x-if="!loading && accounts.length === 0">
                            <div class="py-24 flex flex-col items-center text-center bg-white border border-slate-200 border-dashed rounded-2xl">
                                <div class="w-20 h-20 bg-slate-100 rounded-2xl flex items-center justify-center mb-6">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <p class="text-base font-black text-slate-700 uppercase tracking-widest mb-2">No accounts found</p>
                                <p class="text-sm text-slate-500 max-w-md">Try adjusting your search or filter criteria.</p>
                            </div>
                        </template>

                        <template x-if="!loading && accounts.length > 0">
                            <div class="overflow-x-auto rounded-xl border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left">
                                                <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()"
                                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Email') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Role') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Last Login') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template x-for="a in accounts" :key="a.id">
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <input type="checkbox"
                                                        :checked="selectedAccounts.includes(a.id)"
                                                        @change="toggleAccount(a.id)"
                                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center font-black text-xs border"
                                                            :class="getAvatarColor(a.full_name)"
                                                            x-text="getInitials(a.full_name)"></div>
                                                        <span class="text-sm font-medium text-gray-900" x-text="a.full_name"></span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700" x-text="a.email"></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <span class="px-2 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200"
                                                        x-text="a.role ? a.role.charAt(0).toUpperCase() + a.role.slice(1) : 'Member'"></span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium"
                                                        :class="{
                                                            'bg-green-100 text-green-800 border border-green-200':  a.status === 'active',
                                                            'bg-yellow-100 text-yellow-800 border border-yellow-200': a.status === 'pending',
                                                            'bg-orange-100 text-orange-800 border border-orange-200': a.status === 'suspended',
                                                            'bg-red-100 text-red-800 border border-red-200':      a.status === 'rejected',
                                                        }"
                                                        x-text="a.status ? a.status.charAt(0).toUpperCase() + a.status.slice(1) : 'Unknown'">
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="a.last_login_at"></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                                    <a :href="'/admin/accounts/' + a.id + '/edit'"
                                                        class="text-indigo-600 hover:text-indigo-900 font-medium">{{ __('Edit') }}</a>
                                                    <button @click="viewDetails(a)"
                                                        class="text-blue-600 hover:text-blue-900 font-medium">{{ __('View') }}</button>
                                                    <template x-if="a.status === 'pending'">
                                                        <button @click="approve(a.id)"
                                                            class="text-green-600 hover:text-green-900 font-medium">{{ __('Approve') }}</button>
                                                    </template>
                                                    <template x-if="a.status !== 'suspended'">
                                                        <button @click="confirmAction('suspend', a.id, a.full_name)"
                                                            class="text-orange-600 hover:text-orange-900 font-medium">{{ __('Suspend') }}</button>
                                                    </template>
                                                    <template x-if="a.status === 'suspended'">
                                                        <button @click="confirmAction('activate', a.id, a.full_name)"
                                                            class="text-green-600 hover:text-green-900 font-medium">{{ __('Activate') }}</button>
                                                    </template>
                                                    <template x-if="a.status !== 'rejected'">
                                                        <button @click="confirmAction('reject', a.id, a.full_name)"
                                                            class="text-red-600 hover:text-red-900 font-medium">{{ __('Reject') }}</button>
                                                    </template>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </template>

                        {{-- Pagination --}}
                        <template x-if="totalPages > 1">
                            <div class="flex items-center justify-between mt-4">
                                <div class="text-xs font-bold text-slate-500">
                                    Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                                    &nbsp;(<span x-text="total"></span> total)
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                                        class="px-3 py-1 bg-white border rounded disabled:opacity-30">Prev</button>
                                    <template x-for="p in pageNumbers" :key="p">
                                        <button @click="p !== '…' && goToPage(p)"
                                            class="px-3 py-1 rounded text-xs font-black"
                                            :class="p === currentPage
                                                ? 'bg-indigo-600 text-white'
                                                : p === '…'
                                                    ? 'cursor-default text-slate-400'
                                                    : 'bg-white border text-slate-600 hover:border-slate-400'"
                                            x-text="p">
                                        </button>
                                    </template>
                                    <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                                        class="px-3 py-1 bg-white border rounded disabled:opacity-30">Next</button>
                                </div>
                            </div>
                        </template>

                        {{-- Account Detail Modal --}}
                        <template x-if="showDetailModal">
                            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="closeDetailModal()">
                                <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[80vh] overflow-y-auto p-8">
                                    <div class="flex items-center justify-between mb-6">
                                        <h3 class="text-xl font-black text-gray-900">Account Details</h3>
                                        <button @click="closeDetailModal()" class="p-2 rounded-lg hover:bg-gray-100 transition">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <template x-if="selectedAccount">
                                        <div class="space-y-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-16 h-16 rounded-xl flex items-center justify-center font-black text-xl border"
                                                    :class="getAvatarColor(selectedAccount.full_name)"
                                                    x-text="getInitials(selectedAccount.full_name)"></div>
                                                <div>
                                                    <h4 class="text-lg font-bold text-gray-900" x-text="selectedAccount.full_name"></h4>
                                                    <p class="text-sm text-gray-500" x-text="selectedAccount.email"></p>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Role</p>
                                                    <p class="text-sm font-bold text-slate-900 mt-1" x-text="selectedAccount.role ? selectedAccount.role.charAt(0).toUpperCase() + selectedAccount.role.slice(1) : 'Member'"></p>
                                                </div>
                                                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Status</p>
                                                    <p class="text-sm font-bold mt-1" :class="{
                                                        'text-green-600': selectedAccount.status === 'active',
                                                        'text-yellow-600': selectedAccount.status === 'pending',
                                                        'text-orange-600': selectedAccount.status === 'suspended',
                                                        'text-red-600': selectedAccount.status === 'rejected',
                                                    }" x-text="selectedAccount.status ? selectedAccount.status.charAt(0).toUpperCase() + selectedAccount.status.slice(1) : 'Unknown'"></p>
                                                </div>
                                                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Registered</p>
                                                    <p class="text-sm font-bold text-slate-900 mt-1" x-text="selectedAccount.created_at"></p>
                                                </div>
                                                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Last Login</p>
                                                    <p class="text-sm font-bold text-slate-900 mt-1" x-text="selectedAccount.last_login_at"></p>
                                                </div>
                                                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Email Verified</p>
                                                    <p class="text-sm font-bold mt-1" :class="selectedAccount.email_verified_at !== 'Unverified' ? 'text-green-600' : 'text-red-600'" x-text="selectedAccount.email_verified_at"></p>
                                                </div>
                                                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">2FA Enabled</p>
                                                    <p class="text-sm font-bold mt-1" :class="selectedAccount.two_factor_enabled ? 'text-green-600' : 'text-slate-600'" x-text="selectedAccount.two_factor_enabled ? 'Yes' : 'No'"></p>
                                                </div>
                                            </div>
                                            <div class="flex gap-2 pt-4 border-t border-gray-200">
                                                <a :href="'/admin/accounts/' + selectedAccount.id + '/edit'" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition text-center">Edit</a>
                                                <template x-if="selectedAccount.status === 'pending'">
                                                    <button @click="approve(selectedAccount.id); closeDetailModal()" class="flex-1 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition">Approve</button>
                                                </template>
                                                <template x-if="selectedAccount.status !== 'suspended'">
                                                    <button @click="suspend(selectedAccount.id); closeDetailModal()" class="flex-1 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl transition">Suspend</button>
                                                </template>
                                                <template x-if="selectedAccount.status === 'suspended'">
                                                    <button @click="approve(selectedAccount.id); closeDetailModal()" class="flex-1 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition">Activate</button>
                                                </template>
                                                <template x-if="selectedAccount.status !== 'rejected'">
                                                    <button @click="reject(selectedAccount.id); closeDetailModal()" class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition">Reject</button>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Confirmation Modal --}}
                        <template x-if="showConfirmModal">
                            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="closeConfirmModal()">
                                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8">
                                    <div class="flex items-center gap-4 mb-6">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center"
                                            :class="confirmActionType === 'reject' ? 'bg-red-100' : 'bg-orange-100'">
                                            <svg class="w-6 h-6" :class="confirmActionType === 'reject' ? 'text-red-600' : 'text-orange-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-black text-gray-900" x-text="confirmTitle"></h3>
                                            <p class="text-sm text-gray-500" x-text="confirmMessage"></p>
                                        </div>
                                    </div>
                                    <div class="flex gap-3">
                                        <button @click="closeConfirmModal()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Cancel</button>
                                        <button @click="executeConfirmedAction()" class="flex-1 py-2.5 text-white font-bold rounded-xl transition"
                                            :class="confirmActionType === 'reject' ? 'bg-red-600 hover:bg-red-700' : confirmActionType === 'suspend' ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700'"
                                            x-text="confirmButtonText"></button>
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('accountsApp', () => ({
        accounts: [],
        total: 0,
        totalPages: 1,
        currentPage: 1,
        perPage: 25,
        loading: true,
        errorMessage: '',
        successMessage: '',

        search: '',
        statusFilter: '',
        sortBy: 'created_at',
        sortDirection: 'desc',

        selectAll: false,
        selectedAccounts: [],

        bulkAction: '',

        stats: { total: 0, active: 0, pending: 0, suspended: 0, rejected: 0 },

        showDetailModal: false,
        selectedAccount: null,

        showConfirmModal: false,
        confirmActionType: '',
        confirmAccountId: null,
        confirmAccountName: '',
        confirmTitle: '',
        confirmMessage: '',
        confirmButtonText: '',

        get pageNumbers() {
            const pages = [];
            const total = this.totalPages;
            const cur   = this.currentPage;

            if (total <= 7) {
                for (let i = 1; i <= total; i++) pages.push(i);
                return pages;
            }

            pages.push(1);
            if (cur > 3)          pages.push('…');
            for (let i = Math.max(2, cur - 1); i <= Math.min(total - 1, cur + 1); i++) pages.push(i);
            if (cur < total - 2)  pages.push('…');
            pages.push(total);
            return pages;
        },

        init() {
            this.loadAccounts();
        },

        buildParams() {
            const p = new URLSearchParams();
            if (this.search)       p.set('search',       this.search);
            if (this.statusFilter) p.set('statusFilter', this.statusFilter);
            if (this.sortBy)       p.set('sortBy',       this.sortBy);
            if (this.sortDirection) p.set('sortDirection', this.sortDirection);
            p.set('page',    this.currentPage);
            p.set('perPage', this.perPage);
            return p.toString();
        },

        async loadAccounts() {
            this.loading        = true;
            this.errorMessage   = '';
            this.selectedAccounts = [];
            this.selectAll      = false;

            try {
                const res = await fetch(`/api/accounts?${this.buildParams()}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    throw new Error(err.message ?? `Server error ${res.status}`);
                }

                const data = await res.json();
                this.accounts    = data.accounts    ?? [];
                this.total       = data.total       ?? 0;
                this.totalPages  = data.totalPages  ?? 1;
                this.currentPage = data.currentPage ?? 1;
                if (data.stats) {
                    this.stats = data.stats;
                }
            } catch (e) {
                this.errorMessage = 'Failed to load accounts: ' + e.message;
                console.error('accountsApp loadAccounts error:', e);
            } finally {
                this.loading = false;
            }
        },

        applyFilters() {
            this.currentPage = 1;
            this.loadAccounts();
        },

        toggleSort(column) {
            if (this.sortBy === column) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortBy = column;
                this.sortDirection = 'desc';
            }
            this.applyFilters();
        },

        goToPage(page) {
            if (page < 1 || page > this.totalPages) return;
            this.currentPage = page;
            this.loadAccounts();
        },

        toggleSelectAll() {
            this.selectedAccounts = this.selectAll
                ? this.accounts.map(a => a.id)
                : [];
        },

        toggleAccount(id) {
            if (this.selectedAccounts.includes(id)) {
                this.selectedAccounts = this.selectedAccounts.filter(i => i !== id);
            } else {
                this.selectedAccounts.push(id);
            }
            this.selectAll = this.selectedAccounts.length === this.accounts.length;
        },

        csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        },

        flash(message, isError = false) {
            if (isError) {
                this.errorMessage   = message;
                this.successMessage = '';
            } else {
                this.successMessage = message;
                this.errorMessage   = '';
                setTimeout(() => { this.successMessage = ''; }, 4000);
            }
        },

        getInitials(name) {
            if (!name) return '?';
            return name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
        },

        getAvatarColor(name) {
            const colors = [
                'bg-blue-100 text-blue-700 border-blue-200',
                'bg-green-100 text-green-700 border-green-200',
                'bg-purple-100 text-purple-700 border-purple-200',
                'bg-pink-100 text-pink-700 border-pink-200',
                'bg-indigo-100 text-indigo-700 border-indigo-200',
                'bg-amber-100 text-amber-700 border-amber-200',
                'bg-teal-100 text-teal-700 border-teal-200',
                'bg-cyan-100 text-cyan-700 border-cyan-200',
            ];
            let hash = 0;
            for (let i = 0; i < (name || '').length; i++) {
                hash = name.charCodeAt(i) + ((hash << 5) - hash);
            }
            return colors[Math.abs(hash) % colors.length];
        },

        viewDetails(account) {
            this.selectedAccount = account;
            this.showDetailModal = true;
        },

        closeDetailModal() {
            this.showDetailModal = false;
            this.selectedAccount = null;
        },

        confirmAction(action, id, name) {
            this.confirmActionType = action;
            this.confirmAccountId = id;
            this.confirmAccountName = name;

            if (action === 'suspend') {
                this.confirmTitle = 'Suspend Account';
                this.confirmMessage = `Are you sure you want to suspend ${name}?`;
                this.confirmButtonText = 'Suspend';
            } else if (action === 'activate') {
                this.confirmTitle = 'Activate Account';
                this.confirmMessage = `Are you sure you want to activate ${name}?`;
                this.confirmButtonText = 'Activate';
            } else if (action === 'reject') {
                this.confirmTitle = 'Reject Account';
                this.confirmMessage = `Are you sure you want to reject ${name}?`;
                this.confirmButtonText = 'Reject';
            }

            this.showConfirmModal = true;
        },

        closeConfirmModal() {
            this.showConfirmModal = false;
            this.confirmActionType = '';
            this.confirmAccountId = null;
            this.confirmAccountName = '';
        },

        async executeConfirmedAction() {
            this.closeConfirmModal();
            if (this.confirmActionType === 'suspend') {
                await this.postAction(`/api/accounts/${this.confirmAccountId}/suspend`, 'Account suspended.');
            } else if (this.confirmActionType === 'activate') {
                await this.postAction(`/api/accounts/${this.confirmAccountId}/approve`, 'Account activated.');
            } else if (this.confirmActionType === 'reject') {
                await this.postAction(`/api/accounts/${this.confirmAccountId}/reject`, 'Account rejected.');
            }
        },

        async approve(id) {
            await this.postAction(`/api/accounts/${id}/approve`, 'Account approved.');
        },

        async suspend(id) {
            await this.postAction(`/api/accounts/${id}/suspend`, 'Account suspended.');
        },

        async reject(id) {
            await this.postAction(`/api/accounts/${id}/reject`, 'Account rejected.');
        },

        async postAction(url, successMsg) {
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.csrfToken(),
                        'Content-Type': 'application/json',
                    },
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message ?? `Error ${res.status}`);
                this.flash(data.message ?? successMsg);
                await this.loadAccounts();
            } catch (e) {
                this.flash('Action failed: ' + e.message, true);
            }
        },

        async executeBulkAction() {
            if (!this.bulkAction || this.selectedAccounts.length === 0) return;

            try {
                const res = await fetch('/api/accounts/bulk-action', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.csrfToken(),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        ids:    this.selectedAccounts,
                        action: this.bulkAction,
                    }),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message ?? `Error ${res.status}`);
                this.flash(data.message ?? 'Bulk action completed.');
                this.bulkAction = '';
                await this.loadAccounts();
            } catch (e) {
                this.flash('Bulk action failed: ' + e.message, true);
            }
        },

        exportCSV() {
            window.location.href = `{{ route('admin.export.accounts') }}?${this.buildParams()}`;
        },
    }));
});
</script>