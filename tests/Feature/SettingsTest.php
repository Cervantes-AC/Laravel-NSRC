<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_settings_page(): void
    {
        $admin = User::factory()->admin()->create();
        Setting::factory()->create(['key' => 'site_name', 'value' => 'Test Site', 'group' => 'site']);

        $this->actingAs($admin)
            ->get(route('admin.settings.index'))
            ->assertOk();
    }

    public function test_member_cannot_access_settings_page(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->get(route('admin.settings.index'))
            ->assertRedirect();
    }

    public function test_admin_can_update_site_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $siteData = [
            'site_name' => 'Updated Site Name',
            'site_description' => 'New description',
            'site_url' => 'https://example.com',
            'admin_email' => 'admin@example.com',
            'support_email' => 'support@example.com',
            'contact_phone' => '+1234567890',
            'timezone' => 'America/New_York',
            'date_format' => 'm/d/Y',
            'time_format' => 'h:i A',
            'language' => 'es',
        ];

        $this->actingAs($admin)
            ->withSession([])
            ->post(route('admin.settings.update-site'), array_merge($siteData, ['_token' => csrf_token()]))
            ->assertRedirect()
            ->assertSessionHas('success');

        foreach ($siteData as $key => $value) {
            $this->assertDatabaseHas('settings', [
                'key' => $key,
                'value' => $value,
                'group' => 'site',
            ]);
        }
    }

    public function test_admin_can_update_security_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $securityData = [
            'password_min_length' => 10,
            'password_require_uppercase' => 1,
            'password_require_lowercase' => 1,
            'password_require_numbers' => 1,
            'password_require_special' => 1,
            'session_lifetime' => 60,
            'two_factor_enabled' => 1,
            'max_login_attempts' => 3,
            'lockout_duration' => 30,
            'enable_rate_limiting' => 1,
            'rate_limit_max_attempts' => 100,
            'rate_limit_decay_minutes' => 5,
            'enable_hsts' => 1,
            'enable_csp' => 1,
            'enable_x_frame_options' => 1,
        ];

        $this->actingAs($admin)
            ->withSession([])
            ->post(route('admin.settings.update-security'), array_merge($securityData, ['_token' => csrf_token()]))
            ->assertRedirect()
            ->assertSessionHas('success');
    }

    public function test_site_settings_validation(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->withSession([])
            ->post(route('admin.settings.update-site'), [
                '_token' => csrf_token(),
                'site_name' => '',
                'timezone' => '',
                'date_format' => '',
                'time_format' => '',
                'language' => '',
            ])
            ->assertSessionHasErrors(['site_name', 'timezone', 'date_format', 'time_format', 'language']);
    }

    public function test_security_settings_validation(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->withSession([])
            ->post(route('admin.settings.update-security'), [
                '_token' => csrf_token(),
                'password_min_length' => 3,
                'session_lifetime' => 5,
                'max_login_attempts' => 0,
                'lockout_duration' => 0,
                'rate_limit_max_attempts' => 0,
                'rate_limit_decay_minutes' => 0,
            ])
            ->assertSessionHasErrors([
                'password_min_length',
                'session_lifetime',
                'max_login_attempts',
                'lockout_duration',
                'rate_limit_max_attempts',
                'rate_limit_decay_minutes',
            ]);
    }

    public function test_setting_model_get_typed_value(): void
    {
        $booleanSetting = Setting::factory()->create(['key' => 'test_bool', 'value' => '1', 'type' => 'boolean']);
        $integerSetting = Setting::factory()->create(['key' => 'test_int', 'value' => '42', 'type' => 'integer']);
        $stringSetting = Setting::factory()->create(['key' => 'test_str', 'value' => 'hello', 'type' => 'string']);

        $this->assertTrue($booleanSetting->getTypedValue());
        $this->assertSame(42, $integerSetting->getTypedValue());
        $this->assertSame('hello', $stringSetting->getTypedValue());
    }

    public function test_setting_model_get_value_static(): void
    {
        Setting::factory()->create(['key' => 'site_name', 'value' => 'My Site', 'type' => 'string']);

        $this->assertSame('My Site', Setting::getValue('site_name'));
        $this->assertNull(Setting::getValue('nonexistent'));
        $this->assertSame('default', Setting::getValue('nonexistent', 'default'));
    }
}
