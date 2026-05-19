<?php

namespace Database\Factories;

use App\Models\ConversationHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationHistoryFactory extends Factory
{
    protected $model = ConversationHistory::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'message' => fake()->sentence(),
            'response' => fake()->paragraph(),
            'mode' => fake()->randomElement(['quick', 'chat', 'insight']),
        ];
    }
}
