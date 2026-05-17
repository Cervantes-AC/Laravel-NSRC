<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = \App\Models\Attendance::class;

    public function definition(): array
    {
        $locations = ['Main Gate', 'Admin Office', 'Clinic', 'Canteen', 'Dormitory'];
        $shiftTypes = ['Morning', 'Afternoon', 'Night'];

        return [
            'full_name' => fake()->name(),
            'attendance' => fake()->randomElement(['TIME_IN', 'TIME_OUT']),
            'date_time' => fake()->dateTimeBetween('-1 month', 'now'),
            'location' => fake()->randomElement($locations),
            'shift_type' => fake()->randomElement($shiftTypes),
        ];
    }
}
