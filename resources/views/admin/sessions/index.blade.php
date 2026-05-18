<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Attendance') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div x-data="sessionsTable" class="space-y-6">
                    <div x-show="syncMessage" x-text="syncMessage" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"></div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Filtered Sessions</p>
                            <p class="mt-1 text-2xl font-black text-gray-900" x-text="numberFormat(filteredCount)"></p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Completed</p>
                            <p class="mt-1 text-2xl font-black text-green-600" x-text="numberFormat(completeCount)"></p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Logged Hours</p>
                            <p class="mt-1 text-2xl font-black text-indigo-600" x-text="(totalMinutes / 60).toFixed(1)"></p>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 flex-1">
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Search</label>
                                <input type="search" x-model="search" @input.debounce.300ms="applyFilters()" placeholder="Personnel name..." class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Status</label>
                                <select x-model="status" @change="applyFilters()" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Statuses</option>
                                    <option value="COMPLETE">Complete</option>
                                    <option value="ONGOING">Ongoing</option>
                                    <option value="MISSING_TIMEOUT">Missing Time Out</option>
                                    <option value="INVALID_LOG">Invalid Log</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Sector</label>
                                <select x-model="sector" @change="applyFilters()" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Sectors</option>
                                    <template x-for="s in sectors" :key="s">
                                        <option :value="s" x-text="s"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Location</label>
                                <select x-model="location" @change="applyFilters()" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Locations</option>
                                    <template x-for="l in locations" :key="l">
                                        <option :value="l" x-text="l"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Duration</label>
                                <select x-model="duration" @change="applyFilters()" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Any Duration</option>
                                    <option value="completed_hours">With logged hours</option>
                                    <option value="under_4h">Under 4 hours</option>
                                    <option value="4h_8h">4 to 8 hours</option>
                                    <option value="over_8h">Over 8 hours</option>
                                    <option value="missing">No duration</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Integrity</label>
                                <select x-model="integrity" @change="applyFilters()" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Any Score</option>
                                    <option value="high">90% and above</option>
                                    <option value="medium">70% to 89%</option>
                                    <option value="low">Below 70%</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">From</label>
                                <input type="date" x-model="dateFrom" @change="applyFilters()" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">To</label>
                                <input type="date" x-model="dateTo" @change="applyFilters()" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <select x-model="perPage" @change="applyFilters()" class="rounded-lg border-gray-300 text-sm shadow-sm">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <button @click="clearFilters()" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg">Clear</button>
                            <button @click="exportCSV()" class="px-3 py-2 text-sm bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg">Export CSV</button>
                            <button @click="processLocal()" :disabled="syncing" class="px-3 py-2 text-sm bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg disabled:opacity-50">
                                <span x-text="syncing ? 'Processing...' : 'Process Local'"></span>
                            </button>
                            <button @click="syncAttendance()" :disabled="syncing" class="px-3 py-2 text-sm bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg disabled:opacity-50">
                                <span x-text="syncing ? 'Syncing...' : 'Sync MySQL'"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Error message --}}
                    <div x-show="errorMessage" x-text="errorMessage" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"></div>

                    <div x-show="loading" class="text-center py-8 text-gray-500">Loading...</div>

                    <template x-if="!loading">
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                            <template x-for="s in sessions" :key="s.id">
                                <article class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <h4 class="font-semibold text-gray-900" x-text="s.full_name"></h4>
                                            <p class="text-xs text-gray-500" x-text="s.school_id || 'No linked account'"></p>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider"
                                            :class="s.status === 'COMPLETE'
                                                ? 'bg-green-100 text-green-800'
                                                : s.status === 'ONGOING'
                                                    ? 'bg-blue-100 text-blue-800'
                                                    : s.status === 'MISSING_TIMEOUT'
                                                        ? 'bg-amber-100 text-amber-800'
                                                        : 'bg-red-100 text-red-800'"
                                            x-text="s.status">
                                        </span>
                                    </div>
                                    <dl class="mt-3 grid grid-cols-2 gap-2 text-sm">
                                        <div><dt class="text-gray-500">Date</dt><dd class="font-medium" x-text="s.date"></dd></div>
                                        <div><dt class="text-gray-500">Duration</dt><dd class="font-medium" x-text="s.duration_minutes ? s.duration_minutes + ' min' : 'Ongoing'"></dd></div>
                                        <div><dt class="text-gray-500">Time In</dt><dd x-text="s.time_in || '-'"></dd></div>
                                        <div><dt class="text-gray-500">Time Out</dt><dd x-text="s.time_out || '-'"></dd></div>
                                        <div class="col-span-2"><dt class="text-gray-500">Location / Sector</dt><dd x-text="(s.location || '-') + ' — ' + (s.sector || 'General')"></dd></div>
                                        <div><dt class="text-gray-500">Integrity</dt><dd x-text="Math.round(s.integrity_score || 0) + '%'"></dd></div>
                                    </dl>
                                    <div class="mt-4 flex gap-3 text-sm">
                                        <a :href="s.view_url" class="text-indigo-600 hover:text-indigo-800 font-medium">View</a>
                                    </div>
                                </article>
                            </template>
                            <template x-if="sessions.length === 0">
                                <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center">
                                    <p class="text-sm font-semibold text-slate-700">No attendance summaries match your filters.</p>
                                    <p class="mt-1 text-xs text-slate-500">Clear filters or run MySQL sync to refresh generated attendance records.</p>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Pagination --}}
                    <template x-if="totalPages > 1">
                        <div class="flex items-center justify-between px-5 py-3 bg-white border border-slate-200 rounded-xl shadow-sm">
                            <div class="text-xs font-bold text-slate-500">
                                Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                                &nbsp;(<span x-text="numberFormat(filteredCount)"></span> total)
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                                    class="p-2 rounded-lg bg-white border border-slate-200 hover:border-slate-300 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <template x-for="p in pageNumbers" :key="p">
                                    <button @click="p !== '…' && goToPage(p)"
                                        class="w-8 h-8 rounded-lg text-xs font-black transition-all"
                                        :class="p === currentPage
                                            ? 'bg-blue-600 text-white shadow-md'
                                            : p === '…'
                                                ? 'cursor-default text-slate-400'
                                                : 'bg-white border border-slate-200 text-slate-600 hover:border-slate-300'"
                                        x-text="p">
                                    </button>
                                </template>
                                <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                                    class="p-2 rounded-lg bg-white border border-slate-200 hover:border-slate-300 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
