<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?string $reason = null
    ) {}

    public function build(): static
    {
        return $this->subject('Your NSRC AMS Account Registration Update')
            ->view('emails.account-rejected')
            ->with([
                'name' => $this->user->full_name,
                'reason' => $this->reason,
            ]);
    }
}
