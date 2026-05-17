<?php

namespace App\Livewire;

use App\Models\AuditLog as AuditLogModel;
use App\Services\DataExportService;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLog extends Component
{
    use WithPagination;

    public string $search = '';

    public string $type = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public int $perPage = 50;

    public function resetFilters(): void
    {
        $this->search = '';
        $this->type = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function export(DataExportService $exportService): void
    {
        $query = AuditLogModel::with('user')->orderByDesc('created_at');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('action', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $logs = $query->get();

        $exportService->exportToCSV($logs, 'audit_log_' . now()->format('Ymd_His'));
    }

    public function render()
    {
        $query = AuditLogModel::with('user');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('action', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $logs = $query->orderByDesc('created_at')->paginate($this->perPage);

        return view('livewire.audit-log', ['logs' => $logs])
            ->layout('components.layouts.app');
    }
}
