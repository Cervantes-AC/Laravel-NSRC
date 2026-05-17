@php
    function fmtHours($mins) {
        if ($mins === 0) return '0h';
        $h = floor($mins / 60);
        $m = $mins % 60;
        return $m === 0 ? "{$h}h" : "{$h}h {$m}m";
    }

    function fmtTime($datetime) {
        if (!$datetime) return '—';
        return $datetime->format('H:i');
    }

    function getInitials($name) {
        $parts = explode(' ', $name);
        $initials = '';
        foreach ($parts as $part) {
            $initials .= strtoupper($part[0]);
        }
        return substr($initials, 0, 2);
    }

    $roleColors = [
        'admin' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-800', 'border' => 'border-orange-200', 'icon' => 'text-orange-700'],
        'officer' => ['bg' => 'bg-red-50', 'text' => 'text-red-800', 'border' => 'border-red-200', 'icon' => 'text-red-700'],
        'member' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'icon' => 'text-slate-600'],
        'volunteer' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => 'text-blue-600'],
    ];

    $issueTypeMeta = [
        'MISSING_TIMEOUT' => ['label' => 'Missing Time-Out', 'color' => 'text-red-600', 'bg' => 'bg-red-50', 'border' => 'border-red-200'],
        'DUPLICATE' => ['label' => 'Duplicate Entry', 'color' => 'text-orange-600', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200'],
        'OVERLAP' => ['label' => 'Overlapping Shift', 'color' => 'text-purple-600', 'bg' => 'bg-purple-50', 'border' => 'border-purple-200'],
        'ZERO_DURATION' => ['label' => 'Zero Duration', 'color' => 'text-red-600', 'bg' => 'bg-red-50', 'border' => 'border-red-200'],
        'FUTURE_DATE' => ['label' => 'Future Date', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200'],
        'UNKNOWN' => ['label' => 'Unknown Issue', 'color' => 'text-slate-600', 'bg' => 'bg-slate-50', 'border' => 'border-slate-200'],
    ];
@endphp

<div class="space-y-6 max-w-7xl mx-auto px-1" aria-label="Personnel management">
    <!-- Page Header -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-50 to-blue-50 border border-slate-200 p-8 shadow-lg">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-600/30 shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900">Personnel</h1>
                    <p class="text-sm text-slate-600 font-bold mt-1">{{ $totalPersonnel }} personnel · {{ fmtHours($totalHours) }} total hours</p>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <!-- View mode toggle -->
                <div class="flex items-center bg-white border border-slate-200 rounded-xl p-1 shadow-sm">
                    <button wire:click="$set('viewMode', 'list')" class="p-2.5 rounded-lg transition-all {{ $viewMode === 'list' ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-400 hover:text-slate-600' }}" aria-label="List view">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <button wire:click="$set('viewMode', 'grid')" class="p-2.5 rounded-lg transition-all {{ $viewMode === 'grid' ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-slate-400 hover:text-slate-600' }}" aria-label="Grid view">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>
                    </button>
                </div>

                <!-- Search -->
                <div class="relative group">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none group-focus-within:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input wire:model.debounce.300ms="search" type="text" placeholder="Search personnel..." class="w-full sm:w-72 pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 outline-none transition-all shadow-sm hover:border-slate-300" aria-label="Search personnel" />
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Summary -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white border border-slate-200 rounded-xl px-5 py-4 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Total Staff</p>
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z"></path></svg>
            </div>
            <p class="text-2xl font-black text-slate-900 leading-none">{{ $totalPersonnel }}</p>
            <p class="text-[10px] text-slate-500 font-medium mt-1">registered personnel</p>
        </div>

        <div class="bg-white border border-green-200 rounded-xl px-5 py-4 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Clean Records</p>
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-2xl font-black text-green-600 leading-none">{{ $cleanCount }}</p>
            <p class="text-[10px] text-slate-500 font-medium mt-1">no compliance issues</p>
        </div>

        <div class="bg-white border border-red-200 rounded-xl px-5 py-4 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">With Issues</p>
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-2xl font-black text-red-600 leading-none">{{ $issueCount }}</p>
            <p class="text-[10px] text-slate-500 font-medium mt-1">needs attention</p>
        </div>

        <div class="bg-white border border-blue-200 rounded-xl px-5 py-4 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Total Hours</p>
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-2xl font-black text-blue-600 leading-none">{{ fmtHours($totalHours) }}</p>
            <p class="text-[10px] text-slate-500 font-medium mt-1">across all personnel</p>
        </div>
    </div>

    <!-- Toolbar: Filters + Sort -->
    <div class="flex flex-wrap items-center justify-between gap-3 bg-white border border-slate-200 rounded-xl px-5 py-3 shadow-sm">
        <div class="flex items-center gap-2">
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            <div class="flex gap-1.5">
                <button wire:click="$set('complianceFilter', 'all')" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all border {{ $complianceFilter === 'all' ? 'bg-blue-100 text-blue-700 border-blue-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">All</button>
                <button wire:click="$set('complianceFilter', 'compliance_only')" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all border {{ $complianceFilter === 'compliance_only' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">Clean</button>
                <button wire:click="$set('complianceFilter', 'issues_only')" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all border {{ $complianceFilter === 'issues_only' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">Issues</button>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sort:</span>
            <button wire:click="toggleSort('name')" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all border {{ $sortBy === 'name' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">Name {{ $sortBy === 'name' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}</button>
            <button wire:click="toggleSort('sessions')" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all border {{ $sortBy === 'sessions' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">Sessions {{ $sortBy === 'sessions' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}</button>
            <button wire:click="toggleSort('hours')" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all border {{ $sortBy === 'hours' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">Hours {{ $sortBy === 'hours' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}</button>
            <button wire:click="toggleSort('issues')" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all border {{ $sortBy === 'issues' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">Issues {{ $sortBy === 'issues' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}</button>

            <div class="pl-2 border-l border-slate-200">
                <button wire:click="toggleFormula" class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-200" title="How hours are calculated" aria-label="How hours are calculated">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Personnel Cards -->
    @if($personnel->count() === 0)
        <div class="py-24 flex flex-col items-center text-center bg-white border border-slate-200 border-dashed rounded-2xl">
            <div class="w-20 h-20 bg-slate-100 rounded-2xl flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <p class="text-base font-black text-slate-700 uppercase tracking-widest mb-2">No personnel found</p>
            <p class="text-sm text-slate-500 max-w-md">Try adjusting your search or filter criteria to find what you're looking for.</p>
            @if($search || $complianceFilter !== 'all')
                <button wire:click="$set('search', ''); $set('complianceFilter', 'all')" class="mt-4 px-5 py-2 bg-blue-100 text-blue-700 rounded-lg text-[11px] font-black uppercase tracking-wider hover:bg-blue-200 transition-all">Clear Filters</button>
            @endif
        </div>
    @else
        <div class="{{ $viewMode === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4' : 'space-y-4' }}">
            @foreach($personnel as $person)
                @php
                    $hasIssues = $person['invalidRecordCount'] > 0;
                    $roleColor = $roleColors[$person['role']] ?? $roleColors['member'];
                @endphp

                @if($viewMode === 'grid')
                    <!-- Grid Card -->
                    <div class="bg-white border shadow-sm rounded-2xl overflow-hidden hover:shadow-md transition-all {{ $hasIssues ? 'border-red-200' : 'border-slate-200' }}">
                        <div class="p-5">
                            <!-- Header with avatar -->
                            <div class="flex items-start gap-3 mb-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black border {{ $hasIssues ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-green-100 text-green-700 border-green-200' }}">
                                    {{ getInitials($person['fullName']) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-black text-slate-900 leading-tight truncate">{{ $person['fullName'] }}</h3>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider {{ $roleColor['bg'] }} {{ $roleColor['text'] }} {{ $roleColor['border'] }} border">
                                            {{ $person['role'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Status pill -->
                            <div class="mb-4">
                                @if($person['invalidRecordCount'] > 0)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-red-600 border border-red-200 text-[10px] font-black uppercase tracking-wider">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $person['invalidRecordCount'] }} {{ $person['invalidRecordCount'] === 1 ? 'Issue' : 'Issues' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-600 border border-green-200 text-[10px] font-black uppercase tracking-wider">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Clean
                                    </span>
                                @endif
                            </div>

                            <!-- Quick stats -->
                            <div class="grid grid-cols-3 gap-2 mb-4">
                                <div class="text-center p-2 bg-slate-50 rounded-lg">
                                    <p class="text-xs font-black text-slate-900">{{ $person['sessionCount'] }}</p>
                                    <p class="text-[9px] text-slate-500 font-medium">Sessions</p>
                                </div>
                                <div class="text-center p-2 bg-slate-50 rounded-lg">
                                    <p class="text-xs font-black text-slate-900">{{ fmtHours($person['totalRegularMinutes']) }}</p>
                                    <p class="text-[9px] text-slate-500 font-medium">Regular</p>
                                </div>
                                <div class="text-center p-2 bg-slate-50 rounded-lg">
                                    <p class="text-xs font-black text-slate-900">{{ fmtHours($person['totalOvertimeMinutes']) }}</p>
                                    <p class="text-[9px] text-slate-500 font-medium">OT</p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <button wire:click="viewHistory('{{ $person['fullName'] }}')" class="flex-1 py-2 bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 rounded-lg text-[10px] font-black text-slate-600 hover:text-blue-600 uppercase tracking-wider transition-all">
                                    View History
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- List Card -->
                    <div class="bg-white border shadow-sm rounded-2xl overflow-hidden hover:shadow-md transition-all {{ $hasIssues ? 'border-red-200' : 'border-slate-200' }}">
                        <div class="p-5 sm:p-6">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-5">
                                <!-- Identity block -->
                                <div class="flex items-center gap-4 lg:min-w-[280px]">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center font-black border {{ $hasIssues ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-green-100 text-green-700 border-green-200' }}">
                                        {{ getInitials($person['fullName']) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h3 class="text-base font-black text-slate-900 leading-tight truncate">{{ $person['fullName'] }}</h3>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                            <span class="text-[11px] font-mono text-slate-500">#{{ $person['serialNumber'] }}</span>
                                            <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider {{ $roleColor['bg'] }} {{ $roleColor['text'] }} {{ $roleColor['border'] }} border">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $person['role'] }}
                                            </span>
                                            <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                            <span class="text-[11px] text-slate-500 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                {{ $person['email'] }}
                                            </span>
                                        </div>
                                        <div class="mt-2">
                                            @if($person['invalidRecordCount'] > 0)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-red-600 border border-red-200 text-[10px] font-black uppercase tracking-wider">
                                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    {{ $person['invalidRecordCount'] }} {{ $person['invalidRecordCount'] === 1 ? 'Issue' : 'Issues' }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-600 border border-green-200 text-[10px] font-black uppercase tracking-wider">
                                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Clean
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Metrics grid -->
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 flex-1">
                                    <div class="flex flex-col gap-2 p-3 bg-slate-50 border border-slate-200 rounded-xl hover:shadow-md transition-all">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-600">Regular</span>
                                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-base font-black text-slate-900 leading-none">{{ fmtHours($person['totalRegularMinutes']) }}</p>
                                            <p class="text-[9px] text-slate-500 mt-0.5 font-medium">≤ 8h / day</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2 p-3 bg-orange-50 border border-orange-200 rounded-xl hover:shadow-md transition-all">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[9px] font-black uppercase tracking-widest text-orange-700">Overtime</span>
                                            <svg class="w-3.5 h-3.5 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8L5.257 19.393A2 2 0 005 18.07V5a2 2 0 012-2h10a2 2 0 012 2z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-base font-black text-slate-900 leading-none">{{ fmtHours($person['totalOvertimeMinutes']) }}</p>
                                            <p class="text-[9px] text-slate-500 mt-0.5 font-medium">> 8h / day</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2 p-3 {{ $person['totalUndertimeMinutes'] > 0 ? 'bg-red-50 border border-red-200' : 'bg-slate-50 border border-slate-200' }} rounded-xl hover:shadow-md transition-all">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[9px] font-black uppercase tracking-widest {{ $person['totalUndertimeMinutes'] > 0 ? 'text-red-700' : 'text-slate-600' }}">Undertime</span>
                                            <svg class="w-3.5 h-3.5 {{ $person['totalUndertimeMinutes'] > 0 ? 'text-red-700' : 'text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17H5v2a2 2 0 002 2h10a2 2 0 002-2v-2zm0 0V9a2 2 0 00-2-2H7a2 2 0 00-2 2v8m12-8v8m0 0l-4-4m4 4l4-4"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-base font-black text-slate-900 leading-none">{{ fmtHours($person['totalUndertimeMinutes']) }}</p>
                                            <p class="text-[9px] text-slate-500 mt-0.5 font-medium">< 1h</p>
                                        </div>
                                    </div>

                                    <button wire:click="viewHistory('{{ $person['fullName'] }}')" class="bg-slate-50 border border-slate-200 hover:border-blue-200 hover:bg-blue-50 rounded-xl flex flex-col justify-between transition-all group/btn text-left p-3">
                                        <span class="text-[9px] font-black text-slate-500 group-hover/btn:text-blue-600 uppercase tracking-widest transition-colors">History</span>
                                        <div class="flex items-center justify-between mt-auto pt-2">
                                            <span class="text-sm font-black text-slate-900">View</span>
                                            <svg class="w-4 h-4 text-slate-400 group-hover/btn:text-blue-600 group-hover/btn:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <!-- Pagination -->
    @if($totalPages > 1)
        <div class="flex items-center justify-between px-5 py-3 bg-white border border-slate-200 rounded-xl shadow-sm">
            <div class="text-xs font-bold text-slate-500">
                Showing {{ (($currentPage - 1) * ($viewMode === 'grid' ? 12 : 10)) + 1 }}–{{ min($currentPage * ($viewMode === 'grid' ? 12 : 10), $totalPersonnel) }} of {{ $totalPersonnel }}
            </div>
            <div class="flex items-center gap-1.5">
                <button wire:click="previousPage" {{ $currentPage === 1 ? 'disabled' : '' }} class="p-2 rounded-lg bg-white border border-slate-200 hover:border-slate-300 disabled:opacity-30 disabled:cursor-not-allowed transition-all" aria-label="Previous page">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>

                @for($i = 1; $i <= $totalPages; $i++)
                    @if($totalPages <= 7 || $i === 1 || $i === $totalPages || ($i >= $currentPage - 1 && $i <= $currentPage + 1))
                        <button wire:click="gotoPage({{ $i }})" class="w-8 h-8 rounded-lg text-xs font-black transition-all {{ $currentPage === $i ? 'bg-blue-600 text-white shadow-md' : 'bg-white border border-slate-200 text-slate-600 hover:border-slate-300' }}">
                            {{ $i }}
                        </button>
                    @elseif(($i === 2 && $currentPage > 3) || ($i === $totalPages - 1 && $currentPage < $totalPages - 2))
                        <span class="px-2 text-slate-400">…</span>
                    @endif
                @endfor

                <button wire:click="nextPage" {{ $currentPage === $totalPages ? 'disabled' : '' }} class="p-2 rounded-lg bg-white border border-slate-200 hover:border-slate-300 disabled:opacity-30 disabled:cursor-not-allowed transition-all" aria-label="Next page">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>
    @endif
</div>
