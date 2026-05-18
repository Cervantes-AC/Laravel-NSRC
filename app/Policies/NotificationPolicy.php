<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;

class NotificationPolicy
{
    /**
     * Determine whether the user can view the notification
     */
    public function view(User $user, Notification $notification): bool
    {
        // Users can only view their own notifications
        return $notification->notifiable_id === $user->id &&
               $notification->notifiable_type === User::class;
    }

    /**
     * Determine whether the user can update the notification
     */
    public function update(User $user, Notification $notification): bool
    {
        // Users can only update their own notifications
        return $notification->notifiable_id === $user->id &&
               $notification->notifiable_type === User::class;
    }

    /**
     * Determine whether the user can delete the notification
     */
    public function delete(User $user, Notification $notification): bool
    {
        // Users can only delete their own notifications
        return $notification->notifiable_id === $user->id &&
               $notification->notifiable_type === User::class;
    }

    /**
     * Determine whether the user can restore the notification
     */
    public function restore(User $user, Notification $notification): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the notification
     */
    public function forceDelete(User $user, Notification $notification): bool
    {
        return false;
    }
}
