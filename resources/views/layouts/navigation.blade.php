@php
    $navItems = [];

    $navItems[] = [
        'label' => __('Dashboard'),
        'route' => 'dashboard',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />',
        'active' => request()->routeIs('dashboard'),
    ];

    if (Auth::user()->role === 'admin') {
        $navItems[] = [
            'label' => __('Sessions'),
            'route' => 'admin.sessions.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
            'active' => request()->routeIs('admin.sessions.*'),
        ];
        $navItems[] = [
            'label' => __('Personnel'),
            'route' => 'admin.personnel.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
            'active' => request()->routeIs('admin.personnel.*'),
        ];
        $navItems[] = [
            'label' => __('Accounts'),
            'route' => 'admin.accounts.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
            'active' => request()->routeIs('admin.accounts.*'),
        ];
        $navItems[] = [
            'label' => __('Audit Logs'),
            'route' => 'admin.audit-logs.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
            'active' => request()->routeIs('admin.audit-logs.*'),
        ];
        $navItems[] = [
            'label' => __('Import'),
            'route' => 'admin.import.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L-4 8m4-4v12" />',
            'active' => request()->routeIs('admin.import.*'),
        ];
        $navItems[] = [
            'label' => __('Backup'),
            'route' => 'admin.backup.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
            'active' => request()->routeIs('admin.backup.*'),
        ];
    }

    if (Auth::user()->role === 'member') {
        $navItems[] = [
            'label' => __('My Attendance'),
            'route' => 'member.attendance',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />',
            'active' => request()->routeIs('member.attendance'),
        ];
        $navItems[] = [
            'label' => __('My Performance'),
            'route' => 'member.performance',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />',
            'active' => request()->routeIs('member.performance'),
        ];
    }

    $navItems[] = [
        'label' => __('Reports'),
        'route' => 'reports.index',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
        'active' => request()->routeIs('reports.index') || request()->routeIs('reports.generate') || request()->routeIs('reports.export'),
    ];
    $navItems[] = [
        'label' => __('AI Insights'),
        'route' => 'reports.insights.view',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />',
        'active' => request()->routeIs('reports.insights*'),
    ];

    if (Auth::user()->role === 'admin') {
        $navItems[] = [
            'label' => __('Analytics'),
            'route' => 'analytics.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />',
            'active' => request()->routeIs('analytics.*'),
        ];
        $navItems[] = [
            'label' => __('Ranking'),
            'route' => 'ranking.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />',
            'active' => request()->routeIs('ranking.*'),
        ];
        $navItems[] = [
            'label' => __('Settings'),
            'route' => 'admin.settings.index',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
            'active' => request()->routeIs('admin.settings.*'),
        ];
    }

    $navItems[] = [
        'label' => __('Notifications'),
        'route' => 'notifications.index',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />',
        'active' => request()->routeIs('notifications.*'),
    ];
@endphp

{{-- Logo area --}}
<div class="flex items-center gap-3 h-16 px-6 border-b border-gray-200 shrink-0">
    <x-application-logo class="h-9 w-9" />
    <div class="flex flex-col">
        <span class="text-base font-bold text-gray-900 leading-tight">{{ config('app.name', 'NSRC') }}</span>
        <span class="text-[11px] font-medium text-gray-500 leading-tight">Attendance System</span>
    </div>
</div>

{{-- Navigation links --}}
<nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
    @foreach($navItems as $item)
        <a href="{{ route($item['route']) }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ $item['active'] ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
            <span class="shrink-0 w-5 h-5 {{ $item['active'] ? 'text-indigo-600' : 'text-gray-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5">
                    {!! $item['icon'] !!}
                </svg>
            </span>
            <span class="truncate">{{ $item['label'] }}</span>
        </a>
    @endforeach
</nav>

{{-- Bottom section --}}
<div class="shrink-0 border-t border-gray-200 p-4">
    <div class="flex items-center gap-3 px-3 py-2">
        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 text-sm font-semibold">{{ substr(Auth::user()->name, 0, 2) }}</span>
        <div class="flex flex-col min-w-0">
            <span class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</span>
            <span class="text-xs text-gray-500 truncate">{{ Auth::user()->role }}</span>
        </div>
    </div>
</div>
