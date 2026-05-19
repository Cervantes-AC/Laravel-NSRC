<?php

namespace Database\Factories;

use App\Models\DutySession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DutySessionFactory extends Factory
{
    protected $model = DutySession::class;

    public function definition(): array
    {
        $timeIn = fake()->dateTimeBetween('-1 month', 'now');
        $timeOut = (clone $timeIn)->modify('+'.rand(2, 10).' hours');
        $duration = ($timeOut->getTimestamp() - $timeIn->getTimestamp()) / 60;
        $locations = ['Main Gate', 'Admin Office', 'Clinic', 'Canteen', 'Dormitory'];
        $sectors = ['Security', 'Administration', 'Health Services', 'Food Services', 'Accommodation'];
        $statuses = ['COMPLETE', 'ONGOING', 'MISSING_TIMEOUT', 'INVALID_LOG'];

        return [
            'volunteer_id' => User::factory(),
            'full_name' => fake()->name(),
            'date' => $timeIn->format('Y-m-d'),
            'time_in' => $timeIn,
            'time_out' => $timeOut,
            'duration_minutes' => $duration,
            'status' => fake()->randomElement($statuses),
            'trace_id' => fake()->uuid(),
            'location' => fake()->randomElement($locations),
            'sector' => fake()->randomElement($sectors),
            'integrity_score' => fake()->randomFloat(1, 40, 100),
        ];
    }

    public function complete(): static
    {
        return $this->state(fn () => ['status' => 'COMPLETE']);
    }

    public function ongoing(): static
    {
        return $this->state(fn () => [
            'status' => 'ONGOING',
            'time_out' => null,
            'duration_minutes' => null,
        ]);
    }
}
