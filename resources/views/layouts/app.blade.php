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
    <body class="font-sans antialiased app-shell">
        <div class="min-h-screen flex">
            {{-- Mobile backdrop --}}
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" style="display: none;"></div>

            {{-- Sidebar --}}
            <aside class="app-sidebar fixed inset-y-0 left-0 z-50 w-72 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:z-auto" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
                @include('layouts.navigation')
            </aside>

            {{-- Main content area --}}
            <div class="flex-1 flex flex-col min-w-0">
                {{-- Top bar --}}
                <header class="app-topbar sticky top-0 z-30">
                    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-3">
                            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden inline-flex items-center justify-center p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                            <div x-data="notificationCenter(false)">
                                <div @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-lg cursor-pointer" aria-label="Toggle notifications">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    <template x-if="unreadCount > 0">
                                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full" x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                                    </template>
                                </div>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 sm:w-96 z-50 bg-white border border-gray-200 rounded-lg shadow-lg" style="display: none;">
                                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                                        <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                                        <template x-if="unreadCount > 0">
                                            <button @click="markAllAsRead()" class="text-xs text-indigo-600 hover:text-indigo-800">Mark all read</button>
                                        </template>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto">
                                        <template x-if="notifications.length === 0">
                                            <div class="px-4 py-8 text-center text-sm text-gray-500">No notifications</div>
                                        </template>
                                        <template x-for="n in notifications" :key="n.id">
                                            <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition" :class="n.read_at ? '' : 'bg-indigo-50'">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm text-gray-900" x-text="n.title"></p>
                                                    <p class="text-xs text-gray-500 mt-0.5" x-text="n.message"></p>
                                                    <p class="text-xs text-gray-400 mt-0.5" x-text="n.created_at"></p>
                                                </div>
                                                <div class="flex-shrink-0 flex gap-1">
                                                    <template x-if="!n.read_at">
                                                        <button @click="markAsRead(n.id)" class="text-xs text-indigo-600 hover:text-indigo-800">Read</button>
                                                    </template>
                                                    <button @click="removeNotification(n.id)" class="text-xs text-red-600 hover:text-red-800">Del</button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="px-4 py-2 border-t border-gray-200 text-center">
                                        <a href="{{ route('notifications.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800">View all notifications</a>
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
