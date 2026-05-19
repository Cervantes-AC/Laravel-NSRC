<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DutySessionAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $alertType,
        public string $alertMessage,
        public ?string $sessionDetails = null
    ) {}

    public function build(): static
    {
        $subject = match ($this->alertType) {
            'time_in' => 'Duty Session Started',
            'time_out' => 'Duty Session Ended',
            'reminder' => 'Duty Session Reminder',
            default => 'Duty Session Update',
        };

        return $this->subject($subject)
            ->view('emails.duty-alert')
            ->with([
                'name' => $this->user->full_name,
                'alertType' => $this->alertType,
                'alertMessage' => $this->alertMessage,
                'sessionDetails' => $this->sessionDetails,
            ]);
    }
}
