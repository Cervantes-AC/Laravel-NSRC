<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'NSRC-AMS') }} — Attendance Management System</title>
    <link rel="icon" type="image/png" href="{{ asset(config('app.logo', 'images/nsrc-logo.png')) }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <x-application-logo class="h-10 w-10" />
                    <span class="text-xl font-bold text-gray-900">NSRC<span class="text-blue-600">AMS</span></span>
                </div>
                <nav class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">Register</a>
                            @endif
                        @endauth
                    @endif
                </nav>
            </div>
        </div>
    </header>

    <main>
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-indigo-50"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
                <div class="text-center max-w-3xl mx-auto">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium mb-6">
                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                        National Service Reserve Corps
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 tracking-tight leading-tight">
                        Attendance Management<br>
                        <span class="text-blue-600">Made Simple</span>
                    </h1>
                    <p class="mt-6 text-lg sm:text-xl text-gray-600 leading-relaxed max-w-2xl mx-auto">
                        Streamline volunteer attendance tracking, duty session management, and performance analytics for the National Service Reserve Corps.
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                        @guest
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3 bg-blue-600 text-white text-base font-semibold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition">
                                Get Started
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-3 bg-white text-gray-700 text-base font-semibold rounded-xl border border-gray-300 hover:bg-gray-50 transition">
                                Sign In
                            </a>
                        @endguest
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-8 py-3 bg-blue-600 text-white text-base font-semibold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition">
                                Go to Dashboard
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-gray-50 to-transparent"></div>
        </section>

        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-gray-900">Core Features</h2>
                    <p class="mt-4 text-lg text-gray-600">Everything you need to manage attendance effectively</p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Attendance Tracking</h3>
                        <p class="text-gray-600">Real-time duty session management with time-in/time-out logging and automatic duration calculation.</p>
                    </div>
                    <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Role-Based Access</h3>
                        <p class="text-gray-600">Admin and Member roles with granular permissions ensuring data security and privacy.</p>
                    </div>
                    <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Analytics & Reports</h3>
                        <p class="text-gray-600">Comprehensive dashboards with performance metrics, charts, and exportable reports.</p>
                    </div>
                    <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Secure Authentication</h3>
                        <p class="text-gray-600">Multi-factor authentication, session management, and comprehensive audit logging.</p>
                    </div>
                    <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-rose-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Import & Export</h3>
                        <p class="text-gray-600">Bulk import from Excel/CSV, export to multiple formats including PDF and Excel.</p>
                    </div>
                    <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Rankings & Performance</h3>
                        <p class="text-gray-600">Leaderboards and performance metrics to recognize top contributors and track progress.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-gray-900">How It Works</h2>
                    <p class="mt-4 text-lg text-gray-600">Simple workflow for volunteers and administrators</p>
                </div>
                <div class="grid md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold">1</div>
                        <h3 class="font-semibold text-gray-900 mb-2">Register Account</h3>
                        <p class="text-gray-600 text-sm">Sign up with your NSRC credentials</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold">2</div>
                        <h3 class="font-semibold text-gray-900 mb-2">Log Attendance</h3>
                        <p class="text-gray-600 text-sm">Time in/out at your duty location</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold">3</div>
                        <h3 class="font-semibold text-gray-900 mb-2">Track Performance</h3>
                        <p class="text-gray-600 text-sm">Monitor your attendance and metrics</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold">4</div>
                        <h3 class="font-semibold text-gray-900 mb-2">Generate Reports</h3>
                        <p class="text-gray-600 text-sm">Export data for compliance</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl p-12 text-center">
                    <h2 class="text-3xl font-bold text-white mb-4">Ready to Get Started?</h2>
                    <p class="text-blue-100 text-lg mb-8 max-w-2xl mx-auto">Join thousands of NSRC volunteers managing their attendance efficiently.</p>
                    @guest
                        <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3 bg-white text-blue-600 text-base font-semibold rounded-xl hover:bg-blue-50 transition">
                            Create Your Account
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    @endguest
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-white border-t border-gray-200 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">&copy; {{ date('Y') }} NSRC Attendance Management System. All rights reserved.</span>
                </div>
                <div class="flex items-center gap-6 text-sm text-gray-500">
                    <span>Version 1.0.0</span>
                    <span>Framework Laravel 12</span>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
