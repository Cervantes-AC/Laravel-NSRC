<?php

namespace Database\Seeders;

use App\Models\DutySession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DutySessionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'member')->get();
        $locations = ['Main Gate', 'Admin Office', 'Clinic', 'Canteen', 'Dormitory'];
        $statuses = ['COMPLETE', 'COMPLETE', 'COMPLETE', 'ONGOING', 'MISSING_TIMEOUT'];

        foreach ($users as $user) {
            // Create 10-20 sessions per user over the past 30 days
            $sessionCount = random_int(10, 20);
            for ($i = 0; $i < $sessionCount; $i++) {
                $date = Carbon::now()->subDays(random_int(0, 30));
                $timeIn = $date->copy()->setHour(random_int(6, 10))->setMinute(random_int(0, 59));
                $status = $statuses[array_rand($statuses)];
                $timeOut = null;
                $duration = null;

                if ($status === 'COMPLETE') {
                    $timeOut = $timeIn->copy()->addHours(random_int(4, 12));
                    $duration = $timeIn->diffInMinutes($timeOut);
                } elseif ($status === 'ONGOING') {
                    $timeOut = null;
                    $duration = null;
                } elseif ($status === 'MISSING_TIMEOUT') {
                    $timeOut = null;
                    $duration = null;
                }

                $location = $locations[array_rand($locations)];
                $sector = config("attendance.sectors.$location", 'General');

                DutySession::create([
                    'full_name' => $user->full_name,
                    'volunteer_id' => $user->id,
                    'date' => $date,
                    'time_in' => $timeIn,
                    'time_out' => $timeOut,
                    'duration_minutes' => $duration,
                    'status' => $status,
                    'location' => $location,
                    'sector' => $sector,
                    'integrity_score' => $status === 'COMPLETE' ? 100 : ($status === 'MISSING_TIMEOUT' ? 60 : 40),
                    'trace_id' => 'SEED-'.strtoupper(substr(md5(uniqid()), 0, 8)),
                ]);
            }
        }
    }
}
