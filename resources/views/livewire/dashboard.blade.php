<div class="space-y-6" aria-label="{{ __('Dashboard') }}">
    {{-- Member Hero --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-orange-500 via-red-500 to-amber-600 p-8 shadow-lg">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-20 translate-x-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-16 -translate-x-16"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                    <span class="text-xl font-black text-white">{{ substr($userName, 0, 2) }}</span>
                </div>
                <div>
                    <p class="text-white/70 text-sm font-medium">{{ $greeting }}</p>
                    <h1 class="text-2xl font-black text-white">{{ $userName }}</h1>
                    <p class="text-white/80 text-sm font-medium capitalize">{{ $userRole }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 px-3 py-1.5 bg-white/15 rounded-xl backdrop-blur">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-sm font-medium text-white">{{ $activeNow }} active</span>
                </div>
                <select wire:model.live="dateFilter" class="bg-white/15 backdrop-blur border-0 text-white text-sm rounded-xl px-3 py-1.5 font-medium">
                    <option value="today" class="text-gray-900">{{ __('Today') }}</option>
                    <option value="week" class="text-gray-900">{{ __('This Week') }}</option>
                    <option value="month" class="text-gray-900">{{ __('This Month') }}</option>
                    <option value="all" class="text-gray-900">{{ __('All Time') }}</option>
                </select>
                <select wire:model.live="statusFilter" class="bg-white/15 backdrop-blur border-0 text-white text-sm rounded-xl px-3 py-1.5 font-medium">
                    <option value="" class="text-gray-900">{{ __('All Statuses') }}</option>
                    <option value="COMPLETE" class="text-gray-900">{{ __('Complete') }}</option>
                    <option value="ONGOING" class="text-gray-900">{{ __('Ongoing') }}</option>
                    <option value="MISSING_TIMEOUT" class="text-gray-900">{{ __('Missing') }}</option>
                    <option value="INVALID_LOG" class="text-gray-900">{{ __('Invalid') }}</option>
                </select>
                <select wire:model.live="sectorFilter" class="bg-white/15 backdrop-blur border-0 text-white text-sm rounded-xl px-3 py-1.5 font-medium">
                    <option value="" class="text-gray-900">{{ __('All Sectors') }}</option>
                    @foreach($sectors as $sector)
                        <option value="{{ $sector }}" class="text-gray-900">{{ $sector }}</option>
                    @endforeach
                </select>
                <button wire:click="clearFilters" class="px-3 py-1.5 bg-white/15 backdrop-blur hover:bg-white/25 rounded-xl text-sm font-medium text-white transition">
                    {{ __('Clear') }}
                </button>
                <button wire:click="refresh" class="p-2.5 bg-white/15 backdrop-blur hover:bg-white/25 rounded-xl transition" aria-label="{{ __('Refresh dashboard') }}">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Weekly Compliance Bar (Member) --}}
    @if(!empty($weeklyMetrics) && $weeklyMetrics['session_count'] > 0)
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="text-sm font-black text-gray-900">{{ __('Weekly Compliance') }}</h3>
                    <p class="text-xs text-gray-500 font-medium">{{ __('Target: 40 hours / week') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-black text-gray-900">{{ $weeklyMetrics['compliance_percentage'] }}%</p>
                    <p class="text-xs text-gray-500">{{ floor($weeklyMetrics['total_minutes'] / 60) }}h {{ $weeklyMetrics['total_minutes'] % 60 }}m completed</p>
                </div>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                @php $wkPct = $weeklyMetrics['compliance_percentage']; @endphp
                <div class="h-full rounded-full transition-all duration-500 {{ $wkPct >= 100 ? 'bg-green-500' : ($wkPct >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ $wkPct }}%"></div>
            </div>
            <div class="grid grid-cols-3 gap-3 mt-3">
                <div class="text-center p-2 bg-blue-50 rounded-lg">
                    <p class="text-xs font-black text-blue-700">{{ floor($weeklyMetrics['regular_minutes'] / 60) }}h {{ $weeklyMetrics['regular_minutes'] % 60 }}m</p>
                    <p class="text-[10px] text-blue-500 font-medium uppercase tracking-wider">{{ __('Regular') }}</p>
                </div>
                <div class="text-center p-2 bg-orange-50 rounded-lg">
                    <p class="text-xs font-black text-orange-700">{{ floor($weeklyMetrics['overtime_minutes'] / 60) }}h {{ $weeklyMetrics['overtime_minutes'] % 60 }}m</p>
                    <p class="text-[10px] text-orange-500 font-medium uppercase tracking-wider">{{ __('Overtime') }}</p>
                </div>
                <div class="text-center p-2 bg-red-50 rounded-lg">
                    <p class="text-xs font-black text-red-700">{{ floor($weeklyMetrics['undertime_minutes'] / 60) }}h {{ $weeklyMetrics['undertime_minutes'] % 60 }}m</p>
                    <p class="text-[10px] text-red-500 font-medium uppercase tracking-wider">{{ __('Undertime') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Metric Tiles --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Total Records') }}</span>
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            </div>
            <p class="text-3xl font-black text-gray-900 leading-none">{{ $totalRecords }}</p>
            <p class="text-xs text-gray-500 mt-1 font-medium">{{ __('All time sessions') }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Today') }}</span>
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <p class="text-3xl font-black text-indigo-600 leading-none">{{ $todayCount }}</p>
            <p class="text-xs text-gray-500 mt-1 font-medium">{{ __('Sessions today') }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Active Now') }}</span>
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            </div>
            <p class="text-3xl font-black text-green-600 leading-none">{{ $activeNow }}</p>
            <p class="text-xs text-gray-500 mt-1 font-medium">{{ __('Currently on duty') }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Avg Duration') }}</span>
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <p class="text-3xl font-black text-amber-600 leading-none">{{ number_format($avgDuration, 0) }} <span class="text-base font-bold">m</span></p>
            <p class="text-xs text-gray-500 mt-1 font-medium">{{ __('Per session') }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Completion') }}</span>
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <p class="text-3xl font-black text-green-600 leading-none">{{ $completionRate }}%</p>
            <p class="text-xs text-gray-500 mt-1 font-medium">{{ __('Session completion rate') }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Missing Timeouts') }}</span>
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <p class="text-3xl font-black text-red-600 leading-none">{{ $missingTimeouts }}</p>
            <p class="text-xs text-gray-500 mt-1 font-medium">{{ __('Needs attention') }}</p>
        </div>
    </div>

    {{-- Integrity Score --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-black text-gray-900">{{ __('Data Integrity Score') }}</h3>
            <span class="text-lg font-black {{ $avgIntegrityScore >= 80 ? 'text-green-600' : ($avgIntegrityScore >= 50 ? 'text-amber-600' : 'text-red-600') }}">{{ $avgIntegrityScore }}%</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500 {{ $avgIntegrityScore >= 80 ? 'bg-green-500' : ($avgIntegrityScore >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ $avgIntegrityScore }}%"></div>
        </div>
        <p class="text-xs text-gray-500 mt-1 font-medium">{{ __('Based on session completeness and valid time pairs') }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Sessions Table --}}
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-black text-gray-900">{{ __('Recent Sessions') }}</h3>
                <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">{{ $recentSessions->count() }} entries</span>
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentSessions ?? [] as $session)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $session->volunteer?->full_name ?? $session->full_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $session->date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $session->time_in ? $session->time_in->format('h:i A') : __('N/A') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $session->time_out ? $session->time_out->format('h:i A') : '---' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $session->duration_minutes ? $session->duration_minutes . ' mins' : __('Ongoing') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-session-status-badge :status="$session->status" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('No recent sessions.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Activity Timeline --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-black text-gray-900">{{ __('Recent Activity') }}</h3>
            </div>
            <div class="p-4 space-y-1 max-h-[420px] overflow-y-auto">
                @forelse($recentActivity ?? [] as $activity)
                    <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                        <div class="w-2 h-2 mt-2 rounded-full shrink-0
                            {{ $activity['type'] === 'error' ? 'bg-red-500' : '' }}
                            {{ $activity['type'] === 'warning' ? 'bg-amber-500' : '' }}
                            {{ $activity['type'] === 'info' || !in_array($activity['type'], ['error', 'warning']) ? 'bg-blue-500' : '' }}">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $activity['description'] }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs font-medium text-gray-500">{{ $activity['user'] }}</span>
                                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                <span class="text-xs text-gray-400">{{ $activity['time'] }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-sm text-gray-500">{{ __('No recent activity.') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Distribution Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-base font-black text-gray-900 mb-4">{{ __('Sessions by Status') }}</h3>
            @php $statusTotal = array_sum($sessionsByStatus) ?: 1; @endphp
            <div class="space-y-3">
                @foreach(['COMPLETE', 'ONGOING', 'MISSING_TIMEOUT', 'INVALID_LOG'] as $status)
                    @php
                        $count = $sessionsByStatus[$status] ?? 0;
                        $pct = round(($count / $statusTotal) * 100);
                        $color = match($status) {
                            'COMPLETE' => 'bg-green-500',
                            'ONGOING' => 'bg-blue-500',
                            'MISSING_TIMEOUT' => 'bg-amber-500',
                            'INVALID_LOG' => 'bg-red-500',
                            default => 'bg-gray-500',
                        };
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="font-semibold text-gray-700">{{ str_replace('_', ' ', $status) }}</span>
                            <span class="font-black text-gray-900">{{ $count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                            <div class="{{ $color }} h-full rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-base font-black text-gray-900 mb-4">{{ __('Sessions by Sector') }}</h3>
            @php $sectorTotal = array_sum($sessionsBySector) ?: 1; @endphp
            <div class="space-y-3">
                @forelse($sessionsBySector as $sector => $count)
                    @php $pct = round(($count / $sectorTotal) * 100); @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="font-semibold text-gray-700">{{ $sector ?: 'Unassigned' }}</span>
                            <span class="font-black text-gray-900">{{ $count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-gradient-to-r from-orange-400 to-red-500 h-full rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">{{ __('No sector data available.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
