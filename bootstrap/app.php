<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'throttle.custom' => \App\Http\Middleware\RateLimitMiddleware::class,
            'security.headers' => \App\Http\Middleware\SecurityHeadersMiddleware::class,
            'session.timeout' => \App\Http\Middleware\SessionTimeoutMiddleware::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
            \App\Http\Middleware\SessionTimeoutMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $frequency = config('attendance.google_sheets.sync_schedule', 'hourly');

        $event = $schedule->command('attendance:sync-google-sheets');

        match ($frequency) {
            'everyMinute' => $event->everyMinute(),
            'everyFiveMinutes' => $event->everyFiveMinutes(),
            'everyTenMinutes' => $event->everyTenMinutes(),
            'everyFifteenMinutes' => $event->everyFifteenMinutes(),
            'everyThirtyMinutes' => $event->everyThirtyMinutes(),
            'daily' => $event->daily(),
            default => $event->hourly(),
        };

        $schedule->command('backup:run --type=database')->weeklyOn(0, '02:00');
        $schedule->command('backup:run --type=files')->weeklyOn(0, '03:00');
        $schedule->command('backup:run --type=full')->monthlyOn(1, '04:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
