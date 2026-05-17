<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Accounts extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public int $perPage = 25;

    public string $bulkAction = '';

    public bool $selectAll = false;

    /** @var array<int, int|string> */
    public array $selectedAccounts = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selectedAccounts = User::query()
                ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->all();
        } else {
            $this->selectedAccounts = [];
        }
    }

    public function approve(int $userId): void
    {
        User::findOrFail($userId)->update(['status' => 'active']);
    }

    public function reject(int $userId): void
    {
        User::findOrFail($userId)->update(['status' => 'rejected']);
    }

    public function suspend(int $userId): void
    {
        User::findOrFail($userId)->update(['status' => 'suspended']);
    }

    public function executeBulkAction(): void
    {
        if ($this->bulkAction === '' || empty($this->selectedAccounts)) {
            return;
        }

        $ids = array_map('intval', $this->selectedAccounts);
        $status = match ($this->bulkAction) {
            'approve' => 'active',
            'suspend' => 'suspended',
            'reject' => 'rejected',
            default => null,
        };

        if ($status) {
            User::whereIn('id', $ids)->update(['status' => $status]);
        }

        $this->selectedAccounts = [];
        $this->selectAll = false;
        $this->bulkAction = '';
    }

    public function render()
    {
        $query = User::query();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        $accounts = $query->orderByDesc('created_at')->paginate($this->perPage);

        return view('livewire.accounts', compact('accounts'));
    }
}
