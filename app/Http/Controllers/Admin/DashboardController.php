<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DutySession;
use App\Models\User;
use App\Services\AlertService;

class DashboardController extends Controller
{
    public function index(AlertService $alerts)
    {
        $stats = [
            'total_users' => User::count(),
            'total_sessions' => DutySession::count(),
            'active_sessions' => DutySession::whereNull('time_out')->count(),
            'pending_users' => User::where('status', 'pending')->count(),
        ];

        $storageWarning = $alerts->checkStorageCapacity();

        return view('admin.dashboard', compact('stats', 'storageWarning'));
    }
}
