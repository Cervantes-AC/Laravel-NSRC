<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset(config('app.logo', 'images/nsrc-logo.png')) }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased app-shell" x-data="appShell({ toggleBreakpoint: 1024 })" @load="init()" @resize.window="updateToggleVisibility()">
        <div class="min-h-screen flex">
            {{-- Mobile backdrop --}}
            <div x-show="sidebarOpen && shouldShowToggle" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm" @click="sidebarOpen = false" style="display: none;"></div>

            {{-- Sidebar --}}
            <aside class="app-sidebar fixed inset-y-0 left-0 z-50 w-72 flex flex-col transition-transform duration-300 ease-in-out" :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', shouldShowToggle ? '' : 'lg:translate-x-0 lg:static lg:z-auto']">
                @include('layouts.navigation')
            </aside>

            {{-- Main content area --}}
            <div class="flex-1 flex flex-col min-w-0">
                {{-- Top bar --}}
                <header class="app-topbar sticky top-0 z-30">
                    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-3">
                            <button @click="sidebarOpen = !sidebarOpen" class="nav-toggle inline-flex items-center justify-center p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500" :class="{'hidden': !shouldShowToggle}" @window:resize="updateToggleVisibility()">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': sidebarOpen, 'inline-flex': !sidebarOpen}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': !sidebarOpen, 'inline-flex': sidebarOpen}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="hidden lg:block">
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Operations Console</p>
                                <p class="text-sm font-semibold text-slate-800">{{ now()->format('F j, Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-ai-model-switcher />
                            <div x-data="notificationCenter(false)">
                                <div @click="open = !open" class="relative p-2 text-slate-500 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg cursor-pointer" aria-label="Toggle notifications">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    <template x-if="unreadCount > 0">
                                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full" x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                                    </template>
                                </div>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 sm:w-96 z-50 bg-white border border-slate-200 rounded-lg shadow-lg" style="display: none;">
                                    <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200">
                                        <h3 class="text-sm font-semibold text-slate-900">Notifications</h3>
                                        <template x-if="unreadCount > 0">
                                            <button @click="markAllAsRead()" class="text-xs text-blue-600 hover:text-blue-800">Mark all read</button>
                                        </template>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto">
                                        <template x-if="notifications.length === 0">
                                            <div class="px-4 py-8 text-center text-sm text-slate-500">No notifications</div>
                                        </template>
                                        <template x-for="n in notifications" :key="n.id">
                                            <div class="flex items-start gap-3 px-4 py-3 border-b border-slate-100 hover:bg-slate-50 transition" :class="n.read_at ? '' : 'bg-blue-50'">
                                                <div class="flex-shrink-0 mt-0.5">
                                                    <div class="w-7 h-7 rounded-full flex items-center justify-center"
                                                        :class="{
                                                            'bg-green-100 text-green-600': n.level === 'success' || n.data?.status === 'completed',
                                                            'bg-red-100 text-red-600': n.level === 'error' || n.data?.status === 'failed',
                                                            'bg-amber-100 text-amber-600': n.level === 'warning',
                                                            'bg-blue-100 text-blue-600': n.level === 'info' || n.data?.status === 'started' || n.data?.status === 'scheduled',
                                                            'bg-purple-100 text-purple-600': n.data?.validation_status
                                                        }">
                                                        <template x-if="n.data?.action_type === 'backup' || n.type?.startsWith('backup')">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4" /></svg>
                                                        </template>
                                                        <template x-if="n.data?.action_type === 'import' || n.type?.startsWith('import')">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4l-4 4m0 0l4-4m-4-4l-4 4" /></svg>
                                                        </template>
                                                        <template x-if="n.data?.action_type === 'export' || n.type?.startsWith('export')">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                        </template>
                                                        <template x-if="n.data?.validation_status || n.type?.startsWith('validation')">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                        </template>
                                                        <template x-if="n.type === 'announcement'">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6" /></svg>
                                                        </template>
                                                        <template x-if="!['backup', 'import', 'export', 'announcement'].some(t => n.type?.startsWith(t) || n.data?.action_type === t) && !n.data?.validation_status">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-slate-900" x-text="n.title"></p>
                                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-2" x-text="n.message"></p>
                                                    <p class="text-xs text-slate-400 mt-1" x-text="n.created_at"></p>
                                                </div>
                                                <div class="flex-shrink-0 flex gap-1">
                                                    <template x-if="!n.read_at">
                                                        <button @click="markAsRead(n.id)" class="text-xs text-blue-600 hover:text-blue-800 px-1.5 py-0.5 rounded hover:bg-blue-50">Read</button>
                                                    </template>
                                                    <button @click="removeNotification(n.id)" class="text-xs text-red-600 hover:text-red-800 px-1.5 py-0.5 rounded hover:bg-red-50">Del</button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="px-4 py-2 border-t border-slate-200 text-center">
                                        <a href="{{ route('notifications.index') }}" class="text-xs text-blue-600 hover:text-blue-800">View all notifications</a>
                                    </div>
                                </div>
                            </div>
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 shadow-sm transition hover:border-slate-300 hover:text-slate-900">
                                        <span class="hidden sm:inline">{{ Auth::user()->full_name ?? Auth::user()->name }}</span>
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-700 text-sm font-bold">{{ strtoupper(substr(Auth::user()->full_name ?? Auth::user()->name, 0, 2)) }}</span>
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </header>

                {{-- Page Header --}}
                @isset($header)
                    <div class="border-b border-slate-200/80 bg-white/70 backdrop-blur-xl">
                        <div class="px-4 sm:px-6 lg:px-8 py-5">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                {{-- Page Content --}}
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
