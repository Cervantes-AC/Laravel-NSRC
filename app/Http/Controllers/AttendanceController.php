<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetsAttendanceService;
use App\Services\GoogleSheetsSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly GoogleSheetsAttendanceService $sheets,
        private readonly GoogleSheetsSyncService $sync,
    ) {}

    public function fetchData(Request $request): JsonResponse
    {
        $options = array_filter([
            'name' => $request->input('name'),
            'date' => $request->input('date'),
        ]);

        $data = $this->sheets->fetchAttendanceData($options);

        return response()->json([
            'success' => true,
            'count' => count($data),
            'data' => $data,
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $this->authorize('create', \App\Models\DutySession::class);

        $result = $this->sync->sync(array_filter([
            'name' => $request->input('name'),
            'date' => $request->input('date'),
        ]));

        return response()->json($result);
    }
}
