<?php

namespace App\Helpers;

use App\Services\SettingsService;

class SettingsHelper
{
    /**
     * Get a setting value by key
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return app(SettingsService::class)->get($key, $default);
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'string'): void
    {
        app(SettingsService::class)->set($key, $value, $group, $type);
    }

    /**
     * Get all settings
     */
    public static function all(?string $group = null): array
    {
        return app(SettingsService::class)->all($group);
    }

    /**
     * Get settings grouped by their group field
     */
    public static function grouped(): array
    {
        return app(SettingsService::class)->grouped();
    }

    /**
     * Clear the settings cache
     */
    public static function clearCache(): void
    {
        app(SettingsService::class)->clearCache();
    }
}
