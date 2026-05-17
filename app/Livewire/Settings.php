<?php

namespace App\Livewire;

use App\Models\UserPreference;
use Livewire\Component;

class Settings extends Component
{
    public string $theme = 'light';

    public bool $notificationEnabled = true;

    public bool $emailNotifications = true;

    public bool $smsNotifications = false;

    public string $activeTab = 'preferences';

    public function mount(): void
    {
        $user = auth()->user();

        if (!$user) {
            return;
        }

        $prefs = $user->preferences;

        if ($prefs) {
            $this->theme = $prefs->theme ?? 'light';
            $this->notificationEnabled = $prefs->notification_enabled ?? true;
            $this->emailNotifications = $prefs->email_notifications ?? true;
            $this->smsNotifications = $prefs->sms_notifications ?? false;
        }
    }

    public function savePreferences(): void
    {
        $user = auth()->user();

        if (!$user) {
            return;
        }

        UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'theme' => $this->theme,
                'notification_enabled' => $this->notificationEnabled,
                'email_notifications' => $this->emailNotifications,
                'sms_notifications' => $this->smsNotifications,
            ]
        );

        session()->flash('message', 'Preferences saved successfully.');
    }

    public function render()
    {
        return view('livewire.settings')
            ->layout('components.layouts.app');
    }
}
