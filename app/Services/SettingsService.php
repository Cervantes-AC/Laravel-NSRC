<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    private const CACHE_KEY = 'app_settings';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get a setting value by key
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();
        
        if (!isset($settings[$key])) {
            return $default;
        }

        return $this->castValue($settings[$key]);
    }

    /**
     * Get all settings, optionally filtered by group
     */
    public function all(?string $group = null): array
    {
        $settings = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Setting::all()->keyBy('key')->toArray();
        });

        if ($group) {
            return array_filter($settings, fn ($setting) => $setting['group'] === $group);
        }

        return $settings;
    }

    /**
     * Get settings grouped by their group field
     */
    public function grouped(): array
    {
        $settings = $this->all();
        $grouped = [];

        foreach ($settings as $key => $setting) {
            $group = $setting['group'];
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][$key] = $this->castValue($setting);
        }

        return $grouped;
    }

    /**
     * Set a setting value
     */
    public function set(string $key, mixed $value, string $group = 'general', string $type = 'string'): void
    {
        // Determine type if not specified
        if ($type === 'string') {
            if (is_bool($value)) {
                $type = 'boolean';
                $value = $value ? '1' : '0';
            } elseif (is_array($value)) {
                $type = 'json';
                $value = json_encode($value);
            } elseif (is_int($value)) {
                $type = 'integer';
                $value = (string) $value;
            }
        }

        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : (string) $value,
                'type' => $type,
                'group' => $group,
            ]
        );

        $this->clearCache();
    }

    /**
     * Clear the settings cache
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get settings for a specific group with proper formatting
     */
    public function getGroupForForm(string $group): array
    {
        $settings = $this->all($group);
        $formatted = [];

        foreach ($settings as $key => $setting) {
            $formatted[$key] = $this->castValue($setting);
        }

        return $formatted;
    }

    private function castValue(array $setting): mixed
    {
        return match ($setting['type']) {
            'boolean' => filter_var($setting['value'], FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting['value'],
            'json' => json_decode($setting['value'], true),
            default => $setting['value'],
        };
    }
}
