<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserPreferenceFactory extends Factory
{
    protected $model = UserPreference::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'theme' => fake()->randomElement(['light', 'dark', 'system']),
            'notification_enabled' => fake()->boolean(),
            'email_notifications' => fake()->boolean(),
            'sms_notifications' => fake()->boolean(),
        ];
    }

    public function light(): static
    {
        return $this->state(fn () => ['theme' => 'light']);
    }

    public function dark(): static
    {
        return $this->state(fn () => ['theme' => 'dark']);
    }
}
