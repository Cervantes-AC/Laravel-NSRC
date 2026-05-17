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
    <body class="font-sans antialiased">
        <div class="min-h-screen flex">
            {{-- Left panel --}}
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 via-indigo-700 to-indigo-900 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 25% 50%, white 1px, transparent 1px); background-size: 40px 40px;"></div>
                <div class="relative w-full flex flex-col justify-between p-12">
                    <div class="flex items-center gap-3">
                        <x-application-logo class="h-10 w-10 brightness-0 invert" />
                        <span class="text-xl font-bold text-white">{{ config('app.name', 'NSRC') }}</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white leading-tight">Attendance Management<br>Made Simple</h1>
                        <p class="mt-4 text-indigo-200 text-lg max-w-md">Streamline volunteer attendance tracking, duty session management, and performance analytics.</p>
                        <div class="mt-8 flex gap-4">
                            <div class="flex -space-x-2">
                                <div class="w-10 h-10 rounded-full bg-indigo-400 border-2 border-indigo-600 flex items-center justify-center text-xs font-bold text-white">JD</div>
                                <div class="w-10 h-10 rounded-full bg-purple-400 border-2 border-indigo-600 flex items-center justify-center text-xs font-bold text-white">MK</div>
                                <div class="w-10 h-10 rounded-full bg-pink-400 border-2 border-indigo-600 flex items-center justify-center text-xs font-bold text-white">AL</div>
                                <div class="w-10 h-10 rounded-full bg-indigo-300 border-2 border-indigo-600 flex items-center justify-center text-xs font-bold text-white">+</div>
                            </div>
                            <span class="text-indigo-200 text-sm self-center">Trusted by NSRC volunteers</span>
                        </div>
                    </div>
                    <p class="text-indigo-300 text-sm">&copy; {{ date('Y') }} NSRC-AMS. All rights reserved.</p>
                </div>
            </div>

            {{-- Right panel --}}
            <div class="flex-1 flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 py-12 bg-gray-50">
                <div class="w-full sm:max-w-md">
                    <div class="lg:hidden flex justify-center mb-8">
                        <div class="flex items-center gap-3">
                            <x-application-logo class="h-10 w-10" />
                            <span class="text-xl font-bold text-gray-900">{{ config('app.name', 'NSRC') }}</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
