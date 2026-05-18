<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DutySession;
use App\Models\User;
use App\Services\DutyEngine;
use App\Services\MetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberAttendanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = DutySession::query()
            ->where(function ($query) use ($user) {
                $query->where('volunteer_id', $user->id);

                if ($user->full_name) {
                    $query->orWhere('full_name', $user->full_name);
                }
            });

        if ($request->filled('dateFrom')) {
            $query->whereDate('date', '>=', $request->date('dateFrom'));
        }

        if ($request->filled('dateTo')) {
            $query->whereDate('date', '<=', $request->date('dateTo'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $records = $query->orderByDesc('date')
            ->orderByDesc('time_in')
            ->get();

        return response()->json([
            'results' => [
                'data' => $records->map(fn (DutySession $session) => [
                    'id' => $session->id,
                    'date' => $session->date?->format('M d, Y'),
                    'time_in' => $session->time_in?->format('h:i A'),
                    'time_out' => $session->time_out?->format('h:i A'),
                    'duration_minutes' => $session->duration_minutes,
                    'location' => $session->location,
                    'status' => $session->status,
                ]),
            ],
            'reportStats' => [
                'total_records' => $records->count(),
                'total_duration' => (int) $records->sum('duration_minutes'),
                'generated_at' => now()->toDateTimeString(),
            ],
        ]);
    }

    public function timeIn(Request $request): JsonResponse
    {
        $user = $request->user();
        $now = now();

        $existingOngoing = DutySession::where('volunteer_id', $user->id)
            ->whereNull('time_out')
            ->whereDate('date', today())
            ->first();

        if ($existingOngoing) {
            return response()->json(['success' => false, 'message' => 'You already have an active session. Please log time out first.'], 422);
        }

        $session = DutySession::create([
            'full_name' => $user->full_name ?? $user->name,
            'date' => today(),
            'time_in' => $now,
            'time_out' => null,
            'duration_minutes' => null,
            'status' => 'ONGOING',
            'location' => $request->input('location', 'Member Login'),
            'sector' => 'General',
            'integrity_score' => 60.0,
            'volunteer_id' => $user->id,
            'trace_id' => 'MEMBER-' . strtoupper(substr(md5($user->id . $now), 0, 8)),
        ]);

        Attendance::create([
            'full_name' => $user->full_name ?? $user->name,
            'attendance' => 'time in',
            'date_time' => $now,
            'location' => $request->input('location', 'Member Login'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Time in logged successfully at ' . $now->format('h:i A') . '.',
            'session' => [
                'id' => $session->id,
                'time_in' => $session->time_in?->format('h:i A'),
                'status' => $session->status,
            ],
        ]);
    }

    public function timeOut(Request $request): JsonResponse
    {
        $user = $request->user();
        $now = now();

        $session = DutySession::where('volunteer_id', $user->id)
            ->whereNull('time_out')
            ->whereDate('date', today())
            ->latest()
            ->first();

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'No active session found. Please log time in first.'], 422);
        }

        $dutyEngine = app(DutyEngine::class);
        $duration = $dutyEngine->calculateDuration($session->time_in, $now);

        $session->update([
            'time_out' => $now,
            'duration_minutes' => $duration,
            'status' => $duration >= 1 ? 'COMPLETE' : 'INVALID_LOG',
            'integrity_score' => 100.0,
        ]);

        Attendance::create([
            'full_name' => $user->full_name ?? $user->name,
            'attendance' => 'time out',
            'date_time' => $now,
            'location' => $request->input('location', 'Member Login'),
        ]);

        app(MetricsService::class)->calculateVolunteerMetrics(DutySession::query()->get());

        return response()->json([
            'success' => true,
            'message' => 'Time out logged successfully. Duration: ' . floor($duration / 60) . 'h ' . ($duration % 60) . 'm.',
            'session' => [
                'id' => $session->id,
                'time_in' => $session->time_in?->format('h:i A'),
                'time_out' => $session->time_out?->format('h:i A'),
                'duration_minutes' => $session->duration_minutes,
                'status' => $session->status,
            ],
        ]);
    }
}