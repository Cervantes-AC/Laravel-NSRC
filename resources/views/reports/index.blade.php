<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Reports') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div x-data="reportsApp" class="space-y-6" aria-label="{{ __('Reports') }}">
                        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-500 via-blue-500 to-purple-600 p-8 shadow-lg">
                            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-20 translate-x-20"></div>
                            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-16 -translate-x-16"></div>
                            <div class="relative z-10 flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <h1 class="text-2xl font-black text-white">{{ __('Reports') }}</h1>
                                    <p class="text-white/80 text-sm font-medium">{{ __('Generate and export attendance reports') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <template x-for="[type, info] in Object.entries({user_activity: {label:'User Activity',icon:'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'},transaction_summary:{label:'Transaction Summary',icon:'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'},audit_trail:{label:'Audit Trail',icon:'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'},system_usage:{label:'System Usage',icon:'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'}})">
                                <button @click="reportType = type" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all border" :class="reportType === type ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-200 hover:border-indigo-300 hover:text-indigo-600'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="info.icon"/></svg>
                                    <span x-text="info.label"></span>
                                </button>
                            </template>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                                <div>
                                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('Date From') }}</label>
                                    <input type="date" x-model="dateFrom" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm" />
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('Date To') }}</label>
                                    <input type="date" x-model="dateTo" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm" />
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('Status') }}</label>
                                    <select x-model="status" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm">
                                        <option value="">{{ __('All Statuses') }}</option>
                                        <option value="COMPLETE">{{ __('Complete') }}</option>
                                        <option value="ONGOING">{{ __('Ongoing') }}</option>
                                        <option value="MISSING_TIMEOUT">{{ __('Missing Timeout') }}</option>
                                        <option value="INVALID_LOG">{{ __('Invalid') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('Personnel') }}</label>
                                    <input type="text" x-model="personnel" placeholder="{{ __('Name, ID, email...') }}" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm" />
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('Sector') }}</label>
                                    <select x-model="sector" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm">
                                        <option value="">{{ __('All Sectors') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4 flex flex-wrap items-center gap-3">
                                <button @click="generateReport()" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition shadow-sm">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    {{ __('Generate Report') }}
                                </button>
                                <button @click="clearFilters()" class="inline-flex items-center px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition shadow-sm">{{ __('Clear Filters') }}</button>
                                <template x-if="results">
                                    <button @click="exportCSV()" class="inline-flex items-center px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition shadow-sm">{{ __('CSV') }}</button>
                                </template>
                                <template x-if="results">
                                    <button @click="exportPDF()" class="inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition shadow-sm">{{ __('PDF') }}</button>
                                </template>
                            </div>
                        </div>

                        <template x-if="reportStats">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                                    <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Total Records') }}</p>
                                    <p class="mt-1 text-2xl font-black text-gray-900" x-text="reportStats.total_records"></p>
                                </div>
                                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                                    <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Total Duration') }}</p>
                                    <p class="mt-1 text-2xl font-black text-indigo-600" x-text="Math.floor(reportStats.total_duration / 60) + 'h ' + reportStats.total_duration % 60 + 'm'"></p>
                                </div>
                                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                                    <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Generated At') }}</p>
                                    <p class="mt-1 text-sm font-bold text-gray-700" x-text="reportStats.generated_at"></p>
                                </div>
                            </div>
                        </template>

                        <template x-if="results">
                            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                                    <h3 class="text-base font-black text-gray-900">{{ __('Report Results') }}</h3>
                                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full" x-text="reportStats.total_records + ' records'"></span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                                                <th class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                                <th class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Time In') }}</th>
                                                <th class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Time Out') }}</th>
                                                <th class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Duration') }}</th>
                                                <th class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Location') }}</th>
                                                <th class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <template x-for="r in (results.data.records || results.data || [])" :key="r.id || r.date">
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900" x-text="r.full_name || r.volunteer?.full_name || 'N/A'"></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700" x-text="r.date || 'N/A'"></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700" x-text="r.time_in || 'N/A'"></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700" x-text="r.time_out || 'N/A'"></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700" x-text="(r.duration_minutes || r.duration) ? (r.duration_minutes || r.duration) + ' mins' : 'Ongoing'"></td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700" x-text="r.location || 'N/A'"></td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider" :class="(r.status === 'COMPLETE' || r.status === 'completed') ? 'bg-green-100 text-green-800' : (r.status === 'ONGOING' || r.status === 'ongoing') ? 'bg-blue-100 text-blue-800' : r.status === 'MISSING_TIMEOUT' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800'" x-text="(r.status || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())"></span>
                                                    </td>
                                                </tr>
                                            </template>
                                            <template x-if="!results.data || !results.data.records?.length && !results.data?.length">
                                                <tr><td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('No results found.') }}</td></tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
