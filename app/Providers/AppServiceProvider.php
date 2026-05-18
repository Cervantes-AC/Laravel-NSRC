<?php

namespace App\Providers;

use App\Events\AccountDeleted;
use App\Events\DataModified;
use App\Events\ReportGenerated;
use App\Events\RoleChanged;
use App\Events\SystemError;
use App\Events\UserLoggedIn;
use App\Listeners\LogAccountCreated;
use App\Listeners\LogAccountDeleted;
use App\Listeners\LogDataModified;
use App\Listeners\LogReportGenerated;
use App\Listeners\LogRoleChanged;
use App\Listeners\LogSystemError;
use App\Listeners\LogUserLogin;
use App\Listeners\LogUserLogout;
use App\Models\DutySession;
use App\Policies\DutySessionPolicy;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Password::defaults(fn () => Password::min(8)->mixedCase()->numbers()->symbols());

        Gate::policy(DutySession::class, DutySessionPolicy::class);

        Event::listen(UserLoggedIn::class, LogUserLogin::class);
        Event::listen(Logout::class, LogUserLogout::class);
        Event::listen(Registered::class, LogAccountCreated::class);
        Event::listen(DataModified::class, LogDataModified::class);
        Event::listen(ReportGenerated::class, LogReportGenerated::class);
        Event::listen(SystemError::class, LogSystemError::class);
        Event::listen(RoleChanged::class, LogRoleChanged::class);
        Event::listen(AccountDeleted::class, LogAccountDeleted::class);
    }
}
