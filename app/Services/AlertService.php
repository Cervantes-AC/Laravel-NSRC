<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class AlertService
{
    public function __construct(
        private readonly NotificationService $notifications,
    ) {}

    public function checkFailedLoginAttempts(User $user, int $attempts): void
    {
        if ($attempts >= 3) {
            $this->notifications->sendSecurityAlert(
                $user,
                "Security alert: {$attempts} failed login attempts detected for {$user->email}."
            );

            $admins = User::query()->where('role', 'admin')->where('status', 'active')->get();
            foreach ($admins as $admin) {
                $this->notifications->sendWarningAlert(
                    $admin,
                    "Failed login threshold reached for {$user->email} ({$attempts} attempts)."
                );
            }
        }
    }

    public function checkStorageCapacity(): ?string
    {
        $disk = Storage::disk('local');
        $total = disk_total_space(storage_path()) ?: 1;
        $free = disk_free_space(storage_path()) ?: 0;
        $usedPercent = (int) round((($total - $free) / $total) * 100);

        if ($usedPercent < 85) {
            return null;
        }

        return "Storage is {$usedPercent}% full. Consider clearing old backups and logs.";
    }

    public function confirmRecordDeletion(string $modelLabel): string
    {
        return "You are about to delete {$modelLabel}. This uses soft delete and can be restored by an admin.";
    }
}
