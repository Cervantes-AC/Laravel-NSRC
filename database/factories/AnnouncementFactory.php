<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'priority' => fake()->randomElement(['normal', 'important', 'urgent']),
            'status' => 'draft',
            'audience' => 'members',
            'created_by' => User::factory()->admin(),
            'published_at' => null,
            'expires_at' => null,
            'notified_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
