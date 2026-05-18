<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user
    ) {}

    public function build(): static
    {
        return $this->subject('Your NSRC AMS Account Has Been Approved')
            ->view('emails.account-approved')
            ->with([
                'name' => $this->user->full_name,
                'loginUrl' => route('login'),
            ]);
    }
}
