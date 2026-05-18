<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Analytics') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="analyticsApp" class="space-y-6" aria-label="{{ __('Analytics') }}">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-cyan-500 via-blue-500 to-indigo-600 p-8 shadow-lg">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-20 translate-x-20"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-16 -translate-x-16"></div>
                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-black text-white">{{ __('Analytics') }}</h1>
                                <p class="text-white/80 text-sm font-medium">{{ __('Data insights and trends') }}</p>
                            </div>
                        </div>
                        <div class="flex gap-1 bg-white/10 rounded-xl p-1 flex-wrap">
                            <template x-for="[key, label] in Object.entries({week:'Week', month:'Month', '3m':'3M', '6m':'6M', year:'Year', all:'All'})">
                                <button @click="filter(key)" class="px-3 py-1.5 text-sm font-semibold rounded-lg transition" :class="period === key ? 'bg-white text-indigo-700 shadow-sm' : 'text-white/70 hover:text-white hover:bg-white/10'" x-text="label"></button>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('From') }}</label>
                            <input type="date" x-model="dateFrom" @change="loadData()" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('To') }}</label>
                            <input type="date" x-model="dateTo" @change="loadData()" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('Status') }}</label>
                            <select x-model="status" @change="loadData()" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="COMPLETE">{{ __('Complete') }}</option>
                                <option value="ONGOING">{{ __('Ongoing') }}</option>
                                <option value="MISSING_TIMEOUT">{{ __('Missing Timeout') }}</option>
                                <option value="INVALID_LOG">{{ __('Invalid Log') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('Sector') }}</label>
                            <select x-model="sector" @change="loadData()" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm">
                                <option value="">{{ __('All Sectors') }}</option>
                                <template x-for="s in sectors" :key="s">
                                    <option :value="s" x-text="s"></option>
                                </template>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button @click="clearFilters()" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-sm transition">{{ __('Clear') }}</button>
                        </div>
                    </div>
                </div>

                <div x-show="loading" class="text-center py-4 text-gray-500">{{ __('Loading...') }}</div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Total Sessions') }}</p>
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <p class="text-3xl font-black text-gray-900 leading-none" x-text="totalSessions"></p>
                        <p class="text-xs text-gray-500 mt-1 font-medium">{{ __('All time') }}</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Total Hours') }}</p>
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-3xl font-black text-indigo-600 leading-none" x-text="(totalHours / 60).toFixed(1)"></p>
                        <p class="text-xs text-gray-500 mt-1 font-medium">{{ __('Volunteer hours logged') }}</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Active Volunteers') }}</p>
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                        </div>
                        <p class="text-3xl font-black text-green-600 leading-none" x-text="activeVolunteers"></p>
                        <p class="text-xs text-gray-500 mt-1 font-medium">{{ __('With at least 1 session') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                        <h3 class="text-base font-black text-gray-900 mb-4">{{ __('Session Trends') }}</h3>
                        <div class="h-64 relative">
                            <canvas x-ref="chartCanvas" class="w-full h-full"></canvas>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                            <h3 class="text-base font-black text-gray-900 mb-4">{{ __('Quick Insights') }}</h3>
                            <div class="space-y-4">
                                <div class="flex items-center gap-3 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Peak Day') }}</p>
                                        <p class="text-sm font-black text-gray-900"><span x-text="insights.peak_day || 'N/A'"></span> <span class="font-normal text-gray-500" x-text="'(' + (insights.peak_day_count || 0) + ' sessions)'"></span></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Avg / Volunteer') }}</p>
                                        <p class="text-sm font-black text-gray-900"><span x-text="insights.avg_hours_per_volunteer || 0"></span> <span class="font-normal text-gray-500">hours</span></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-xl">
                                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Total Hours') }}</p>
                                        <p class="text-sm font-black text-gray-900"><span x-text="insights.total_hours_rounded || 0"></span> <span class="font-normal text-gray-500">hours logged</span></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Efficiency') }}</p>
                                        <p class="text-sm font-black text-gray-900"><span x-text="insights.efficiency || 0"></span>% <span class="font-normal text-gray-500">completion rate</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                            <h3 class="text-base font-black text-gray-900 mb-4">{{ __('Status Breakdown') }}</h3>
                            <div class="space-y-2.5">
                                <template x-for="[status, count] in Object.entries(sessionsByStatus)" :key="status">
                                    <div>
                                        <div class="flex justify-between text-xs mb-0.5">
                                            <span class="font-semibold text-gray-600" x-text="status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())"></span>
                                            <span class="font-bold text-gray-900" x-text="Math.round(count / Math.max(Object.values(sessionsByStatus).reduce((a, b) => a + b, 0), 1) * 100) + '%'"></span>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                            <div class="h-full rounded-full transition-all" :class="status === 'COMPLETE' ? 'bg-green-500' : status === 'ONGOING' ? 'bg-blue-500' : status === 'MISSING_TIMEOUT' ? 'bg-amber-500' : 'bg-red-500'" :style="'width: ' + Math.round(count / Math.max(Object.values(sessionsByStatus).reduce((a, b) => a + b, 0), 1) * 100) + '%'"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
