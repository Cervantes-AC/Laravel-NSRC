<?php

use App\Http\Controllers\Admin\AccountsController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PersonnelController;
use App\Http\Controllers\Admin\SessionsController as AdminSessionsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AIModelController;
use App\Http\Controllers\Api\AIProviderController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AuditLogsController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MemberAttendanceController;
use App\Http\Controllers\Api\NotificationsController;
use App\Http\Controllers\Api\RankingsController;
use App\Http\Controllers\Api\SessionsController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\PerformanceController as MemberPerformanceController;
use App\Http\Controllers\AnnouncementController as MemberAnnouncementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
    Route::get('/sessions', [SessionsController::class, 'index'])->name('sessions.index');
    Route::post('/sessions/sync', [SessionsController::class, 'sync'])->name('sessions.sync');
    Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/stream', [NotificationsController::class, 'stream'])->name('notifications.stream');
    Route::get('/announcements/recent', [App\Http\Controllers\Api\MemberAnnouncementsController::class, 'recent'])->name('announcements.recent');
    Route::post('/notifications/{id}/read', [NotificationsController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationsController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{id}', [NotificationsController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/analytics/data', [AnalyticsController::class, 'data'])->name('analytics.data');
    Route::get('/rankings', [RankingsController::class, 'index'])->name('rankings.index');
    Route::get('/personnel', [App\Http\Controllers\Api\PersonnelController::class, 'index'])->name('personnel.index');
    Route::get('/personnel/history', [App\Http\Controllers\Api\PersonnelController::class, 'history'])->name('personnel.history');
    Route::get('/accounts', [App\Http\Controllers\Api\AccountsController::class, 'index'])->name('accounts.index');
    Route::post('/accounts/{id}/approve', [App\Http\Controllers\Api\AccountsController::class, 'approve'])->name('accounts.approve');
    Route::post('/accounts/{id}/reject', [App\Http\Controllers\Api\AccountsController::class, 'reject'])->name('accounts.reject');
    Route::post('/accounts/{id}/suspend', [App\Http\Controllers\Api\AccountsController::class, 'suspend'])->name('accounts.suspend');
    Route::post('/accounts/bulk-action', [App\Http\Controllers\Api\AccountsController::class, 'bulkAction'])->name('accounts.bulk-action');
    Route::get('/audit-logs', [AuditLogsController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/export', [AuditLogsController::class, 'export'])->name('audit-logs.export');

    Route::post('/ai/provider/switch', [AIProviderController::class, 'switchProvider'])->name('ai.provider.switch');
    Route::post('/ai/api-key/switch', [AIProviderController::class, 'switchApiKey'])->name('ai.api-key.switch');
    Route::get('/ai-model/current', [AIModelController::class, 'current'])->name('ai-model.current');
    Route::get('/ai-model', [AIModelController::class, 'index'])->name('ai-model.index');
    Route::post('/ai-model/update', [AIModelController::class, 'update'])->name('ai-model.update');
    Route::get('/ai-model/tier/{tier}', [AIModelController::class, 'byTier'])->name('ai-model.by-tier');
    Route::get('/ai-model/provider/{provider}', [AIModelController::class, 'byProvider'])->name('ai-model.by-provider');

    // ChatBot API routes
    Route::post('/chatbot/send', [ChatBotController::class, 'sendMessage'])->name('chatbot.send');
    Route::get('/chatbot/models', [ChatBotController::class, 'getModels'])->name('chatbot.models');
    Route::post('/chatbot/stream', [ChatBotController::class, 'streamMessage'])->name('chatbot.stream');
});

Route::middleware(['auth', 'role:admin'])->prefix('api')->name('api.')->group(function () {
    Route::post('/reports/generate', [App\Http\Controllers\Api\ReportsController::class, 'generate'])->name('reports.generate');
    Route::post('/reports/export-csv', [App\Http\Controllers\Api\ReportsController::class, 'exportCsv'])->name('reports.export-csv');
    Route::post('/reports/export-pdf', [App\Http\Controllers\Api\ReportsController::class, 'exportPdf'])->name('reports.export-pdf');
});

Route::middleware(['auth', 'role:member'])->prefix('api/member')->name('api.member.')->group(function () {
    Route::get('/attendance', [MemberAttendanceController::class, 'index'])->name('attendance');
    Route::post('/time-in', [MemberAttendanceController::class, 'timeIn'])->name('time-in');
    Route::post('/time-out', [MemberAttendanceController::class, 'timeOut'])->name('time-out');
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
    Route::get('/chatbot', [ChatBotController::class, 'index'])->name('chatbot.index');
});

Route::middleware(['auth', 'role:admin', 'throttle.custom'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('personnel/restore/{id}', [PersonnelController::class, 'restore'])->name('personnel.restore');
    Route::resource('personnel', PersonnelController::class);

    Route::get('announcements/restore/{id}', [AnnouncementController::class, 'restore'])->name('announcements.restore');
    Route::resource('announcements', AnnouncementController::class)->except(['show']);

    Route::get('attendance', [AdminSessionsController::class, 'index'])->name('attendance.index');
    Route::get('attendance/{session}', [AdminSessionsController::class, 'show'])->name('attendance.show');
    Route::get('sessions/restore/{id}', [AdminSessionsController::class, 'restore'])->name('sessions.restore');
    Route::resource('sessions', AdminSessionsController::class);

    Route::get('accounts', [AccountsController::class, 'index'])->name('accounts.index');
    Route::get('accounts/analytics', [AccountsController::class, 'analytics'])->name('accounts.analytics');
    Route::get('accounts/create', [AccountsController::class, 'create'])->name('accounts.create');
    Route::post('accounts', [AccountsController::class, 'store'])->name('accounts.store');
    Route::get('accounts/{user}/edit', [AccountsController::class, 'edit'])->name('accounts.edit');
    Route::patch('accounts/{user}', [AccountsController::class, 'update'])->name('accounts.update');
    Route::delete('accounts/{user}', [AccountsController::class, 'destroy'])->name('accounts.destroy');
    Route::get('accounts/restore/{id}', [AccountsController::class, 'restore'])->name('accounts.restore');
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
    Route::get('import/template/{type?}', [ImportController::class, 'downloadTemplate'])->name('import.template');

    Route::get('export', [ExportController::class, 'index'])->name('export.index');
    Route::get('export/accounts', [ExportController::class, 'accounts'])->name('export.accounts');
    Route::get('export/sessions', [ExportController::class, 'sessions'])->name('export.sessions');
    Route::get('export/personnel', [ExportController::class, 'personnel'])->name('export.personnel');
    Route::get('export/attendance', [ExportController::class, 'attendance'])->name('export.attendance');

    Route::get('backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('backup/run', [BackupController::class, 'runBackup'])->name('backup.run');
    Route::get('backup/download/{id}', [BackupController::class, 'download'])->name('backup.download');
    Route::post('backup/resend-email/{id}', [BackupController::class, 'resendEmail'])->name('backup.resend-email');
    Route::post('backup/toggle-email', [BackupController::class, 'toggleEmailNotifications'])->name('backup.toggle-email');

    Route::post('attendance/sync', [AttendanceController::class, 'sync'])->name('attendance.sync');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/site', [SettingsController::class, 'updateSite'])->name('settings.update-site');
    Route::post('settings/security', [SettingsController::class, 'updateSecurity'])->name('settings.update-security');
    Route::post('settings/email', [SettingsController::class, 'updateEmail'])->name('settings.update-email');
    Route::post('settings/backup', [SettingsController::class, 'updateBackup'])->name('settings.update-backup');
    Route::post('settings/notification', [SettingsController::class, 'updateNotification'])->name('settings.update-notification');
    Route::post('settings/branding', [SettingsController::class, 'updateBranding'])->name('settings.update-branding');
    Route::post('settings/api', [SettingsController::class, 'updateApi'])->name('settings.update-api');
    Route::post('settings/api-keys/generate', [SettingsController::class, 'generateApiKey'])->name('settings.generate-api-key');
    Route::post('settings/api-keys/{apiKey}/revoke', [SettingsController::class, 'revokeApiKey'])->name('settings.revoke-api-key');
    Route::delete('settings/api-keys/{apiKey}', [SettingsController::class, 'deleteApiKey'])->name('settings.delete-api-key');
    Route::post('settings/reset', [SettingsController::class, 'resetToDefaults'])->name('settings.reset');
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
    Route::patch('/profile/email-notifications', [ProfileController::class, 'updateEmailNotifications'])->name('profile.email-notifications');

    // Announcement routes
    Route::get('/announcements', [MemberAnnouncementController::class, 'index'])->name('announcements.index');

    // Notification routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', fn () => view('pages.notifications'))->name('index');
        Route::get('/failures', [NotificationController::class, 'failures'])->name('failures');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
        Route::post('/{notification}/acknowledge', [NotificationController::class, 'acknowledge'])->name('acknowledge');
        Route::post('/acknowledge-all-critical', [NotificationController::class, 'acknowledgeAllCritical'])->name('acknowledge-all-critical');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::post('/delete-all-read', [NotificationController::class, 'deleteAllRead'])->name('delete-all-read');
        Route::get('/api/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::get('/api/recent-failures', [NotificationController::class, 'recentFailures'])->name('recent-failures');
        Route::get('/api/critical-alerts', [NotificationController::class, 'criticalAlerts'])->name('critical-alerts');
        Route::get('/export', [NotificationController::class, 'export'])->name('export');
    });
});

require __DIR__.'/auth.php';
