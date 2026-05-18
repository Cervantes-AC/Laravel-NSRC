<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            // Branding
            ['key' => 'site_name', 'value' => 'NSRC AMS', 'type' => 'string', 'group' => 'branding'],
            ['key' => 'logo', 'value' => '', 'type' => 'string', 'group' => 'branding'],
            ['key' => 'primary_color', 'value' => '#4f46e5', 'type' => 'string', 'group' => 'branding'],
            ['key' => 'secondary_color', 'value' => '#6366f1', 'type' => 'string', 'group' => 'branding'],

            // Email
            ['key' => 'smtp_host', 'value' => 'smtp.mailtrap.io', 'type' => 'string', 'group' => 'email'],
            ['key' => 'smtp_port', 'value' => '587', 'type' => 'integer', 'group' => 'email'],
            ['key' => 'smtp_username', 'value' => '', 'type' => 'string', 'group' => 'email'],
            ['key' => 'smtp_password', 'value' => '', 'type' => 'string', 'group' => 'email'],
            ['key' => 'smtp_encryption', 'value' => 'tls', 'type' => 'string', 'group' => 'email'],
            ['key' => 'from_address', 'value' => 'noreply@nsrc.org', 'type' => 'string', 'group' => 'email'],
            ['key' => 'from_name', 'value' => 'NSRC AMS', 'type' => 'string', 'group' => 'email'],

            // Security
            ['key' => 'password_min_length', 'value' => '8', 'type' => 'integer', 'group' => 'security'],
            ['key' => 'session_lifetime', 'value' => '120', 'type' => 'integer', 'group' => 'security'],
            ['key' => 'two_factor_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'max_login_attempts', 'value' => '5', 'type' => 'integer', 'group' => 'security'],

            // Backup
            ['key' => 'auto_backup', 'value' => '1', 'type' => 'boolean', 'group' => 'backup'],
            ['key' => 'backup_frequency', 'value' => 'daily', 'type' => 'string', 'group' => 'backup'],
            ['key' => 'backup_retention_days', 'value' => '30', 'type' => 'integer', 'group' => 'backup'],
            ['key' => 'backup_location', 'value' => 'storage/backups', 'type' => 'string', 'group' => 'backup'],

            // Notifications
            ['key' => 'email_notifications', 'value' => '1', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'push_notifications', 'value' => '0', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'duty_reminders', 'value' => '1', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'report_generation', 'value' => '1', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'system_alerts', 'value' => '1', 'type' => 'boolean', 'group' => 'notifications'],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
