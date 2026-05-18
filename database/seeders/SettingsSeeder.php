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
            // Site Settings (10)
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

            // Security Settings (15)
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
        ];

        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
