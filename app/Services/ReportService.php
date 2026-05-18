<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\DutySession;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    protected AIProviderService $aiProvider;

    protected NameNormalizationService $nameService;

    private const MERGE_THRESHOLD = 85.0;

    public function __construct(AIProviderService $aiProvider, NameNormalizationService $nameService)
    {
        $this->aiProvider = $aiProvider;
        $this->nameService = $nameService;
    }

    public function mergeSimilarNames(Collection $sessions): Collection
    {
        if ($sessions->isEmpty()) {
            return $sessions;
        }

        $groups = collect();
        $assigned = [];

        foreach ($sessions as $session) {
            $name = $session->full_name ?? '';
            if (empty($name)) {
                $groups->push($session);
                continue;
            }

            $matched = false;
            foreach ($groups as $groupKey => $groupSessions) {
                if ($this->nameService->areNamesSimilar($name, $groupKey, self::MERGE_THRESHOLD)) {
                    $groups[$groupKey]->push($session);
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                $groups[$name] = collect([$session]);
            }
        }

        return $groups->map(function ($groupSessions) {
            $first = $groupSessions->first();
            $first->full_name = $groupSessions->pluck('full_name')->unique()->implode(' / ');
            return $groupSessions;
        })->flatten();
    }

    public function generateUserActivityReport(array $filters): array
    {
        $query = DutySession::with('volunteer');

        if (!empty($filters['user_id'])) {
            $query->where('volunteer_id', $filters['user_id']);
        }

        if (!empty($filters['personnel_search'])) {
            $search = $filters['personnel_search'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                    ->orWhereHas('volunteer', function ($volunteer) use ($search) {
                        $volunteer->where('full_name', 'like', '%' . $search . '%')
                            ->orWhere('name', 'like', '%' . $search . '%')
                            ->orWhere('school_id', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
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

        if (!empty($filters['sector'])) {
            $query->where('sector', $filters['sector']);
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
            $query->whereDate('date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['sector'])) {
            $query->where('sector', $filters['sector']);
        }

        if (!empty($filters['personnel_search'])) {
            $search = $filters['personnel_search'];
            $query->where('full_name', 'like', '%' . $search . '%');
        }

        $summary = (object) [
            'total_transactions' => $query->count(),
            'complete' => (clone $query)->where('status', 'COMPLETE')->count(),
            'ongoing' => (clone $query)->where('status', 'ONGOING')->count(),
            'missing_timeout' => (clone $query)->where('status', 'MISSING_TIMEOUT')->count(),
            'invalid' => (clone $query)->where('status', 'INVALID_LOG')->count(),
        ];

        $data = $query->orderByDesc('date')->get();

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
        $query = DutySession::query();

        if (!empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['sector'])) {
            $query->where('sector', $filters['sector']);
        }

        $userCount = User::count();
        $sessionCount = (clone $query)->count();
        $totalDuration = (clone $query)->sum('duration_minutes');

        $stats = [
            'total_users' => $userCount,
            'total_sessions' => $sessionCount,
            'total_duration_minutes' => $totalDuration,
            'avg_duration_per_session' => $sessionCount > 0 ? round($totalDuration / $sessionCount, 2) : 0,
            'sessions_by_status' => (clone $query)->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'sessions_by_sector' => (clone $query)->select('sector', DB::raw('count(*) as count'))
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

    /**
     * Get AI-powered insights for a report
     */
    public function getReportInsights(array $reportData, string $reportType): array
    {
        try {
            $insights = $this->aiProvider->generateReportInsights($reportData, $reportType);

            return [
                'success' => true,
                'insights' => $insights,
                'provider' => $this->aiProvider->getProvider(),
                'generated_at' => now()->toDateTimeString(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => $this->aiProvider->getProvider(),
            ];
        }
    }

    /**
     * Switch AI provider
     */
    public function switchAIProvider(string $provider): self
    {
        $this->aiProvider->switchProvider($provider);
        return $this;
    }

    /**
     * Switch to alternate API key
     */
    public function switchAPIKey(): self
    {
        $this->aiProvider->switchApiKey();
        return $this;
    }

    /**
     * Get current AI provider
     */
    public function getCurrentProvider(): string
    {
        return $this->aiProvider->getProvider();
    }
}
