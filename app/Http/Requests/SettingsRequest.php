<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->input('group')) {
            'branding' => [
                'site_name' => ['required', 'string', 'max:255'],
                'logo' => ['nullable', 'image', 'max:2048'],
                'primary_color' => ['nullable', 'string', 'max:7'],
                'secondary_color' => ['nullable', 'string', 'max:7'],
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
