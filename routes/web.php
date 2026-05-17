<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PersonnelController;
use App\Http\Controllers\Admin\SessionsController as AdminSessionsController;
use App\Http\Controllers\Admin\AccountsController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\PerformanceController as MemberPerformanceController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    if (! $user) {
        return redirect()->route('login');
    }

    return redirect()->route($user->role === 'admin' ? 'admin.dashboard' : 'member.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'throttle.custom'])->group(function () {
    Route::get('/analytics', fn () => view('pages.analytics'))->name('analytics.index');
    Route::get('/ranking', fn () => view('pages.ranking'))->name('ranking.index');
    Route::get('/notifications', fn () => view('pages.notifications'))->name('notifications.index');
});

Route::middleware(['auth', 'verified', 'role:admin', 'throttle.custom'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('personnel', PersonnelController::class);

    Route::get('sessions/restore/{id}', [AdminSessionsController::class, 'restore'])->name('sessions.restore');
    Route::resource('sessions', AdminSessionsController::class);

    Route::get('accounts', [AccountsController::class, 'index'])->name('accounts.index');
    Route::post('accounts/{user}/approve', [AccountsController::class, 'approve'])->name('accounts.approve');
    Route::post('accounts/{user}/reject', [AccountsController::class, 'reject'])->name('accounts.reject');
    Route::post('accounts/{user}/suspend', [AccountsController::class, 'suspend'])->name('accounts.suspend');
    Route::post('accounts/bulk-action', [AccountsController::class, 'bulkAction'])->name('accounts.bulk-action');
    Route::post('accounts/{user}/impersonate', [UserManagementController::class, 'impersonate'])->name('accounts.impersonate');
    Route::post('accounts/{user}/force-logout', [UserManagementController::class, 'forceLogout'])->name('accounts.force-logout');
    Route::get('accounts/{user}/history', [UserManagementController::class, 'loginHistory'])->name('accounts.history');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/branding', [SettingsController::class, 'updateBranding'])->name('settings.branding');
    Route::post('settings/email', [SettingsController::class, 'updateEmail'])->name('settings.email');
    Route::post('settings/security', [SettingsController::class, 'updateSecurity'])->name('settings.security');
    Route::post('settings/backup', [SettingsController::class, 'updateBackup'])->name('settings.backup');
    Route::post('settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications');
    Route::post('settings/update', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');

    Route::get('import', [ImportController::class, 'index'])->name('import.index');
    Route::post('import/preview', [ImportController::class, 'preview'])->name('import.preview');
    Route::post('import/process', [ImportController::class, 'process'])->name('import.process');
    Route::post('import/sync-google-sheets', [ImportController::class, 'syncGoogleSheets'])->name('import.sync-google-sheets');
    Route::get('import/template', [ImportController::class, 'downloadTemplate'])->name('import.template');

    Route::get('backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('backup/run', [BackupController::class, 'runBackup'])->name('backup.run');
    Route::get('backup/download/{id}', [BackupController::class, 'download'])->name('backup.download');

    Route::post('attendance/sync', [AttendanceController::class, 'sync'])->name('attendance.sync');
});

Route::middleware(['auth', 'verified', 'role:admin', 'throttle.custom'])->prefix('api/admin')->name('api.admin.')->group(function () {
    Route::get('attendance/fetch', [AttendanceController::class, 'fetchData'])->name('attendance.fetch');
});

Route::post('/admin/stop-impersonating', [UserManagementController::class, 'stopImpersonating'])
    ->middleware(['auth'])
    ->name('admin.stop-impersonating');

Route::middleware(['auth', 'verified', 'role:member', 'throttle.custom'])->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
    Route::get('/attendance', fn () => view('member.attendance'))->name('attendance');
    Route::get('/performance', [MemberPerformanceController::class, 'index'])->name('performance');
    Route::get('/how-to-log', fn () => view('member.how-to-log'))->name('how-to-log');
    Route::get('/rules', fn () => view('member.rules'))->name('rules');
});

Route::middleware(['auth', 'verified', 'throttle.custom'])->group(function () {
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [ReportsController::class, 'generate'])->name('reports.generate');
    Route::get('/reports/export', [ReportsController::class, 'export'])->name('reports.export');
    Route::get('/reports/insights', [ReportsController::class, 'insightsPage'])->name('reports.insights.view');
    Route::post('/reports/insights', [ReportsController::class, 'getInsights'])->name('reports.insights');
    Route::post('/reports/provider/switch', [ReportsController::class, 'switchProvider'])->name('reports.provider.switch');
    Route::post('/reports/api-key/switch', [ReportsController::class, 'switchApiKey'])->name('reports.api-key.switch');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
