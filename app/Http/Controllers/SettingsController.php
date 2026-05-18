<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsRequest;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct(private SettingsService $settingsService)
    {
    }

    public function index()
    {
        $settings = $this->settingsService->grouped();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $group = $request->input('group', 'general');
        $this->normalizeBooleanInputs($request, $group);
        
        // Validate based on group
        $validated = $request->validate($this->getValidationRules($group));

        foreach ($validated as $key => $value) {
            $this->settingsService->set($key, $value, $group, $this->settingType($key));
        }

        return redirect()
            ->route('admin.settings.index', ['tab' => $group])
            ->with('success', __('Settings updated successfully.'));
    }

    private function normalizeBooleanInputs(Request $request, string $group): void
    {
        $booleanFields = match ($group) {
            'security' => ['two_factor_enabled'],
            'backup' => ['auto_backup'],
            'notifications' => ['email_notifications', 'push_notifications', 'duty_reminders', 'report_generation', 'system_alerts'],
            default => [],
        };

        foreach ($booleanFields as $field) {
            $request->merge([$field => $request->boolean($field)]);
        }
    }

    private function settingType(string $key): string
    {
        return in_array($key, [
            'two_factor_enabled',
            'auto_backup',
            'email_notifications',
            'push_notifications',
            'duty_reminders',
            'report_generation',
            'system_alerts',
        ], true) ? 'boolean' : 'string';
    }

    private function getValidationRules(string $group): array
    {
        return match ($group) {
            'branding' => [
                'site_name' => ['required', 'string', 'max:255'],
                'logo' => ['nullable', 'url', 'max:2048'],
                'primary_color' => ['nullable', 'string', 'regex:/^#[0-9A-F]{6}$/i'],
                'secondary_color' => ['nullable', 'string', 'regex:/^#[0-9A-F]{6}$/i'],
            ],
            'email' => [
                'smtp_host' => ['required', 'string', 'max:255'],
                'smtp_port' => ['required', 'integer', 'min:1', 'max:65535'],
                'smtp_username' => ['nullable', 'string', 'max:255'],
                'smtp_password' => ['nullable', 'string', 'max:255'],
                'smtp_encryption' => ['nullable', 'string', 'in:ssl,tls'],
                'from_address' => ['required', 'email'],
                'from_name' => ['required', 'string', 'max:255'],
            ],
            'security' => [
                'password_min_length' => ['required', 'integer', 'min:6', 'max:128'],
                'session_lifetime' => ['required', 'integer', 'min:1', 'max:1440'],
                'two_factor_enabled' => ['nullable', 'boolean'],
                'max_login_attempts' => ['required', 'integer', 'min:1', 'max:10'],
            ],
            'backup' => [
                'auto_backup' => ['required', 'boolean'],
                'backup_frequency' => ['required_if:auto_backup,true', 'string', 'in:daily,weekly,monthly'],
                'backup_retention_days' => ['required', 'integer', 'min:1', 'max:365'],
                'backup_location' => ['nullable', 'string', 'max:255'],
            ],
            'notifications' => [
                'email_notifications' => ['nullable', 'boolean'],
                'push_notifications' => ['nullable', 'boolean'],
                'duty_reminders' => ['nullable', 'boolean'],
                'report_generation' => ['nullable', 'boolean'],
                'system_alerts' => ['nullable', 'boolean'],
            ],
            default => [],
        };
    }
}
