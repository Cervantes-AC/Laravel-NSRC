@php
    $navSections = [];

    // Dashboard section
    $navSections['dashboard'] = [
        'label' => __('Dashboard'),
        'items' => [
            [
                'label' => __('Dashboard'),
                'route' => 'dashboard',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />',
                'active' => request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('member.dashboard'),
            ]
        ]
    ];

    // Admin sections
    if (Auth::user()->role === 'admin') {
        $navSections['management'] = [
            'label' => __('Management'),
            'items' => [
                ['label' => __('Sessions'), 'route' => 'admin.sessions.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />', 'active' => request()->routeIs('admin.sessions.*')],
                ['label' => __('Personnel'), 'route' => 'admin.personnel.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />', 'active' => request()->routeIs('admin.personnel.*')],
                ['label' => __('Accounts'), 'route' => 'admin.accounts.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />', 'active' => request()->routeIs('admin.accounts.*')],
            ]
        ];

        $navSections['communications'] = [
            'label' => __('Communications'),
            'items' => [
                ['label' => __('Announcements'), 'route' => 'admin.announcements.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592L6.5 17H5a2 2 0 01-2-2v-4a2 2 0 012-2h1.5l8.447-3.224A1 1 0 0116.3 6.71v10.58a1 1 0 01-1.353.936L11 16.718M18 10.5a3.5 3.5 0 010 5" />', 'active' => request()->routeIs('admin.announcements.*')],
            ]
        ];

        $navSections['data'] = [
            'label' => __('Data & Reports'),
            'items' => [
                ['label' => __('Reports'), 'route' => 'reports.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />', 'active' => request()->routeIs('reports.index') || request()->routeIs('reports.generate') || request()->routeIs('reports.export')],
                ['label' => __('Analytics'), 'route' => 'analytics.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />', 'active' => request()->routeIs('analytics.*')],
                ['label' => __('Ranking'), 'route' => 'ranking.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />', 'active' => request()->routeIs('ranking.*')],
            ]
        ];

        $navSections['system'] = [
            'label' => __('System'),
            'items' => [
                ['label' => __('Audit Logs'), 'route' => 'admin.audit-logs.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />', 'active' => request()->routeIs('admin.audit-logs.*')],
                ['label' => __('Import'), 'route' => 'admin.import.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12" />', 'active' => request()->routeIs('admin.import.*')],
                ['label' => __('Export'), 'route' => 'admin.export.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />', 'active' => request()->routeIs('admin.export.*')],
                ['label' => __('Backup'), 'route' => 'admin.backup.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />', 'active' => request()->routeIs('admin.backup.*')],
            ]
        ];
    }

    // Member sections
    if (Auth::user()->role === 'member') {
        $navSections['attendance'] = [
            'label' => __('Attendance'),
            'items' => [
                ['label' => __('My Attendance'), 'route' => 'member.attendance', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />', 'active' => request()->routeIs('member.attendance')],
                ['label' => __('My Performance'), 'route' => 'member.performance', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />', 'active' => request()->routeIs('member.performance')],
            ]
        ];

        $navSections['resources'] = [
            'label' => __('Resources'),
            'items' => [
                ['label' => __('How to Log'), 'route' => 'member.how-to-log', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />', 'active' => request()->routeIs('member.how-to-log')],
                ['label' => __('Rules'), 'route' => 'member.rules', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3l7 4v5c0 4.418-2.985 8.19-7 9-4.015-.81-7-4.582-7-9V7l7-4z" />', 'active' => request()->routeIs('member.rules')],
                ['label' => __('Notifications'), 'route' => 'notifications.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />', 'active' => request()->routeIs('notifications.index')],
            ]
        ];
    }

@endphp

<div class="flex items-center gap-3 h-16 px-6 border-b border-white/10 shrink-0">
    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white shadow-sm">
        <x-application-logo class="h-8 w-8" />
    </div>
    <div class="flex flex-col">
        <span class="text-base font-bold text-white leading-tight">{{ config('app.name', 'NSRC') }}</span>
        <span class="text-[11px] font-semibold uppercase tracking-[0.14em] text-blue-200 leading-tight">Attendance System</span>
    </div>
</div>

<nav class="sidebar-scroll flex-1 overflow-y-auto py-4 px-3 space-y-2" x-data="{ expandedSections: {} }">
    @foreach($navSections as $sectionKey => $section)
        <div class="space-y-1">
            {{-- Section header (collapsible) --}}
            <button @click="expandedSections['{{ $sectionKey }}'] = !expandedSections['{{ $sectionKey }}']"
                    class="w-full flex items-center justify-between px-3 py-2 text-[11px] font-bold uppercase tracking-[0.16em] text-slate-500 hover:text-slate-400 transition group">
                <span>{{ $section['label'] }}</span>
                <svg class="w-4 h-4 transition-transform duration-200" :class="expandedSections['{{ $sectionKey }}'] ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </button>

            {{-- Section items --}}
            <div x-show="expandedSections['{{ $sectionKey }}'] !== false" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-1">
                @foreach($section['items'] as $item)
                    <a href="{{ route($item['route']) }}"
                       class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $item['active'] ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <span class="shrink-0 w-5 h-5 {{ $item['active'] ? 'text-blue-600' : 'text-slate-500 group-hover:text-blue-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5">
                                {!! $item['icon'] !!}
                            </svg>
                        </span>
                        <span class="truncate">{{ $item['label'] }}</span>
                        @if($item['active'])
                            <span class="ml-auto h-2 w-2 rounded-full bg-blue-600"></span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</nav>

<div class="shrink-0 border-t border-white/10 p-4 space-y-3">
    <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-3">
        <div class="mb-3 flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.14em] text-emerald-300">
            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
            {{ __('Signed in') }}
        </div>
        <div class="flex items-center gap-3">
            @if (Auth::user()->avatar)
                <img src="{{ asset('storage/'.Auth::user()->avatar) }}" alt="{{ __('Profile avatar') }}" class="h-9 w-9 rounded-lg object-cover ring-2 ring-emerald-400/30">
            @else
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-500 text-white text-sm font-bold">{{ strtoupper(substr(Auth::user()->full_name ?? Auth::user()->name, 0, 2)) }}</span>
            @endif
            <div class="flex flex-col min-w-0">
                <span class="text-sm font-semibold text-white truncate">{{ Auth::user()->full_name ?? Auth::user()->name }}</span>
                <span class="text-xs font-medium text-slate-400 truncate">{{ ucfirst(Auth::user()->role) }}</span>
            </div>
        </div>
    </div>
</div>
