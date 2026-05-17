<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\DutySession;
use App\Models\VolunteerMetrics;
use Illuminate\Support\Facades\Auth;

class PerformanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $sessions = DutySession::where('volunteer_id', $user->id)->orWhere('full_name', $user->full_name)->get();
        $volunteerMetrics = VolunteerMetrics::where('volunteer_id', $user->id)->first();

        $totalMinutes = (int) $sessions->sum('duration_minutes');
        $sessionCount = $sessions->count();
        $metricsData = [
            'total_hours' => round($totalMinutes / 60, 1),
            'total_sessions' => $sessionCount,
            'avg_duration' => $sessionCount > 0 ? (int) round($totalMinutes / $sessionCount) : 0,
            'monthly' => [],
        ];

        return view('member.performance', [
            'metrics' => $metricsData,
            'volunteerMetrics' => $volunteerMetrics,
            'sessions' => $sessions->sortByDesc('date')->take(20),
        ]);
    }
}
