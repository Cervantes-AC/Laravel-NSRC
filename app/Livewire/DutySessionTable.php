<?php

namespace App\Livewire;

use App\Models\DutySession;
use Livewire\Component;
use Livewire\WithPagination;

class DutySessionTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $sector = '';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public int $perPage = 25;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sector' => ['except' => ''],
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

    public function clearFilters(): void
    {
        $this->reset(['search', 'status', 'sector', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function render()
    {
        $query = DutySession::query()->with('volunteer');

        if ($this->search !== '') {
            $query->where('full_name', 'like', '%' . $this->search . '%');
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if ($this->sector !== '') {
            $query->where('sector', $this->sector);
        }

        if ($this->dateFrom) {
            $query->whereDate('date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('date', '<=', $this->dateTo);
        }

        $sessions = $query->latest('date')->paginate($this->perPage);
        $sectors = DutySession::query()->whereNotNull('sector')->distinct()->orderBy('sector')->pluck('sector');

        return view('livewire.duty-session-table', [
            'sessions' => $sessions,
            'sectors' => $sectors,
        ]);
    }
}
