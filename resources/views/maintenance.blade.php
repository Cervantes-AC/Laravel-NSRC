<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'NSRC AMS') }} - Maintenance</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-slate-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md mx-auto text-center px-6">
        <div class="mb-8 inline-flex items-center justify-center w-20 h-20 rounded-full bg-amber-100">
            <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 mb-3">Under Maintenance</h1>
        <p class="text-slate-600 leading-relaxed">{{ $message ?? 'System is undergoing scheduled maintenance. Please check back shortly.' }}</p>
        <div class="mt-8 text-sm text-slate-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'NSRC AMS') }}
        </div>
    </div>
</body>
</html>
