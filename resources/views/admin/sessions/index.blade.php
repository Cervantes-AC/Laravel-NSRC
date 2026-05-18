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
                                <input type="search" x-model="search" @input.debounce.300ms="loadSessions()" placeholder="Personnel name..." class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Status</label>
                                <select x-model="status" @change="loadSessions()" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Statuses</option>
                                    <option value="COMPLETE">Complete</option>
                                    <option value="ONGOING">Ongoing</option>
                                    <option value="MISSING_TIMEOUT">Missing Time Out</option>
                                    <option value="INVALID_LOG">Invalid Log</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Sector</label>
                                <select x-model="sector" @change="loadSessions()" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Sectors</option>
                                    <template x-for="s in sectors" :key="s">
                                        <option :value="s" x-text="s"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Location</label>
                                <select x-model="location" @change="loadSessions()" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Locations</option>
                                    <template x-for="l in locations" :key="l">
                                        <option :value="l" x-text="l"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Duration</label>
                                <select x-model="duration" @change="loadSessions()" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                                <select x-model="integrity" @change="loadSessions()" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Any Score</option>
                                    <option value="high">90% and above</option>
                                    <option value="medium">70% to 89%</option>
                                    <option value="low">Below 70%</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">From</label>
                                <input type="date" x-model="dateFrom" @change="loadSessions()" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">To</label>
                                <input type="date" x-model="dateTo" @change="loadSessions()" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <select x-model="perPage" @change="loadSessions()" class="rounded-lg border-gray-300 text-sm shadow-sm">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <button @click="clearFilters()" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg">Clear</button>
                            <button @click="syncAttendance()" class="px-3 py-2 text-sm bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg">Sync MySQL</button>
                        </div>
                    </div>

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
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider" :class="s.status === 'COMPLETE' ? 'bg-green-100 text-green-800' : s.status === 'ONGOING' ? 'bg-blue-100 text-blue-800' : s.status === 'MISSING_TIMEOUT' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800'" x-text="s.status"></span>
                                    </div>
                                    <dl class="mt-3 grid grid-cols-2 gap-2 text-sm">
                                        <div><dt class="text-gray-500">Date</dt><dd class="font-medium" x-text="s.date"></dd></div>
                                        <div><dt class="text-gray-500">Duration</dt><dd class="font-medium" x-text="s.duration_minutes ? s.duration_minutes + ' min' : 'Ongoing'"></dd></div>
                                        <div><dt class="text-gray-500">Time In</dt><dd x-text="s.time_in || '-'"></dd></div>
                                        <div><dt class="text-gray-500">Time Out</dt><dd x-text="s.time_out || '-'"></dd></div>
                                        <div class="col-span-2"><dt class="text-gray-500">Location / Sector</dt><dd x-text="(s.location || '-') + ' - ' + (s.sector || 'General')"></dd></div>
                                        <div><dt class="text-gray-500">Integrity</dt><dd x-text="Math.round(s.integrity_score || 0) + '%'"></dd></div>
                                    </dl>
                                    <div class="mt-4 flex gap-3 text-sm">
                                        <a :href="s.view_url" class="text-indigo-600 hover:text-indigo-800">View</a>
                                    </div>
                                </article>
                            </template>
                            <template x-if="sessions.length === 0">
                                <p class="col-span-full text-center py-12 text-gray-500">No attendance summaries match your filters.</p>
                            </template>
                        </div>
                    </template>

                    {{-- Pagination --}}
                    <template x-if="totalPages > 1">
                        <div class="flex items-center justify-between px-5 py-3 bg-white border border-slate-200 rounded-xl shadow-sm">
                            <div class="text-xs font-bold text-slate-500">
                                Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1" class="p-2 rounded-lg bg-white border border-slate-200 hover:border-slate-300 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <template x-for="p in totalPages" :key="p">
                                    <template x-if="totalPages <= 7 || p === 1 || p === totalPages || (p >= currentPage - 1 && p <= currentPage + 1)">
                                        <button @click="goToPage(p)" class="w-8 h-8 rounded-lg text-xs font-black transition-all" :class="currentPage === p ? 'bg-blue-600 text-white shadow-md' : 'bg-white border border-slate-200 text-slate-600 hover:border-slate-300'" x-text="p"></button>
                                    </template>
                                </template>
                                <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages" class="p-2 rounded-lg bg-white border border-slate-200 hover:border-slate-300 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
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
function numberFormat(n) { return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); }
</script>
