<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\DutySession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberLogController extends Controller
{
    public function timeIn(Request $request): JsonResponse
    {
        $user = Auth::user();

        $active = DutySession::where('volunteer_id', $user->id)
            ->whereNull('time_out')
            ->first();

        if ($active) {
            return response()->json(['success' => false, 'message' => 'You already have an active session. Log out first.'], 422);
        }

        $session = DutySession::create([
            'full_name' => $user->full_name ?? $user->name,
            'date' => now()->toDateString(),
            'time_in' => now(),
            'volunteer_id' => $user->id,
            'status' => 'ONGOING',
            'location' => $request->input('location', ''),
            'sector' => 'General',
            'integrity_score' => 60,
        ]);

        Attendance::create([
            'full_name' => $user->full_name ?? $user->name,
            'attendance' => 'Time in',
            'date_time' => now(),
            'location' => $request->input('location', ''),
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'full_name' => $user->full_name ?? $user->name,
            'type' => 'ATTENDANCE',
            'action' => 'MEMBER_TIME_IN',
            'details' => "Member time in: {$user->full_name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Time in recorded successfully.', 'session' => $session]);
    }

    public function timeOut(Request $request): JsonResponse
    {
        $user = Auth::user();

        $session = DutySession::where('volunteer_id', $user->id)
            ->whereNull('time_out')
            ->latest()
            ->first();

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'No active session found.'], 422);
        }

        $session->time_out = now();
        $session->duration_minutes = $session->time_in->diffInMinutes($session->time_out);
        $session->status = 'COMPLETE';
        $session->integrity_score = 100;
        $session->save();

        Attendance::create([
            'full_name' => $user->full_name ?? $user->name,
            'attendance' => 'Time out',
            'date_time' => now(),
            'location' => $request->input('location', ''),
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'full_name' => $user->full_name ?? $user->name,
            'type' => 'ATTENDANCE',
            'action' => 'MEMBER_TIME_OUT',
            'details' => "Member time out: {$user->full_name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Time out recorded successfully.', 'session' => $session]);
    }

    public function status(): JsonResponse
    {
        $user = Auth::user();

        $active = DutySession::where('volunteer_id', $user->id)
            ->whereNull('time_out')
            ->latest()
            ->first();

        $todayTotal = DutySession::where('volunteer_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->whereNotNull('time_out')
            ->sum('duration_minutes');

        return response()->json([
            'hasActiveSession' => $active !== null,
            'activeSession' => $active ? [
                'id' => $active->id,
                'time_in' => $active->time_in?->format('h:i A'),
                'time_in_raw' => $active->time_in?->toIso8601String(),
                'date' => $active->date?->format('M d, Y'),
                'duration' => 0,
            ] : null,
            'todayTotalMinutes' => $todayTotal,
        ]);
    }
}
