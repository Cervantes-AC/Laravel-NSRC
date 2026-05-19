<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaultSettings = [
            // Site Settings
            ['key' => 'site_name', 'value' => 'NSRC AMS', 'type' => 'string', 'group' => 'site'],
            ['key' => 'site_description', 'value' => 'NSRC Attendance Management System', 'type' => 'string', 'group' => 'site'],
            ['key' => 'site_url', 'value' => '', 'type' => 'string', 'group' => 'site'],
            ['key' => 'admin_email', 'value' => 'admin@nsrc.org', 'type' => 'string', 'group' => 'site'],
            ['key' => 'support_email', 'value' => 'support@nsrc.org', 'type' => 'string', 'group' => 'site'],
            ['key' => 'contact_phone', 'value' => '', 'type' => 'string', 'group' => 'site'],
            ['key' => 'timezone', 'value' => 'Asia/Manila', 'type' => 'string', 'group' => 'site'],
            ['key' => 'date_format', 'value' => 'Y-m-d', 'type' => 'string', 'group' => 'site'],
            ['key' => 'time_format', 'value' => 'H:i', 'type' => 'string', 'group' => 'site'],
            ['key' => 'language', 'value' => 'en', 'type' => 'string', 'group' => 'site'],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'group' => 'site'],
            ['key' => 'maintenance_message', 'value' => 'System is undergoing scheduled maintenance. Please check back shortly.', 'type' => 'string', 'group' => 'site'],

            // Security Settings
            ['key' => 'password_min_length', 'value' => '8', 'type' => 'integer', 'group' => 'security'],
            ['key' => 'password_require_uppercase', 'value' => '1', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'password_require_lowercase', 'value' => '1', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'password_require_numbers', 'value' => '1', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'password_require_special', 'value' => '0', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'session_lifetime', 'value' => '120', 'type' => 'integer', 'group' => 'security'],
            ['key' => 'two_factor_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'max_login_attempts', 'value' => '5', 'type' => 'integer', 'group' => 'security'],
            ['key' => 'lockout_duration', 'value' => '15', 'type' => 'integer', 'group' => 'security'],
            ['key' => 'enable_rate_limiting', 'value' => '1', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'rate_limit_max_attempts', 'value' => '60', 'type' => 'integer', 'group' => 'security'],
            ['key' => 'rate_limit_decay_minutes', 'value' => '1', 'type' => 'integer', 'group' => 'security'],
            ['key' => 'enable_hsts', 'value' => '1', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'enable_csp', 'value' => '0', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'enable_x_frame_options', 'value' => '1', 'type' => 'boolean', 'group' => 'security'],

            // Email Settings
            ['key' => 'mail_from_address', 'value' => 'noreply@nsrc.org', 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_from_name', 'value' => 'NSRC AMS', 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_mailer', 'value' => 'log', 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_host', 'value' => '', 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_port', 'value' => '587', 'type' => 'integer', 'group' => 'email'],
            ['key' => 'mail_username', 'value' => '', 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_password', 'value' => '', 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_encryption', 'value' => 'tls', 'type' => 'string', 'group' => 'email'],
            ['key' => 'enable_email_notifications', 'value' => '1', 'type' => 'boolean', 'group' => 'email'],

            // Backup Settings
            ['key' => 'backup_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'backup'],
            ['key' => 'backup_frequency', 'value' => 'weekly', 'type' => 'string', 'group' => 'backup'],
            ['key' => 'backup_retention_days', 'value' => '30', 'type' => 'integer', 'group' => 'backup'],
            ['key' => 'backup_include_files', 'value' => '1', 'type' => 'boolean', 'group' => 'backup'],
            ['key' => 'backup_include_database', 'value' => '1', 'type' => 'boolean', 'group' => 'backup'],
            ['key' => 'backup_notification_email', 'value' => '', 'type' => 'string', 'group' => 'backup'],

            // Notification Settings
            ['key' => 'notification_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'notification_email_alerts', 'value' => '1', 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'notification_system_alerts', 'value' => '1', 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'notification_retention_days', 'value' => '30', 'type' => 'integer', 'group' => 'notification'],
            ['key' => 'notification_batch_send', 'value' => '0', 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'notification_batch_interval', 'value' => '5', 'type' => 'integer', 'group' => 'notification'],

            // Branding Settings
            ['key' => 'branding_logo', 'value' => '', 'type' => 'string', 'group' => 'branding'],
            ['key' => 'branding_favicon', 'value' => '', 'type' => 'string', 'group' => 'branding'],
            ['key' => 'branding_primary_color', 'value' => '#f97316', 'type' => 'string', 'group' => 'branding'],
            ['key' => 'branding_secondary_color', 'value' => '#3b82f6', 'type' => 'string', 'group' => 'branding'],
            ['key' => 'branding_accent_color', 'value' => '#10b981', 'type' => 'string', 'group' => 'branding'],

            // API Settings
            ['key' => 'api_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'api'],
            ['key' => 'api_rate_limit_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'api'],
            ['key' => 'api_rate_limit_max_attempts', 'value' => '60', 'type' => 'integer', 'group' => 'api'],
            ['key' => 'api_rate_limit_decay_minutes', 'value' => '1', 'type' => 'integer', 'group' => 'api'],
            ['key' => 'api_key_required', 'value' => '1', 'type' => 'boolean', 'group' => 'api'],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
