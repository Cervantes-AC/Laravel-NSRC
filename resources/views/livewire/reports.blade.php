<div class="space-y-6" aria-label="{{ __('Reports') }}">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-500 via-blue-500 to-purple-600 p-8 shadow-lg">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-20 translate-x-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-16 -translate-x-16"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-white">{{ __('Reports') }}</h1>
                    <p class="text-white/80 text-sm font-medium">{{ __('Generate and export attendance reports') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Report Type Tabs --}}
    <div class="flex flex-wrap gap-2">
        @foreach([
            'user_activity' => ['label' => 'User Activity', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            'transaction_summary' => ['label' => 'Transaction Summary', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            'audit_trail' => ['label' => 'Audit Trail', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            'system_usage' => ['label' => 'System Usage', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
        ] as $type => $info)
            <button wire:click="$set('reportType', '{{ $type }}')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all border {{ $reportType === $type ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-200 hover:border-indigo-300 hover:text-indigo-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $info['icon'] }}" /></svg>
                {{ __($info['label']) }}
            </button>
        @endforeach
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label for="date-from-r" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('Date From') }}</label>
                <input id="date-from-r" wire:model="dateFrom" type="date" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm" />
            </div>
            <div>
                <label for="date-to-r" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('Date To') }}</label>
                <input id="date-to-r" wire:model="dateTo" type="date" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm" />
            </div>
            <div>
                <label for="status-filter-r" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('Status') }}</label>
                <select id="status-filter-r" wire:model="status" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="COMPLETE">{{ __('Complete') }}</option>
                    <option value="ONGOING">{{ __('Ongoing') }}</option>
                    <option value="MISSING_TIMEOUT">{{ __('Missing Timeout') }}</option>
                    <option value="INVALID_LOG">{{ __('Invalid') }}</option>
                </select>
            </div>
            <div>
                <label for="personnel-r" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('Personnel') }}</label>
                <input id="personnel-r" wire:model.live.debounce.300ms="personnel" type="text" placeholder="{{ __('Name, ID, email...') }}" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm" />
            </div>
            <div>
                <label for="sector-r" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('Sector') }}</label>
                <select id="sector-r" wire:model="sector" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm">
                    <option value="">{{ __('All Sectors') }}</option>
                    @foreach($sectors as $sec)
                        <option value="{{ $sec }}">{{ $sec }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap items-center gap-3">
            <button wire:click="generateReport" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition shadow-sm">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                {{ __('Generate Report') }}
            </button>
            <button wire:click="clearFilters" class="inline-flex items-center px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition shadow-sm">
                {{ __('Clear Filters') }}
            </button>
            @if(!empty($results))
                <button wire:click="exportCSV" class="inline-flex items-center px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition shadow-sm">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    {{ __('CSV') }}
                </button>
                <button wire:click="exportPDF" class="inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition shadow-sm">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    {{ __('PDF') }}
                </button>
                <div class="flex gap-1.5">
                    <button wire:click="toggleFormalTemplate('certificate')" class="px-3 py-2.5 bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-800 font-semibold rounded-xl text-sm transition">
                        {{ __('Certificate') }}
                    </button>
                    <button wire:click="toggleFormalTemplate('summary')" class="px-3 py-2.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-800 font-semibold rounded-xl text-sm transition">
                        {{ __('Summary') }}
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Report Stats --}}
    @if(!empty($reportStats))
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Total Records') }}</p>
                <p class="mt-1 text-2xl font-black text-gray-900">{{ $reportStats['total_records'] }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Total Duration') }}</p>
                <p class="mt-1 text-2xl font-black text-indigo-600">{{ floor($reportStats['total_duration'] / 60) }}h {{ $reportStats['total_duration'] % 60 }}m</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Generated At') }}</p>
                <p class="mt-1 text-sm font-bold text-gray-700">{{ $reportStats['generated_at'] }}</p>
            </div>
        </div>
    @endif

    {{-- Formal Template Preview --}}
    @if($showFormalTemplate)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:click.self="closeFormalTemplate">
            <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[80vh] overflow-y-auto p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-black text-gray-900">
                        {{ $selectedTemplate === 'certificate' ? __('Certificate of Appearance') : __('Summary of Hours') }}
                    </h3>
                    <button wire:click="closeFormalTemplate" class="p-2 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="border border-gray-200 rounded-xl p-8 bg-white shadow-inner" style="min-height: 400px;">
                    @if($selectedTemplate === 'certificate')
                        <div class="text-center mb-8">
                            <h2 class="text-2xl font-black text-gray-900">CERTIFICATE OF APPEARANCE</h2>
                            <p class="text-sm text-gray-500 mt-1">National Service Reserve Corps</p>
                        </div>
                        <div class="space-y-4 text-sm text-gray-700">
                            <p>This is to certify that the following volunteer(s) have rendered duty session(s) during the specified period:</p>
                            <table class="w-full border-collapse border border-gray-300 text-xs">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="border border-gray-300 px-3 py-2 font-bold text-left">Name</th>
                                        <th class="border border-gray-300 px-3 py-2 font-bold text-left">Date</th>
                                        <th class="border border-gray-300 px-3 py-2 font-bold text-left">Time In</th>
                                        <th class="border border-gray-300 px-3 py-2 font-bold text-left">Time Out</th>
                                        <th class="border border-gray-300 px-3 py-2 font-bold text-left">Duration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $rows = $results['data'] ?? []; if(is_array($rows) && isset($rows['records'])) $rows = $rows['records']; @endphp
                                    @forelse(is_array($rows) ? array_slice($rows, 0, 15) : [] as $row)
                                        @php $r = is_array($row) ? (object) $row : $row; @endphp
                                        <tr>
                                            <td class="border border-gray-300 px-3 py-1.5">{{ $r->volunteer->full_name ?? $r->full_name ?? 'N/A' }}</td>
                                            <td class="border border-gray-300 px-3 py-1.5">{{ $r->date ?? 'N/A' }}</td>
                                            <td class="border border-gray-300 px-3 py-1.5">{{ isset($r->time_in) && $r->time_in instanceof \Carbon\Carbon ? $r->time_in->format('h:i A') : ($r->time_in ?? 'N/A') }}</td>
                                            <td class="border border-gray-300 px-3 py-1.5">{{ isset($r->time_out) && $r->time_out instanceof \Carbon\Carbon ? $r->time_out->format('h:i A') : ($r->time_out ?? '---') }}</td>
                                            <td class="border border-gray-300 px-3 py-1.5">{{ $r->duration_minutes ?? '0' }} mins</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="border border-gray-300 px-3 py-4 text-center text-gray-500">No data available</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="mt-8 pt-4 border-t border-gray-200">
                                <div class="flex justify-between">
                                    <div>
                                        <p class="font-bold">Prepared by:</p>
                                        <p class="mt-6 text-sm">________________________</p>
                                    </div>
                                    <div>
                                        <p class="font-bold">Date Issued:</p>
                                        <p class="mt-6 text-sm">{{ now()->format('F d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center mb-8">
                            <h2 class="text-2xl font-black text-gray-900">SUMMARY OF DUTY HOURS</h2>
                            <p class="text-sm text-gray-500 mt-1">National Service Reserve Corps</p>
                        </div>
                        @php
                            $allRows = $results['data'] ?? [];
                            if(is_array($allRows) && isset($allRows['records'])) $allRows = $allRows['records'];
                            $grouped = [];
                            if(is_array($allRows)) {
                                foreach($allRows as $row) {
                                    $r = is_array($row) ? (object) $row : $row;
                                    $name = $r->volunteer->full_name ?? $r->full_name ?? 'Unknown';
                                    if(!isset($grouped[$name])) $grouped[$name] = ['name' => $name, 'total_minutes' => 0, 'sessions' => 0];
                                    $grouped[$name]['total_minutes'] += (int) ($r->duration_minutes ?? 0);
                                    $grouped[$name]['sessions']++;
                                }
                            }
                        @endphp
                        <table class="w-full border-collapse border border-gray-300 text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="border border-gray-300 px-4 py-2 font-bold text-left">Volunteer</th>
                                    <th class="border border-gray-300 px-4 py-2 font-bold text-right">Total Sessions</th>
                                    <th class="border border-gray-300 px-4 py-2 font-bold text-right">Total Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($grouped as $g)
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2">{{ $g['name'] }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-right">{{ $g['sessions'] }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-right font-bold">{{ floor($g['total_minutes'] / 60) }}h {{ $g['total_minutes'] % 60 }}m</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="border border-gray-300 px-4 py-4 text-center text-gray-500">No data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    @endif
                </div>
                <div class="mt-4 flex justify-end">
                    <button wire:click="closeFormalTemplate" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Results Table --}}
    @if(!empty($results))
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-black text-gray-900">{{ __('Report Results') }}</h3>
                <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">{{ $reportStats['total_records'] ?? 0 }} records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Time In') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Time Out') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Duration') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Location') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $rows = $results['data'] ?? [];
                            if(is_array($rows) && isset($rows['records'])) $rows = $rows['records'];
                        @endphp
                        @forelse((is_array($rows) ? $rows : []) as $result)
                            @php $r = is_array($result) ? (object) $result : $result; @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $r->volunteer->full_name ?? $r->full_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $r->date instanceof \Carbon\Carbon ? $r->date->format('M d, Y') : ($r->date ?? 'N/A') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ isset($r->time_in) && $r->time_in instanceof \Carbon\Carbon ? $r->time_in->format('h:i A') : ($r->time_in ?? 'N/A') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ isset($r->time_out) && $r->time_out instanceof \Carbon\Carbon ? $r->time_out->format('h:i A') : ($r->time_out ?? 'N/A') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ isset($r->duration_minutes) && $r->duration_minutes ? $r->duration_minutes . ' mins' : (isset($r->duration) && $r->duration ? $r->duration . ' mins' : 'Ongoing') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $r->location ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php $status = $r->status ?? ''; @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                                        {{ $status === 'COMPLETE' || $status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $status === 'ONGOING' || $status === 'ongoing' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $status === 'MISSING_TIMEOUT' ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $status === 'INVALID_LOG' || $status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ !in_array($status, ['COMPLETE', 'completed', 'ONGOING', 'ongoing', 'MISSING_TIMEOUT', 'INVALID_LOG', 'cancelled']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ str_replace('_', ' ', ucwords(strtolower($status))) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('No results found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
