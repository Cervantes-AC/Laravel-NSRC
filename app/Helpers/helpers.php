<?php

use App\Helpers\SettingsHelper;

if (!function_exists('setting')) {
    /**
     * Get a setting value by key
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return SettingsHelper::get($key, $default);
    }
}

if (!function_exists('settings')) {
    /**
     * Get all settings or settings for a specific group
     */
    function settings(?string $group = null): array
    {
        return SettingsHelper::all($group);
    }
}
