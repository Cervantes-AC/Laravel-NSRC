<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        $groups = ['branding', 'email', 'security', 'backup', 'notifications', 'general'];
        $types = ['string', 'boolean', 'integer', 'json'];

        return [
            'key' => fake()->unique()->word(),
            'value' => fake()->word(),
            'type' => fake()->randomElement($types),
            'group' => fake()->randomElement($groups),
        ];
    }
}
