<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset(config('app.logo', 'images/nsrc-logo.png')) }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50">
        <div class="min-h-screen flex">
            {{-- Left panel --}}
            <div class="hidden lg:flex lg:w-1/2 bg-slate-950 relative overflow-hidden">
                <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(37,99,235,0.34),transparent_42%),linear-gradient(315deg,rgba(5,150,105,0.24),transparent_38%)]"></div>
                <div class="absolute inset-0 opacity-[0.08]" style="background-image: linear-gradient(rgba(255,255,255,.8) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.8) 1px, transparent 1px); background-size: 42px 42px;"></div>
                <div class="relative w-full flex flex-col justify-between p-12">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-white">
                            <x-application-logo class="h-9 w-9" />
                        </div>
                        <span class="text-xl font-bold text-white">{{ config('app.name', 'NSRC') }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-blue-200">National Service Reserve Corps</p>
                        <h1 class="mt-4 text-4xl font-bold text-white leading-tight">Attendance operations with cleaner data and faster decisions.</h1>
                        <p class="mt-5 text-slate-300 text-lg max-w-md">Manage duty sessions, personnel, reports, backups, and analytics from one secure console.</p>
                        <div class="mt-8 grid grid-cols-3 gap-3 max-w-md">
                            <div class="rounded-lg border border-white/10 bg-white/10 p-4">
                                <p class="text-2xl font-bold text-white">24/7</p>
                                <p class="mt-1 text-xs font-medium text-slate-300">Session access</p>
                            </div>
                            <div class="rounded-lg border border-white/10 bg-white/10 p-4">
                                <p class="text-2xl font-bold text-white">AI</p>
                                <p class="mt-1 text-xs font-medium text-slate-300">Insights ready</p>
                            </div>
                            <div class="rounded-lg border border-white/10 bg-white/10 p-4">
                                <p class="text-2xl font-bold text-white">PDF</p>
                                <p class="mt-1 text-xs font-medium text-slate-300">Export reports</p>
                            </div>
                        </div>
                    </div>
                    <p class="text-slate-400 text-sm">&copy; {{ date('Y') }} NSRC-AMS. All rights reserved.</p>
                </div>
            </div>

            {{-- Right panel --}}
            <div class="flex-1 flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 py-12">
                <div class="w-full sm:max-w-md">
                    <div class="lg:hidden flex justify-center mb-8">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-white shadow-sm">
                                <x-application-logo class="h-9 w-9" />
                            </div>
                            <span class="text-xl font-bold text-gray-900">{{ config('app.name', 'NSRC') }}</span>
                        </div>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/80 sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script>
        // Ensure CSRF token is refreshed on page load
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[method="POST"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const token = document.querySelector('meta[name="csrf-token"]')?.content;
                    const csrfInput = form.querySelector('input[name="_token"]');
                    if (csrfInput && token) {
                        csrfInput.value = token;
                    }
                });
            }
        });
    </script>
</html>
