<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\DutySession;
use App\Models\VolunteerMetrics;
use App\Services\DutyEngine;
use App\Services\MetricsService;
use App\Services\MySQLAttendanceService;
use Livewire\Component;
use Livewire\WithPagination;

class DutySessionTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $sector = '';

    public string $location = '';

    public string $duration = '';

    public string $integrity = '';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public int $perPage = 25;

    public ?string $syncMessage = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sector' => ['except' => ''],
        'location' => ['except' => ''],
        'duration' => ['except' => ''],
        'integrity' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingSector(): void
    {
        $this->resetPage();
    }

    public function updatingLocation(): void
    {
        $this->resetPage();
    }

    public function updatingDuration(): void
    {
        $this->resetPage();
    }

    public function updatingIntegrity(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'status', 'sector', 'location', 'duration', 'integrity', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function syncAttendance(): void
    {
        $service = app(MySQLAttendanceService::class);
        $records = $service->fetchAttendanceData();

        if (empty($records)) {
            $this->syncMessage = 'No data found in MySQL attendance source.';
            return;
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $seenInBatch = [];

        foreach ($records as $record) {
            $signature = $service->recordSignature($record);
            if (isset($seenInBatch[$signature])) {
                $skipped++;
                continue;
            }
            $seenInBatch[$signature] = true;

            $exists = Attendance::where('source_signature', $signature)->exists();
            if ($exists) {
                $skipped++;
                continue;
            }

            Attendance::create([
                'full_name' => $record['fullName'],
                'attendance' => $record['attendance'],
                'date_time' => $record['dateTime'],
                'location' => $record['location'],
                'shift_type' => $record['shiftType'],
                'source_signature' => $signature,
                'source_payload' => $record['payload'] ?? [],
            ]);
            $imported++;
        }

        if ($imported > 0) {
            $dutyEngine = app(DutyEngine::class);
            $logs = Attendance::query()->orderBy('date_time')->get();
            $sessions = $dutyEngine->processDutyLogs($logs);
            $created = 0;
            $updated = 0;

            foreach ($sessions as $session) {
                $match = DutySession::query()
                    ->where('full_name', $session->full_name)
                    ->whereDate('date', $session->date)
                    ->when($session->time_in, fn ($q) => $q->where('time_in', $session->time_in))
                    ->first();

                if ($match) {
                    $match->update($session->toArray());
                    $updated++;
                } else {
                    DutySession::create($session->toArray());
                    $created++;
                }
            }

            VolunteerMetrics::query()->delete();
            app(MetricsService::class)->calculateVolunteerMetrics(DutySession::query()->get());
        }

        $this->syncMessage = sprintf(
            'MySQL sync complete: %d imported, %d skipped, %d sessions created, %d updated.',
            $imported, $skipped, $created ?? 0, $updated ?? 0
        );

        if (! empty($errors)) {
            $this->syncMessage .= ' Errors: ' . implode(' ', $errors);
        }

        $this->resetPage();
    }

    public function render()
    {
        $query = DutySession::query()->with('volunteer');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('volunteer', function ($volunteer) {
                        $volunteer->where('full_name', 'like', '%' . $this->search . '%')
                            ->orWhere('name', 'like', '%' . $this->search . '%')
                            ->orWhere('school_id', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if ($this->sector !== '') {
            $query->where('sector', $this->sector);
        }

        if ($this->location !== '') {
            $query->where('location', $this->location);
        }

        if ($this->duration === 'completed_hours') {
            $query->whereNotNull('duration_minutes')->where('duration_minutes', '>', 0);
        } elseif ($this->duration === 'under_4h') {
            $query->whereBetween('duration_minutes', [1, 239]);
        } elseif ($this->duration === '4h_8h') {
            $query->whereBetween('duration_minutes', [240, 480]);
        } elseif ($this->duration === 'over_8h') {
            $query->where('duration_minutes', '>', 480);
        } elseif ($this->duration === 'missing') {
            $query->whereNull('duration_minutes');
        }

        if ($this->integrity === 'high') {
            $query->where('integrity_score', '>=', 90);
        } elseif ($this->integrity === 'medium') {
            $query->whereBetween('integrity_score', [70, 89.99]);
        } elseif ($this->integrity === 'low') {
            $query->where('integrity_score', '<', 70);
        }

        if ($this->dateFrom) {
            $query->whereDate('date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('date', '<=', $this->dateTo);
        }

        $totalMinutes = (clone $query)->sum('duration_minutes');
        $completeCount = (clone $query)->where('status', 'COMPLETE')->count();
        $filteredCount = (clone $query)->count();

        $sessions = $query->orderByDesc('date')->orderByDesc('time_in')->paginate($this->perPage);
        $sectors = DutySession::query()->whereNotNull('sector')->distinct()->orderBy('sector')->pluck('sector');
        $locations = DutySession::query()->whereNotNull('location')->distinct()->orderBy('location')->pluck('location');

        return view('livewire.duty-session-table', [
            'sessions' => $sessions,
            'sectors' => $sectors,
            'locations' => $locations,
            'filteredCount' => $filteredCount,
            'totalMinutes' => $totalMinutes,
            'completeCount' => $completeCount,
        ]);
    }
}
