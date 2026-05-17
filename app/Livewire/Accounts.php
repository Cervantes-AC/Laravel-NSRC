<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Accounts extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public int $perPage = 25;

    public function approve(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['status' => 'active']);
    }

    public function reject(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['status' => 'rejected']);
    }

    public function suspend(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['status' => 'suspended']);
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $users = $query->orderByDesc('created_at')->paginate($this->perPage);

        return view('livewire.accounts', ['accounts' => $users])
            ->layout('components.layouts.app');
    }
}
