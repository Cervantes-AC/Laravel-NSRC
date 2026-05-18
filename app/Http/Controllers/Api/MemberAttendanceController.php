<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DutySession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberAttendanceController extends Controller
{
    public function __invoke(Request $request): JsonResponse
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
}
