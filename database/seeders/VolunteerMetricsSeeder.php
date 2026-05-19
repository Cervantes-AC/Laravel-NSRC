<?php

namespace Database\Seeders;

use App\Models\DutySession;
use App\Models\User;
use App\Models\VolunteerMetrics;
use Illuminate\Database\Seeder;

class VolunteerMetricsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing metrics
        VolunteerMetrics::truncate();

        $users = User::where('role', 'member')->get();

        foreach ($users as $user) {
            // Get all completed duty sessions for this user
            $completedSessions = DutySession::where('volunteer_id', $user->id)
                ->where('status', 'COMPLETE')
                ->whereNotNull('duration_minutes')
                ->get();

            // Calculate total minutes
            $totalRegularMinutes = 0;
            $totalOvertimeMinutes = 0;
            $totalUndertimeMinutes = 0;
            $invalidRecordCount = 0;
            $sessionCount = $completedSessions->count();

            foreach ($completedSessions as $session) {
                $duration = $session->duration_minutes;

                // Assuming 8 hours (480 minutes) is regular duty
                if ($duration >= 480) {
                    $totalRegularMinutes += 480;
                    $totalOvertimeMinutes += ($duration - 480);
                } elseif ($duration > 0) {
                    $totalUndertimeMinutes += (480 - $duration);
                    $totalRegularMinutes += $duration;
                } else {
                    $invalidRecordCount++;
                }
            }

            // Count incomplete sessions as invalid
            $incompleteSessions = DutySession::where('volunteer_id', $user->id)
                ->whereIn('status', ['ONGOING', 'MISSING_TIMEOUT'])
                ->count();
            $invalidRecordCount += $incompleteSessions;

            // Create or update volunteer metrics
            VolunteerMetrics::updateOrCreate(
                ['volunteer_id' => $user->id],
                [
                    'full_name' => $user->full_name,
                    'total_regular_minutes' => $totalRegularMinutes,
                    'total_overtime_minutes' => $totalOvertimeMinutes,
                    'total_undertime_minutes' => $totalUndertimeMinutes,
                    'invalid_record_count' => $invalidRecordCount,
                    'session_count' => $sessionCount,
                ]
            );
        }
    }
}
