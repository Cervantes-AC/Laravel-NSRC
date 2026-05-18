<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DutySession;
use App\Models\User;
use App\Services\AlertService;
use App\Services\MetricsService;

class DashboardController extends Controller
{
    public function index(AlertService $alerts, MetricsService $metricsService)
    {
        $summary = $metricsService->getSystemSummary();
        
        $stats = [
            'total_users' => $summary['total_users'],
            'active_users' => $summary['active_users'],
            'total_sessions' => $summary['total_sessions'],
            'total_attendance_records' => $summary['total_attendance_records'],
            'active_sessions' => $summary['active_sessions'],
            'today_sessions' => $summary['today_count'],
            'today_attendance' => $summary['attendance_today'],
            'pending_users' => User::where('status', 'pending')->count(),
            'completion_rate' => $summary['completion_rate'],
            'avg_duration' => $summary['average_duration_minutes'],
        ];

        $storageWarning = $alerts->checkStorageCapacity();

        return view('admin.dashboard', compact('stats', 'storageWarning'));
    }
}
