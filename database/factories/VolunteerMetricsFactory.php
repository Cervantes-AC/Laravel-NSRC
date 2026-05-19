<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\VolunteerMetrics;
use Illuminate\Database\Eloquent\Factories\Factory;

class VolunteerMetricsFactory extends Factory
{
    protected $model = VolunteerMetrics::class;

    public function definition(): array
    {
        $regular = fake()->numberBetween(2400, 4800);
        $overtime = fake()->numberBetween(0, 600);
        $undertime = fake()->numberBetween(0, 300);

        return [
            'volunteer_id' => User::factory(),
            'full_name' => fake()->name(),
            'total_regular_minutes' => $regular,
            'total_overtime_minutes' => $overtime,
            'total_undertime_minutes' => $undertime,
            'invalid_record_count' => fake()->numberBetween(0, 5),
            'session_count' => fake()->numberBetween(5, 30),
        ];
    }
}
