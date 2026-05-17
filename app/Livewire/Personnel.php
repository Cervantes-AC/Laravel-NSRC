<?php

namespace App\Livewire;

use App\Models\DutySession;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Personnel extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortBy = 'name';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    public string $complianceFilter = 'all'; // 'all', 'issues_only', 'compliance_only'

    public string $viewMode = 'list'; // 'list' or 'grid'

    public bool $showFormula = false;

    public ?string $selectedPersonnelName = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingComplianceFilter(): void
    {
        $this->resetPage();
    }

    public function updatingViewMode(): void
    {
        $this->resetPage();
    }

    public function toggleSort(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleFormula(): void
    {
        $this->showFormula = !$this->showFormula;
    }

    public function viewHistory(string $name): void
    {
        $this->selectedPersonnelName = $name;
    }

    public function closeHistory(): void
    {
        $this->selectedPersonnelName = null;
    }

    public function nextPage(): void
    {
        $this->setPage($this->getPage() + 1);
    }

    public function previousPage(): void
    {
        $this->setPage(max(1, $this->getPage() - 1));
    }

    public function gotoPage(int $page): void
    {
        $this->setPage($page);
    }

    private function deriveIssues(DutySession $session): array
    {
        $issues = [];

        // Missing timeout
        if (!$session->time_out) {
            $issues[] = [
                'date' => $session->date->format('Y-m-d'),
                'type' => 'MISSING_TIMEOUT',
                'description' => "No time-out recorded on {$session->date->format('Y-m-d')}. Session appears still open.",
            ];
        }

        // Zero duration
        if ($session->time_in && $session->time_out) {
            $inMs = $session->time_in->timestamp * 1000;
            $outMs = $session->time_out->timestamp * 1000;
            if ($outMs - $inMs <= 0) {
                $issues[] = [
                    'date' => $session->date->format('Y-m-d'),
                    'type' => 'ZERO_DURATION',
                    'description' => "Time-out is not after time-in on {$session->date->format('Y-m-d')}.",
                ];
            }
        }

        // Future date
        if ($session->date && $session->date > now()->toDateString()) {
            $issues[] = [
                'date' => $session->date->format('Y-m-d'),
                'type' => 'FUTURE_DATE',
                'description' => "Session date {$session->date->format('Y-m-d')} is in the future.",
            ];
        }

        return $issues;
    }

    private function calculateMetrics(User $user): array
    {
        $sessions = $user->dutySessions()->get();
        $totalRegularMinutes = 0;
        $totalOvertimeMinutes = 0;
        $totalUndertimeMinutes = 0;
        $invalidRecordCount = 0;
        $allIssues = [];

        foreach ($sessions as $session) {
            $issues = $this->deriveIssues($session);
            if (count($issues) > 0) {
                $invalidRecordCount++;
                $allIssues = array_merge($allIssues, $issues);
            }

            // Calculate hours
            if ($session->time_in && $session->time_out) {
                $minutes = $session->time_in->diffInMinutes($session->time_out);

                if ($minutes < 60) {
                    $totalUndertimeMinutes += $minutes;
                } elseif ($minutes <= 480) { // 8 hours
                    $totalRegularMinutes += $minutes;
                } else {
                    $totalRegularMinutes += 480;
                    $totalOvertimeMinutes += ($minutes - 480);
                }
            }
        }

        return [
            'sessionCount' => $sessions->count(),
            'totalRegularMinutes' => $totalRegularMinutes,
            'totalOvertimeMinutes' => $totalOvertimeMinutes,
            'totalUndertimeMinutes' => $totalUndertimeMinutes,
            'invalidRecordCount' => $invalidRecordCount,
            'issues' => $allIssues,
            'lastActive' => $sessions->first()?->date?->format('Y-m-d'),
        ];
    }

    public function render()
    {
        // Get all users with their metrics
        $allUsers = User::query()
            ->where('role', '!=', 'admin')
            ->get();

        $enrichedData = $allUsers->map(function (User $user) {
            $metrics = $this->calculateMetrics($user);

            return [
                'id' => $user->id,
                'fullName' => $user->full_name,
                'volunteerId' => "REG-{$user->id}",
                'email' => $user->email,
                'serialNumber' => $user->serial_number ?? "REG-{$user->id}",
                'role' => $user->role ?? 'member',
                'avatar' => $user->avatar,
                'sessionCount' => $metrics['sessionCount'],
                'totalRegularMinutes' => $metrics['totalRegularMinutes'],
                'totalOvertimeMinutes' => $metrics['totalOvertimeMinutes'],
                'totalUndertimeMinutes' => $metrics['totalUndertimeMinutes'],
                'invalidRecordCount' => $metrics['invalidRecordCount'],
                'issues' => $metrics['issues'],
                'lastActive' => $metrics['lastActive'],
            ];
        });

        // Apply search filter
        if ($this->search) {
            $searchLower = strtolower($this->search);
            $enrichedData = $enrichedData->filter(function ($item) use ($searchLower) {
                return str_contains(strtolower($item['fullName']), $searchLower) ||
                       str_contains(strtolower($item['serialNumber']), $searchLower) ||
                       str_contains(strtolower($item['email']), $searchLower);
            });
        }

        // Apply compliance filter
        if ($this->complianceFilter === 'issues_only') {
            $enrichedData = $enrichedData->filter(fn($item) => $item['invalidRecordCount'] > 0);
        } elseif ($this->complianceFilter === 'compliance_only') {
            $enrichedData = $enrichedData->filter(fn($item) => $item['invalidRecordCount'] === 0);
        }

        // Apply sorting
        $enrichedData = $enrichedData->sort(function ($a, $b) {
            $cmp = 0;

            match ($this->sortBy) {
                'name' => $cmp = strcmp($a['fullName'], $b['fullName']),
                'sessions' => $cmp = $a['sessionCount'] <=> $b['sessionCount'],
                'hours' => $cmp = ($a['totalRegularMinutes'] + $a['totalOvertimeMinutes']) <=> ($b['totalRegularMinutes'] + $b['totalOvertimeMinutes']),
                'issues' => $cmp = $a['invalidRecordCount'] <=> $b['invalidRecordCount'],
                'role' => $cmp = strcmp($a['role'], $b['role']),
                default => $cmp = 0,
            };

            return $this->sortDirection === 'asc' ? $cmp : -$cmp;
        });

        // Calculate summary stats
        $totalIssues = $enrichedData->sum('invalidRecordCount');
        $cleanCount = $enrichedData->filter(fn($item) => $item['invalidRecordCount'] === 0)->count();
        $issueCount = $enrichedData->count() - $cleanCount;
        $totalHours = $enrichedData->sum(fn($item) => $item['totalRegularMinutes'] + $item['totalOvertimeMinutes']);

        // Paginate
        $page = $this->getPage();
        $perPage = $this->viewMode === 'grid' ? 12 : 10;
        $total = $enrichedData->count();
        $paginatedData = $enrichedData->slice(($page - 1) * $perPage, $perPage)->values();
        $totalPages = ceil($total / $perPage);

        // Get history sessions if a person is selected
        $historySessions = [];
        if ($this->selectedPersonnelName) {
            $historySessions = DutySession::where('full_name', $this->selectedPersonnelName)
                ->orderBy('date', 'desc')
                ->get();
        }

        return view('livewire.personnel', [
            'personnel' => $paginatedData,
            'totalPersonnel' => $enrichedData->count(),
            'cleanCount' => $cleanCount,
            'issueCount' => $issueCount,
            'totalIssues' => $totalIssues,
            'totalHours' => $totalHours,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'historySessions' => $historySessions,
        ]);
    }
}
