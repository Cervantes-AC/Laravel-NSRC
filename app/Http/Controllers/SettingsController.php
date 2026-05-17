<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsRequest;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function updateBranding(SettingsRequest $request)
    {
        foreach ($request->validated() as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'branding'],
                ['value' => $value, 'type' => 'string']
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Branding settings updated successfully.');
    }

    public function updateEmail(SettingsRequest $request)
    {
        foreach ($request->validated() as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'email'],
                ['value' => $value, 'type' => 'string']
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Email settings updated successfully.');
    }

    public function updateSecurity(SettingsRequest $request)
    {
        foreach ($request->validated() as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'security'],
                ['value' => $value, 'type' => 'string']
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Security settings updated successfully.');
    }

    public function updateBackup(SettingsRequest $request)
    {
        foreach ($request->validated() as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'backup'],
                ['value' => $value, 'type' => 'string']
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Backup settings updated successfully.');
    }

    public function updateNotifications(SettingsRequest $request)
    {
        foreach ($request->validated() as $key => $value) {
            // Convert arrays to JSON strings
            $storedValue = is_array($value) ? json_encode($value) : $value;
            
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'notifications'],
                ['value' => $storedValue, 'type' => is_array($value) ? 'json' : 'string']
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Notification settings updated successfully.');
    }

    public function update(Request $request)
    {
        $groups = ['branding', 'email', 'security', 'backup', 'notifications'];

        foreach ($groups as $group) {
            $fields = match ($group) {
                'branding' => ['app_name', 'app_logo', 'primary_color'],
                'email' => ['mail_driver', 'mail_host', 'mail_port', 'mail_from'],
                'security' => ['two_factor', 'session_timeout', 'max_login_attempts'],
                'backup' => ['auto_backup', 'backup_frequency', 'backup_retention'],
                'notifications' => ['email_notifications', 'sms_notifications', 'in_app_notifications', 'notify_events'],
                default => [],
            };

            foreach ($fields as $field) {
                if ($request->has($field)) {
                    $value = $request->input($field);
                    // Convert arrays to JSON strings
                    $storedValue = is_array($value) ? json_encode($value) : $value;
                    
                    Setting::updateOrCreate(
                        ['key' => $field, 'group' => $group],
                        ['value' => $storedValue, 'type' => is_array($value) ? 'json' : 'string']
                    );
                }
            }
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully.');
    }
}
