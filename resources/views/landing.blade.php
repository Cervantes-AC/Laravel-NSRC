<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'NSRC-AMS') }} - Attendance Management System</title>
    <link rel="icon" type="image/png" href="{{ asset(config('app.logo', 'images/nsrc-logo.png')) }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900">
    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                    <x-application-logo class="h-8 w-8" />
                </span>
                <span class="text-lg font-bold tracking-normal">NSRC<span class="text-blue-600">AMS</span></span>
            </a>
            <nav class="flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-950">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary">Register</a>
                        @endif
                    @endauth
                @endif
            </nav>
        </div>
    </header>

    <main>
        <section class="relative overflow-hidden bg-slate-950 text-white">
            <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(37,99,235,0.38),transparent_38%),linear-gradient(315deg,rgba(5,150,105,0.26),transparent_36%)]"></div>
            <div class="relative mx-auto grid min-h-[calc(100vh-4rem)] max-w-7xl items-center gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[1.05fr_.95fr] lg:px-8">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-blue-200">National Service Reserve Corps</p>
                    <h1 class="mt-5 text-4xl font-extrabold leading-tight tracking-normal sm:text-5xl lg:text-6xl">NSRC Attendance Management System</h1>
                    <p class="mt-6 max-w-xl text-lg leading-8 text-slate-300">A production-ready command center for attendance logging, personnel oversight, AI-assisted insights, compliance reporting, imports, backups, and audit visibility.</p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        @guest
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-primary bg-white text-blue-700 hover:bg-blue-50">Create account</a>
                            @endif
                            <a href="{{ route('login') }}" class="btn-secondary border-white/20 bg-white/10 text-white hover:bg-white/15">Sign in</a>
                        @endguest
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-primary bg-white text-blue-700 hover:bg-blue-50">Open dashboard</a>
                        @endauth
                    </div>
                </div>

                <div class="rounded-lg border border-white/10 bg-white/[0.08] p-5 shadow-2xl backdrop-blur-xl">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-lg bg-white p-5 text-slate-900">
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Today</p>
                            <p class="mt-3 text-3xl font-extrabold">Live</p>
                            <p class="mt-1 text-sm text-slate-500">Duty session monitoring</p>
                        </div>
                        <div class="rounded-lg border border-white/10 bg-white/10 p-5">
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-blue-200">Reports</p>
                            <p class="mt-3 text-3xl font-extrabold">PDF</p>
                            <p class="mt-1 text-sm text-slate-300">Export-ready records</p>
                        </div>
                        <div class="rounded-lg border border-white/10 bg-white/10 p-5">
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-emerald-200">Data</p>
                            <p class="mt-3 text-3xl font-extrabold">Sync</p>
                            <p class="mt-1 text-sm text-slate-300">CSV and Sheets imports</p>
                        </div>
                        <div class="rounded-lg border border-white/10 bg-white/10 p-5">
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-amber-200">Security</p>
                            <p class="mt-3 text-3xl font-extrabold">Audit</p>
                            <p class="mt-1 text-sm text-slate-300">Traceable activity logs</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl">
                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-blue-600">Core workspace</p>
                    <h2 class="mt-3 text-3xl font-bold tracking-normal text-slate-950">Built for daily attendance operations.</h2>
                </div>
                <div class="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach([
                        ['Attendance Tracking', 'Capture time-in, time-out, duration, integrity status, location, and sector context.'],
                        ['Personnel Management', 'Search, filter, rank, and review member compliance without leaving the console.'],
                        ['Reports and AI Insights', 'Generate operational reports and summarize patterns with provider-switchable AI support.'],
                        ['Import and Sync', 'Bring in CSV, Excel, and Google Sheets data while preserving source visibility.'],
                        ['Audit and Security', 'Trace account activity, role changes, data edits, sessions, and system events.'],
                        ['Backup Readiness', 'Run and review backup activity from the admin workspace.'],
                    ] as [$title, $body])
                        <article class="app-card-padded">
                            <h3 class="text-base font-bold text-slate-950">{{ $title }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $body }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-slate-50 py-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 text-sm text-slate-500 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
            <span>&copy; {{ date('Y') }} NSRC Attendance Management System.</span>
            <span>Laravel 12 powered operations console</span>
        </div>
    </footer>
</body>
</html>
