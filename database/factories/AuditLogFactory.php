<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = \App\Models\AuditLog::class;

    public function definition(): array
    {
        $types = ['SECURITY', 'REGISTRY', 'OPERATIONS', 'SYSTEM'];
        $actions = ['LOGIN', 'User Logged Out', 'Account Created', 'Account Deleted',
                     'Role Changed', 'Data Modified', 'Report Generated', 'System Error'];

        return [
            'user_id' => User::factory(),
            'full_name' => fake()->name(),
            'type' => fake()->randomElement($types),
            'action' => fake()->randomElement($actions),
            'details' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'timestamp' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function security(): static
    {
        return $this->state(fn() => ['type' => 'SECURITY']);
    }

    public function operation(): static
    {
        return $this->state(fn() => ['type' => 'OPERATIONS']);
    }
}
