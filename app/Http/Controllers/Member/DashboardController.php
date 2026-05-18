<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DutySession;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $sessions = DutySession::where('volunteer_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $attendanceCount = Attendance::where('full_name', $user->full_name)->count();

        $stats = [
            'total_sessions' => DutySession::where('volunteer_id', $user->id)->count(),
            'total_attendance_records' => $attendanceCount,
            'total_duration' => DutySession::where('volunteer_id', $user->id)->sum('duration_minutes'),
            'active_session' => DutySession::where('volunteer_id', $user->id)->whereNull('time_out')->first(),
            'recent_sessions' => $sessions,
        ];

        return view('member.dashboard', compact('user', 'stats'));
    }
}
