<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PersonnelController;
use App\Http\Controllers\Admin\SessionsController as AdminSessionsController;
use App\Http\Controllers\Admin\AccountsController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Api\MemberAttendanceController;
use App\Http\Controllers\Member\PerformanceController as MemberPerformanceController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    Route::get('/dashboard/data', [\App\Http\Controllers\Api\DashboardController::class, 'data'])->name('dashboard.data');
    Route::get('/sessions', [\App\Http\Controllers\Api\SessionsController::class, 'index'])->name('sessions.index');
    Route::post('/sessions/sync', [\App\Http\Controllers\Api\SessionsController::class, 'sync'])->name('sessions.sync');
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationsController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/stream', [\App\Http\Controllers\Api\NotificationsController::class, 'stream'])->name('notifications.stream');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationsController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Api\NotificationsController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\Api\NotificationsController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/analytics/data', [\App\Http\Controllers\Api\AnalyticsController::class, 'data'])->name('analytics.data');
    Route::get('/rankings', [\App\Http\Controllers\Api\RankingsController::class, 'index'])->name('rankings.index');
    Route::get('/personnel', [\App\Http\Controllers\Api\PersonnelController::class, 'index'])->name('personnel.index');
    Route::get('/personnel/history', [\App\Http\Controllers\Api\PersonnelController::class, 'history'])->name('personnel.history');
    Route::get('/accounts', [\App\Http\Controllers\Api\AccountsController::class, 'index'])->name('accounts.index');
    Route::post('/accounts/{id}/approve', [\App\Http\Controllers\Api\AccountsController::class, 'approve'])->name('accounts.approve');
    Route::post('/accounts/{id}/reject', [\App\Http\Controllers\Api\AccountsController::class, 'reject'])->name('accounts.reject');
    Route::post('/accounts/{id}/suspend', [\App\Http\Controllers\Api\AccountsController::class, 'suspend'])->name('accounts.suspend');
    Route::post('/accounts/bulk-action', [\App\Http\Controllers\Api\AccountsController::class, 'bulkAction'])->name('accounts.bulk-action');
    Route::get('/audit-logs', [\App\Http\Controllers\Api\AuditLogsController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/export', [\App\Http\Controllers\Api\AuditLogsController::class, 'export'])->name('audit-logs.export');

    Route::post('/ai/provider/switch', [\App\Http\Controllers\Api\AIProviderController::class, 'switchProvider'])->name('ai.provider.switch');
    Route::post('/ai/api-key/switch', [\App\Http\Controllers\Api\AIProviderController::class, 'switchApiKey'])->name('ai.api-key.switch');
});

Route::middleware(['auth', 'role:admin'])->prefix('api')->name('api.')->group(function () {
    Route::post('/reports/generate', [\App\Http\Controllers\Api\ReportsController::class, 'generate'])->name('reports.generate');
    Route::post('/reports/export-csv', [\App\Http\Controllers\Api\ReportsController::class, 'exportCsv'])->name('reports.export-csv');
    Route::post('/reports/export-pdf', [\App\Http\Controllers\Api\ReportsController::class, 'exportPdf'])->name('reports.export-pdf');
});

Route::middleware(['auth', 'role:member'])->prefix('api/member')->name('api.member.')->group(function () {
    Route::get('/attendance', [MemberAttendanceController::class, 'index'])->name('attendance');
    Route::post('/time-in', [\App\Http\Controllers\Api\MemberAttendanceController::class, 'timeIn'])->name('time-in');
    Route::post('/time-out', [\App\Http\Controllers\Api\MemberAttendanceController::class, 'timeOut'])->name('time-out');
});

