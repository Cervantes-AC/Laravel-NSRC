<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Reports') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div x-data="reportsApp" class="space-y-6" aria-label="{{ __('Reports') }}">

                {{-- Hero --}}
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-blue-600 to-violet-700 p-8 shadow-xl">
                    <div class="absolute top-0 right-0 w-72 h-72 bg-white/5 rounded-full -translate-y-24 translate-x-24 pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-52 h-52 bg-white/5 rounded-full translate-y-20 -translate-x-20 pointer-events-none"></div>
                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg shrink-0">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-white tracking-tight">{{ __('Reports & Analytics') }}</h1>
                                <p class="text-white/75 text-sm font-medium mt-0.5">{{ __('Generate, filter, and export attendance reports') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-white/80 text-sm font-medium bg-white/10 backdrop-blur px-4 py-2 rounded-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-text="currentDateTime"></span>
                        </div>
                    </div>
                </div>

                {{-- Banners --}}
                <div x-show="errorMessage" x-transition
                    class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 font-medium flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span x-text="errorMessage"></span>
                </div>
                <div x-show="successMessage" x-transition
                    class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 font-medium flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span x-text="successMessage"></span>
                </div>

                {{-- Report Type Tabs --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-3 shadow-sm">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                        <template x-for="[type, info] in Object.entries(reportTypes)" :key="type">
                            <button @click="reportType = type; clearResults()"
                                class="inline-flex items-start gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all text-left"
                                :class="reportType === type
                                    ? 'bg-indigo-600 text-white shadow-md ring-2 ring-indigo-300'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600 border border-gray-100'">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                                    :class="reportType === type ? 'bg-white/20' : 'bg-indigo-50'">
                                    <svg class="w-5 h-5" :class="reportType === type ? 'text-white' : 'text-indigo-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="info.icon"/>
                                    </svg>
                                </div>
                                <div>
                                    <span x-text="info.label"></span>
                                    <p class="text-[11px] mt-0.5 opacity-70" x-text="info.description"></p>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Quick Filter Presets --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Quick Date Filters</h3>
                        <span class="text-[10px] text-gray-400 font-medium">Click to apply</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button @click="applyQuickFilter('today')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                            :class="quickFilter === 'today' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Today
                        </button>
                        <button @click="applyQuickFilter('yesterday')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                            :class="quickFilter === 'yesterday' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Yesterday
                        </button>
                        <button @click="applyQuickFilter('week')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                            :class="quickFilter === 'week' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            This Week
                        </button>
                        <button @click="applyQuickFilter('month')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                            :class="quickFilter === 'month' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            This Month
                        </button>
                        <button @click="applyQuickFilter('all')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                            :class="quickFilter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            All Time
                        </button>
                        <div class="ml-auto flex items-center gap-2">
                            <span class="text-[10px] text-gray-400 font-medium" x-show="dateFrom || dateTo" x-text="'Custom: ' + (dateFrom || '...') + ' → ' + (dateTo || '...')"></span>
                        </div>
                    </div>
                </div>

                {{-- Filters --}}
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                    <button @click="filtersOpen = !filtersOpen"
                        class="w-full flex items-center justify-between px-6 py-4 text-left">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-widest">Advanced Filters</h3>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 transition-transform" :class="filtersOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="filtersOpen" x-collapse class="px-6 pb-6 space-y-4 border-t border-gray-100 pt-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('Date From') }}</label>
                                <input type="date" x-model="dateFrom"
                                    class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('Date To') }}</label>
                                <input type="date" x-model="dateTo"
                                    class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('Status') }}</label>
                                <select x-model="status"
                                    class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    <option value="COMPLETE">{{ __('Complete') }}</option>
                                    <option value="ONGOING">{{ __('Ongoing') }}</option>
                                    <option value="MISSING_TIMEOUT">{{ __('Missing Timeout') }}</option>
                                    <option value="INVALID_LOG">{{ __('Invalid') }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('Personnel') }}</label>
                                <input type="text" x-model="personnel" placeholder="{{ __('Name, ID, email...') }}"
                                    class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">{{ __('Sector') }}</label>
                                <select x-model="sector"
                                    class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm">
                                    <option value="">{{ __('All Sectors') }}</option>
                                    <template x-for="s in sectors" :key="s">
                                        <option :value="s" x-text="s"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="flex flex-wrap items-center gap-3">
                    <button @click="generateReport()" :disabled="generating"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition shadow-lg shadow-indigo-600/25 text-sm">
                        <svg x-show="!generating" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <svg x-show="generating" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        <span x-text="generating ? 'Generating Report...' : 'Generate Report'"></span>
                    </button>
                    <button @click="clearFilters()"
                        class="inline-flex items-center gap-2 px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        {{ __('Clear All') }}
                    </button>
                    <template x-if="records.length > 0">
                        <div class="flex items-center gap-2 ml-auto">
                            <button @click="printPreview()"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-800 hover:bg-gray-900 text-white font-semibold rounded-xl transition text-sm shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Preview
                            </button>
                            <button @click="exportCSV()" :disabled="exporting"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 text-white font-semibold rounded-xl transition text-sm shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                CSV
                            </button>
                            <button @click="exportPDF()" :disabled="exporting"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 disabled:opacity-60 text-white font-semibold rounded-xl transition text-sm shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                PDF
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Loading skeleton --}}
                <template x-if="generating">
                    <div class="bg-white border border-gray-200 rounded-2xl p-12 shadow-sm text-center space-y-4">
                        <div class="relative w-16 h-16 mx-auto">
                            <div class="absolute inset-0 border-4 border-indigo-200 rounded-full"></div>
                            <div class="absolute inset-0 border-4 border-indigo-600 rounded-full border-t-transparent animate-spin"></div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Generating report...</p>
                            <p class="text-xs text-gray-400 mt-1">Please wait while we compile your data.</p>
                        </div>
                    </div>
                </template>

                {{-- Stats Cards --}}
                <template x-if="reportStats && !generating">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">TOTAL</span>
                            </div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Records</p>
                            <p class="text-2xl font-bold text-gray-900 leading-none mt-1" x-text="reportStats.total_records.toLocaleString()"></p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">DURATION</span>
                            </div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Hours</p>
                            <p class="text-2xl font-bold text-blue-600 leading-none mt-1">
                                <span x-text="Math.floor(reportStats.total_duration / 60)"></span><span class="text-base">h</span>
                                <span x-text="reportStats.total_duration % 60"></span><span class="text-base">m</span>
                            </p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">RATE</span>
                            </div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Completion</p>
                            <p class="text-2xl font-bold text-emerald-600 leading-none mt-1" x-text="completionRate + '%'"></p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-bold text-violet-600 bg-violet-50 px-2 py-0.5 rounded-full">AVG</span>
                            </div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Per Session</p>
                            <p class="text-2xl font-bold text-violet-600 leading-none mt-1"><span x-text="avgDuration"></span><span class="text-base">m</span></p>
                        </div>
                    </div>
                </template>

                {{-- Results Table --}}
                <template x-if="records.length > 0 && !generating">
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Report Results</h3>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="reportTypes[reportType]?.label + ' · ' + filteredRecords.length + ' records shown'"></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input type="search" x-model="tableSearch" placeholder="Filter results..."
                                        class="rounded-lg border-gray-200 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-400 py-1.5 pl-9 w-48" />
                                </div>
                                <select x-model="tablePerPage" @change="tablePage = 1"
                                    class="rounded-lg border-gray-200 text-sm shadow-sm py-1.5">
                                    <option value="25">25 / page</option>
                                    <option value="50">50 / page</option>
                                    <option value="100">100 / page</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-10">#</th>
                                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Time In</th>
                                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Time Out</th>
                                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Duration</th>
                                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Location</th>
                                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sector</th>
                                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    <template x-for="(r, idx) in pagedRecords" :key="r.id || (r.date + r.full_name)">
                                        <tr class="hover:bg-indigo-50/40 transition-colors">
                                            <td class="px-5 py-3.5 whitespace-nowrap text-xs text-gray-400 font-mono" x-text="(tablePage - 1) * parseInt(tablePerPage) + idx + 1"></td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-sm font-semibold text-gray-900"
                                                x-text="r.full_name || r.volunteer?.full_name || 'N/A'"></td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-gray-600" x-text="formatDate(r.date)"></td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-gray-600" x-text="r.time_in || '—'"></td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-gray-600" x-text="r.time_out || '—'"></td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-sm font-medium"
                                                :class="(r.duration_minutes || r.duration) > 0 ? 'text-gray-900' : 'text-gray-400'"
                                                x-text="(r.duration_minutes || r.duration) ? ((r.duration_minutes || r.duration) + ' min') : 'Ongoing'"></td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-gray-600" x-text="r.location || '—'"></td>
                                            <td class="px-5 py-3.5 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium"
                                                    :class="r.sector ? 'bg-gray-100 text-gray-700' : 'bg-gray-50 text-gray-400'"
                                                    x-text="r.sector || '—'"></span>
                                            </td>
                                            <td class="px-5 py-3.5 whitespace-nowrap">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                                    :class="{
                                                        'bg-green-100 text-green-800':  r.status === 'COMPLETE'  || r.status === 'completed',
                                                        'bg-blue-100 text-blue-800':    r.status === 'ONGOING'   || r.status === 'ongoing',
                                                        'bg-amber-100 text-amber-800':  r.status === 'MISSING_TIMEOUT',
                                                        'bg-red-100 text-red-800':      r.status === 'INVALID_LOG',
                                                        'bg-gray-100 text-gray-600':    !r.status
                                                    }"
                                                    x-text="(r.status || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())">
                                                </span>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="filteredRecords.length === 0">
                                        <tr>
                                            <td colspan="9" class="px-6 py-12 text-center text-sm text-gray-400">
                                                No records match your search.
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        {{-- Table Pagination --}}
                        <template x-if="tablePages > 1">
                            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                                <p class="text-xs text-gray-500 font-medium">
                                    Showing <span class="font-semibold" x-text="((tablePage - 1) * parseInt(tablePerPage)) + 1"></span>–<span class="font-semibold" x-text="Math.min(tablePage * parseInt(tablePerPage), filteredRecords.length)"></span>
                                    of <span class="font-semibold" x-text="filteredRecords.length"></span>
                                </p>
                                <div class="flex items-center gap-1">
                                    <button @click="tablePage = 1" :disabled="tablePage === 1"
                                        class="px-2 py-1.5 text-xs rounded-lg border border-gray-200 disabled:opacity-30 hover:border-indigo-300 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                                    </button>
                                    <button @click="tablePage = Math.max(1, tablePage - 1)" :disabled="tablePage === 1"
                                        class="px-2 py-1.5 text-xs rounded-lg border border-gray-200 disabled:opacity-30 hover:border-indigo-300 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                    <template x-for="p in visiblePages" :key="p">
                                        <button @click="tablePage = p"
                                            class="w-8 h-8 rounded-lg text-xs font-bold transition"
                                            :class="tablePage === p ? 'bg-indigo-600 text-white' : 'border border-gray-200 text-gray-600 hover:border-indigo-300'"
                                            x-text="p"></button>
                                    </template>
                                    <button @click="tablePage = Math.min(tablePages, tablePage + 1)" :disabled="tablePage === tablePages"
                                        class="px-2 py-1.5 text-xs rounded-lg border border-gray-200 disabled:opacity-30 hover:border-indigo-300 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                    <button @click="tablePage = tablePages" :disabled="tablePage === tablePages"
                                        class="px-2 py-1.5 text-xs rounded-lg border border-gray-200 disabled:opacity-30 hover:border-indigo-300 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Empty state after generate with no results --}}
                <template x-if="generated && records.length === 0 && !generating">
                    <div class="bg-white border border-dashed border-gray-300 rounded-2xl px-6 py-16 text-center">
                        <svg class="w-14 h-14 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm font-semibold text-gray-500">No records found for the selected filters.</p>
                        <p class="text-xs text-gray-400 mt-1">Try adjusting your date range, status, or sector.</p>
                        <button @click="clearFilters()" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Reset Filters
                        </button>
                    </div>
                </template>

            </div>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('reportsApp', () => ({
        // Report type
        reportType: 'user_activity',
        reportTypes: {
            user_activity:       { label: 'User Activity',       description: 'Individual session logs',    icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
            transaction_summary: { label: 'Transaction Summary', description: 'Aggregate attendance data',  icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
            audit_trail:         { label: 'Audit Trail',         description: 'System activity logs',       icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
            system_usage:        { label: 'System Usage',        description: 'Platform analytics',         icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' },
        },

        // Filters
        dateFrom:  '',
        dateTo:    '',
        status:    '',
        personnel: '',
        sector:    '',
        sectors:   [],

        // Quick filter
        quickFilter: 'all',
        filtersOpen: true,

        // State
        generating:    false,
        exporting:     false,
        generated:     false,
        errorMessage:  '',
        successMessage:'',

        // Results
        results:     null,
        reportStats: null,
        records:     [],

        // Table client-side filter/pagination
        tableSearch:  '',
        tablePerPage: 25,
        tablePage:    1,

        get filteredRecords() {
            const q = this.tableSearch.toLowerCase().trim();
            if (!q) return this.records;
            return this.records.filter(r =>
                Object.values(r).some(v => String(v ?? '').toLowerCase().includes(q))
            );
        },

        get tablePages() {
            return Math.max(1, Math.ceil(this.filteredRecords.length / parseInt(this.tablePerPage)));
        },

        get pagedRecords() {
            const pp = parseInt(this.tablePerPage);
            const start = (this.tablePage - 1) * pp;
            return this.filteredRecords.slice(start, start + pp);
        },

        get visiblePages() {
            const total = this.tablePages;
            const current = this.tablePage;
            if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
            const pages = [];
            pages.push(1);
            if (current > 3) pages.push('...');
            for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) {
                pages.push(i);
            }
            if (current < total - 2) pages.push('...');
            pages.push(total);
            return pages;
        },

        get completionRate() {
            if (!this.records.length) return 0;
            const complete = this.records.filter(r => r.status === 'COMPLETE' || r.status === 'completed').length;
            return Math.round((complete / this.records.length) * 100);
        },

        get avgDuration() {
            if (!this.records.length) return 0;
            const total = this.records.reduce((sum, r) => sum + (parseInt(r.duration_minutes) || 0), 0);
            return Math.round(total / this.records.length);
        },

        get currentDateTime() {
            return new Date().toLocaleString('en-PH', { dateStyle: 'medium', timeStyle: 'short' });
        },

        init() {
            this.fetchSectors();
        },

        async fetchSectors() {
            try {
                const res = await fetch('/api/sessions?perPage=1', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (res.ok) {
                    const data = await res.json();
                    this.sectors = data.sectors ?? [];
                }
            } catch (_) {}
        },

        csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        },

        flash(msg, isError = false) {
            if (isError) {
                this.errorMessage   = msg;
                this.successMessage = '';
            } else {
                this.successMessage = msg;
                this.errorMessage   = '';
                setTimeout(() => { this.successMessage = ''; }, 5000);
            }
        },

        formatDate(dateStr) {
            if (!dateStr || dateStr === 'N/A') return '—';
            try {
                const d = new Date(dateStr);
                if (isNaN(d)) return dateStr;
                return d.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
            } catch {
                return dateStr;
            }
        },

        applyQuickFilter(preset) {
            this.quickFilter = preset;
            const today = new Date();
            switch (preset) {
                case 'today':
                    this.dateFrom = this.formatDateISO(today);
                    this.dateTo   = this.formatDateISO(today);
                    break;
                case 'yesterday':
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    this.dateFrom = this.formatDateISO(yesterday);
                    this.dateTo   = this.formatDateISO(yesterday);
                    break;
                case 'week':
                    const startOfWeek = new Date(today);
                    startOfWeek.setDate(today.getDate() - today.getDay());
                    this.dateFrom = this.formatDateISO(startOfWeek);
                    this.dateTo   = this.formatDateISO(today);
                    break;
                case 'month':
                    this.dateFrom = this.formatDateISO(new Date(today.getFullYear(), today.getMonth(), 1));
                    this.dateTo   = this.formatDateISO(today);
                    break;
                case 'all':
                default:
                    this.dateFrom = '';
                    this.dateTo   = '';
                    break;
            }
        },

        formatDateISO(date) {
            return date.toISOString().split('T')[0];
        },

        clearResults() {
            this.results     = null;
            this.reportStats = null;
            this.records     = [];
            this.generated   = false;
            this.tableSearch = '';
            this.tablePage   = 1;
        },

        clearFilters() {
            this.dateFrom    = '';
            this.dateTo      = '';
            this.status      = '';
            this.personnel   = '';
            this.sector      = '';
            this.quickFilter = 'all';
            this.clearResults();
        },

        buildPayload() {
            return {
                reportType: this.reportType,
                dateFrom:   this.dateFrom   || undefined,
                dateTo:     this.dateTo     || undefined,
                status:     this.status     || undefined,
                personnel:  this.personnel  || undefined,
                sector:     this.sector     || undefined,
            };
        },

        async generateReport() {
            if (this.generating) return;
            this.generating   = true;
            this.errorMessage = '';
            this.clearResults();

            try {
                const res = await fetch('/api/reports/generate', {
                    method: 'POST',
                    headers: {
                        'Accept':           'application/json',
                        'Content-Type':     'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     this.csrfToken(),
                    },
                    body: JSON.stringify(this.buildPayload()),
                });

                const data = await res.json();
                if (!res.ok) throw new Error(data.message ?? `Server error ${res.status}`);

                this.results     = data.results     ?? null;
                this.reportStats = data.reportStats ?? null;
                this.sectors     = data.sectors     ?? this.sectors;

                const raw = data.results?.data ?? [];
                this.records = Array.isArray(raw?.records) ? raw.records
                    : Array.isArray(raw) ? raw
                    : [];

                this.generated = true;
                this.tablePage = 1;

                if (this.records.length === 0) {
                    this.flash('Report generated — no records matched the filters.');
                } else {
                    this.flash(`Report generated with ${this.records.length} records.`);
                }
            } catch (e) {
                this.flash('Failed to generate report: ' + e.message, true);
                console.error('reportsApp generateReport error:', e);
            } finally {
                this.generating = false;
            }
        },

        async exportCSV() {
            if (this.exporting || this.records.length === 0) return;
            this.exporting = true;
            try {
                const res = await fetch('/api/reports/export-csv', {
                    method: 'POST',
                    headers: {
                        'Accept':           'application/json',
                        'Content-Type':     'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     this.csrfToken(),
                    },
                    body: JSON.stringify({ data: { records: this.records } }),
                });

                if (!res.ok) throw new Error(`Export failed: ${res.status}`);

                const blob = await res.blob();
                const url  = URL.createObjectURL(blob);
                const a    = document.createElement('a');
                a.href     = url;
                a.download = `attendance_report_${new Date().toISOString().slice(0,10)}.csv`;
                a.click();
                URL.revokeObjectURL(url);
                this.flash('CSV exported successfully.');
            } catch (e) {
                this.flash('CSV export failed: ' + e.message, true);
            } finally {
                this.exporting = false;
            }
        },

        async exportPDF() {
            if (this.exporting || this.records.length === 0) return;
            this.exporting = true;
            try {
                const res = await fetch('/api/reports/export-pdf', {
                    method: 'POST',
                    headers: {
                        'Accept':           'application/pdf',
                        'Content-Type':     'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     this.csrfToken(),
                    },
                    body: JSON.stringify({
                        data: { records: this.records },
                        dateFrom: this.dateFrom || undefined,
                        dateTo: this.dateTo || undefined,
                    }),
                });

                if (!res.ok) throw new Error(`Export failed: ${res.status}`);

                const blob = await res.blob();
                const url  = URL.createObjectURL(blob);
                const a    = document.createElement('a');
                a.href     = url;
                a.download = `attendance_report_${new Date().toISOString().slice(0,10)}.pdf`;
                a.click();
                URL.revokeObjectURL(url);
                this.flash('PDF exported successfully.');
            } catch (e) {
                this.flash('PDF export failed: ' + e.message, true);
            } finally {
                this.exporting = false;
            }
        },

        printPreview() {
            if (this.records.length === 0) return;
            const printWindow = window.open('', '_blank');
            const html = `
<!DOCTYPE html>
<html>
<head>
    <title>Report Preview</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f3f4f6; }
        .preview-container { max-width: 1000px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.1); overflow: hidden; }
        .preview-header { background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #fff; padding: 24px; }
        .preview-header h1 { margin: 0; font-size: 20px; }
        .preview-header p { margin: 4px 0 0; opacity: 0.8; font-size: 12px; }
        .preview-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; padding: 16px 24px; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
        .stat-card { text-align: center; }
        .stat-label { font-size: 10px; text-transform: uppercase; color: #6b7280; font-weight: 700; letter-spacing: 0.05em; }
        .stat-value { font-size: 20px; font-weight: 900; color: #111827; margin-top: 2px; }
        .stat-value.blue { color: #1d4ed8; }
        .stat-value.green { color: #15803d; }
        .stat-value.amber { color: #b45309; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        thead { background: #1e3a8a; color: #fff; }
        th { padding: 8px 12px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; }
        td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; color: #374151; }
        tr:nth-child(even) { background: #f8faff; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; }
        .badge-complete { background: #dcfce7; color: #15803d; }
        .badge-ongoing { background: #dbeafe; color: #1d4ed8; }
        .badge-missing { background: #fef3c7; color: #b45309; }
        .badge-invalid { background: #fee2e2; color: #b91c1c; }
        .preview-footer { padding: 16px 24px; border-top: 1px solid #e5e7eb; background: #f9fafb; display: flex; justify-content: space-between; font-size: 11px; color: #6b7280; }
        .toolbar { display: flex; justify-content: center; gap: 8px; padding: 12px; background: #fff; border-bottom: 1px solid #e5e7eb; }
        .toolbar button { padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: 1px solid #e5e7eb; background: #fff; color: #374151; }
        .toolbar button:hover { background: #f3f4f6; }
        .toolbar button.primary { background: #1e3a8a; color: #fff; border-color: #1e3a8a; }
        @media print {
            body { background: #fff; padding: 0; }
            .preview-container { box-shadow: none; }
            .toolbar { display: none; }
            .preview-footer { position: fixed; bottom: 0; left: 0; right: 0; }
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="toolbar">
            <button class="primary" onclick="window.print()">Print</button>
            <button onclick="window.close()">Close</button>
        </div>
        <div class="preview-header">
            <h1>${this.reportTypes[this.reportType]?.label || 'Report'} Preview</h1>
            <p>Generated ${new Date().toLocaleString('en-PH')} · ${this.records.length} records</p>
        </div>
        <div class="preview-stats">
            <div class="stat-card"><div class="stat-label">Total Records</div><div class="stat-value">${this.records.length.toLocaleString()}</div></div>
            <div class="stat-card"><div class="stat-label">Total Hours</div><div class="stat-value blue">${Math.floor(this.reportStats?.total_duration / 60)}h ${this.reportStats?.total_duration % 60}m</div></div>
            <div class="stat-card"><div class="stat-label">Completion</div><div class="stat-value green">${this.completionRate}%</div></div>
            <div class="stat-card"><div class="stat-label">Avg Duration</div><div class="stat-value amber">${this.avgDuration}m</div></div>
        </div>
        <table>
            <thead><tr><th>#</th><th>Name</th><th>Date</th><th>Time In</th><th>Time Out</th><th>Duration</th><th>Sector</th><th>Status</th></tr></thead>
            <tbody>
                ${this.records.slice(0, 100).map((r, i) => `
                    <tr>
                        <td>${i + 1}</td>
                        <td><strong>${r.full_name || 'N/A'}</strong></td>
                        <td>${r.date || '—'}</td>
                        <td>${r.time_in || '—'}</td>
                        <td>${r.time_out || '—'}</td>
                        <td>${r.duration_minutes ? r.duration_minutes + ' min' : 'Ongoing'}</td>
                        <td>${r.sector || '—'}</td>
                        <td><span class="badge badge-${(r.status || '').toLowerCase().replace('_timeout', '').replace('invalid', 'invalid')}">${(r.status || '').replace(/_/g, ' ')}</span></td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
        ${this.records.length > 100 ? `<div style="padding: 16px 24px; text-align: center; color: #6b7280; font-size: 12px;">Showing first 100 of ${this.records.length} records. Export PDF/CSV for full data.</div>` : ''}
        <div class="preview-footer">
            <span>NSRC AMS · Confidential</span>
            <span>${new Date().toLocaleDateString('en-PH')}</span>
        </div>
    </div>
</body>
</html>`;
            printWindow.document.write(html);
            printWindow.document.close();
        },
    }));
});
</script>