function numberFormat(n) {
    return (n ?? 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

document.addEventListener('alpine:init', () => {
    Alpine.data('sessionsTable', () => ({
        // State
        sessions: [],
        sectors: [],
        locations: [],
        filteredCount: 0,
        completeCount: 0,
        totalMinutes: 0,
        totalPages: 1,
        currentPage: 1,
        loading: true,
        syncing: false,
        syncMessage: '',
        errorMessage: '',

        // Filters
        search: '',
        status: '',
        sector: '',
        location: '',
        duration: '',
        integrity: '',
        dateFrom: '',
        dateTo: '',
        perPage: 25,

        // Computed page numbers with ellipsis
        get pageNumbers() {
            const pages = [];
            const total = this.totalPages;
            const cur   = this.currentPage;

            if (total <= 7) {
                for (let i = 1; i <= total; i++) pages.push(i);
                return pages;
            }

            pages.push(1);
            if (cur > 3)           pages.push('…');
            for (let i = Math.max(2, cur - 1); i <= Math.min(total - 1, cur + 1); i++) pages.push(i);
            if (cur < total - 2)   pages.push('…');
            pages.push(total);
            return pages;
        },

        init() {
            this.loadData();
        },

        buildParams() {
            const p = new URLSearchParams();
            if (this.search)    p.set('search',    this.search);
            if (this.status)    p.set('status',    this.status);
            if (this.sector)    p.set('sector',    this.sector);
            if (this.location)  p.set('location',  this.location);
            if (this.duration)  p.set('duration',  this.duration);
            if (this.integrity) p.set('integrity', this.integrity);
            if (this.dateFrom)  p.set('dateFrom',  this.dateFrom);
            if (this.dateTo)    p.set('dateTo',    this.dateTo);
            p.set('page',    this.currentPage);
            p.set('perPage', this.perPage);
            return p.toString();
        },

        async loadData() {
            this.loading      = true;
            this.errorMessage = '';
            try {
                const res = await fetch(`/api/sessions?${this.buildParams()}`, {
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

                this.sessions      = data.sessions      ?? [];
                this.sectors       = data.sectors       ?? [];
                this.locations     = data.locations     ?? [];
                this.filteredCount = data.filteredCount ?? 0;
                this.completeCount = data.completeCount ?? 0;
                this.totalMinutes  = data.totalMinutes  ?? 0;
                this.totalPages    = data.totalPages    ?? 1;
                this.currentPage   = data.currentPage   ?? 1;
            } catch (e) {
                this.errorMessage = 'Failed to load attendance data: ' + e.message;
                console.error('sessionsTable loadData error:', e);
            } finally {
                this.loading = false;
            }
        },

        applyFilters() {
            this.currentPage = 1;
            this.loadData();
        },

        goToPage(page) {
            if (page < 1 || page > this.totalPages) return;
            this.currentPage = page;
            this.loadData();
        },

        clearFilters() {
            this.search      = '';
            this.status      = '';
            this.sector      = '';
            this.location    = '';
            this.duration    = '';
            this.integrity   = '';
            this.dateFrom    = '';
            this.dateTo      = '';
            this.perPage     = 25;
            this.currentPage = 1;
            this.loadData();
        },

        numberFormat(n) {
            return numberFormat(n);
        },

        exportCSV() {
            const params = this.buildParams();
            window.location.href = `{{ route('admin.export.sessions') }}?${params}`;
        },

        async processLocal() {
            if (this.syncing) return;
            this.syncing     = true;
            this.syncMessage = '';
            this.errorMessage = '';
            try {
                const res = await fetch('/api/sessions/sync', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ mode: 'local' }),
                });
                const data = await res.json();
                this.syncMessage = data.message ?? 'Processing complete.';
                await this.loadData();
            } catch (e) {
                this.errorMessage = 'Processing failed: ' + e.message;
            } finally {
                this.syncing = false;
            }
        },

        async syncAttendance() {
            if (this.syncing) return;
            this.syncing      = true;
            this.syncMessage  = '';
            this.errorMessage = '';
            try {
                const res = await fetch('/api/sessions/sync', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        'Content-Type': 'application/json',
                    },
                });
                const data = await res.json();
                this.syncMessage = data.message ?? 'Sync complete.';
                await this.loadData();
            } catch (e) {
                this.errorMessage = 'Sync failed: ' + e.message;
            } finally {
                this.syncing = false;
            }
        },
    }));
});
</script>