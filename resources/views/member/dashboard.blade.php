<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-800 leading-tight">{{ __('My Dashboard') }}</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div x-data="dashboard" class="space-y-6" aria-label="{{ __('Dashboard') }}">
                <div x-show="loading" class="text-center py-4 text-slate-500">Loading...</div>
                <template x-if="data">
                    <div class="space-y-6">
                        {{-- Hero --}}
                        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-orange-500 via-red-500 to-amber-600 p-8 shadow-lg">
                            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-20 translate-x-20"></div>
                            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-16 -translate-x-16"></div>
                            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                                        <span class="text-xl font-bold text-white" x-text="data.userInitials"></span>
                                    </div>
                                    <div>
                                        <p class="text-white/70 text-sm font-medium" x-text="data.greeting"></p>
                                        <h1 class="text-2xl font-bold text-white" x-text="data.userName"></h1>
                                        <p class="text-white/80 text-sm font-medium capitalize" x-text="data.userRole"></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-2 px-3 py-1.5 bg-white/15 rounded-xl backdrop-blur">
                                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                        <span class="text-sm font-medium text-white" x-text="data.activeNow + ' active'"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="relative z-10 mt-4 flex flex-wrap gap-3">
                                <button x-show="!data.hasActiveSession" @click="logTimeIn()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-xl transition shadow-lg shadow-emerald-600/30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    Log Time In
                                </button>
                                <button x-show="data.hasActiveSession" @click="logTimeOut()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-500 hover:bg-red-400 text-white font-bold rounded-xl transition shadow-lg shadow-red-600/30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Log Time Out
                                </button>
                            </div>
                        </div>

                        {{-- Log Time In/Out --}}
                        <div x-data="logControl" class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900" x-text="hasActiveSession ? 'You are logged in' : 'Ready to log in'"></h3>
                                    <p class="text-xs text-slate-500 font-medium" x-text="hasActiveSession ? 'Logged in at ' + activeSince : 'Tap the button to record your time in'"></p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <template x-if="!hasActiveSession">
                                        <button @click="logTimeIn()" :disabled="logging" class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-bold rounded-xl transition shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                                            <span x-text="logging ? 'Logging...' : 'Log Time In'"></span>
                                        </button>
                                    </template>
                                    <template x-if="hasActiveSession">
                                        <button @click="logTimeOut()" :disabled="logging" class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white font-bold rounded-xl transition shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                                            <span x-text="logging ? 'Logging...' : 'Log Time Out'"></span>
                                        </button>
                                    </template>
                                    <template x-if="logMessage">
                                        <p class="text-sm font-medium" :class="logSuccess ? 'text-green-600' : 'text-red-600'" x-text="logMessage"></p>
                                    </template>
                                </div>
                            </div>
                            <template x-if="hasActiveSession && elapsedMinutes > 0">
                                <p class="mt-3 text-xs text-slate-500">Session duration: <span class="font-semibold" x-text="fmtDuration(elapsedMinutes)"></span></p>
                            </template>
                        </div>

                        {{-- Weekly Compliance --}}
                        <template x-if="data.weeklyMetrics && data.weeklyMetrics.session_count > 0">
                            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900">Weekly Compliance</h3>
                                        <p class="text-xs text-slate-500 font-medium">Target: 40 hours / week</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-slate-900" x-text="data.weeklyMetrics.compliance_percentage + '%'"></p>
                                        <p class="text-xs text-slate-500" x-text="Math.floor(data.weeklyMetrics.total_minutes / 60) + 'h ' + data.weeklyMetrics.total_minutes % 60 + 'm completed'"></p>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500" :class="data.weeklyMetrics.compliance_percentage >= 100 ? 'bg-green-500' : (data.weeklyMetrics.compliance_percentage >= 50 ? 'bg-amber-500' : 'bg-red-500')" :style="'width: ' + data.weeklyMetrics.compliance_percentage + '%'"></div>
                                </div>
                                <div class="grid grid-cols-3 gap-3 mt-3">
                                    <div class="text-center p-2 bg-blue-50 rounded-lg">
                                        <p class="text-xs font-bold text-blue-700" x-text="Math.floor(data.weeklyMetrics.regular_minutes / 60) + 'h ' + data.weeklyMetrics.regular_minutes % 60 + 'm'"></p>
                                        <p class="text-[10px] text-blue-500 font-medium uppercase tracking-wider">Regular</p>
                                    </div>
                                    <div class="text-center p-2 bg-orange-50 rounded-lg">
                                        <p class="text-xs font-bold text-orange-700" x-text="Math.floor(data.weeklyMetrics.overtime_minutes / 60) + 'h ' + data.weeklyMetrics.overtime_minutes % 60 + 'm'"></p>
                                        <p class="text-[10px] text-orange-500 font-medium uppercase tracking-wider">Overtime</p>
                                    </div>
                                    <div class="text-center p-2 bg-red-50 rounded-lg">
                                        <p class="text-xs font-bold text-red-700" x-text="Math.floor(data.weeklyMetrics.undertime_minutes / 60) + 'h ' + data.weeklyMetrics.undertime_minutes % 60 + 'm'"></p>
                                        <p class="text-[10px] text-red-500 font-medium uppercase tracking-wider">Undertime</p>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- Metric Tiles --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Total Records</span>
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                <p class="text-3xl font-bold text-slate-900 leading-none" x-text="data.totalRecords"></p>
                                <p class="text-xs text-slate-500 mt-1 font-medium">All time sessions</p>
                            </div>
                            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Today</span>
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-3xl font-bold text-indigo-600 leading-none" x-text="data.todayCount"></p>
                                <p class="text-xs text-slate-500 mt-1 font-medium">Sessions today</p>
                            </div>
                            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Active Now</span>
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <p class="text-3xl font-bold text-green-600 leading-none" x-text="data.activeNow"></p>
                                <p class="text-xs text-slate-500 mt-1 font-medium">Currently on duty</p>
                            </div>
                            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Avg Duration</span>
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-3xl font-bold text-amber-600 leading-none"><span x-text="Math.round(data.avgDuration)"></span> <span class="text-base font-bold">m</span></p>
                                <p class="text-xs text-slate-500 mt-1 font-medium">Per session</p>
                            </div>
                            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Completion</span>
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-3xl font-bold text-green-600 leading-none"><span x-text="data.completionRate"></span>%</p>
                                <p class="text-xs text-slate-500 mt-1 font-medium">Session completion rate</p>
                            </div>
                            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Missing Timeouts</span>
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <p class="text-3xl font-bold text-red-600 leading-none" x-text="data.missingTimeouts"></p>
                                <p class="text-xs text-slate-500 mt-1 font-medium">Needs attention</p>
                            </div>
                        </div>

                        {{-- Integrity Score --}}
                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-bold text-slate-900">Data Integrity Score</h3>
                                <span class="text-lg font-bold" :class="data.avgIntegrityScore >= 80 ? 'text-green-600' : (data.avgIntegrityScore >= 50 ? 'text-amber-600' : 'text-red-600')" x-text="data.avgIntegrityScore + '%'"></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500" :class="data.avgIntegrityScore >= 80 ? 'bg-green-500' : (data.avgIntegrityScore >= 50 ? 'bg-amber-500' : 'bg-red-500')" :style="'width: ' + data.avgIntegrityScore + '%'"></div>
                            </div>
                            <p class="text-xs text-slate-500 mt-1 font-medium">Based on session completeness and valid time pairs</p>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            {{-- Recent Sessions --}}
                            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                                    <h3 class="text-base font-bold text-slate-900">Recent Sessions</h3>
                                    <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full" x-text="data.recentSessions.length + ' entries'"></span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Name</th>
                                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Time In</th>
                                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Time Out</th>
                                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Duration</th>
                                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <template x-for="s in data.recentSessions" :key="s.id">
                                                <tr class="hover:bg-slate-50 transition-colors">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900" x-text="s.full_name"></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700" x-text="s.date"></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700" x-text="s.time_in || 'N/A'"></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700" x-text="s.time_out || '---'"></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700" x-text="s.duration_minutes ? s.duration_minutes + ' mins' : 'Ongoing'"></td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider" :class="s.status === 'COMPLETE' ? 'bg-green-100 text-green-800' : s.status === 'ONGOING' ? 'bg-blue-100 text-blue-800' : s.status === 'MISSING_TIMEOUT' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800'" x-text="s.status"></span>
                                                    </td>
                                                </tr>
                                            </template>
                                            <template x-if="data.recentSessions.length === 0">
                                                <tr><td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">No recent sessions.</td></tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Activity Timeline --}}
                            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                                <div class="px-6 py-4 border-b border-slate-200">
                                    <h3 class="text-base font-bold text-slate-900">Recent Activity</h3>
                                </div>
                                <div class="p-4 space-y-1 max-h-[420px] overflow-y-auto">
                                    <template x-for="a in data.recentActivity" :key="a.time + a.action">
                                        <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                                            <div class="w-2 h-2 mt-2 rounded-full shrink-0" :class="a.type === 'error' ? 'bg-red-500' : a.type === 'warning' ? 'bg-amber-500' : 'bg-blue-500'"></div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-slate-900 truncate" x-text="a.description"></p>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span class="text-xs font-medium text-slate-500" x-text="a.user"></span>
                                                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                                    <span class="text-xs text-slate-400" x-text="a.time"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="data.recentActivity.length === 0">
                                        <div class="text-center py-8 text-sm text-slate-500">No recent activity.</div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Distribution --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                                <h3 class="text-base font-bold text-slate-900 mb-4">Sessions by Status</h3>
                                <div class="space-y-3">
                                    <template x-for="[status, count] in Object.entries(data.sessionsByStatus)" :key="status">
                                        <div>
                                            <div class="flex items-center justify-between text-sm mb-1">
                                                <span class="font-semibold text-slate-700" x-text="status.replace(/_/g, ' ')"></span>
                                                <span class="font-bold text-slate-900" x-text="count + ' (' + Math.round(count / Math.max(Object.values(data.sessionsByStatus).reduce((a, b) => a + b, 0), 1) * 100) + '%)'"></span>
                                            </div>
                                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                                <div class="h-full rounded-full transition-all" :class="status === 'COMPLETE' ? 'bg-green-500' : status === 'ONGOING' ? 'bg-blue-500' : status === 'MISSING_TIMEOUT' ? 'bg-amber-500' : 'bg-red-500'" :style="'width: ' + Math.round(count / Math.max(Object.values(data.sessionsByStatus).reduce((a, b) => a + b, 0), 1) * 100) + '%'"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                                <h3 class="text-base font-bold text-slate-900 mb-4">Sessions by Sector</h3>
                                <div class="space-y-3">
                                    <template x-for="[sector, count] in Object.entries(data.sessionsBySector)" :key="sector">
                                        <div>
                                            <div class="flex items-center justify-between text-sm mb-1">
                                                <span class="font-semibold text-slate-700" x-text="sector || 'Unassigned'"></span>
                                                <span class="font-bold text-slate-900" x-text="count + ' (' + Math.round(count / Math.max(Object.values(data.sessionsBySector).reduce((a, b) => a + b, 0), 1) * 100) + '%)'"></span>
                                            </div>
                                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                                <div class="bg-gradient-to-r from-orange-400 to-red-500 h-full rounded-full transition-all" :style="'width: ' + Math.round(count / Math.max(Object.values(data.sessionsBySector).reduce((a, b) => a + b, 0), 1) * 100) + '%'"></div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="Object.keys(data.sessionsBySector).length === 0">
                                        <p class="text-sm text-slate-500 text-center py-4">No sector data available.</p>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</x-app-layout>
