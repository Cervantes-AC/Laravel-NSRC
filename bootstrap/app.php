<?php

use App\Http\Middleware\AccessLogMiddleware;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\InputSanitizationMiddleware;
use App\Http\Middleware\MaintenanceMiddleware;
use App\Http\Middleware\OptimisticLockingMiddleware;
use App\Http\Middleware\RateLimitMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\SessionTimeoutMiddleware;
use App\Http\Middleware\XssProtectionMiddleware;
use App\Providers\BrandingServiceProvider;
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
            'role' => CheckRole::class,
            'permission' => CheckPermission::class,
            'throttle.custom' => RateLimitMiddleware::class,
            'security.headers' => SecurityHeadersMiddleware::class,
            'session.timeout' => SessionTimeoutMiddleware::class,
            'access.log' => AccessLogMiddleware::class,
            'xss.protect' => XssProtectionMiddleware::class,
            'sanitize' => InputSanitizationMiddleware::class,
            'optimistic.lock' => OptimisticLockingMiddleware::class,
            'maintenance' => MaintenanceMiddleware::class,
        ]);

        $middleware->web(append: [
            SecurityHeadersMiddleware::class,
            SessionTimeoutMiddleware::class,
            AccessLogMiddleware::class,
        ]);

        $middleware->api(prepend: [
            MaintenanceMiddleware::class,
        ]);
    })
    ->withProviders([
        BrandingServiceProvider::class,
    ])
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('backup:run --type=database')->weeklyOn(0, '02:00');
        $schedule->command('backup:run --type=files')->weeklyOn(0, '03:00');
        $schedule->command('backup:run --type=full')->monthlyOn(1, '04:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
