<?php

namespace App\Policies;

use App\Models\DutySession;
use App\Models\User;

class DutySessionPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'member'], true);
    }

    public function view(User $user, DutySession $session): bool
    {
        return $user->role === 'admin' || $user->id === $session->volunteer_id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, DutySession $session): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, DutySession $session): bool
    {
        return $user->role === 'admin';
    }

    public function restore(User $user, DutySession $session): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, DutySession $session): bool
    {
        return $user->role === 'admin';
    }
}
