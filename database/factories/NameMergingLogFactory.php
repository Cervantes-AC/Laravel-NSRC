<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NameMergingLogFactory extends Factory
{
    protected $model = \App\Models\NameMergingLog::class;

    public function definition(): array
    {
        return [
            'original_name' => fake()->name(),
            'merged_name' => fake()->name(),
            'similarity_score' => fake()->randomFloat(2, 0.7, 1.0),
            'session_id' => fake()->optional()->uuid(),
        ];
    }
}