Route::get('/', function () {
    return view('landing');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    if (! $user) {
        return redirect()->route('login');
    }

    return redirect()->route($user->role === 'admin' ? 'admin.dashboard' : 'member.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'throttle.custom'])->group(function () {
    Route::get('/analytics', fn () => view('pages.analytics'))->name('analytics.index');
    Route::get('/ranking', fn () => view('pages.ranking'))->name('ranking.index');
    Route::get('/notifications', function () {
        $announcements = \App\Models\Announcement::visibleToMembers()
            ->latest('published_at')
            ->latest()
            ->paginate(15);

        return view('pages.notifications', compact('announcements'));
    })->name('notifications.index');
});

Route::middleware(['auth', 'role:admin', 'throttle.custom'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('personnel', PersonnelController::class);
    Route::resource('announcements', AnnouncementController::class)->except(['show']);

    Route::get('attendance', [AdminSessionsController::class, 'index'])->name('attendance.index');
    Route::get('attendance/{session}', [AdminSessionsController::class, 'show'])->name('attendance.show');
    Route::get('sessions/restore/{id}', [AdminSessionsController::class, 'restore'])->name('sessions.restore');
    Route::resource('sessions', AdminSessionsController::class);

    Route::get('accounts', [AccountsController::class, 'index'])->name('accounts.index');
    Route::get('accounts/{user}/edit', [AccountsController::class, 'edit'])->name('accounts.edit');
    Route::patch('accounts/{user}', [AccountsController::class, 'update'])->name('accounts.update');
    Route::post('accounts/{user}/approve', [AccountsController::class, 'approve'])->name('accounts.approve');
    Route::post('accounts/{user}/reject', [AccountsController::class, 'reject'])->name('accounts.reject');
    Route::post('accounts/{user}/suspend', [AccountsController::class, 'suspend'])->name('accounts.suspend');
    Route::post('accounts/bulk-action', [AccountsController::class, 'bulkAction'])->name('accounts.bulk-action');
    Route::post('accounts/{user}/impersonate', [UserManagementController::class, 'impersonate'])->name('accounts.impersonate');
    Route::post('accounts/{user}/force-logout', [UserManagementController::class, 'forceLogout'])->name('accounts.force-logout');
    Route::get('accounts/{user}/history', [UserManagementController::class, 'loginHistory'])->name('accounts.history');

    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');

    Route::get('import', [ImportController::class, 'index'])->name('import.index');
    Route::post('import/preview', [ImportController::class, 'preview'])->name('import.preview');
    Route::post('import/process', [ImportController::class, 'process'])->name('import.process');
    Route::get('import/template', [ImportController::class, 'downloadTemplate'])->name('import.template');

    Route::get('export', [\App\Http\Controllers\ExportController::class, 'index'])->name('export.index');
    Route::get('export/accounts', [\App\Http\Controllers\ExportController::class, 'accounts'])->name('export.accounts');
    Route::get('export/sessions', [\App\Http\Controllers\ExportController::class, 'sessions'])->name('export.sessions');
    Route::get('export/personnel', [\App\Http\Controllers\ExportController::class, 'personnel'])->name('export.personnel');
    Route::get('export/attendance', [\App\Http\Controllers\ExportController::class, 'attendance'])->name('export.attendance');

    Route::get('backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('backup/run', [BackupController::class, 'runBackup'])->name('backup.run');
    Route::get('backup/download/{id}', [BackupController::class, 'download'])->name('backup.download');
    Route::post('backup/resend-email/{id}', [BackupController::class, 'resendEmail'])->name('backup.resend-email');
    Route::post('backup/toggle-email', [BackupController::class, 'toggleEmailNotifications'])->name('backup.toggle-email');

    Route::post('attendance/sync', [AttendanceController::class, 'sync'])->name('attendance.sync');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/site', [SettingsController::class, 'updateSite'])->name('settings.update-site');
    Route::post('settings/security', [SettingsController::class, 'updateSecurity'])->name('settings.update-security');
});

Route::middleware(['auth', 'role:admin', 'throttle.custom'])->prefix('api/admin')->name('api.admin.')->group(function () {
    Route::get('attendance/fetch', [AttendanceController::class, 'fetchData'])->name('attendance.fetch');
});

Route::post('/admin/stop-impersonating', [UserManagementController::class, 'stopImpersonating'])
    ->middleware(['auth'])
    ->name('admin.stop-impersonating');

Route::middleware(['auth', 'role:member', 'throttle.custom'])->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
    Route::get('/attendance', fn () => view('member.attendance'))->name('attendance');
    Route::get('/performance', [MemberPerformanceController::class, 'index'])->name('performance');
    Route::get('/how-to-log', fn () => view('member.how-to-log'))->name('how-to-log');
    Route::get('/rules', fn () => view('member.rules'))->name('rules');
});

Route::middleware(['auth', 'role:admin', 'throttle.custom'])->group(function () {
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
    Route::post('/profile/enable-2fa', [ProfileController::class, 'enableTwoFactor'])->name('profile.enable-2fa');
    Route::delete('/profile/disable-2fa', [ProfileController::class, 'disableTwoFactor'])->name('profile.disable-2fa');
});

require __DIR__.'/auth.php';
