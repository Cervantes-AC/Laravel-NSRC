<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function index()
    {
        $siteSettings = Setting::where('group', 'site')->get()->keyBy('key');
        $securitySettings = Setting::where('group', 'security')->get()->keyBy('key');
        $emailSettings = Setting::where('group', 'email')->get()->keyBy('key');
        $backupSettings = Setting::where('group', 'backup')->get()->keyBy('key');
        $notificationSettings = Setting::where('group', 'notification')->get()->keyBy('key');

        return view('admin.settings.index', compact(
            'siteSettings',
            'securitySettings',
            'emailSettings',
            'backupSettings',
            'notificationSettings'
        ));
    }

    public function updateSite(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'site_url' => 'nullable|url|max:255',
            'admin_email' => 'nullable|email|max:255',
            'support_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'timezone' => 'required|string|max:100',
            'date_format' => 'required|string|max:50',
            'time_format' => 'required|string|max:50',
            'language' => 'required|string|max:10',
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string|max:500',
        ]);

        foreach ($validated as $key => $value) {
            Setting::where('key', $key)->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'string', 'group' => 'site']
            );
        }

        Log::info('Site settings updated', ['user_id' => auth()->id()]);

        return back()->with('success', 'Site settings updated successfully.');
    }

    public function updateSecurity(Request $request)
    {
        $validated = $request->validate([
            'password_min_length' => 'required|integer|min:6|max:128',
            'password_require_uppercase' => 'boolean',
            'password_require_lowercase' => 'boolean',
            'password_require_numbers' => 'boolean',
            'password_require_special' => 'boolean',
            'session_lifetime' => 'required|integer|min:10|max:525600',
            'two_factor_enabled' => 'boolean',
            'max_login_attempts' => 'required|integer|min:1|max:100',
            'lockout_duration' => 'required|integer|min:1|max:1440',
            'enable_rate_limiting' => 'boolean',
            'rate_limit_max_attempts' => 'required|integer|min:1|max:1000',
            'rate_limit_decay_minutes' => 'required|integer|min:1|max:120',
            'enable_hsts' => 'boolean',
            'enable_csp' => 'boolean',
            'enable_x_frame_options' => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                $type = $setting->type;
                if ($type === 'boolean') {
                    $value = $value ? '1' : '0';
                }
                $setting->update(['value' => (string) $value]);
            }
        }

        Log::info('Security settings updated', ['user_id' => auth()->id()]);

        return back()->with('success', 'Security settings updated successfully.');
    }

    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
            'mail_mailer' => 'required|string|in:log,smtp,mailgun,postmark,sendmail',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|in:tls,ssl',
            'enable_email_notifications' => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            Setting::where('key', $key)->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'string', 'group' => 'email']
            );
        }

        Log::info('Email settings updated', ['user_id' => auth()->id()]);

        return back()->with('success', 'Email settings updated successfully.');
    }

    public function updateBackup(Request $request)
    {
        $validated = $request->validate([
            'backup_enabled' => 'boolean',
            'backup_frequency' => 'required|string|in:daily,weekly,monthly',
            'backup_retention_days' => 'required|integer|min:1|max:365',
            'backup_include_files' => 'boolean',
            'backup_include_database' => 'boolean',
            'backup_notification_email' => 'nullable|email|max:255',
        ]);

        foreach ($validated as $key => $value) {
            Setting::where('key', $key)->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'string', 'group' => 'backup']
            );
        }

        Log::info('Backup settings updated', ['user_id' => auth()->id()]);

        return back()->with('success', 'Backup settings updated successfully.');
    }

    public function updateNotification(Request $request)
    {
        $validated = $request->validate([
            'notification_enabled' => 'boolean',
            'notification_email_alerts' => 'boolean',
            'notification_system_alerts' => 'boolean',
            'notification_retention_days' => 'required|integer|min:1|max:365',
            'notification_batch_send' => 'boolean',
            'notification_batch_interval' => 'required|integer|min:1|max:1440',
        ]);

        foreach ($validated as $key => $value) {
            Setting::where('key', $key)->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'string', 'group' => 'notification']
            );
        }

        Log::info('Notification settings updated', ['user_id' => auth()->id()]);

        return back()->with('success', 'Notification settings updated successfully.');
    }

    public function resetToDefaults(Request $request)
    {
        $group = $request->validate(['group' => 'required|string|in:site,security,email,backup,notification'])['group'];

        Setting::where('group', $group)->delete();

        Log::warning("Settings reset to defaults for group: {$group}", ['user_id' => auth()->id()]);

        return back()->with('success', "All {$group} settings have been reset to defaults.");
    }
}
