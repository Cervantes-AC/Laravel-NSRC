<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DutySession;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_sessions' => DutySession::count(),
            'active_sessions' => DutySession::whereNull('time_out')->count(),
            'pending_users' => User::where('status', 'pending')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
