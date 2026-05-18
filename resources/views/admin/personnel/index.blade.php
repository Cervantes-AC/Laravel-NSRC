<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-800 leading-tight">{{ __('Personnel Management') }}</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg space-y-6">
                <div class="p-6 text-slate-900">
                    <div x-data="personnelApp" class="space-y-6 max-w-7xl mx-auto px-1" aria-label="Personnel management">
                        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-50 to-blue-50 border border-slate-200 p-8 shadow-lg">
                            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-600/30 shrink-0">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z"></path></svg>
                                    </div>
                                    <div>
                                        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">Personnel</h1>
                                        <p class="text-sm text-slate-600 font-bold mt-1"><span x-text="totalPersonnel"></span> personnel / <span x-text="fmtHours(totalHours)"></span> total hours</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 flex-wrap">
                                    <div class="flex items-center bg-white border border-slate-200 rounded-xl p-1 shadow-sm">
                                        <button @click="viewMode = 'list'; applyFilters()" class="p-2.5 rounded-lg transition-all" :class="viewMode === 'list' ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                        </button>
                                        <button @click="viewMode = 'grid'; applyFilters()" class="p-2.5 rounded-lg transition-all" :class="viewMode === 'grid' ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>
                                        </button>
                                    </div>
                                    <div class="relative group">
                                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        <input type="text" x-model="search" @input.debounce.300ms="applyFilters()" placeholder="Search personnel..." class="w-full sm:w-72 pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 outline-none transition-all shadow-sm hover:border-slate-300" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="bg-white border border-slate-200 rounded-xl px-5 py-4 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Total Staff</p>
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z"></path></svg>
                                </div>
                                <p class="text-2xl font-bold text-slate-900 leading-none" x-text="totalPersonnel"></p>
                                <p class="text-[10px] text-slate-500 font-medium mt-1">registered personnel</p>
                            </div>
                            <div class="bg-white border border-green-200 rounded-xl px-5 py-4 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Clean Records</p>
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-2xl font-bold text-green-600 leading-none" x-text="cleanCount"></p>
                                <p class="text-[10px] text-slate-500 font-medium mt-1">no compliance issues</p>
                            </div>
                            <div class="bg-white border border-red-200 rounded-xl px-5 py-4 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">With Issues</p>
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-2xl font-bold text-red-600 leading-none" x-text="issueCount"></p>
                                <p class="text-[10px] text-slate-500 font-medium mt-1">needs attention</p>
                            </div>
                            <div class="bg-white border border-blue-200 rounded-xl px-5 py-4 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Total Hours</p>
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-2xl font-bold text-blue-600 leading-none" x-text="fmtHours(totalHours)"></p>
                                <p class="text-[10px] text-slate-500 font-medium mt-1">across all personnel</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3 bg-white border border-slate-200 rounded-xl px-5 py-3 shadow-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                <div class="flex gap-1.5">
                                    <button @click="complianceFilter = 'all'; applyFilters()" class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all border" :class="complianceFilter === 'all' ? 'bg-blue-100 text-blue-700 border-blue-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700'">All</button>
                                    <button @click="complianceFilter = 'compliance_only'; applyFilters()" class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all border" :class="complianceFilter === 'compliance_only' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700'">Clean</button>
                                    <button @click="complianceFilter = 'issues_only'; applyFilters()" class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all border" :class="complianceFilter === 'issues_only' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700'">Issues</button>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sort:</span>
                                <button @click="toggleSort('name')" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all border" :class="sortBy === 'name' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700'">Name <span x-text="sortBy === 'name' ? (sortDirection === 'asc' ? '↑' : '↓') : ''"></span></button>
                                <button @click="toggleSort('sessions')" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all border" :class="sortBy === 'sessions' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700'">Sessions <span x-text="sortBy === 'sessions' ? (sortDirection === 'asc' ? '↑' : '↓') : ''"></span></button>
                                <button @click="toggleSort('hours')" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all border" :class="sortBy === 'hours' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700'">Hours <span x-text="sortBy === 'hours' ? (sortDirection === 'asc' ? '↑' : '↓') : ''"></span></button>
                                <button @click="toggleSort('issues')" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all border" :class="sortBy === 'issues' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700'">Issues <span x-text="sortBy === 'issues' ? (sortDirection === 'asc' ? '↑' : '↓') : ''"></span></button>
                                <div class="pl-2 border-l border-slate-200 flex gap-1">
                                    <button @click="exportCSV()" class="p-2 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition-all border border-transparent hover:border-emerald-200" title="Export CSV">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </button>
                                    <button @click="toggleFormula()" class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-200" title="How hours are calculated">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div x-show="loading" class="text-center py-8 text-slate-500">Loading...</div>

                        <template x-if="!loading && personnel.length === 0">
                            <div class="py-24 flex flex-col items-center text-center bg-white border border-slate-200 border-dashed rounded-2xl">
                                <div class="w-20 h-20 bg-slate-100 rounded-2xl flex items-center justify-center mb-6">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <p class="text-base font-bold text-slate-700 uppercase tracking-widest mb-2">No personnel found</p>
                                <p class="text-sm text-slate-500 max-w-md">Try adjusting your search, filter criteria, or sync attendance records from the Attendance page.</p>
                            </div>
                        </template>

                        <template x-if="!loading && personnel.length > 0">
                            <div>
                                <template x-if="viewMode === 'grid'">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <template x-for="p in personnel" :key="'g-' + p.id">
                                            <div class="bg-white border shadow-sm rounded-2xl overflow-hidden hover:shadow-md transition-all" :class="p.invalidRecordCount > 0 ? 'border-red-200' : 'border-slate-200'">
                                                <div class="p-5">
                                                    <div class="flex items-start gap-3 mb-4">
                                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold border" :class="p.invalidRecordCount > 0 ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-green-100 text-green-700 border-green-200'" x-text="getInitials(p.fullName)"></div>
                                                        <div class="flex-1 min-w-0">
                                                            <h3 class="text-sm font-bold text-slate-900 leading-tight truncate" x-text="p.fullName"></h3>
                                                            <div class="flex items-center gap-1.5 mt-1">
                                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider bg-slate-50 text-slate-700 border border-slate-200" x-text="p.role"></span>
                                                                <template x-if="!p.linked">
                                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">Unlinked</span>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-4">
                                                        <template x-if="p.invalidRecordCount > 0">
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-red-600 border border-red-200 text-[10px] font-bold uppercase tracking-wider" x-text="p.invalidRecordCount + ' Issue' + (p.invalidRecordCount !== 1 ? 's' : '')"></span>
                                                        </template>
                                                        <template x-if="p.invalidRecordCount === 0">
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-600 border border-green-200 text-[10px] font-bold uppercase tracking-wider">Clean</span>
                                                        </template>
                                                    </div>
                                                    <div class="grid grid-cols-3 gap-2 mb-4">
                                                        <div class="text-center p-2 bg-slate-50 rounded-lg">
                                                            <p class="text-xs font-bold text-slate-900" x-text="p.sessionCount"></p>
                                                            <p class="text-[9px] text-slate-500 font-medium">Sessions</p>
                                                        </div>
                                                        <div class="text-center p-2 bg-slate-50 rounded-lg">
                                                            <p class="text-xs font-bold text-slate-900" x-text="fmtHours(p.totalRegularMinutes)"></p>
                                                            <p class="text-[9px] text-slate-500 font-medium">Regular</p>
                                                        </div>
                                                        <div class="text-center p-2 bg-slate-50 rounded-lg">
                                                            <p class="text-xs font-bold text-slate-900" x-text="fmtHours(p.totalOvertimeMinutes)"></p>
                                                            <p class="text-[9px] text-slate-500 font-medium">OT</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <button @click="viewHistory(p.fullName)" class="flex-1 py-2 bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 rounded-lg text-[10px] font-bold text-slate-600 hover:text-blue-600 uppercase tracking-wider transition-all">View History</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="viewMode === 'list'">
                                    <div class="space-y-4">
                                        <template x-for="p in personnel" :key="'l-' + p.id">
                                            <div class="bg-white border shadow-sm rounded-2xl overflow-hidden hover:shadow-md transition-all" :class="p.invalidRecordCount > 0 ? 'border-red-200' : 'border-slate-200'">
                                                <div class="p-5 sm:p-6">
                                                    <div class="flex flex-col lg:flex-row lg:items-center gap-5">
                                                        <div class="flex items-center gap-4 lg:min-w-[280px]">
                                                            <div class="w-12 h-12 rounded-xl flex items-center justify-center font-bold border" :class="p.invalidRecordCount > 0 ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-green-100 text-green-700 border-green-200'" x-text="getInitials(p.fullName)"></div>
                                                            <div class="min-w-0 flex-1">
                                                                <h3 class="text-base font-bold text-slate-900 leading-tight truncate" x-text="p.fullName"></h3>
                                                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                                                    <span class="text-[11px] font-mono text-slate-500" x-text="'#' + p.serialNumber"></span>
                                                                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider bg-slate-50 text-slate-700 border border-slate-200" x-text="p.role"></span>
                                                                    <template x-if="!p.linked">
                                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">Unlinked Attendance</span>
                                                                    </template>
                                                                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                                                    <span class="text-[11px] text-slate-500" x-text="p.email"></span>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <template x-if="p.invalidRecordCount > 0">
                                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-red-600 border border-red-200 text-[10px] font-bold uppercase tracking-wider" x-text="p.invalidRecordCount + ' Issue' + (p.invalidRecordCount !== 1 ? 's' : '')"></span>
                                                                    </template>
                                                                    <template x-if="p.invalidRecordCount === 0">
                                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-600 border border-green-200 text-[10px] font-bold uppercase tracking-wider">Clean</span>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 flex-1">
                                                            <div class="flex flex-col gap-2 p-3 bg-slate-50 border border-slate-200 rounded-xl hover:shadow-md transition-all">
                                                                <div class="flex items-center justify-between">
                                                                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-600">Regular</span>
                                                                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                </div>
                                                                <p class="text-base font-bold text-slate-900 leading-none" x-text="fmtHours(p.totalRegularMinutes)"></p>
                                                                <p class="text-[9px] text-slate-500 mt-0.5 font-medium">&le; 8h / day</p>
                                                            </div>
                                                            <div class="flex flex-col gap-2 p-3 bg-orange-50 border border-orange-200 rounded-xl hover:shadow-md transition-all">
                                                                <div class="flex items-center justify-between">
                                                                    <span class="text-[9px] font-bold uppercase tracking-widest text-orange-700">Overtime</span>
                                                                    <svg class="w-3.5 h-3.5 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8L5.257 19.393A2 2 0 005 18.07V5a2 2 0 012-2h10a2 2 0 012 2z"></path></svg>
                                                                </div>
                                                                <p class="text-base font-bold text-slate-900 leading-none" x-text="fmtHours(p.totalOvertimeMinutes)"></p>
                                                                <p class="text-[9px] text-slate-500 mt-0.5 font-medium">&gt; 8h / day</p>
                                                            </div>
                                                            <div class="flex flex-col gap-2 p-3 rounded-xl hover:shadow-md transition-all" :class="p.totalUndertimeMinutes > 0 ? 'bg-red-50 border border-red-200' : 'bg-slate-50 border border-slate-200'">
                                                                <div class="flex items-center justify-between">
                                                                    <span class="text-[9px] font-bold uppercase tracking-widest" :class="p.totalUndertimeMinutes > 0 ? 'text-red-700' : 'text-slate-600'">Undertime</span>
                                                                    <svg class="w-3.5 h-3.5" :class="p.totalUndertimeMinutes > 0 ? 'text-red-700' : 'text-slate-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17H5v2a2 2 0 002 2h10a2 2 0 002-2v-2zm0 0V9a2 2 0 00-2-2H7a2 2 0 00-2 2v8m12-8v8m0 0l-4-4m4 4l4-4"></path></svg>
                                                                </div>
                                                                <p class="text-base font-bold text-slate-900 leading-none" x-text="fmtHours(p.totalUndertimeMinutes)"></p>
                                                                <p class="text-[9px] text-slate-500 mt-0.5 font-medium">&lt; 1h</p>
                                                            </div>
                                                            <button @click="viewHistory(p.fullName)" class="bg-slate-50 border border-slate-200 hover:border-blue-200 hover:bg-blue-50 rounded-xl flex flex-col justify-between transition-all text-left p-3">
                                                                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest transition-colors">History</span>
                                                                <div class="flex items-center justify-between mt-auto pt-2">
                                                                    <span class="text-sm font-bold text-slate-900">View</span>
                                                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                                </div>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="totalPages > 1">
                            <div class="flex items-center justify-between mt-6 px-2">
                                <div class="text-xs font-bold text-slate-500">
                                    Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                                    &nbsp;(<span x-text="totalPersonnel"></span> total)
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                                        class="px-3 py-1 bg-white border rounded disabled:opacity-30 text-xs font-bold">Prev</button>
                                    <template x-for="p in pageNumbers" :key="p">
                                        <button @click="p !== '…' && goToPage(p)"
                                            class="px-3 py-1 rounded text-xs font-bold"
                                            :class="p === currentPage
                                                ? 'bg-blue-600 text-white'
                                                : p === '…'
                                                    ? 'cursor-default text-slate-400'
                                                    : 'bg-white border text-slate-600 hover:border-slate-400'"
                                            x-text="p">
                                        </button>
                                    </template>
                                    <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                                        class="px-3 py-1 bg-white border rounded disabled:opacity-30 text-xs font-bold">Next</button>
                                </div>
                            </div>
                        </template>

                        {{-- History Modal --}}
                        <template x-if="selectedPersonnelName">
                            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="closeHistory()">
                                <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto p-8">
                                    <div class="flex items-center justify-between mb-6">
                                        <h3 class="text-xl font-bold text-slate-900" x-text="'History: ' + selectedPersonnelName"></h3>
                                        <button @click="closeHistory()" class="p-2 rounded-lg hover:bg-slate-100 transition">
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <template x-if="historySessions.length === 0">
                                        <p class="text-center py-8 text-slate-500">No session history found.</p>
                                    </template>
                                    <template x-if="historySessions.length > 0">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-slate-50">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 uppercase">Date</th>
                                                    <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 uppercase">Time In</th>
                                                    <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 uppercase">Time Out</th>
                                                    <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 uppercase">Duration</th>
                                                    <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 uppercase">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <template x-for="s in historySessions" :key="s.id">
                                                    <tr>
                                                        <td class="px-4 py-2 text-sm" x-text="s.date"></td>
                                                        <td class="px-4 py-2 text-sm" x-text="s.time_in"></td>
                                                        <td class="px-4 py-2 text-sm" x-text="s.time_out"></td>
                                                        <td class="px-4 py-2 text-sm" x-text="s.duration_minutes ? s.duration_minutes + ' min' : 'Ongoing'"></td>
                                                        <td class="px-4 py-2">
                                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="s.status === 'COMPLETE' ? 'bg-green-100 text-green-800' : s.status === 'ONGOING' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800'" x-text="s.status"></span>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </template>
                                    <div class="mt-4 flex justify-end">
                                        <button @click="closeHistory()" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition">Close</button>
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
