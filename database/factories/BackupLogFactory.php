<?php

namespace Database\Factories;

use App\Models\BackupLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class BackupLogFactory extends Factory
{
    protected $model = BackupLog::class;

    public function definition(): array
    {
        $types = ['database', 'files', 'full'];
        $statuses = ['success', 'failed', 'in_progress'];

        return [
            'type' => fake()->randomElement($types),
            'filename' => 'backup-'.fake()->date('Y-m-d').'.sql',
            'size' => fake()->numberBetween(1024, 1048576),
            'status' => fake()->randomElement($statuses),
            'details' => fake()->sentence(),
        ];
    }

    public function successful(): static
    {
        return $this->state(fn () => ['status' => 'success']);
    }

    public function failed(): static
    {
        return $this->state(fn () => ['status' => 'failed']);
    }
}
