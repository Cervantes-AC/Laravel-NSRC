<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Account Management') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div x-data="accountsApp" class="space-y-4" aria-label="{{ __('Account management') }}">

                        {{-- Error / Success banners --}}
                        <div x-show="errorMessage" x-text="errorMessage" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"></div>
                        <div x-show="successMessage" x-text="successMessage" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"></div>

                        {{-- Toolbar --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex flex-col sm:flex-row gap-4 flex-1">
                                <div class="flex-1">
                                    <input type="text" x-model="search" @input.debounce.300ms="applyFilters()"
                                        placeholder="{{ __('Search by name or email...') }}"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                                <div>
                                    <select x-model="statusFilter" @change="applyFilters()"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">{{ __('All Statuses') }}</option>
                                        <option value="active">{{ __('Active') }}</option>
                                        <option value="pending">{{ __('Pending') }}</option>
                                        <option value="suspended">{{ __('Suspended') }}</option>
                                        <option value="rejected">{{ __('Rejected') }}</option>
                                    </select>
                                </div>
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
                                    {{ __('Export CSV') }}
                                </button>
                            </div>
                        </div>

                        {{-- Per-page + selection info --}}
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

                        <div x-show="loading" class="text-center py-8 text-gray-500">{{ __('Loading...') }}</div>

                        <template x-if="!loading">
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
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Registered') }}</th>
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
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="a.full_name"></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700" x-text="a.email"></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"
                                                    x-text="a.role ? a.role.charAt(0).toUpperCase() + a.role.slice(1) : 'Member'"></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium"
                                                        :class="{
                                                            'bg-green-100 text-green-800':  a.status === 'active',
                                                            'bg-yellow-100 text-yellow-800': a.status === 'pending',
                                                            'bg-orange-100 text-orange-800': a.status === 'suspended',
                                                            'bg-red-100 text-red-800':      a.status === 'rejected',
                                                        }"
                                                        x-text="a.status ? a.status.charAt(0).toUpperCase() + a.status.slice(1) : 'Unknown'">
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700" x-text="a.created_at"></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                                    <button @click="approve(a.id)"
                                                        class="text-green-600 hover:text-green-900 font-medium">{{ __('Approve') }}</button>
                                                    <button @click="suspend(a.id)"
                                                        class="text-orange-600 hover:text-orange-900 font-medium">{{ __('Suspend') }}</button>
                                                    <button @click="reject(a.id)"
                                                        class="text-red-600 hover:text-red-900 font-medium">{{ __('Reject') }}</button>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="accounts.length === 0">
                                            <tr>
                                                <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                                    {{ __('No accounts found.') }}
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

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('accountsApp', () => ({
        // State
        accounts: [],
        total: 0,
        totalPages: 1,
        currentPage: 1,
        perPage: 25,
        loading: true,
        errorMessage: '',
        successMessage: '',

        // Filters
        search: '',
        statusFilter: '',

        // Selection
        selectAll: false,
        selectedAccounts: [],

        // Bulk
        bulkAction: '',

        // Computed ellipsis page numbers
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

        goToPage(page) {
            if (page < 1 || page > this.totalPages) return;
            this.currentPage = page;
            this.loadAccounts();
        },

        // Selection helpers
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

        // CSRF helper
        csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        },

        // Flash message helper
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

        // Individual actions
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

        // Bulk action
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

        // CSV export
        exportCSV() {
            window.location.href = `{{ route('export.accounts') }}?${this.buildParams()}`;
        },
    }));
});
</script>