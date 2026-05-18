<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Volunteer Rankings') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="rankingsApp" class="space-y-6" aria-label="{{ __('Rankings') }}">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-500 via-orange-500 to-red-500 p-8 shadow-lg">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-20 translate-x-20"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-16 -translate-x-16"></div>
                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-black text-white">{{ __('Volunteer Rankings') }}</h1>
                                <p class="text-white/80 text-sm font-medium">{{ __('Top performers leaderboard') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <select x-model="period" @change="loadRankings()" class="bg-white/15 backdrop-blur border-0 text-white text-sm rounded-xl px-3 py-1.5 font-medium">
                                <option value="all" class="text-gray-900">{{ __('All Time') }}</option>
                                <option value="this_week" class="text-gray-900">{{ __('This Week') }}</option>
                                <option value="this_month" class="text-gray-900">{{ __('This Month') }}</option>
                            </select>
                            <button @click="toggleScoringGuide()" class="px-3 py-1.5 bg-white/15 backdrop-blur hover:bg-white/25 text-white text-sm font-medium rounded-xl transition">{{ __('Scoring Guide') }}</button>
                        </div>
                    </div>
                </div>

                <template x-if="showScoringGuide">
                    <div class="bg-white border border-amber-200 rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-black text-gray-900">{{ __('How Scoring Works') }}</h3>
                            <button @click="toggleScoringGuide()" class="p-1.5 rounded-lg hover:bg-gray-100 transition">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="p-4 bg-green-50 border border-green-200 rounded-xl">
                                <div class="text-2xl mb-1">🌱</div>
                                <p class="text-sm font-black text-green-800">Beginner</p>
                                <p class="text-xs text-green-600">&lt; 10 hours</p>
                            </div>
                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                                <div class="text-2xl mb-1">💪</div>
                                <p class="text-sm font-black text-blue-800">Rising</p>
                                <p class="text-xs text-blue-600">10 – 24 hours</p>
                            </div>
                            <div class="p-4 bg-orange-50 border border-orange-200 rounded-xl">
                                <div class="text-2xl mb-1">🔥</div>
                                <p class="text-sm font-black text-orange-800">Dedicated</p>
                                <p class="text-xs text-orange-600">25 – 49 hours</p>
                            </div>
                            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                                <div class="text-2xl mb-1">⭐</div>
                                <p class="text-sm font-black text-amber-800">Veteran</p>
                                <p class="text-xs text-amber-600">50 – 99 hours</p>
                            </div>
                        </div>
                        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-center">
                            <div class="text-3xl mb-1">🏆</div>
                            <p class="text-sm font-black text-yellow-800">Century Club</p>
                            <p class="text-xs text-yellow-600">100+ hours — Top achiever status</p>
                        </div>
                    </div>
                </template>

                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model="search" @input.debounce.300ms="loadRankings()" placeholder="{{ __('Search by name...') }}" class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-orange-500/30 focus:border-orange-400 outline-none transition-all shadow-sm" />
                </div>

                <div x-show="loading" class="text-center py-4 text-gray-500">{{ __('Loading...') }}</div>

                <template x-if="!loading && topThree.length > 0">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                        <template x-for="(entry, idx) in [1,0,2]" :key="idx">
                            <template x-if="topThree[entry]">
                                <div class="relative bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all text-center" :class="entry === 0 ? 'sm:mb-0 ring-2 ring-yellow-400 ring-offset-2' : (entry === 1 ? 'sm:mb-4' : 'sm:mb-8')">
                                    <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-black shadow-lg" :class="entry === 0 ? 'bg-yellow-400 text-yellow-900' : (entry === 1 ? 'bg-gray-300 text-gray-700' : 'bg-orange-300 text-orange-900')" x-text="entry === 0 ? '1st' : (entry === 1 ? '2nd' : '3rd')"></span>
                                    </div>
                                    <div class="w-16 h-16 mx-auto rounded-2xl flex items-center justify-center font-black text-xl border-2 shadow-sm mt-2" :class="entry === 0 ? 'bg-yellow-100 text-yellow-800 border-yellow-300' : (entry === 1 ? 'bg-gray-100 text-gray-700 border-gray-300' : 'bg-orange-100 text-orange-800 border-orange-300')" x-text="(topThree[entry].full_name || '??').substring(0, 2).toUpperCase()"></div>
                                    <h3 class="mt-3 text-base font-black text-gray-900 truncate" x-text="topThree[entry].full_name || 'Unknown'"></h3>
                                    <p class="text-xs text-gray-500 font-medium" x-text="(topThree[entry].session_count || 0) + ' sessions'"></p>
                                    <div class="mt-3 grid grid-cols-2 gap-2">
                                        <div class="p-2 bg-gray-50 rounded-lg">
                                            <p class="text-sm font-black text-gray-900" x-text="fmtHours(topThree[entry].total_minutes || 0)"></p>
                                            <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">Total</p>
                                        </div>
                                        <div class="p-2 bg-gray-50 rounded-lg">
                                            <p class="text-sm font-black text-gray-900" x-text="topThree[entry].session_count || 0"></p>
                                            <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">Sessions</p>
                                        </div>
                                    </div>
                                    <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-gray-50 border border-gray-200">
                                        <template x-for="ach in getAchievements(topThree[entry].total_minutes || 0)" :key="ach.label">
                                            <span><span x-text="ach.icon"></span> <span x-text="ach.label"></span></span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </template>
                    </div>
                </template>

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-base font-black text-gray-900">{{ __('Leaderboard') }}</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full" x-text="rankings.length + ' volunteers'"></span>
                            <select x-model="sortBy" @change="loadRankings()" class="text-sm border-gray-200 rounded-lg shadow-sm focus:border-orange-400 focus:ring-orange-400">
                                <option value="total_hours">{{ __('Total Hours') }}</option>
                                <option value="total_sessions">{{ __('Total Sessions') }}</option>
                                <option value="avg_duration">{{ __('Regular Hours') }}</option>
                                <option value="compliance">{{ __('Compliance') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Rank') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Achievement') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Total Hours') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Regular') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Overtime') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Sessions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="(entry, idx) in rankings" :key="entry.id || entry.full_name">
                                    <tr :class="idx < 3 ? 'bg-amber-50/50' : 'hover:bg-gray-50'">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <template x-if="idx === 0"><span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-800 font-black text-sm">🥇</span></template>
                                            <template x-if="idx === 1"><span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-800 font-black text-sm">🥈</span></template>
                                            <template x-if="idx === 2"><span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-800 font-black text-sm">🥉</span></template>
                                            <template x-if="idx > 2"><span class="inline-flex items-center justify-center w-8 h-8 text-gray-600 font-bold text-sm" x-text="idx + 1"></span></template>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-black" :class="idx < 3 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700'" x-text="(entry.full_name || '??').substring(0, 2).toUpperCase()"></div>
                                                <span class="text-sm font-semibold text-gray-900" x-text="entry.full_name || 'Unknown'"></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <template x-for="ach in getAchievements(entry.total_minutes || 0)" :key="ach.label">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-white border border-gray-200" :class="ach.color"><span x-text="ach.icon"></span><span x-text="ach.label"></span></span>
                                            </template>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-gray-900" x-text="fmtHours(entry.total_minutes || 0)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700" x-text="fmtHours(entry.total_regular_minutes || 0)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-orange-600 font-medium" x-text="fmtHours(entry.total_overtime_minutes || 0)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700" x-text="entry.session_count || 0"></td>
                                    </tr>
                                </template>
                                <template x-if="rankings.length === 0">
                                    <tr><td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('No ranking data available yet.') }}</td></tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
