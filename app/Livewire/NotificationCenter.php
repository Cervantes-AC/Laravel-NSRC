<?php

namespace App\Livewire;

use Illuminate\Notifications\DatabaseNotification;
use Livewire\Component;

class NotificationCenter extends Component
{
    public $notifications;

    public int $unreadCount = 0;

    public bool $fullPage = false;

    public function mount(bool $fullPage = false): void
    {
        $this->fullPage = $fullPage;
        $this->loadNotifications();
    }

    public function loadNotifications(): void
    {
        $user = auth()->user();

        if (!$user) {
            $this->notifications = collect();
            $this->unreadCount = 0;
            return;
        }

        $this->notifications = $user->notifications()->latest()->take(50)->get();
        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function markAsRead(string $id): void
    {
        $notification = DatabaseNotification::find($id);

        if ($notification && (string) $notification->notifiable_id === (string) auth()->id()) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

    public function markAllAsRead(): void
    {
        auth()->user()?->unreadNotifications->markAsRead();
        $this->loadNotifications();
    }

    public function delete(string $id): void
    {
        $notification = DatabaseNotification::find($id);

        if ($notification && (string) $notification->notifiable_id === (string) auth()->id()) {
            $notification->delete();
            $this->loadNotifications();
        }
    }

    public function getListeners(): array
    {
        return [
            'echo-notification' => 'loadNotifications',
        ];
    }

    public function render()
    {
        $view = view('livewire.notification-center');

        return $this->fullPage
            ? $view->layout('components.layouts.app')
            : $view;
    }
}
