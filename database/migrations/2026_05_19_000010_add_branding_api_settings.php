<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $brandingSettings = [
            ['key' => 'branding_logo', 'value' => '', 'type' => 'string', 'group' => 'branding'],
            ['key' => 'branding_favicon', 'value' => '', 'type' => 'string', 'group' => 'branding'],
            ['key' => 'branding_primary_color', 'value' => '#f97316', 'type' => 'string', 'group' => 'branding'],
            ['key' => 'branding_secondary_color', 'value' => '#3b82f6', 'type' => 'string', 'group' => 'branding'],
            ['key' => 'branding_accent_color', 'value' => '#10b981', 'type' => 'string', 'group' => 'branding'],
        ];

        $apiSettings = [
            ['key' => 'api_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'api'],
            ['key' => 'api_rate_limit_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'api'],
            ['key' => 'api_rate_limit_max_attempts', 'value' => '60', 'type' => 'integer', 'group' => 'api'],
            ['key' => 'api_rate_limit_decay_minutes', 'value' => '1', 'type' => 'integer', 'group' => 'api'],
            ['key' => 'api_key_required', 'value' => '1', 'type' => 'boolean', 'group' => 'api'],
        ];

        foreach (array_merge($brandingSettings, $apiSettings) as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('key');
            $table->string('status')->default('active');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Setting::whereIn('group', ['branding', 'api'])->delete();
        Schema::dropIfExists('api_keys');
    }
};
