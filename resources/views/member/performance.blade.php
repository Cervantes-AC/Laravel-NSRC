<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Performance') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Total Hours') }}</dt>
                        <dd class="mt-2 text-3xl font-bold text-indigo-600">{{ $metrics['total_hours'] ?? 0 }}</dd>
                        <dd class="text-xs text-gray-500">{{ __('Lifetime volunteer hours') }}</dd>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Total Sessions') }}</dt>
                        <dd class="mt-2 text-3xl font-bold text-green-600">{{ $metrics['total_sessions'] ?? 0 }}</dd>
                        <dd class="text-xs text-gray-500">{{ __('Completed duty sessions') }}</dd>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Avg Duration') }}</dt>
                        <dd class="mt-2 text-3xl font-bold text-blue-600">{{ $metrics['avg_duration'] ?? 0 }} <span class="text-lg">{{ __('min') }}</span></dd>
                        <dd class="text-xs text-gray-500">{{ __('Average session length') }}</dd>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{ perPage: 12, currentPage: 1, get paginatedMonths() { const months = @json($metrics['monthly'] ?? []); const start = (this.currentPage - 1) * this.perPage; return months.slice(start, start + this.perPage); }, get totalMonths() { return Math.max(1, Math.ceil((@json($metrics['monthly'] ?? [])).length / this.perPage)); }, get monthPageNumbers() { const pages = []; const total = this.totalMonths; const cur = this.currentPage; if (total <= 7) { for (let i = 1; i <= total; i++) pages.push(i); return pages; } pages.push(1); if (cur > 3) pages.push('…'); for (let i = Math.max(2, cur - 1); i <= Math.min(total - 1, cur + 1); i++) pages.push(i); if (cur < total - 2) pages.push('…'); pages.push(total); return pages; }, goToPage(page) { if (page < 1 || page > this.totalMonths) return; this.currentPage = page; } }">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Detailed Breakdown') }}</h3>

                    @if(isset($metrics['monthly']) && count($metrics['monthly']) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" aria-label="{{ __('Monthly breakdown table') }}">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Month') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Sessions') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Total Hours') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Avg Duration') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="month in paginatedMonths" :key="month.label">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="month.label"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="month.sessions"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="month.total_hours"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="month.avg_duration + ' min'"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <template x-if="totalMonths > 1">
                            <div class="flex items-center justify-between mt-4">
                                <div class="text-xs font-bold text-gray-500">
                                    Page <span x-text="currentPage"></span> of <span x-text="totalMonths"></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                                        class="px-3 py-1 bg-white border rounded disabled:opacity-30 text-sm">Prev</button>
                                    <template x-for="p in monthPageNumbers" :key="p">
                                        <button @click="p !== '…' && goToPage(p)"
                                            class="px-3 py-1 rounded text-xs font-bold"
                                            :class="p === currentPage
                                                ? 'bg-indigo-600 text-white'
                                                : p === '…'
                                                    ? 'cursor-default text-gray-400'
                                                    : 'bg-white border text-gray-600 hover:border-gray-400'"
                                            x-text="p">
                                        </button>
                                    </template>
                                    <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalMonths"
                                        class="px-3 py-1 bg-white border rounded disabled:opacity-30 text-sm">Next</button>
                                </div>
                            </div>
                        </template>
                    @else
                        <p class="text-sm text-gray-500">{{ __('No performance data available yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
