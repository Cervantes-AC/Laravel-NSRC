<?php

namespace App\Providers;

use App\Events\UserLoggedIn;
use App\Listeners\LogAccountCreated;
use App\Listeners\LogAccountDeleted;
use App\Listeners\LogDataModified;
use App\Listeners\LogReportGenerated;
use App\Listeners\LogRoleChanged;
use App\Listeners\LogSystemError;
use App\Listeners\LogUserLogin;
use App\Listeners\LogUserLogout;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(UserLoggedIn::class, LogUserLogin::class);
        Event::listen(Logout::class, LogUserLogout::class);
        Event::listen(Registered::class, LogAccountCreated::class);
    }
}
