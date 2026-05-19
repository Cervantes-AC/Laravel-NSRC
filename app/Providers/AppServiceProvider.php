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
use App\Models\Notification;
use App\Policies\DutySessionPolicy;
use App\Policies\NotificationPolicy;
use App\Services\AIProviderService;
use App\Services\AlertService;
use App\Services\BackupService;
use App\Services\CrudService;
use App\Services\DataExportService;
use App\Services\ExportService;
use App\Services\ImportService;
use App\Services\MetricsService;
use App\Services\NameNormalizationService;
use App\Services\NotificationService;
use App\Services\PDFService;
use App\Services\ReportService;
use App\Services\UserManagementService;
use App\Services\WarningService;
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
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService;
        });

        $this->app->singleton(WarningService::class, function ($app) {
            return new WarningService(
                $app->make(NotificationService::class),
            );
        });

        $this->app->singleton(AlertService::class, function ($app) {
            return new AlertService($app->make(NotificationService::class));
        });

        $this->app->singleton(BackupService::class, function ($app) {
            return new BackupService;
        });

        $this->app->singleton(ImportService::class, function ($app) {
            return new ImportService($app->make(WarningService::class));
        });

        $this->app->singleton(ExportService::class, function ($app) {
            return new ExportService($app->make(DataExportService::class), $app->make(PDFService::class));
        });

        $this->app->singleton(PDFService::class, function ($app) {
            return new PDFService($app->make(DataExportService::class));
        });

        $this->app->singleton(ReportService::class, function ($app) {
            return new ReportService(
                $app->make(AIProviderService::class),
                $app->make(NameNormalizationService::class),
            );
        });

        $this->app->singleton(UserManagementService::class, function ($app) {
            return new UserManagementService;
        });

        $this->app->singleton(MetricsService::class, function ($app) {
            return new MetricsService;
        });

        $this->app->singleton(CrudService::class, function ($app) {
            return new CrudService(
                $app->make(NotificationService::class),
            );
        });
    }

    public function boot(): void
    {
        Password::defaults(fn () => Password::min(8)->mixedCase()->numbers()->symbols());

        Gate::policy(DutySession::class, DutySessionPolicy::class);
        Gate::policy(Notification::class, NotificationPolicy::class);

        Gate::before(function ($user) {
            if ($user->role === 'admin') {
                return true;
            }
        });

        foreach (config('permissions', []) as $role => $modules) {
            foreach ($modules as $module => $actions) {
                foreach ($actions as $action) {
                    Gate::define("{$module}.{$action}", function ($user) use ($role) {
                        return $user->role === $role;
                    });
                }
            }
        }

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
