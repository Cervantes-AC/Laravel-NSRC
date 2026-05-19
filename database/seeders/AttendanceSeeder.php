<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\DutySession;
use App\Models\User;
use App\Models\VolunteerMetrics;
use App\Services\DutyEngine;
use App\Services\MetricsService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    private array $locations = [
        'Main Gate', 'Admin Office', 'Library', 'Clinic', 'Canteen',
        'Dormitory', 'Auditorium', 'Satellite Campus',
    ];

    private array $shiftTypes = ['Morning', 'Afternoon', 'Night'];

    public function run(): void
    {
        Attendance::truncate();
        DutySession::truncate();
        VolunteerMetrics::truncate();

        $users = User::where('role', 'member')->where('status', 'active')->get();

        foreach ($users as $user) {
            $this->seedUserAttendance($user);
        }

        $this->rebuildDutySessions();
    }

    private function seedUserAttendance(User $user): void
    {
        $sessionCount = random_int(12, 25);

        for ($i = 0; $i < $sessionCount; $i++) {
            $daysAgo = random_int(0, 60);
            $date = Carbon::now()->subDays($daysAgo);

            $hourStart = random_int(6, 10);
            $timeIn = $date->copy()->setHour($hourStart)->setMinute(random_int(0, 59))->setSecond(0);

            $location = $this->locations[array_rand($this->locations)];
            $shiftType = $this->shiftTypes[array_rand($this->shiftTypes)];

            $attendanceIn = [
                'full_name' => $user->full_name,
                'attendance' => 'Time in',
                'date_time' => $timeIn,
                'location' => $location,
                'shift_type' => $shiftType,
            ];

            $hasTimeout = random_int(1, 10) > 2;

            if ($hasTimeout) {
                $durationHours = random_int(3, 12);
                $timeOut = $timeIn->copy()->addHours($durationHours)->addMinutes(random_int(0, 59));

                if ($timeOut->isAfter(now())) {
                    $timeOut = now()->subMinutes(random_int(5, 120));
                }

                $attendanceOut = [
                    'full_name' => $user->full_name,
                    'attendance' => 'Time out',
                    'date_time' => $timeOut,
                    'location' => $location,
                    'shift_type' => $shiftType,
                ];

                Attendance::create($attendanceIn);
                Attendance::create($attendanceOut);
            } else {
                Attendance::create($attendanceIn);
            }
        }
    }

    private function rebuildDutySessions(): void
    {
        $dutyEngine = app(DutyEngine::class);
        $logs = Attendance::query()->orderBy('date_time')->get();

        if ($logs->isEmpty()) {
            return;
        }

        $sessions = $dutyEngine->processDutyLogs($logs);

        DB::transaction(function () use ($sessions) {
            foreach ($sessions as $session) {
                $volunteerId = $this->resolveVolunteerId($session->full_name);

                $attributes = [
                    'full_name' => $session->full_name,
                    'date' => $session->date,
                    'time_in' => $session->time_in,
                    'time_out' => $session->time_out,
                    'duration_minutes' => $session->duration_minutes,
                    'status' => $session->status,
                    'location' => $session->location,
                    'sector' => $session->sector,
                    'integrity_score' => $session->integrity_score,
                    'volunteer_id' => $volunteerId,
                    'trace_id' => 'SEED-'.strtoupper(substr(md5(uniqid()), 0, 8)),
                ];

                $match = DutySession::query()
                    ->where('full_name', $session->full_name)
                    ->whereDate('date', $session->date)
                    ->when($session->time_in, fn ($q) => $q->where('time_in', $session->time_in))
                    ->first();

                if ($match) {
                    $match->update($attributes);
                } else {
                    DutySession::create($attributes);
                }
            }

            VolunteerMetrics::query()->delete();
            app(MetricsService::class)->calculateVolunteerMetrics(DutySession::query()->get());
        });
    }

    private function resolveVolunteerId(string $fullName): ?int
    {
        return User::where('full_name', $fullName)->value('id');
    }
}
