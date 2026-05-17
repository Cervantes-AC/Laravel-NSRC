<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\DutySession;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function generateUserActivityReport(array $filters): array
    {
        $query = DutySession::with('volunteer');

        if (!empty($filters['user_id'])) {
            $query->where('volunteer_id', $filters['user_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $data = $query->orderByDesc('date')->get();

        return [
            'data' => $data,
            'meta' => [
                'total_records' => $data->count(),
                'total_duration' => $data->sum('duration_minutes'),
                'filters_applied' => $filters,
                'generated_at' => now()->toDateTimeString(),
            ],
        ];
    }

    public function generateTransactionSummary(array $filters): array
    {
        $query = DutySession::query();

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $summary = (object) [
            'total_transactions' => $query->count(),
            'complete' => (clone $query)->where('status', 'COMPLETE')->count(),
            'ongoing' => (clone $query)->where('status', 'ONGOING')->count(),
            'missing_timeout' => (clone $query)->where('status', 'MISSING_TIMEOUT')->count(),
            'invalid' => (clone $query)->where('status', 'INVALID_LOG')->count(),
        ];

        $data = $query->orderByDesc('created_at')->get();

        return [
            'data' => [
                'summary' => $summary,
                'records' => $data,
            ],
            'meta' => [
                'filters_applied' => $filters,
                'generated_at' => now()->toDateTimeString(),
            ],
        ];
    }

    public function generateAuditTrailReport(array $filters): array
    {
        $query = AuditLog::with('user');

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['action'])) {
            $query->where('action', 'LIKE', '%' . $filters['action'] . '%');
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $data = $query->orderByDesc('created_at')->get();

        return [
            'data' => $data,
            'meta' => [
                'total_records' => $data->count(),
                'filters_applied' => $filters,
                'generated_at' => now()->toDateTimeString(),
            ],
        ];
    }

    public function generateSystemUsageStats(array $filters): array
    {
        $userCount = User::count();
        $sessionCount = DutySession::count();
        $totalDuration = DutySession::sum('duration_minutes');

        $stats = [
            'total_users' => $userCount,
            'total_sessions' => $sessionCount,
            'total_duration_minutes' => $totalDuration,
            'avg_duration_per_session' => $sessionCount > 0 ? round($totalDuration / $sessionCount, 2) : 0,
            'sessions_by_status' => DutySession::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'sessions_by_sector' => DutySession::select('sector', DB::raw('count(*) as count'))
                ->groupBy('sector')
                ->pluck('count', 'sector')
                ->toArray(),
        ];

        return [
            'data' => $stats,
            'meta' => [
                'filters_applied' => $filters,
                'generated_at' => now()->toDateTimeString(),
            ],
        ];
    }

    public function generateCustomReport(array $filters, array $columns): array
    {
        $query = DutySession::with('volunteer');

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $records = $query->get();

        $data = $records->map(function ($record) use ($columns) {
            $row = [];
            foreach ($columns as $column) {
                $row[$column] = data_get($record, $column, null);
            }
            return $row;
        });

        return [
            'data' => $data,
            'meta' => [
                'columns' => $columns,
                'total_records' => $data->count(),
                'filters_applied' => $filters,
                'generated_at' => now()->toDateTimeString(),
            ],
        ];
    }
}
